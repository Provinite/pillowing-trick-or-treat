<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function trickOrTreat() {
        if ($this->session->userdata('uuid') === false) {
            $this->output->set_status_header('401');
            die();
        }
        $enforceResets = $this->config->item('enforce_reset_timers');
        $reset_time = $this->config->item('reset_time');
        $reset_time_watching = $this->config->item('reset_time_watching');
        $isWatching = $this->session->userdata('is_watching');

        $this->db->query("LOCK TABLES `trick_or_treat_events` WRITE, `win_events` WRITE, `prizes` WRITE;");
        //fetch our user's latest ToT event
        $lastEvent = $this->trickortreateventdao->getFirstHavingUser_IDEqualsOrderDescByDate_Time($this->session->userdata('uuid'));

        if (count($lastEvent) == 0) {
            $lastEvent = array(new TrickOrTreatEvent());
            $lastEvent[0]->setDateTime(date("Y-m-d H:i:s", strtotime("-100 days")));
        }

        $now = new DateTime();
        $lastTime = new DateTime($lastEvent[0]->getDateTime());

        $lastReset = new DateTime($reset_time);
        $lastWatchReset = new DateTime($reset_time_watching);

        $nextReset = new DateTime($reset_time);
        $nextWatchReset = new DateTime($reset_time_watching);

        while ($lastReset > $now) {
            $lastReset->modify("-1 day");
            $nextReset->modify("-1 day");
        }

        while ($lastWatchReset > $now) {
            $lastWatchReset->modify("-1 day");
            $nextWatchReset->modify("-1 day");
        }

        $nextWatchReset->modify("+1 day");
        $nextReset->modify("+1 day");

        if ($isWatching === true) {
            $lastReset = ($lastReset > $lastWatchReset) ? $lastReset : $lastWatchReset;
            $nextReset = ($nextReset < $nextWatchReset) ? $nextReset : $nextWatchReset;
        }

        if ($enforceResets === false) {
            $lastTime = $lastReset;
        }

        if ($lastTime > $lastReset) { //no trick or treating yet
            $this->db->query("UNLOCK TABLES;");
            $return = new stdClass();

            $tor = $nextReset->diff($now);
            $tor->format("H:i:s");

            $return->result = false;
            $return->time_until_reset = $tor->format("%H:%I:%S");

            header("Content-type: application/json");
            echo(json_encode($return));
            die();
        } else { //tricky treats time
            $winrate = $this->config->item('win_rate');
            $isWinner = (random_int(0, 99999) <= $winrate*1000);
            $prizesLeft = false;
            if ($isWinner) {
                $prizeWon = null;
                $prizes = $this->prizedao->getWithStockGreaterThan(0);
                $prizeRanges = array();
                $start = 0;
                $end = 0;
                foreach ($prizes as $prize) {
                    $range = $prize->getStock() * $prize->getWeight();
                    $end = floor($start + 100*$range) - 1;
                    $prizeRanges[] = array(
                        'min' => $start,
                        'max' => $end,
                        'prize' => $prize
                    );
                    $start = $end + 1;
                }
                if ($end > 0) { //there's still prizes left
                    $prizesLeft = true;
                    $selection = random_int(0, $end);
                    foreach ($prizeRanges as $range) {
                        if ($selection >= $range['min'] && $selection <= $range['max']) {
                            $prizeWon = $range['prize'];
                            break;
                        }
                    }
                    if (false) { $prizeWon = new Prize(); };
                    $prizeWon->setStock($prizeWon->getStock() - 1);
                    $uuid = $this->session->userdata('uuid');
                    $ip = $this->session->userdata('ip_address');

                    $tEvent = new TrickOrTreatEvent();
                    $tEvent->setWinLoss("win");
                    $tEvent->setIpAddress($ip);
                    $tEvent->setUserId($uuid);
                    $tEvent->setDateTime($now->format("Y-m-d H:i:s"));

                    $wEvent = new WinEvent();
                    $wEvent->setPrizeId($prizeWon->getId());
                    $wEvent->setDatetime($now->format("Y-m-d H:i:s"));
                    $wEvent->setUserId($uuid);
                    $wEvent->setIpAddress($ip);

                    $this->trickortreateventdao->save($tEvent);
                    $this->wineventdao->save($wEvent);
                    $this->prizedao->save($prizeWon);
                    $this->db->query("UNLOCK TABLES;");

                    $return = new stdClass();
                    $return->result = true;
                    $return->image = $this->trickortreatmodel->getImageLink($prizeWon->getImage());
                    $return->prize = $prizeWon->getName();
                    $return->description = $prizeWon->getDescription();

                    header("Content-type: application/json");

                    echo json_encode($return);
                    die();
                }
            }

            if (!$isWinner || $prizesLeft == false) {
                //pick a candy somehow
                //save a ToT event as a loss
                $uuid = $this->session->userdata('uuid');
                $ip = $this->session->userdata('ip_address');

                $tEvent = new TrickOrTreatEvent();
                $tEvent->setWinLoss("loss");
                $tEvent->setIpAddress($ip);
                $tEvent->setUserId($uuid);
                $tEvent->setDateTime($now->format("Y-m-d H:i:s"));
                $this->db->query("UNLOCK TABLES;");

                $this->trickortreateventdao->save($tEvent);

                $return = new stdClass();
                $return->result = true;
                $return->prize = "A Sweet Treat!";
                $return->image = $this->trickortreatmodel->getRandomFile();
                $return->description = "Oh wow, candy! This really is Halloween after all, jeez looks like you were scared for nothing. You'll have to try again later to see what other treats you get!";

                header("Content-type: application/json");

                echo json_encode($return);
                die();
            }
        }
    }

    public function index() {
        $viewData = array();
        if ($this->session->userdata('error_message') !== false) {
            $viewData['hasError'] = true;
            $viewData['errorMessage'] = $this->session->userdata('error_message');
            $this->session->unset_userdata('error_message');
        } else {
            $viewData['hasError'] = false;
            $viewData['errorMessage'] = "";
        }
        if ($this->session->userdata('uuid')) {
            $viewData['loggedIn'] = true;
            $viewData['username'] = $this->session->userdata('username');
            $viewData['icon'] = $this->session->userdata('usericon');
        } else {
            $viewData['loggedIn'] = false;
            $viewData['icon'] = '';
            $viewData['username'] = '';
        }

        $this->load->view('app', $viewData);
    }

    public function myprizes() {
        if ($this->session->userdata('uuid') === false) {
            $this->output->set_status_header('401');
            die();
        }

        $ret = new stdClass();
        $ret->result = true;
        $ret->count = 0;
        $ret->events = array();

        $events = $this->trickortreateventdao->getWithUser_IdEquals($this->session->userdata('uuid'));
        foreach ($events as $event) {
            $evt = new stdClass();
            if ($event->getWinLoss() == "win") {
                $evt->win = true;
                $evt->datetime = $event->getDateTime();
                $winevent = $this->wineventdao->getWithUser_IdEqualsAndDate_TimeEquals($this->session->userdata('uuid'), $evt->datetime);
                $winevent = $winevent[0];

                $theprize = $this->prizedao->get($winevent->getPrizeId());

                $evt->prize = $theprize->getName();
            } else {
                $evt->win = false;
                $evt->datetime = $event->getDateTime();
                $evt->prize = "";
            }
            $ret->events[] = $evt;
            $ret->count++;
        }

        header("Content-type: application/json");

        echo json_encode($ret);
        die();
    }

}

/* end of file welcome.php */
/* location ./trickortreat/controllers/welcome.php */