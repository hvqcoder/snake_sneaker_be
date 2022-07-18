<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once(APPPATH . '../vendor/autoload.php');

class Pdf extends \Mpdf\Mpdf { 
    public function __construct() { 
        parent::__construct();
        return new \Mpdf\Mpdf();
    }   
} 
?>