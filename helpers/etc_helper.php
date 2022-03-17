<?php
defined('BASEPATH') OR exit('No direct script access allowed');

    $client_id = "staym";

    function get_ip()
    {
        $CI =& get_instance();

        //info_log("get_ip", "addr 0  = [" . $_SERVER['REMOTE_ADDR'] . "]");
        //info_log("get_ip", "addr 1  = [" . $_SERVER['HTTP_X_FORWARDED_FOR'] . "]");
        //info_log("get_ip", "addr 2  = [" . $_SERVER['HTTP_CLIENT_IP'] . "]");
        $addr = $_SERVER['REMOTE_ADDR'];

        //info_log("get_ip", "bef addr  = [" . $addr . "]");

        if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
        {
            //info_log("get_ip", "addr 1  = [" . $_SERVER['HTTP_X_FORWARDED_FOR'] . "]");
            $addr .=  "/" . $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if( isset($_SERVER['HTTP_CLIENT_IP']) )
        {
            //info_log("get_ip", "addr 2  = [" . $_SERVER['HTTP_CLIENT_IP'] . "]");
            $addr .= "/" . $_SERVER['HTTP_CLIENT_IP'];
        }

        $t_addr = $CI->input->ip_address();

        if (strlen($t_addr) > 0 && strpos($addr, $t_addr) === false)
        {
            //info_log("get_ip", "temp ip addr  = [" . $t_addr . "]");
            $addr .= "/" . $t_addr;
        }

        //info_log("get_ip", "aft addr1  = [" . $_SERVER['HTTP_X_FORWARDED_FOR'] . "]");
        //info_log("get_ip", "aft addr2  = [" . $_SERVER['HTTP_CLIENT_IP'] . "]");

        //if( $inet )
        //{
        //  $tmp = explode("/", $addr);
        //  $addr = ip2long($tmp[0]);
        //  if( isset($tmp[1]) ) $addr .= ".".ip2long($tmp[1]);
        //}

        //info_log("get_ip", "addr  = [" . $addr . "]");
        return $addr;
    }


    function get_list_url()
    {
        $CI =& get_instance();

        $page_pos = strpos(current_url(), '/page');
        if ($page_pos === false)
        {
            $list_url = current_url();
        }
        else
        {
            $list_url = substr(current_url(), 0, $page_pos);
        }

        $c_list_url = array('name'   => 'list_url'
                           ,'value'  => $list_url
                           ,'expire' => '300'
                           );

        set_cookie($c_list_url);

        return;
    }


    function get_mobile_cls()
    {
        $CI =& get_instance();

        info_log("etc_Helper/get_mobile_cls", "_SERVER['HTTP_USER_AGENT']  = [" . $_SERVER['HTTP_USER_AGENT'] . "]");

        if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') )
        {
            $device = "ipad";
        }
        else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') ||
            strstr($_SERVER['HTTP_USER_AGENT'],'iphone') )
        {
            $device = "iphone";
        }
        else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') )
        {
            $device = "blackberry";
        }
        else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') )
        {
            $device = "android";
        }
        else
        {
            $device = "etc";
        }

        info_log("etc_Helper/get_mobile_cls", "device  = [" . $device . "]");

        return $device;
    }


	function json_output($statusHeader,$response)
	{
		$ci =& get_instance();
		$ci->output->set_content_type('application/json');
		$ci->output->set_status_header($statusHeader);
		$ci->output->set_output(json_encode($response));
	}


    function g_auth($prcs_cls, $rsv_srno, $bef_hsrm_cls, $evnt_id=NULL, $hsrm_nm=NULL, $cal_id=NULL)
    {
        $CI =& get_instance();

        info_log("g_auth", "================================================================================");
        info_log("g_auth", "캘린더 관리 프로세스 Begin!");

        // Load previously authorized credentials from a file.
        //$credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
        //$credentialsPath = "D:\WWW\application\controllers\accesstoken.json";
        //iis
        //$credentialsPath = "D:\xampp\htdocs\application\controllers\accesstoken.json";

        //apache(Real)
        //$credentialsPath = "D:/xampp/htdocs/application/controllers/g_auth/accesstoken.json";
        if (isset($_SESSION['db_no']))
        {
            //$credentialsPath = "C:/xampp/htdocs/application/controllers/g_auth/accesstoken_" . $_SESSION['db_no'] . ".json";
            $credentialsPath = "/var/www/html/application/controllers/g_auth/accesstoken_" . $_SESSION['db_no'] . ".json";
        }
        else
        {
            alert_log("g_auth", "로그인 정보가 없습니다!", base_url("auth/login"));
        }

        //$clientsecretPath = "C:/xampp/htdocs/application/controllers/g_auth/client_secret_884682539806-7e552r6afdtp78jcvqrl723jg98lk4k8.apps.googleusercontent.com.json";
        $clientsecretPath = "/var/www/html/application/controllers/g_auth/client_secret_884682539806-7e552r6afdtp78jcvqrl723jg98lk4k8.apps.googleusercontent.com.json";

        // Include two files from google-php-client library in controller
        //include_once APPPATH . "libraries/google-api-php-client-master/src/Google/Client.php";
        //include_once APPPATH . "libraries/google-api-php-client-master/src/Google/Service/Oauth2.php";

        //define('CREDENTIALS_PATH', '~/.credentials/calendar-php-quickstart.json');

        // Create Client Request to access Google API
        $client = new Google_Client();
        $client->setApplicationName("PHP Google OAuth Login Example");
        $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/calendar.readonly', 'https://www.googleapis.com/auth/calendar'));
        //$client->setClientId($client_id);
        //$client->setClientSecret($client_secret);
        //$client->setRedirectUri($redirect_uri);
        $client->setAuthConfig($clientsecretPath);
        $client->setAccessType("offline");
        $client->setApprovalPrompt('force');

        if (file_exists($credentialsPath))
        {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        }
        else
        {
            // Add Access Token to Session
            if (isset($_GET['code']))
            {
                $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                file_put_contents($credentialsPath, json_encode($accessToken));
                redirect(base_url("/setup/g_acnt"));
            }
            else
            {
                $authUrl = $client->createAuthUrl();
                //$data['authUrl'] = $authUrl;

                // Load view and send values stored in $data
                //$CI->load->view('g_auth_v', $data);
                redirect($authUrl);
            }
        }

        // 계정등록이 아닌 경우
        if (strncmp($prcs_cls, "reg", 3) != 0)
        {
            if (file_exists($credentialsPath) || isset($_GET['code']))
            {
                $client->setAccessToken($accessToken);

                // Refresh the token if it's expired.
                if ($client->isAccessTokenExpired())
                {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
                }

                // Send Client Request
                $objOAuthService = new Google_Service_Oauth2($client);

                // Get User Data from Google and store them in $data
                if ($client->getAccessToken())
                {
                    $userData = $objOAuthService->userinfo->get();
                    $data['userData'] = $userData;

                    $service = new Google_Service_Calendar($client);

                    //$CI->db->trans_strict(FALSE);
                    //$CI->db->trans_begin();

                    info_log("etc_helper/g_auth", "prcs_cls     = [" . $prcs_cls . "]");
                    info_log("etc_helper/g_auth", "rsv_srno     = [" . $rsv_srno . "]");
                    info_log("etc_helper/g_auth", "bef_hsrm_cls = [" . $bef_hsrm_cls  . "]");
                    info_log("etc_helper/g_auth", "evnt_id      = [" . $evnt_id  . "]");
                    info_log("etc_helper/g_auth", "hsrm_nm      = [" . $hsrm_nm  . "]");

                    if (strncmp($prcs_cls, "cal_ins", 7) != 0 && strncmp($prcs_cls, "cal_upd", 7) != 0 && strncmp($prcs_cls, "cal_del", 7) != 0)
                    {
                        $rsvt_info = $CI->stay_m->get_rsvt_info($rsv_srno);
                        // 2018.01.07. 숙소 변경시 변경 후 캘린더 삭제 시도로 Not Found 오류 발생 수정
                        $bef_hsrm_info = $CI->stay_m->get_item_list('HSRM_CLS', $bef_hsrm_cls, 'single');
                        $aft_hsrm_info = $CI->stay_m->get_item_list('HSRM_CLS', $rsvt_info->hsrm_cls, 'single');

                        //info_log("g_auth", "rsvt_info->hsrm_cls = [" . $rsvt_info->hsrm_cls . "]");
                        //info_log("g_auth", "hsrm_info->othr_info = [" . $hsrm_info->othr_info . "]");
                    }

                    if (strncmp($prcs_cls, "upd", 3) == 0 || strncmp($prcs_cls, "cncl", 4) == 0)
                    {
                        info_log("etc_helper/g_auth", "캘린더 기존 이벤트 삭제!");
                        // 이벤트ID가 미존재하는 일정을 취소시 캘린더 반영 제외 처리
                        if (empty($evnt_id))
                        {
                            //alert_log("google_auth", "이전 데이터 입니다. 캘린더에서 직접 수정하시기 바랍니다!", base_url("rsvt/list"));
                            //2020.05.09. 이벤트ID 미존재건도 캘린더 등록 처리하도록 변경
                            //info_log("google_auth", "이벤트ID 미존재!", base_url("rsvt/list"));
                            info_log("google_auth", "이벤트ID 미존재! 처리 진행 중!");
                        }
                        else
                        {
                            $event = $service->events->delete($bef_hsrm_info->othr_info, $evnt_id);

                            //info_log("etc_helper/g_auth", "del_evnt_id      = [" . $event->id  . "]");

                            if (!empty($event->id))
                            {
                                $CI->db->trans_rollback();
                                alert_log("etc_helper/g_auth", "캘린더 이벤트 삭제 오류!");
                            }

                            if (strncmp($prcs_cls, "cncl", 4) == 0)
                            {
                                info_log("etc_helper/g_auth", "캘린더 기존 이벤트 삭제 완료!");
                                $CI->db->trans_commit();
                            }
                        }
                    }

                    if (strncmp($prcs_cls, "ins", 3) == 0 || strncmp($prcs_cls, "upd", 3) == 0)
                    {
                        info_log("etc_helper/g_auth", "캘린더 신규 이벤트 입력!");

                        // v1 예약채널 블로그의 경우 캘린더에 표시하지 않음. 쁜이 요청사항
                        // 2020.11.02. gst_desc 컬럼 미사용. memo 컬럼만 표시하도록 수정
                        // 2020.12.26. 성인, 자녀 표시
                        $gst_desc = "";
                        if ($rsvt_info->chld_cnt == 0)
                        {
                            $gst_desc = "(성인:" . $rsvt_info->adlt_cnt . ") ";
                        }
                        else if ($rsvt_info->chld_cnt > 0)
                        {
                            $gst_desc = "(성인:" . $rsvt_info->adlt_cnt . " 자녀:" . $rsvt_info->chld_cnt . ") ";
                        }


                        if (strncmp($rsvt_info->rsv_chnl_cls, "1", 1) === 0)
                        {
                            //$summary = $rsvt_info->gst_nm . " " . $rsvt_info->gst_desc . " " . $rsvt_info->memo;
                            //$summary = $rsvt_info->gst_nm . " " . $rsvt_info->memo;
                            $summary = $rsvt_info->gst_nm . $gst_desc . $rsvt_info->memo;
                        }
                        else
                        {
                            //$summary = $rsvt_info->g_rsv_chnl_cls . " " . $rsvt_info->gst_nm . " " . $rsvt_info->gst_desc . " " . $rsvt_info->memo;
                            //$summary = $rsvt_info->g_rsv_chnl_cls . " " . $rsvt_info->gst_nm . " " . $rsvt_info->memo;
                            $summary = $rsvt_info->g_rsv_chnl_cls . " " . $rsvt_info->gst_nm . $gst_desc . $rsvt_info->memo;
                        }

                        $event = new Google_Service_Calendar_Event(array(
                                                                        'summary' => $summary,
                                                                        'start'   => array('date' => $rsvt_info->stnd_srt_dt,),
                                                                        'end'     => array('date' => $rsvt_info->stnd_g_end_dt,),
                                                                        )
                                                                  );

                        $event = $service->events->insert($aft_hsrm_info->othr_info, $event);

                        if (empty($event->id))
                        {
                            $CI->db->trans_rollback();
                            alert_log("etc_helper/g_auth", "캘린더 신규 이벤트 입력 오류!");
                        }

                        info_log("etc_helper/g_auth", "ins_event_id = [" . $event->id . "]");

                        $u_data = array('rsv_srno' => $rsv_srno
                                       ,'evnt_id'  => $event->id
                                       );

                        $result = $CI->stay_m->update_tba005l00_3($u_data);

                        if ($result)
                        {
                            // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                            $prcs_cnt = $CI->db->affected_rows();
                            if ($prcs_cnt != 1)
                            {
                                info_log("etc_helper/g_auth/update_tba005l00_3", "last_query  = [" . $CI->db->last_query() . "]");
                                $CI->db->trans_rollback();
                                alert_log("etc_helper/g_auth/update_tba005l00_3", "[SQL ERR] 캘린더 이벤트ID 수정 건수 오류![" . $prcs_cnt . "]!");
                            }
                            else
                            {
                                info_log("etc_helper/g_auth", "캘린더 신규 이벤트 등록/수정 완료!");
                                //$CI->db->trans_commit();
                            }
                        }
                        else
                        {
                            info_log("etc_helper/g_auth/update_tba005l00_3", "last_query  = [" . $CI->db->last_query() . "]");
                            $CI->db->trans_rollback();
                            alert_log("etc_helper/g_auth/update_tba005l00_3", "[SQL ERR] 캘린더 이벤트ID 수정 오류!");
                        }

                    }


                    if (strncmp($prcs_cls, "cal_ins", 7) == 0)
                    {
                        info_log("etc_helper/g_auth/cal_ins", "캘린더 신규 생성!");
                        info_log("etc_helper/g_auth", "hsrm_nm      = [" . $hsrm_nm  . "]");

                        $calendar = new Google_Service_Calendar_Calendar();
                        $calendar->setSummary($hsrm_nm);
                        $calendar->setTimeZone('Asia/Seoul');

                        $createdCalendar = $service->calendars->insert($calendar);

                        //echo $createdCalendar->getId();

                        info_log("etc_helper/g_auth/cal_ins", "cal_id = " . "[" . $createdCalendar->getId() . "]");

                        if (empty($createdCalendar->getId()))
                        {
                            alert_log("etc_helper/g_auth/cal_ins", "캘린더 신규 생성 오류!");
                        }

                        return $createdCalendar->getId();

                    }


                    if (strncmp($prcs_cls, "cal_upd", 7) == 0)
                    {
                        info_log("etc_helper/g_auth/cal_upd", "캘린더 수정!");
                        info_log("etc_helper/g_auth", "hsrm_nm = [" . $hsrm_nm  . "]");
                        info_log("etc_helper/g_auth", "cal_id  = [" . $cal_id  . "]");

                        // First retrieve the calendar from the API.
                        $calendar = $service->calendars->get($cal_id);

                        $calendar->setSummary($hsrm_nm);

                        $updatedCalendar = $service->calendars->update($cal_id, $calendar);

                        //echo $updatedCalendar->getEtag();
                        info_log("etc_helper/g_auth/cal_ins", "getEtag = " . "[" . $updatedCalendar->getEtag() . "]");

                        //if (empty($createdCalendar->getId()))
                        //{
                        //    alert_log("etc_helper/g_auth/cal_ins", "캘린더 신규 생성 오류!");
                        //}

                        return;

                    }


                    if (strncmp($prcs_cls, "cal_del", 7) == 0)
                    {
                        info_log("etc_helper/g_auth/cal_del", "캘린더 삭제!");
                        //info_log("etc_helper/g_auth", "hsrm_nm = [" . $hsrm_nm  . "]");
                        info_log("etc_helper/g_auth", "cal_id  = [" . $cal_id  . "]");

                        $service->calendars->delete($cal_id);

                        return;

                    }
                }
            }
        }

        info_log("g_auth", "캘린더 관리 프로세스 End!");
        info_log("g_auth", "================================================================================");
    }

    function cash_bal_ins_upd($stnd_yymm)
    {
        $CI =& get_instance();

        info_log("cash_bal_ins_upd", "================================================================================");
        info_log("cash_bal_ins_upd", "입출금거래 현금지출 합계금액 등록 Begin!");

        info_log("etc_helper/cash_bal_ins_upd/bal_chk_tbb003l00", "stnd_yymm = [" . $stnd_yymm . "]");

        // 현금지출, 잔고 Update
        $i_data = array('tr_yymm' => $stnd_yymm
                    );

        //$result = $CI->stay_m->bal_update_tbb002l00($i_data);

        $result = $CI->stay_m->bal_chk_tbb003l00($i_data);

        if ($result)
        {
            info_log("etc_helper/cash_bal_ins_upd/bal_chk_tbb003l00", "입출금거래 현금지출합계 조회 정상 처리!");
        }
        else
        {
            info_log("etc_helper/cash_bal_ins_upd/bal_chk_tbb003l00", "last_query  = [" . $CI->db->last_query() . "]");
            alert_log("etc_helper/cash_bal_ins_upd/bal_chk_tbb003l00", "[SQL ERR] 입출금거래 현금지출/잔고 조회 오류!!");
        }

        $cash_expns_amt     = $result->cash_expns_amt    ;
        $cur_cash_expns_amt = $result->cur_cash_expns_amt;
        $cur_io_tr_srno     = $result->cur_io_tr_srno    ;
        $last_dt            = $result->last_dt           ;

        $stnd_dt = date("Ymd");

        info_log("etc_helper/cash_bal_ins_upd/bal_chk_tbb003l00", "cash_expns_amt      = [" . $cash_expns_amt     . "]");
        info_log("etc_helper/cash_bal_ins_upd/bal_chk_tbb003l00", "cur_cash_expns_amt  = [" . $cur_cash_expns_amt . "]");
        info_log("etc_helper/cash_bal_ins_upd/bal_chk_tbb003l00", "cur_io_tr_srno      = [" . $cur_io_tr_srno . "]");
        info_log("etc_helper/cash_bal_ins_upd/bal_chk_tbb003l00", "last_dt             = [" . $last_dt            . "]");

        if (strlen($cur_io_tr_srno) == 0)
        {
            // insert
            $CI->db->trans_begin();

            $io_tr_srno = $CI->stay_m->get_clm_sr_val('IO_TR_SRNO');

            info_log("etc_helper/cash_bal_ins_upd/insert", "io_tr_srno             = [" . $io_tr_srno            . "]");

            // 2022.03.11. dt 월말일자에서 조회일 현재일자로 변경
            //$i_data = array('io_tr_srno' => $io_tr_srno
            //               ,'dt'         => $last_dt
            //               ,'io_tr_cls'  => '201'
            //               ,'memo'       => NULL
            //               ,'amt'        => $cash_expns_amt
            //               );

            $i_data = array('io_tr_srno' => $io_tr_srno
                           ,'dt'         => $stnd_dt
                           ,'io_tr_cls'  => '201'
                           ,'memo'       => NULL
                           ,'amt'        => $cash_expns_amt
                           );

            $result = $CI->stay_m->insert_tbb003l00($i_data);

            if ($result)
            {
                $CI->db->trans_commit();

                info_log("etc_helper/cash_bal_ins_upd/", "현금지출 합계금액 입력 완료!");
            }
            else
            {
                info_log("etc_helper/cash_bal_ins_upd/insert_tbb003l00", "last_query  = [" . $CI->db->last_query() . "]");
                $CI->db->trans_rollback();
                alert_log("etc_helper/cash_bal_ins_upd/insert_tbb003l00", "[SQL ERR] 현금지출 합계금액 입력 오류!");
            }
        }
        else if (strlen($cur_io_tr_srno) != 0 && $cash_expns_amt != $cur_cash_expns_amt)
        {
            // update
            $CI->db->trans_begin();

            info_log("etc_helper/cash_bal_ins_upd/update", "io_tr_srno             = [" . $cur_io_tr_srno            . "]");

            // 2022.03.11. dt 월말일자에서 조회일 현재일자로 변경
            //            $u_data = array('io_tr_srno' => $cur_io_tr_srno
            //                           ,'dt'         => $last_dt
            //                           ,'io_tr_cls'  => '201'
            //                           ,'memo'       => NULL
            //                           ,'amt'        => $cash_expns_amt
            //                           );

            $u_data = array('io_tr_srno' => $cur_io_tr_srno
                           ,'dt'         => $stnd_dt
                           ,'io_tr_cls'  => '201'
                           ,'memo'       => NULL
                           ,'amt'        => $cash_expns_amt
                           );

            $result = $CI->stay_m->update_tbb003l00_1($u_data);

            if ($result)
            {
                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $CI->db->affected_rows();
                if ($prcs_cnt != 1)
                {
                    info_log("etc_helper/cash_bal_ins_upd/update_tbb003l00_1", "last_query  = [" . $CI->db->last_query() . "]");
                    $CI->db->trans_rollback();
                    alert_log("etc_helper/cash_bal_ins_upd/update_tbb003l00_1", "[SQL ERR] 현금지출 합계금액 수정 처리 오류![" . $prcs_cnt . "]!");
                }
                else
                {
                    $CI->db->trans_commit();

                    info_log("etc_helper/cash_bal_ins_upd/", "현금지출 합계금액 수정 완료!");
                }
            }
            else
            {
                info_log("etc_helper/cash_bal_ins_upd/update_tbb003l00_1", "last_query  = [" . $CI->db->last_query() . "]");
                $CI->db->trans_rollback();
                alert_log("etc_helper/cash_bal_ins_upd/update_tbb003l00_1", "[SQL ERR] 현금지출 합계금액 수정 오류!");
            }

        }

        info_log("cash_bal_ins_upd", "입출금거래 현금지출 합계금액 등록 End!");
        info_log("cash_bal_ins_upd", "================================================================================");

        return;
    }


    function rsv_term_msg($hsrm_cls, $srt_dt, $end_dt, $stnd_srt_dt, $stnd_g_end_dt)
    {
        $CI =& get_instance();

        info_log("rsv_term_msg/", "================================================================================");
        info_log("rsv_term_msg/", "예약 기간 메시지 처리 Begin!");

        info_log("etc_helper/rsv_term_msg/", "hsrm_cls      = [" . $hsrm_cls . "]");
        info_log("etc_helper/rsv_term_msg/", "srt_dt        = [" . $srt_dt . "]");
        info_log("etc_helper/rsv_term_msg/", "end_dt        = [" . $end_dt . "]");
        info_log("etc_helper/rsv_term_msg/", "stnd_srt_dt   = [" . $stnd_srt_dt . "]");
        info_log("etc_helper/rsv_term_msg/", "stnd_g_end_dt = [" . $stnd_g_end_dt . "]");

        $term_info = $CI->stay_m->get_rsv_term($hsrm_cls, $srt_dt, $end_dt, 'Y');

        $loop_times = 0;
        $loop_cnt = 0;
        foreach ($term_info as $term_i)
        {
            $loop_cnt = $loop_cnt + 1;

            //info_log("rsvt/prc", "dayofweek      = [" . $term_i->dayofweek . "]"     , $log_lvl);

            if ($loop_cnt == 1)
            {
                $begin_dayofweek = $term_i->dayofweek;

                // 2021.05.05. 1박 등록을 위해 시작일과 종료일이 동일한 경우 처리
                if (strcmp($srt_dt, $end_dt) == 0)
                {
                    $end_dayofweek = $term_i->dayofweek;
                }
            }
            else if (strcmp($end_dt, $term_i->dt) == 0)
            {
                $end_dayofweek = $term_i->dayofweek;
            }
        }

        info_log("rsvt/cnfm_msg/", "begin_dayofweek      = [" . $begin_dayofweek . "]");
        info_log("rsvt/cnfm_msg/", "end_dayofweek        = [" . $end_dayofweek . "]");

        // 숙박시작일과 종료일이 같은 해인 경우 월/일만 표시
        if (strncmp($stnd_srt_dt, $stnd_g_end_dt, 4) == 0)
        {
            $srt_dt = str_replace('-', '/', substr($stnd_srt_dt, 5, 5));
            //$end_dt = str_replace('-', '/', substr($rsvt_info->stnd_end_dt, 5, 5));
            $end_dt = str_replace('-', '/', substr($stnd_g_end_dt, 5, 5));
        }
        else
        {
            $srt_dt = str_replace('-', '/', $stnd_srt_dt);
            //$end_dt = str_replace('-', '/', $rsvt_info->stnd_end_dt);
            $end_dt = str_replace('-', '/', $stnd_g_end_dt);
        }

        info_log("rsvt/cnfm_msg/", "stnd_g_end_dt = [" . $stnd_g_end_dt . "]");
        info_log("rsvt/cnfm_msg/", "end_dt   = [" . $end_dt . "]");

        // 1박인 경우
        if ($loop_cnt <= 1)
        {
            $rsv_term_msg = $srt_dt . " ~ " . $end_dt . " (" . $begin_dayofweek . ") " . $loop_cnt . "박";
        }
        // 2박 이상인 경우
        else if ($loop_cnt >= 2)
        {
            $rsv_term_msg = $srt_dt . " ~ " . $end_dt . " (" . $begin_dayofweek . " ~ " . $end_dayofweek . ") " . $loop_cnt . "박";
        }

        info_log("rsv_term_msg", "예약 기간 메시지 처리 End!");
        info_log("rsv_term_msg", "================================================================================");

        return $rsv_term_msg;
    }

?>

