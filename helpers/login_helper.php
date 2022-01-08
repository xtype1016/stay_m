<?php
defined('BASEPATH') OR exit('No direct script access allowed');



    // 로그인 체크
    function login_chk()
    {
        $CI =& get_instance();
        $CI->load->helper('alert_log');

        if (!isset($_SESSION['usr_no']))
        {
            //$usr_no = get_cookie('usr_no');
            //$idntfr = get_cookie('idntfr');
            //
            //info_log("login_chk", "usr_no = [" . $usr_no . "]");
            //info_log("login_chk", "idntfr = [" . $idntfr . "]");

            alert_log("login_chk/", "로그인정보가 없습니다! 로그인해 주십시요!", base_url("auth/login/r"));
        }
        else if (strlen($_SESSION['usr_no']) == 10)
        {
        }
        else
        {
            alert_log("login_chk/", "잘못된 접근입니다!", base_url("milla/auth/login/r"));
        //    info_log("login_chk", "로그인정보 존재! usr_no = [" . $_SESSION['usr_no'] . "]");
        //    $cntrlr = $CI->uri->segment(1);
        //
        //    //info_log("login_chk", "cntrlr = [" . $cntrlr . "]");
        //    if (strncmp($cntrlr, "incm", 4) != 0)
        //    {
        //        redirect(base_url("/incm"));
        //    }
        }

        //$previous_url = $CI->agent->referrer();
        //$previous_url = str_replace(base_url(), "/", $previous_url);
        ////info_log("login_chk/", "previous_url        = [" . $previous_url . "]");
        //// bef_url 세션데이터 초기화
        //unset($_SESSION['previous_url']);
        //$CI->session->set_userdata('previous_url', $previous_url);

        //$bef_url = get_cookie('bef_url');
        //$cur_url = get_cookie('cur_url');
        //
        //if (strcmp($bef_url, $cur_url) != 0)
        //{
        //    $bef_url = $cur_url;
        //}
        //
        //$cur_url = uri_string();
        //
        ////info_log("login_chk", "bef_url = [" . $bef_url . "]");
        ////info_log("login_chk", "cur_url = [" . $cur_url . "]");
        //
        //$c_bef_url = array('name'   => 'bef_url'
        //                  ,'value'  => $bef_url
        //                  ,'expire' => '300'
        //                  );
        //
        //$c_cur_url = array('name'   => 'cur_url'
        //                  ,'value'  => $cur_url
        //                  ,'expire' => '300'
        //                  );
        //
        //set_cookie($c_bef_url);
        //set_cookie($c_cur_url);

    }


    // 자동로그인 처리
    function auto_login()
    {
        //변수 초기화
        $usr_no   = "";
        $idntfr   = "";
        $tkn      = "";

        $CI =& get_instance();

        info_log("login_helper/auto_login", "================================================================================");
        info_log("login_helper/auto_login", "자동로그인 시작!");

        if (!isset($_SESSION['usr_no']))
        {
            $usr_no = get_cookie('usr_no');
            $idntfr = get_cookie('idntfr');
            $tkn    = get_cookie('tkn');

            //info_log("login_helper/auto_login", "usr_no = [" . $usr_no . "]");
            //info_log("login_helper/auto_login", "idntfr = [" . $idntfr . "]");
            //info_log("login_helper/auto_login", "tkn    = [" . $tkn    . "]");

            if (strlen($usr_no) > 0 && strlen($idntfr) > 0)
            {
                $q_data = $CI->stay_m->get_usr_tkn($usr_no, $idntfr);

                //print_r($q_data);
                //info_log("login_helper/auto_login", "sqlcode = [" . $q_data['sql_result']['code'] . "]");
                //info_log("login_helper/auto_login", "sqlmsg = [" . $q_data['sql_result']['message'] . "]");

                //info_log("login_helper/auto_login", "q_data->tkn = [" . $q_data->tkn . "]");

                if ($q_data->expired_ymdh < date("Y-m-d H:i:s"))
                {
                    info_log("login_helper/auto_login", "만기일자 =  [" . $q_data->expired_ymdh . "]");
                    info_log("login_helper/auto_login", "현재일자 =  [" . date("Y-m-d H:i:s") . "]");
                    alert_log("login_helper/auto_login", "로그인 유지기간 종료! 재로그인 하시기 바랍니다!", base_url("auth/login/r"));
                }

                if (isset($q_data->tkn) && password_verify($tkn , $q_data->tkn))
                {
                    info_log("login_helper/auto_login", "자동로그인 정보 일치!");

                    $ip_addr = get_ip();
                    //2019.12.28
                    $mbl_cls = get_mobile_cls();

                    $s_data = array('usr_no'  => $q_data->usr_no
                                   ,'usr_id'  => $q_data->usr_id
                                   ,'db_no'   => $q_data->db_no
                                   ,'ip_addr' => $ip_addr
                                   ,'mbl_cls' => $mbl_cls
                                   );

                    $CI->session->set_userdata($s_data);

                    //자동 로그인 정보 재설정
                    //사용된 토큰 재생성 및 자동로그인 정보 Update
                    $t_tkn = random_bytes(25);
                    $tkn = bin2hex($t_tkn);

                    //info_log("login_helper/auto_login", "new tkn1 = [" . $tkn . "]");

                    $options = [
                                'cost' => 10
                               ];

                    $h_tkn = password_hash($tkn, PASSWORD_DEFAULT, $options);

                    $CI->db->trans_begin();

                    $u_data = array('usr_no'          => $usr_no
                                   ,'idntfr'          => $idntfr
                                   ,'tkn'             => $h_tkn
                                   ,'ip_addr'         => $ip_addr
                                   );

                    $result = $CI->stay_m->update_tba001i01_1($u_data);

                    if ($result)
                    {
                        // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                        $prcs_cnt = $CI->db->affected_rows();
                        if ($prcs_cnt != 1)
                        {
                            info_log("login_helper/auto_login/update_tba001i01_1", "last_query  = [" . $CI->db->last_query() . "]");
                            $CI->db->trans_rollback();
                            alert_log("login_helper/auto_login/update_tba001i01_1", "[SQL ERR] 자동로그인정보 수정 건수 오류![" . $prcs_cnt . "]!", base_url("auth/login/r"));
                        }
                        else
                        {
                            info_log("login_helper/auto_login/update_tba001i01_1", "자동 로그인 정보 변경 완료");
                        }
                    }
                    else
                    {
                        info_log("login_helper/auto_login/update_tba001i01_1", "last_query  = [" . $CI->db->last_query() . "]");
                        $CI->db->trans_rollback();
                        alert_log("login_helper/auto_login/update_tba001i01_1", "[SQL ERR] 자동로그인정보 수정 오류!", base_url("auth/login/r"));
                    }

                    //로그인 이력 생성
                    $i_data = array('usr_no'      => $usr_no
                                   ,'idntfr'      => $idntfr
                                   //,'decode_tkn'  => $tkn
                                   //,'encode_tkn'  => $h_tkn
                                   ,'ip_addr'     => $ip_addr
                                   );

                    $result = $CI->stay_m->insert_tba001i03($i_data);

                    if ($result)
                    {
                        info_log("login_helper/auto_login/insert_tba001i03", "자동로그인 이력 생성 완료");
                    }
                    else
                    {
                        info_log("login_helper/auto_login/insert_tba001i03", "last_query  = [" . $CI->db->last_query() . "]");
                        $CI->db->trans_rollback();
                        alert_log("login_helper/auto_login/insert_tba001i03", "[SQL ERR] 자동로그인 이력 생성 오류!", base_url("auth/login/r"));
                    }

                    //보관기관 초과 자동로그인정보 삭제
                    $result = $CI->stay_m->delete_tba001i01_2($usr_no);

                    if ($result)
                    {
                        info_log("login_helper/auto_login/delete_tba001i01_2", "보관기간 초과 자동로그인정보 삭제 완료");

                        $c_usr_no = array('name'  => 'usr_no'
                                         ,'value'  => $usr_no
                                         ,'expire' => 604800
                                         );
                        set_cookie($c_usr_no);

                        $c_idntfr = array('name'   => 'idntfr'
                                         ,'value'  => $idntfr
                                         ,'expire' => 604800
                                         );
                        set_cookie($c_idntfr);

                        //info_log("login_helper/auto_login", "new tkn2 = [" . $tkn . "]");

                        $c_tkn   = array('name'   => 'tkn'
                                        ,'value'  => $tkn
                                        ,'expire' => 604800
                                        );
                        set_cookie($c_tkn);

                        $CI->db->trans_commit();

                        //2021.01.13 주석 처리
                        //redirect(current_url());

                        info_log("login_helper/auto_login", "자동로그인 완료!");
                    }
                    else
                    {
                        info_log("login_helper/auto_login/delete_tba001i01_2", "last_query  = [" . $CI->db->last_query() . "]");
                        $CI->db->trans_rollback();
                        alert_log("login_helper/auto_login/delete_tba001i01_2", "[SQL ERR] 자동로그인정보 삭제 오류!", base_url("auth/login/r"));
                    }

                    info_log("login_helper/auto_login", "사용자 " . $usr_no . " 로그인 성공!");
                    info_log("login_helper/auto_login", "================================================================================");

                    //2021.01.06 주석 처리
                    //redirect(base_url("/incm"));
                }
                // 자동로그인 정보가 일치하지 않는 경우 쿠키 정보 해킹으로 간주 사용자에게 경고 및 로그인 정보를 초기화 한다.
                // 참고 https://isme2n.github.io/devlog/2017/06/13/security-remember-me/
                else
                {
                    info_log("login_helper/auto_login", "자동로그인 정보 불일치!!!");

                    $CI->db->trans_begin();

                    $u_data = array('usr_no'          => $usr_no
                                   ,'idntfr'          => $idntfr
                                   );

                    $result = $CI->stay_m->delete_tba001i01_1($u_data);

                    if ($result)
                    {
                        $CI->db->trans_commit();

                        info_log("login_helper/auto_login/delete_tba001i01_1", "자동로그인정보 삭제 완료!");
                        info_log("login_helper/auto_login/delete_tba001i01_1", "================================================================================");

                        alert_log("login_helper/auto_login", "자동 로그인정보가 불일치합니다! 자동 로그인 정보를 초기화합니다! 재로그인 해주십시요!!", base_url("auth/login/r"));
                    }
                    else
                    {
                        info_log("login_helper/auto_login/delete_tba001i01_1", "last_query  = [" . $CI->db->last_query() . "]");
                        $CI->db->trans_rollback();
                        alert_log("login_helper/auto_login/delete_tba001i01_1", "[SQL ERR] 자동로그인정보 초기화 오류!", base_url("auth/login/r"));
                    }
                }
            }
        }
    }

?>
