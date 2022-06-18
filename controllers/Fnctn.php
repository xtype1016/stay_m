<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Fnctn extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $this->load->model('milla_m');
        $this->load->library('form_validation');
        //$this->load->library('session');
        $this->load->helper('url');
        //$this->load->helper('cookie');
        //$this->load->helper('My_alert_log');

        $usr_no = get_cookie('usr_no');
        $idntfr = get_cookie('idntfr');

        if (!isset($_SESSION['usr_no']) && strlen($usr_no) > 0 && strlen($idntfr) > 0)
        {
            info_log("milla/fnctn", "autologin start!");
            milla_auto_login();
        }

        milla_login_chk();
    }

    public function getLctgr(){
        // POST data
        //$postData = $this->input->post();
        $postData = $this->input->post('cmpny_cls', 'TRUE');

        //info_log("milla/fnctn/getLctgr", "postData = [" . $postData[0] . "]");
        //print_r($postData);

        // get data
        $data = $this->milla_m->get_meta_list('LCTGR_CLS_01', '', 'data');
        echo json_encode($data);
      }

}
