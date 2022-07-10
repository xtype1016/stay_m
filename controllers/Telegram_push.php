<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
/** 텔레그램 알림(push) php 소스 **/


class Telegram_push extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('stay_m');
    }

    public function push()
    {
        info_log("Telegram_Push/", "================================================================================");
        info_log("Telegram_Push/", "메시지 발송 시작!");

        //if (!is_cli())
        //{
        //    info_log("Telegram_Push/", "CLI 실행이 아닙니다!");
        //    exit;
        //}

        // define('BOT_TOKEN', '발급받은 token');
        define('BOT_TOKEN', '5507332599:AAH5kdXozI0qxEplQanxOO-a4S6uEpDxNz4');
        define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

        // $_TELEGRAM_CHAT_ID = array('페이지에서 얻은 chat_id값');
        //$_TELEGRAM_CHAT_ID = array('@xtype1016');  실행안됨
        //민석: 53817767, 인선: 63318243
        $_TELEGRAM_CHAT_ID = array('53817767', '63318243');

        function telegramExecCurlRequest($handle) {

            $response = curl_exec($handle);

            if ($response === false) {
                $errno = curl_errno($handle);
                $error = curl_error($handle);
                error_log("Curl returned error $errno: $error\n");
                curl_close($handle);
                return false;
            }

            $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
            curl_close($handle);

            if ($http_code >= 500) {
                // do not wat to DDOS server if something goes wrong
                sleep(10);
                return false;
            } 
            else if ($http_code != 200) {

                $response = json_decode($response, true);

                error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");

                if ($http_code == 401) {
                    throw new Exception('Invalid access token provided');
                }

                return false;
            } 
            else {

                $response = json_decode($response, true);

                if (isset($response['description'])) {
                    error_log("Request was successfull: {$response['description']}\n");
                }

                $response = $response['result'];
            }

            return $response;
        }

        function telegramApiRequest($method, $parameters) {

            if (!is_string($method)) {
                error_log("Method name must be a string\n");
                return false;
            }

            if (!$parameters) {
                $parameters = array();
            } 
            else if (!is_array($parameters)) {
                error_log("Parameters must be an array\n");
                return false;
            }

            foreach ($parameters as $key => &$val) {
                // encoding to JSON array parameters, for example reply_markup
                if (!is_numeric($val) && !is_string($val)) {
                    $val = json_encode($val);
                }
            }

            $url = API_URL.$method.'?'.http_build_query($parameters);

            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($handle, CURLOPT_TIMEOUT, 60);

            return telegramExecCurlRequest($handle);
        }

        //$stnd_dt = '20220624';
        //$fm_dt   = '2022.06.24';
        $stnd_dt = date("Ymd");
        $fm_dt   = date("Y.m.d");
        info_log("Telegram_Push/", "stnd_dt = [" . $stnd_dt . "]");
        info_log("Telegram_Push/", "fm_dt   = [" . $fm_dt . "]");

        $deposit_refund_chk = $this->stay_m->get_deposit_refund_chk($stnd_dt);
        info_log("Telegram_Push/", "deposit_chk cnt = [" . $deposit_refund_chk->cnt . "]");

        if ($deposit_refund_chk->cnt > 0)
        {
            $alarm_msg = "금일(" . $fm_dt . ") 보증금 환불 " . $deposit_refund_chk->cnt . " 건이 있습니다!";

            // 메시지 발송 부분
            foreach($_TELEGRAM_CHAT_ID AS $_TELEGRAM_CHAT_ID_STR) {

                $_TELEGRAM_QUERY_STR    = array(
                    'chat_id' => $_TELEGRAM_CHAT_ID_STR,
                    'text'    => $alarm_msg
                );
                
                telegramApiRequest("sendMessage", $_TELEGRAM_QUERY_STR);

                $this->db->trans_begin();

                $i_data = array('message' => $alarm_msg
                               ,'chat_id' => $_TELEGRAM_CHAT_ID_STR
                               );

                $result = $this->stay_m->insert_tbz098l00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();
                    info_log("Telegram_Push/", "메시지 발송내역 입력 완료!");
                }
                else
                {
                    info_log("Telegram_Push/insert_tbz098l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("Telegram_Push/insert_tbz098l00", "[SQL ERR] 메시지 발송내역 입력 오류!");
                }

            }
            
        }

        info_log("Telegram_Push/", "메시지 발송 완료!");
        info_log("Telegram_Push/", "================================================================================");

    }
}

?>
