<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    설정 확인 및 변경
*/

Class Setup extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $this->load->model('stay_m');
        $this->load->library('form_validation');
        //$this->load->library('session');
        $this->load->helper('url');
        //$this->load->helper('cookie');
        //$this->load->helper('My_alert_log');
        $this->load->library('googleapi');

        $usr_no = get_cookie('usr_no');
        $idntfr = get_cookie('idntfr');

        if (!isset($_SESSION['usr_no']) && strlen($usr_no) > 0 && strlen($idntfr) > 0)
        {
            info_log("setup", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        $this->g_acnt();
    }

    /*
        사이트 헤더, 푸터 추가
    */
    public function _remap($method, $params = array())
    {
        // Header
        $this->load->view('header_v');

        if (method_exists($this, $method))
        {
            //$this->{"{$method}"}();
            call_user_func_array(array($this, $method), $params);
        }

        // footer
        $this->load->view('footer_v');
    }


    public function g_acnt()
    {
        //$g_code = $this->input->get('code', TRUE);
        //info_log("setup/g_acnt", "g_code = [" . $g_code . "]");
        info_log("setup/g_acnt", "db_no = [" . $_SESSION['db_no'] . "]");

        $credentialsPath = "C:/xampp/htdocs/application/controllers/g_auth/accesstoken_" . $_SESSION['db_no'] . ".json";

        if (file_exists($credentialsPath))
        {
            $this->load->view('g_acnt_v');
        }
        else
        {
            g_auth('reg', '', '', '', '');
        }
    }

    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->form_validation->set_rules('shr_usr_id'    , '공유사용자ID'      , 'trim|required');

            if ($this->form_validation->run() == TRUE)
            {
                $shr_usr_id  = $this->input->post('shr_usr_id' , 'TRUE');

                $shr_usr_info = $this->stay_m->get_usr_info($shr_usr_id);

                $this->db->trans_begin();

                $u_data = array('shr_usr_no'      => $shr_usr_info->usr_no
                               );

                $result = $this->stay_m->update_tba001i00_2($u_data);

                if ($result)
                {
                    $this->db->trans_commit();
                    alert_log("setting/upd", "변경된 설정 반영을 위해 로그인이 필요합니다!", base_url("auth/logout"));
                }
                else
                {
                    $this->db->trans_rollback();
                    //echo "<script>alert('회원 가입 오류!(회원정보 INSERT)');</script>";
                    alert_log("setting/upd", "설정 변경 오류!");
                    exit;
                }
            }
            else
            {
                $this->setting_reg_v('u');
            }
        }
        else
        {
            $this->setting_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        //$this->output->enable_profiler();

        if ($_POST)
        {
            //info_log("hsrm", "Delete Begin!");

            $this->form_validation->set_rules('hsrm_cls'   , '숙소일련번호', 'trim|required');

            if ($this->form_validation->run() == TRUE)
            {
                $hsrm_nm  = $this->input->post('hsrm_nm' , 'TRUE');
                $cal_id   = $this->input->post('cal_id'  , 'TRUE');
                $hsrm_cls = $this->input->post('hsrm_cls', 'TRUE');

                $usr_no = $_SESSION['usr_no'];
                //info_log("hsrm", "usr_no = [" . $usr_no . "]");

                $this->db->trans_begin();

                $ip_addr = $this->input->ip_address();
                //info_log("join", "ip_addr = [" . $ip_addr . "]");

                $u_data = array('usr_no'      => $usr_no
                               ,'clm_nm'      => 'HSRM_CLS'
                               ,'clm_val'     => $hsrm_cls
                               ,'ip_addr'     => $ip_addr
                               );

                $result = $this->stay_m->update_tba003i00_2($u_data);

                if ($result)
                {
                    $this->db->trans_commit();
                    redirect(base_url("hsrm/list"));
                    exit;
                }
                else
                {
                    $this->db->trans_rollback();
                    //echo "<script>alert('회원 가입 오류!(회원정보 INSERT)');</script>";
                    alert_log("hsrm", "숙소 삭제 오류!");
                    exit;
                }
            }
            else
            {
                info_log("hsrm", "upd form valid err!");
                $this->setting_reg_v('u');
            }
        }
        else
        {
            $this->setting_reg_v('u');
        }
    }


    public function setting_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            //$data['view'] = $this->stay_m->get_hsrm_list($_SESSION['usr_no'], $this->uri->segment(3), 'data');
            //print_r($data);
        }

        $this->load->view('setting_menu_v', $data);

    }
}