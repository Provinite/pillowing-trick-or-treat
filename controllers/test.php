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