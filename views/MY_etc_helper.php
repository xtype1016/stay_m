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

        info_log("My_etc_Helper/get_mobile_cls", "_SERVER['HTTP_USER_AGENT']  = [" . $_SERVER['HTTP_USER_AGENT'] . "]");

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

        info_log("My_etc_Helper/get_mobile_cls", "device  = [" . $device . "]");

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
        info_log("g_auth", "캘린더 처리 Begin!");

        // Load previously authorized credentials from a file.
        //$credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
        //$credentialsPath = "D:\WWW\application\controllers\accesstoken.json";
        //iis
        //$credentialsPath = "D:\xampp\htdocs\application\controllers\accesstoken.json";

        //apache(Real)
        //$credentialsPath = "D:/xampp/htdocs/application/controllers/g_auth/accesstoken.json";
        if (isset($_SESSION['db_no']))
        {
            $credentialsPath = "C:/xampp/htdocs/application/controllers/g_auth/accesstoken_" . $_SESSION['db_no'] . ".json";
        }
        else
        {
            alert_log("g_auth", "로그인 정보가 없습니다!", base_url("auth/login"));
        }

        $clientsecretPath = "C:/xampp/htdocs/application/controllers/g_auth/client_secret_884682539806-7e552r6afdtp78jcvqrl723jg98lk4k8.apps.googleusercontent.com.json";

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
                        if (strncmp($rsvt_info->rsv_chnl_cls, "1", 1) === 0)
                        {
                            //$summary = $rsvt_info->gst_nm . " " . $rsvt_info->gst_desc . " " . $rsvt_info->memo;
                            $summary = $rsvt_info->gst_nm . " " . $rsvt_info->memo;
                        }
                        else
                        {
                            //$summary = $rsvt_info->g_rsv_chnl_cls . " " . $rsvt_info->gst_nm . " " . $rsvt_info->gst_desc . " " . $rsvt_info->memo;
                            $summary = $rsvt_info->g_rsv_chnl_cls . " " . $rsvt_info->gst_nm . " " . $rsvt_info->memo;
                        }

                        $event = new Google_Service_Calendar_Event(array(
                                                                        'summary' => $summary,
                                                                        'start'   => array('date' => $rsvt_info->stnd_srt_dt,),
                                                                        'end'     => array('date' => $rsvt_info->g_end_dt,),
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

                        info_log("g_auth", "캘린더 입력 처리 End!");
                        info_log("g_auth", "================================================================================");
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

                        info_log("g_auth", "캘린더 수정 처리 End!");
                        info_log("g_auth", "================================================================================");
                        return;

                    }


                    if (strncmp($prcs_cls, "cal_del", 7) == 0)
                    {
                        info_log("etc_helper/g_auth/cal_del", "캘린더 삭제!");
                        //info_log("etc_helper/g_auth", "hsrm_nm = [" . $hsrm_nm  . "]");
                        info_log("etc_helper/g_auth", "cal_id  = [" . $cal_id  . "]");

                        $service->calendars->delete($cal_id);

                        info_log("g_auth", "캘린더 삭제 처리 End!");
                        info_log("g_auth", "================================================================================");
                        return;

                    }
                }
            }
        }
    }

?>