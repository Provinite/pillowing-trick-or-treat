<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

    public function apientry() {
        $this->session->set_userdata('state', 'myState');
        echo "<pre>Auth URL: " . $this->deviantartapi->authorizationUrl('myState') . "\n";
        echo '<a href="' . $this->deviantartapi->authorizationUrl('myState') . '">Auth Link</a>' . "\n";
    }

    public function apireturn() {
        $code = $this->input->get('code');
        $this->deviantartapi->setAuthCode($code);
        $this->deviantartapi->requestToken();
        $result = $this->deviantartapi->whoami();
        $result = $result['result'];
        echo "<pre>";
        echo "\n";
        echo "Logged in as: <img src=\"" . $result->usericon . "\" />" . $result->username;
        echo "\n";
        print_r($result);
    }

    public function apiview() {
        echo "<pre>";
        echo "Watching? ";
        print_r($this->deviantartapi->userIsWatching("pillowing-pile"));
    }

    public function sample() {
        $this->output->enable_profiler(TRUE);
        $string = "getHavingNameLike";
        echo $string . "('name')";
        echo '<pre>';
        print_r($this->prizedao->$string('prize'));
    }

    public function trickOrTreat() {
        $reset_time = $this->config->item('reset_time');
        $reset_time_watching = $this->config->item('reset_time_watching');
        $isWatching = $this->session->userdata('is_watching');

        $this->db->query("LOCK TABLES `trick_or_treat_events` WRITE, `win_events` WRITE, `prizes` WRITE;");
        //fetch our last event
        $lastEvent = $this->trickortreateventdao->getFirstHavingUser_IDEqualsOrderDescByDate_Time($this->session->userdata('uuid'));

        $now = new DateTime();
        $lastTime = new DateTime($lastEvent[0]->getDateTime());

        $lastReset = new DateTime($reset_time);
        $lastWatchReset = new DateTime($reset_time_watching);

        $nextReset = new DateTime($reset_time);
        $nextWatchReset = new DateTime($reset_time_watching);

        $last = null;

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

        if ($lastTime < $lastReset) { //no trick or treating yet
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
                    $return->image = $prizeWon->getImage();
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
                $return->image = ""; // todo: pick a random image
                $return->description = "";

                header("Content-type: application/json");

                echo json_encode($return);
                die();
            }
        }
    }

    public function getRandomPrize() {
        $this->output->enable_profiler();
        $prizes = $this->prizedao->getWithStockGreaterThan(0);
        echo "<pre>";
        $stats = array(0,0,0,0,0,0,0,0);
        $iterations = 12000;
        $winrate = 60;
        $prizeRanges = array();
        for ($k = 0; $k < $iterations; $k++) {
            $winner = (random_int(0, 9999) <= $winrate);
            if (!$winner) {
                $stats[0]++;
                continue;
            }
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
            if ($end <= 0) { $stats[0]++; continue; }
            $selection = random_int(0, $end);
            foreach ($prizeRanges as $range) {
                if ($selection >= $range['min'] && $selection <= $range['max']) {
                    $prize = $range['prize'];
                    $prize->setStock($prize->getStock() - 1);
                    $stats[$range['prize']->getId()]++;

                    break;
                }
            }
        }
        $totalwinners = 0;
        foreach ($stats as $key => $stat) {
            $prizename = "";
            if ($stat == 0) {
                continue;
            }
            if ($key > 0) {
                $totalwinners += $stat;
            }
            if ($key > 0 && array_key_exists($key-1, $prizes)) {
                $prizename = $prizes[$key-1]->getName();
            }
            $percent = $stat / $iterations;
            $percent = $percent * 100;
            echo "$key: $stat $prizename ($percent%)\n";
        }
        echo "Win Rate: " . $winrate / 10000 * 100 . "%\n";
        echo "Total Winners: $totalwinners (" . ($totalwinners - 81) . ")";
        echo "\n";
    }

    public function patchTest() {
        $u1 = new User();
        $u2 = new User();

        $u1->setUsername("Provinite");
        $u1->setAccessToken("ATOKEN");
        $u1->setAuthCode("ACODE");
        $u1->setIpAddress("1.1.1.1");
        $u1->setRefreshToken("RTOKEN");

        $u2->setUsername("CloverCoin");

        echo "<pre>";
        print_r($this->userdao->patch($u1, $u2));
    }

    public function generateSchema() {
        $this->prizedao->generateSchema();
        $this->trickortreateventdao->generateSchema();
        $this->userdao->generateSchema();
        $this->wineventdao->generateSchema();
    }

    public function index() {
        $viewData = array();
        if ($this->session->userdata('uuid')) {
            $viewData['loggedIn'] = true;
            $viewData['username'] = $this->session->userdata('username');
            $viewData['icon'] = $this->session->userdata('usericon');
        } else {
            $viewData['loggedIn'] = false;
            $viewData['icon'] = '';
            $viewData['username'] = '';
        }

        $this->load->view('test', $viewData);
    }

}

/* end of file test.php */
/* location ./trickortreat/controllers/test.php */