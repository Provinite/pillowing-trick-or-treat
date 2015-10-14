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

    public function populateJunkData() {

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

    public function dbTest() {
        $u1 = $this->userdao->get("001");
        echo "<pre>";
        echo "u1\n";

        print_r($u1);

        $this->userdao->delete($u1);

        $result = $this->userdao->get("001");
        if ($result === false) {
            echo "Not found: GET returned FALSE";
        }
        $result = $this->userdao->exists("001");
        if ($result === false) {
            echo "Not found: EXISTS returned FALSE";
        }
    }

}

/* end of file test.php */
/* location ./trickortreat/controllers/test.php */