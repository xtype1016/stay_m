<?php
defined('BASEPATH') OR exit('No direct script access allowed');


Class Todo_api extends yidas\rest\Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $this->load->model('todo_m');
        $this->load->library('form_validation');
        //$this->load->library('session');
        $this->load->helper('url');
        //$this->load->helper('cookie');
        //$this->load->helper('My_alert_log');

        // $usr_no = get_cookie('usr_no');
        // $idntfr = get_cookie('idntfr');

        // if (!isset($_SESSION['usr_no']) && strlen($usr_no) > 0 && strlen($idntfr) > 0)
        // {
        //     info_log("incm", "autologin start!");
        //     auto_login();
        // }

        // login_chk();
    }

    // public function index()
    // {
    //     // $this->login();
    //     //redirect('incm/smmry','refresh');
    // }

    /*
        사이트 헤더, 푸터 추가
    */
    public function _remap($method, $params = array())
    {
        if (method_exists($this, $method))
        {
            //$this->{"{$method}"}();
            call_user_func_array(array($this, $method), $params);
        }
    }

    public function chk_usr_email()
    {
        info_log("ToDo_api", "chk_usr_email Begin!");

        $usr_email = $this->input->post('userEmail', 'TRUE');
        //print_r($_POST);

        info_log("ToDo_api", "usr_email = [" . $_POST['userEmail'] . "]");
        info_log("ToDo_api", "usr_email = [" . $usr_email . "]");

        $usr_email_dup_yn = $this->todo_m->chk_usr_email($usr_email);

        // $data = $this->pack(['bar'=>'foo'], 403, 'Forbidden');
        // return $this->response->json($data, 403);

        if ($usr_email_dup_yn) {
            info_log("ToDo_api", "SQL ERROR!!! [" . $this->db->error()['code'] . "]");
            if ($usr_email_dup_yn->cnt == 1)
            {
                //echo "<script>alert('회원 가입 오류!(동일 아이디 존재)');</script>";
                info_log("ToDo_api", "회원 가입 오류!(동일 아이디 존재 [" . $usr_email . "]");
                // echo json_encode(array("existEmailYn" => 'Y'));
                // $result = array("existEmailYn" => 'Y', "result" => 'SUC');
                // $data = $this->pack(['bar'=>'foo'], 200, 'Forbidden');
                // return $this->response->json($data, 200);
                return $this->response
    ->withAddedHeader('Access-Control-Allow-Origin', '*')
    ->withAddedHeader('X-Frame-Options', 'deny')
    ->json(['bar'=>'foo']);
            } else {
                info_log("ToDo_api", "동일 아이디 미존재 [" . $usr_email . "]");
                // $result = array("existEmailYn" => 'N', "result" => 'SUC');
            }
        } else {
            info_log("ToDo_api", "SQL ERROR! [" . $this->db->error()['code'] . "]");
            info_log("ToDo_api", "last_query  = [" . $this->db->last_query() . "]");
            // $result = array("result" => 'ERR');
        }

        // $this->output->set_content_type('text/json');
        // $this->output->set_output(json_encode($result));

        // echo json_encode($return);

        // $this->response( $result, 200 );
    }

    public function user_ins()
    {
        $usr_email = $this->input->post('usr_email', 'TRUE');
        $usr_pswd  = $this->input->post('usr_pswd', 'TRUE');

        $options = [
            'cost' => 10
           ];
        //PASSWORD_DEFAULT: Note that this constant is designed to change over time as new and stronger algorithms are added to PHP.
        //password_hash("rasmuslerdorf", PASSWORD_DEFAULT, $options)

        $u_data = array('usr_id'       => trim($this->input->post('usr_id', 'TRUE'))
                       ,'pswd'         => password_hash($this->input->post('password', 'TRUE'), PASSWORD_DEFAULT, $options)
                       ,'ip_addr'      => $ip_addr
                    );


    }

}