<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    메인
*/

Class Auth extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $this->load->model('milla_m');
        $this->load->library('form_validation');
        //$this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('cookie');
        //$this->load->helper('My_alert_log');
    }

    public function index()
    {
        //$this->login();  //initial
        redirect('milla/auth/login','refresh');
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

    public function login()
    {
        $usr_no = get_cookie('usr_no');
        $idntfr = get_cookie('idntfr');

        $prcs_cls = $this->uri->segment(4);

        //info_log("milla/auth", "_SESSION['usr_no'] = [" . $_SESSION['usr_no'] . "]");
        //info_log("milla/auth", "usr_no = [" . $usr_no . "]");
        //info_log("milla/auth", "idntfr = [" . $idntfr . "]");
        //info_log("milla/auth", "prcs_cls = [" . $prcs_cls . "]");

        if (!isset($_SESSION['usr_no']) && strlen($usr_no) > 0 && strlen($idntfr) > 0 && strncmp($prcs_cls, "r", 1) !== 0)
        {
            info_log("milla/auth/login", "Autologin Begin!");
            milla_auto_login();
        }

        if (isset($_SESSION['usr_no']))
        {
            redirect(base_url("milla/itm"));
        }

        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("milla/auth/login", "================================================================================");
            info_log("milla/auth/login", "로그인 시작!");

            $this->form_validation->set_rules('usr_id'       , '아이디'      , 'trim|required|valid_email');
            $this->form_validation->set_rules('password'     , '비밀번호'    , 'trim|required|min_length[8]');

            if ($this->form_validation->run() == TRUE)
            {
                $options = [
                            'cost' => 15
                           ];

                $usr_id = $this->input->post('usr_id'  , 'TRUE');
                $pswd   = $this->input->post('password', 'TRUE');

                $usr_info = $this->milla_m->get_usr_info($usr_id);

                $this->db->trans_begin();

                $usr_no = $usr_info->usr_no;
                $db_no  = $usr_info->db_no;

                $ip_addr = get_ip();
                //2019.12.28
                $mbl_cls = get_mobile_cls();
                //info_log("milla/auth", "mbl_cls = [" . $mbl_cls . "]");

                if (password_verify($pswd , $usr_info->pswd))
                {
                    // 8일전 자동로그인 정보 삭제
                    // ==========================================================================
                    // 쿠키가 7일간 유지되도록 설정되어 있고, 자동로그인시 매번 정보를 갱신하므로
                    // 8일전의 자동로그인 정보는 무의미한 데이터로 삭제 처리함
                    $result = $this->milla_m->delete_milla001i01_2($usr_no);

                    if ($result)
                    {
                        info_log("milla/auth/login/delete_milla001i01_2", "보관기간 초과 자동로그인정보 삭제 완료");
                    }
                    else
                    {
                        info_log("milla/auth/login/delete_milla001i01_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("milla/auth/login/delete_milla001i01_2", "[SQL ERR] 보관기한 초과 자동로그인정보 삭제 오류!");
                    }


                    $atln = $this->input->post('atln', 'TRUE');

                    // 자동로그인
                    // 참고 https://isme2n.github.io/devlog/2017/06/13/security-remember-me/
                    // 1. 사용자가 자동로그인을 체크하고 로그인하면 표준 세션 관리 쿠키와 함께 로그인 쿠키가 발행된다.
                    // 2. 로그인 쿠키는 사용자의 이름과 식별자, 토큰을 포함한다. 식별자와 토큰은 충분히 큰 난수이다(암호화).
                    //    이 셋의 쌍은 데이터베이스 테이블에 저장된다.
                    // 3. 로그인하지 않은 사용자가 사이트를 방문하여 로그인 쿠키를 제공하면 사용자 이름, 식별자, 토큰이 DB에서 조회된다.
                    // 4. 셋의 쌍이 있는 경우 사용자는 인증 된 것으로 간주된다. 사용 된 토큰이 DB에서 삭제되고, 새 토큰이 생성된다. 새 토큰은
                    //    사용자 이름과 이전과 동일한 식별자와 함께 DB에 저장되고, 새 로그인 쿠키가 사용자에게 발급된다.
                    // 5. 사용자 이름과 식별자가 같은 DB 정보와 토큰이 일치하지 않으면 도난이 일어난 것으로 간주된다. 사용자에게 강한 경고를
                    //    보내고 사용자가 기억 한 모든 세션이 삭제된다.
                    // 6. 사용자 이름 과 식별자가 없으면 로그인 쿠키가 무시된다.
                    // 식별자는 재사용되는 것이 중요하다. 식별자가 매번 바뀔경우 도난당한 경우와 만료되어 삭제된 경우를 구별할 수가 없다.
                    if (strncmp($atln, "1", 1) == 0)
                    {
                        //info_log("milla/auth/login", "Autologin Setting Begin!");
                        //idntfr 는 식별자 역할만이 필요하므로 암호화 필요성이 없음.
                        $t_idntfr = random_bytes(25);
                        $h_idntfr = bin2hex($t_idntfr);

                        $t_tkn = random_bytes(25);
                        $tkn = bin2hex($t_tkn);

                        $options = [
                                    'cost' => 10
                                   ];

                        $h_tkn = password_hash($tkn, PASSWORD_DEFAULT, $options);

                        $i_data = array('usr_no'          => $usr_no
                                       ,'idntfr'          => $h_idntfr
                                       ,'tkn'             => $h_tkn
                                       ,'ip_addr'         => $ip_addr
                                       );

                        $result = $this->milla_m->insert_milla001i01($i_data);

                        if ($result)
                        {
                            info_log("milla/auth/login/insert_milla001i01", "자동 로그인 정보 생성 완료");

                            //자동로그인 로그인 이력 생성
                            $i_data = array('usr_no'      => $usr_no
                                           ,'idntfr'      => $h_idntfr
                                           //,'decode_tkn'  => $tkn
                                           //,'encode_tkn'  => $h_tkn
                                           ,'result'      => 'A0'
                                           ,'ip_addr'     => $ip_addr
                                           );

                            $result = $this->milla_m->insert_milla001i03($i_data);

                            if ($result)
                            {
                                $this->db->trans_commit();
                                info_log("milla/auth/login/insert_milla001i03", "자동로그인 이력 생성 완료");
                            }
                            else
                            {
                                info_log("milla/auth/login/insert_milla001i03", "last_query  = [" . $this->db->last_query() . "]");
                                $this->db->trans_rollback();
                                alert_log("milla/auth/login/insert_milla001i03", "[SQL ERR] 자동로그인 이력 생성 오류!");
                            }

                            // 2020.02.26. 처리 중 오류가 발생해도 쿠키가 설정되어 있으면 오류 메시지 출력 후에도
                            //             정상 로그인 되므로 모든 처리 종료 후 쿠키 처리
                            $c_idntfr = array('name'   => 'idntfr'
                                             ,'value'  => $h_idntfr
                                             ,'expire' => 604800
                                             );
                            set_cookie($c_idntfr);

                            $c_tkn   = array('name'   => 'tkn'
                                            ,'value'  => $tkn
                                            ,'expire' => 604800
                                            );
                            set_cookie($c_tkn);

                            info_log("milla/auth/login", "자동로그인 처리 정상 완료!");
                            info_log("milla/auth/login", "로그인 완료!");
                            info_log("milla/auth/login", "================================================================================");

                        }
                        else
                        {

                            info_log("milla/auth/login/insert_milla001i01", "last_query  = [" . $this->db->last_query() . "]");
                            $this->db->trans_rollback();
                            alert_log("milla/auth/login/insert_milla001i01", "[SQL ERR] 자동로그인정보 입력 오류!");
                        }

                    }
                    else
                    {
                        //자동로그인이 아닌 경우 로그인 이력 생성
                        $i_data = array('usr_no'      => $usr_no
                                       ,'idntfr'      => NULL
                                       ,'result'      => '00'
                                       ,'ip_addr'     => $ip_addr
                                       );

                        $result = $this->milla_m->insert_milla001i03($i_data);

                        if ($result)
                        {
                            $this->db->trans_commit();
                            info_log("milla/auth/login/insert_milla001i03", "로그인 이력 생성 완료(일반 로그인)");
                        }
                        else
                        {
                            info_log("milla/auth/login/insert_milla001i03", "last_query  = [" . $this->db->last_query() . "]");
                            $this->db->trans_rollback();
                            alert_log("milla/auth/login/insert_milla001i03", "[SQL ERR] 로그인 이력 생성 오류!(일반 로그인)");
                        }
                    }

                    // 모든 처리 종료 후 로그인 세션 처리
                    $s_data = array('usr_no'  => $usr_no
                                   ,'usr_id'  => $usr_id
                                   ,'ip_addr' => $ip_addr
                                   ,'mbl_cls' => $mbl_cls
                                   );

                    $this->session->set_userdata($s_data);

                    $c_usr_no = array('name'  => 'usr_no'
                                     ,'value'  => $usr_no
                                     ,'expire' => 604800
                                     );
                    set_cookie($c_usr_no);

                    info_log("milla/auth/login", "사용자 " . $usr_no . " 로그인 되었습니다!!!");
                    info_log("milla/auth/login", "로그인 완료!");
                    info_log("milla/auth/login", "================================================================================");

                    redirect(base_url("milla/itm"));
                }
                else
                {
                    //로그인 실패 이력 생성
                    $i_data = array('usr_no'      => $usr_no
                                   ,'idntfr'      => NULL
                                   ,'result'      => '01'
                                   ,'ip_addr'     => $ip_addr
                                   );

                    $result = $this->milla_m->insert_milla001i03($i_data);

                    if ($result)
                    {
                        $this->db->trans_commit();
                        info_log("milla/auth/login/insert_milla001i03", "로그인 이력 생성 완료(비밀번호 불일치!)");
                    }
                    else
                    {
                        info_log("milla/auth/login/insert_milla001i03", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("milla/auth/login/insert_milla001i03", "[SQL ERR] 자동로그인 이력 생성 오류!(비밀번호 불일치!)");
                    }

                    alert_log("milla/auth", "아이디/비밀번호가 일치하지 않습니다!!![" . $usr_id . "]");
                    exit;
                }
            }
            else
            {
                $this->login_v();
            }
        }
        else
        {
            $this->login_v();
        }
    }


    public function logout()
    {

        info_log("milla/auth/logout", "================================================================================");
        info_log("milla/auth/logout", "로그아웃 시작!");

        $usr_no = get_cookie('usr_no');
        $idntfr = get_cookie('idntfr');

        //info_log("milla/auth/logout","usr_no = [" . $usr_no . "]");
        //info_log("milla/auth/logout","idntfr = [" . $idntfr . "]");

        // 자동로그인의 경우 자동로그인 설정정보 삭제처리
        if (isset($idntfr))
        {
            $this->db->trans_begin();

            $d_data = array('usr_no'      => $usr_no
                           ,'idntfr'      => $idntfr
                           );

            $result = $this->milla_m->delete_milla001i01_1($d_data);

            if ($result)
            {
                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $this->db->affected_rows();
                if ($prcs_cnt == 0)
                {
                    info_log("milla/auth/logout/delete_milla001i01_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    info_log("milla/auth/logout/delete_milla001i01_1", "[SQL ERR] 자동 로그인 정보 삭제 건수 오류[" . $prcs_cnt . "]!");
                }
                else
                {
                    $this->db->trans_commit();
                    info_log("milla/auth/logout", $usr_no . " 자동 로그인 정보 삭제 완료!");

                }
            }
            else
            {
                info_log("milla/auth/logout/delete_milla001i01_1", "last_query  = [" . $this->db->last_query() . "]");
                $this->db->trans_rollback();
                alert_log("milla/auth/logout/delete_milla001i01_1", "[SQL ERR] 자동 로그인 정보 삭제 오류!");
            }
        }

        session_destroy();

        delete_cookie('usr_no');
        delete_cookie('idntfr');
        delete_cookie('tkn');

        alert_log("milla/auth", "로그아웃 되었습니다!!!", base_url("milla/auth"));
        info_log("milla/auth/logout", "로그아웃 완료!");
        info_log("milla/auth/logout", "================================================================================");
    }

    public function login_v()
    {
        $this->load->view('milla/login_v');
    }

}
