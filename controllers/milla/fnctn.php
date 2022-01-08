<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Fnctn extends CI_Controller {

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
        $cmpny_cls = $this->input->post('cmpny_cls', 'TRUE');

        info_log("milla/fnctn/getLctgr", "cmpny_cls = [" . $cmpny_cls . "]");

        // get data
        $lctgr_nm = 'LCTGR_CLS_' . $cmpny_cls;
        $data = $this->milla_m->get_meta_list($lctgr_nm, '', 'data');

        echo json_encode($data);
    }

    public function getCtgr(){
        // POST data
        //$postData = $this->input->post();
        $cmpny_cls = $this->input->post('cmpny_cls', 'TRUE');
        $lctgr_cls = $this->input->post('lctgr_cls', 'TRUE');

        info_log("milla/fnctn/getCtgr", "cmpny_cls = [" . $cmpny_cls . "]");
        info_log("milla/fnctn/getCtgr", "lctgr_cls = [" . $lctgr_cls . "]");

        // get data
        $ctgr_nm = 'CTGR_CLS_' . $cmpny_cls;
        $data = $this->milla_m->get_ctgr_list($cmpny_cls, $ctgr_nm, $lctgr_cls, 'data');

        //info_log("milla/fnctn/getCtgr", "data[mt_kor_nm][0] = [" . $data[mt_kor_nm][0] . "]");
        //info_log("milla/fnctn/getCtgr", "data->mt_kor_nm = [" . $data->mt_kor_nm . "]");

        echo json_encode($data);
    }

}
