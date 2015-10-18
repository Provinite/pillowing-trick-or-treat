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
        $this->load->view('test');
    }

}

/* end of file test.php */
/* location ./trickortreat/controllers/test.php */