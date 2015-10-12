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

        echo "<pre>";
        print_r($this->session->all_userdata());
    }

    public function apiview() {

    }

}

/* end of file test.php */
/* location ./trickortreat/controllers/test.php */