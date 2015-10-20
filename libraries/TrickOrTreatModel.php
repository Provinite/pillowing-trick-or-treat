<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TrickOrTreatModel {
    private $lossPath;
    private $lossUrl;
    private $winUrl;

    private $CI;

    public function __construct($data) {
        $this->lossPath = $data['loss_img_path'];
        $this->lossUrl = $data['loss_img_base'];
        $this->winUrl = $data['win_img_base'];
        $this->winPath = $data['win_img_path'];

        $this->CI =& get_instance();
    }

    public function getRandomFile($dir = null) {
        if ($dir === null) {
            $dir = $this->lossPath;
        }
        $files = glob($dir . '*');
        $file = $files[random_int(0, count($files) - 1)];
        return $this->lossUrl . basename($file);
    }

    public function getImageLink($filename) {
        return $this->winUrl . $filename;
    }
}