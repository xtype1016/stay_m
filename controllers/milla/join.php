<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    메인
*/

Class Join extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $this->load->model('milla_m');
        $this->load->library('form_validation');
        $this->load->helper('url');
        //$this->load->helper('My_alert_log');
    }

    public function index()
    {
        //$this->ins();
        redirect('milla/join/ins','refresh');
    }

    /*
        사이트 헤더, 푸터 추가
    */
    public function _remap($method, $params = array())
    {
        // Header
        $this->load->view('milla/header_v');

        if (method_exists($this, $method))
        {
            //$this->{"{$method}"}();
            call_user_func_array(array($this, $method), $params);
        }

        // footer
        $this->load->view('milla/footer_v');
    }

    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->form_validation->set_rules('usr_id'       , '아이디'      , 'trim|required|valid_email');
            $this->form_validation->set_rules('password'     , '비밀번호'    , 'trim|required|min_length[8]|matches[password_re]');
            $this->form_validation->set_rules('password_re'  , '비밀번호확인', 'trim|required');

            if ($this->form_validation->run() == TRUE)
            {
                $usr_id = $this->input->post('usr_id', 'TRUE');
                $dup_chk = $this->milla_m->get_usr_dup_chk($usr_id);

                //info_log("join", "usr_id = [" . $this->input->post('usr_id', 'TRUE') . "]");

                if ($dup_chk->cnt > 0)
                {
                    //echo "<script>alert('회원 가입 오류!(동일 아이디 존재)');</script>";
                    alert_log("milla/join", "회원 가입 오류!(동일 아이디 존재[" . $usr_id . "]");
                    exit;
                }

                $this->db->trans_begin();

                //$ip_addr = $this->input->ip_address();
                $ip_addr = get_ip();
                //info_log("join", "ip_addr = [" . $ip_addr . "]");

                $usr_no = $this->milla_m->get_clm_sr_val('USR_NO');

                //info_log("join", "usr_no = [" . $usr_no . "]");

                if (strlen($usr_no) > 0)
                {
                    //echo "Check Point 01!<br>";
                    //$this->db->trans_rollback();
                    //exit;

                    $options = [
                                'cost' => 15
                               ];
                    //PASSWORD_DEFAULT: Note that this constant is designed to change over time as new and stronger algorithms are added to PHP.
                    //password_hash("rasmuslerdorf", PASSWORD_DEFAULT, $options)

                    $i_data = array('usr_no'       => $usr_no
                                   ,'usr_id'       => trim($this->input->post('usr_id', 'TRUE'))
                                   ,'pswd'         => password_hash($this->input->post('password', 'TRUE'), PASSWORD_DEFAULT, $options)
                                   ,'ip_addr'      => $ip_addr
                                   );

                    $result = $this->milla_m->insert_milla001i00($i_data);

                    if ($result)
                    {
                        $this->db->trans_commit();

                        info_log("milla/join/ins/insert_milla001i00", "회원정보 입력 완료!");
                        info_log("milla/join/ins/insert_milla001i00", "================================================================================");

                        alert_log("milla/join/ins", "회원 가입되었습니다. 로그인해 주십시요.", base_url("milla/auth/login"));
                    }
                    else
                    {
                        info_log("milla/join/ins/insert_milla001i00", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("milla/join/ins/insert_milla001i00", "[SQL ERR] 회원정보 입력 오류!");
                    }
                }
                else
                {
                    $this->db->trans_rollback();
                    //echo "<script>alert('회원 가입 오류!(회원번호 GET)');</script>";
                    alert_log("milla/join", "회원 가입 오류!(회원번호 GET)");
                    exit;
                }

            }
            else
            {
                $this->join_v();
            }
        }
        else
        {
            $this->join_v();
        }
    }

    public function join_v()
    {
        $this->load->view('milla/join_v');
    }

}
