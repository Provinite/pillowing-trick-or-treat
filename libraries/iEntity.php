<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

interface iEntity {
    public function getIdField();
    public function getId();
}