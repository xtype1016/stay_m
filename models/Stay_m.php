<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stay_m extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    //Query Bindings(바인딩으로 사용하면 값들은 자동으로 이스케이프 되기 때문에 보안에도 좋습니다.)
    //$sql = "SELECT * FROM some_table WHERE id = ? AND status = ? AND author = ?";
    //$this->db->query($sql, array(3, 'live', 'Rick'));
    //$result = $query->result();  // 객체 $result->board_id

    //active record
    //이 함수로 전달되는 모든 변수는 자동으로 이스케이프 되어 안전한 쿼리를 생성합니다.
    //$this->db->where()
    //$this->db->like()

    //$this->db->_error_message();
    //$this->db->_error_number();

    //$q_result   // 조회결과



    public function get_clm_sr_val($clm_nm)
    {
        $db_no = "";
        $ip_addr = "";
        $nxt_val  = "";

        if (strncmp($clm_nm, "USR_NO", 6) == 0) {
            $db_no = '0000000000';
        } else {
            $db_no = $_SESSION['db_no'];
        }

        if (isset($_SESSION['ip_addr']) > 0) {
            $ip_addr = $_SESSION['ip_addr'];
        } else {
            $ip_addr = get_ip();
        }

        $this->db->select('clm_nm
                          ,clm_val
                          ');
        $this->db->from('tba002i00');
        $this->db->where('db_no =', $db_no);
        $this->db->where('clm_nm =', $clm_nm);

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$q_result = $query->result();  // 객체 $result->board_id
        //$q_result = $query->result_array();  //배열 $result['board_id']
        $q_result = $query->row();  // 단건, 객체 $result->board_id

        if (!$q_result) {
            if ($query->num_rows() == 0) {
                // initial value setting

                if (strncmp($clm_nm, "USR_NO", 6) == 0 || strncmp($clm_nm, "GST_NO", 6) == 0) {
                    $nxt_val = '0000000001';
                } elseif (strncmp($clm_nm, "HSRM_CLS", 8) == 0) {
                    $nxt_val = '01';
                } elseif (strncmp($clm_nm, "TR_CLS", 6) == 0) {
                    $nxt_val = '21';
                } elseif (strncmp($clm_nm, "EXPNS_CLS", 9) == 0) {
                    $nxt_val = '01';
                } elseif (strncmp($clm_nm, "EXPNS_CHNL_CLS", 14) == 0) {
                    $nxt_val = '02';
                } else {
                    $nxt_val = '1';
                    //echo "초기값이 정의되지 않았습니다![" . $clm_nm . "]<br>";
                    //alert_log("get_clm_sr_val", "초기값이 정의되지 않았습니다![" . $clm_nm . "]");
                }

                $i_data = array(
                                 'db_no'       => $db_no
                                ,'clm_nm'      => $clm_nm
                                ,'clm_val'     => $nxt_val
                                ,'mnpl_usr_no' => $_SESSION['usr_no']
                                ,'mnpl_ip'     => $ip_addr
                                ,'mnpl_ymdh'   => date("YmdHis")
                               );

                $i_result = $this->db->insert('tba002i00', $i_data);

                if (!$i_result) {
                    //$sql_result = $this->db->error();

                    info_log("get_clm_sr_val", "last_query  = [" . $this->db->last_query() . "]");
                    //info_log("get_clm_sr_val", "sqlcode  = [" . $sql_result['code'] . "]");
                    //info_log("get_clm_sr_val", "sqlcode  = [" . $sql_result['message'] . "]");
                    alert_log("get_clm_sr_val", "초기값 채번 오류!(INSERT)[" . $db_no . "] [" . $clm_nm . "]");
                }
            } else {
                info_log("get_clm_sr_val", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_clm_sr_val", "[SQL ERR] 사용자 일련번호 조회 오류!");
            }
        } else {
            //print_r ($q_result);
            //echo "q_result->clm_val = [" . $q_result[0]->clm_val . "]<br>";

            if (strncmp($clm_nm, "USR_NO", 6) == 0 || strncmp($clm_nm, "GST_NO", 6) == 0) {
                $nxt_val = sprintf("%010d", (int)$q_result->clm_val + 1);
            } elseif (strncmp($clm_nm, "TR_CLS", 6) == 0          || strncmp($clm_nm, "EXPNS_CLS", 6) == 0 ||
                     strncmp($clm_nm, "EXPNS_CHNL_CLS", 14) == 0 || strncmp($clm_nm, "HSRM_CLS", 8) == 0  ||
                     strncmp($clm_nm, "IO_TR_CLS", 9) == 0
                    ) {
                if (strncmp($q_result->clm_val, "99", 2) == 0) {
                    alert_log("get_clm_sr_val", "더 이상 생성할 수 없습니다!(" .  $clm_nm . " 최대값 도달)");
                } else {
                    $nxt_val = sprintf("%02d", (int)$q_result->clm_val + 1);
                }

                info_log("get_clm_sr_val", "nxt_val  = [" . $nxt_val . "]");
            }
            //else if (strncmp($clm_nm, "EXPNS_CLS", 9) == 0)
            //{
            //    if (strncmp($q_result->clm_val, "9999", 2) == 0)
            //    {
            //        alert_log("get_clm_sr_val", "더 이상 생성할 수 없습니다!(" .  $clm_nm . " 최대값 도달)");
            //    }
            //    else
            //    {
            //        $nxt_val = sprintf("%04d", (int)$q_result->clm_val + 1);
            //    }
            //
            //    //info_log("get_clm_sr_val", "nxt_val  = [" . $nxt_val . "]");
            //}
            else {
                $nxt_val = sprintf("%d", (int)$q_result->clm_val + 1);
            }

            //echo "general nxt_val = [" . $nxt_val . "]<br>";

            $u_data = array(
                            'clm_val'     => $nxt_val
                           ,'mnpl_usr_no' => $_SESSION['usr_no']
                           ,'mnpl_ip'     => $ip_addr
                           ,'mnpl_ymdh'   => date("YmdHis")
                           );
            $this->db->where('db_no =', $db_no);
            $this->db->where('clm_nm =', $clm_nm);

            $u_result = $this->db->update('tba002i00', $u_data);

            if (!$u_result) {
                info_log("get_clm_sr_val", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_clm_sr_val", "다음 일련번호 채번 오류!(UPDATE CHK1)[" . $db_no . "] [" . $clm_nm . "]");
            }

            $prcs_cnt = $this->db->affected_rows();

            if ($prcs_cnt != 1) {
                info_log("get_clm_sr_val", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_clm_sr_val", "다음 일련번호 채번 오류!(Update Cnt ERR! prcs_cnt=, " . $prcs_cnt . ")");
            }
        }

        return $nxt_val;
    }


    public function get_usr_dup_chk($usr_id)
    {
        $this->db->select('count(*) cnt
                          ');
        $this->db->from('tba001i00  a');
        $this->db->where('a.usr_id = ', $usr_id);

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            if ($query->num_rows() == 0) {
                info_log("get_usr_dup_chk", "No Data Found!(clm_nm=" . $clm_nm . ", clm_val=" . $clm_val . ")");
            } else {
                info_log("get_usr_dup_chk", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_usr_dup_chk", "[SQL ERR] User ID 조회 오류!");
            }
        }

        return $result;
    }


    public function insert_tba001i00($arr_data)
    {
        $i_data = array('usr_no'     => $arr_data['usr_no']
                       ,'usr_id'     => $arr_data['usr_id']
                       ,'pswd'       => $arr_data['pswd']
                       ,'mnpl_ip'    => $arr_data['ip_addr']
                       ,'mnpl_ymdh'  => date("YmdHis")
                       );

        $result = $this->db->insert('tba001i00', $i_data);

        return $result;
    }


    public function get_usr_info($usr_id)
    {
        //$input_client_id = $this->input->get_request_header('Client-ID', TRUE);

        $this->db->select('a.usr_no
                          ,a.usr_id
                          ,a.pswd
                          ,ifnull(b.usr_no, a.usr_no)  db_no
                          ');
        $this->db->from('tba001i00  a');
        $this->db->join('tba001i02  b', 'b.shr_usr_no = a.usr_no', 'left');
        $this->db->where('a.usr_id = ', $usr_id);

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            $result_rows = $query->num_rows();
            if ($result_rows == 0) {
                alert_log("get_usr_info", "존재하지않는 사용자ID 입니다!(" . $usr_id . ")");

            //if ($client_id == $input_client_id)
                //{
                //    return array('status' => 204,'message' => 'Username not found.');
                //}
                //else
                //{
                //    alert_log("get_usr_info", "존재하지않는 사용자ID 입니다!(" . $usr_id . ")");
                //}
            } else {
                info_log("get_usr_info", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_usr_info", "[SQL ERR] 사용자 정보 조회 오류!");
            }
        }

        return $result;
    }


//    public function update_tba001i00_1($arr_data)
//    {
//        //print_r($u_data);
//        //exit;
//        $u_data = array('slctr'   => $arr_data['slctr']
//                       ,'tkn'     => $arr_data['tkn']
//                       ,'mnpl_ip' => $_SESSION['ip_addr']
//                       );
//        $this->db->where('usr_no = ', $arr_data['usr_no']);
//
//        $result = $this->db->update('tba001i00', $u_data);
//
//        if (!$result)
//        {
//            info_log("update_tba001i00_1", "last_query  = [" . $this->db->last_query() . "]");
//            alert_log("update_tba001i00_1", "SQL ERR!");
//        }
//
//        $prcs_cnt = $this->db->affected_rows();
//
//        if ($prcs_cnt != 1)
//        {
//            info_log("update_tba001i00_1", "last_query  = [" . $this->db->last_query() . "]");
//            alert_log("update_tba001i00_1", "Update Cnt ERR!(" . $prcs_cnt . ")");
//        }
//
//        return $result;
//    }
//
//
//    public function update_tba001i00_2($arr_data)
//    {
//
//        $u_data = array('shr_usr_no' => $arr_data['shr_usr_no']
//                       ,'mnpl_ip' => $_SESSION['ip_addr']
//                       );
//        $this->db->where('usr_no = ', $_SESSION['usr_no']);
//
//        $result = $this->db->update('tba001i00', $u_data);
//
//        if (!$result)
//        {
//            info_log("update_tba001i00_2", "last_query  = [" . $this->db->last_query() . "]");
//            alert_log("update_tba001i00_2", "SQL ERR!");
//        }
//
//        $prcs_cnt = $this->db->affected_rows();
//
//        if ($prcs_cnt != 1)
//        {
//            info_log("update_tba001i00_2", "last_query  = [" . $this->db->last_query() . "]");
//            alert_log("update_tba001i00_2", "Update Cnt ERR!(" . $prcs_cnt . ")");
//        }
//
//        return $result;
//    }

    public function insert_tba001i01($arr_data)
    {
        //$i_data = array('usr_no'       => $arr_data['usr_no']
        //               ,'idntfr'       => $arr_data['idntfr']
        //               ,'tkn'          => $arr_data['tkn']
        //               ,'mnpl_ip'      => $arr_data['ip_addr']
        //               ,'mnpl_ymdh'    => date("YmdHis")
        //               ,'expired_ymdh' => date("YmdHis", strtotime("+7 days"))
        //               );
        //
        //$result = $this->db->insert('tba001i01', $i_data);

        $sql = "insert into tba001i01 values (?
                                             ,?
                                             ,?
                                             ,?
                                             ,?
                                             ,?
                                            ) on duplicate key update tkn = ?, mnpl_ip = ?, mnpl_ymdh = ?, expired_ymdh = ?";

        $query = $this->db->query($sql, array($arr_data['usr_no']
                                             ,$arr_data['idntfr']
                                             ,$arr_data['tkn']
                                             ,$arr_data['ip_addr']
                                             ,date("YmdHis")
                                             ,date("YmdHis", strtotime("+7 days"))
                                             ,$arr_data['tkn']
                                             ,$arr_data['ip_addr']
                                             ,date("YmdHis")
                                             ,date("YmdHis", strtotime("+7 days"))
                                             ));

        //$result = $query->row();  // 단건, 객체 $result->board_id

        $result = $query;

        if (!$result) {
            info_log("insert_tba001i01", "last_query  = [" . $this->db->last_query() . "]");
            return $result;
        }

        return $result;
    }


    public function update_tba001i01_1($arr_data)
    {
        $u_data = array('tkn'          => $arr_data['tkn']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       ,'expired_ymdh' => date("YmdHis", strtotime("+7 days"))
                       );
        $this->db->where('usr_no = ', $arr_data['usr_no']);
        $this->db->where('idntfr = ', $arr_data['idntfr']);

        $result = $this->db->update('tba001i01', $u_data);

        return $result;
    }


    public function delete_tba001i01_1($arr_data)
    {
        $this->db->where('usr_no = ', $arr_data['usr_no']);
        $this->db->where('idntfr = ', $arr_data['idntfr']);

        $result = $this->db->delete('tba001i01');

        return $result;
    }


    public function delete_tba001i01_2($usr_no)
    {
        $this->db->where('usr_no = ', $usr_no);
        $this->db->where('expired_ymdh <= adddate(now(), -8)');

        $result = $this->db->delete('tba001i01');

        return $result;
    }


    public function insert_tba001i03($arr_data)
    {
        $i_data = array('usr_no'     => $arr_data['usr_no']
                       ,'idntfr'     => $arr_data['idntfr']
                       ,'login_ip'   => $arr_data['ip_addr']
                       ,'login_ymdh' => date("YmdHis")
                       );

        $result = $this->db->insert('tba001i03', $i_data);

        return $result;
    }


    public function insert_tba003i00($arr_data)
    {
        $i_data = array('db_no'       => $_SESSION['db_no']
                       ,'clm_nm'      => $arr_data['clm_nm']
                       ,'clm_val'     => $arr_data['clm_val']
                       ,'clm_val_nm'  => $arr_data['clm_val_nm']
                       ,'othr_info'   => $arr_data['othr_info']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $result = $this->db->insert('tba003i00', $i_data);

        return $result;
    }


    public function update_tba003i00_1($arr_data)
    {
        //print_r($u_data);
        //exit;
        $u_data = array('clm_val_nm'  => $arr_data['clm_val_nm']
                       ,'othr_info'   => $arr_data['othr_info']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $this->db->where('db_no   = ', $_SESSION['db_no']);
        $this->db->where('clm_nm  = ', $arr_data['clm_nm']);
        $this->db->where('clm_val = ', $arr_data['clm_val']);

        $result = $this->db->update('tba003i00', $u_data);

        return $result;
    }


    public function update_tba003i00_2($arr_data)
    {
        $u_data = array('del_yn'       => 'Y'
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

        $this->db->where('db_no   = ', $_SESSION['db_no']);
        $this->db->where('clm_nm  = ', $arr_data['clm_nm']);
        $this->db->where('clm_val = ', $arr_data['clm_val']);

        $result = $this->db->update('tba003i00', $u_data);

        return $result;
    }


//    public function update_tba003i00_3($arr_data)
//    {
//        //print_r($u_data);
//        //exit;
//        $u_data = array('clm_val'     => $arr_data['clm_val']
//                       ,'clm_val_nm'  => $arr_data['clm_val_nm']
//                       ,'othr_info'   => $arr_data['othr_info']
//                       ,'mnpl_usr_no' => $_SESSION['usr_no']
//                       ,'mnpl_ip'     => $_SESSION['ip_addr']
//                       );
//
//        $this->db->where('db_no   = ', $_SESSION['db_no'] );
//        $this->db->where('clm_nm  = ', $arr_data['clm_nm'] );
//        $this->db->where('clm_val = ', $arr_data['bef_clm_val']);
//
//        $result = $this->db->update('tba003i00', $u_data);
//
//        return $result;
//    }


    public function get_item_list($clm_nm=null, $clm_val=null, $prcs_cls=null, $offset=null, $limit=null)
    {
        $this->db->select('clm_val
                          ,clm_val_nm
                          ,othr_info
                          ');
        $this->db->from('tba003i00');
        $this->db->where('db_no = ', $_SESSION['db_no']);
        $this->db->where('clm_nm = ', $clm_nm);
        $this->db->like('clm_val', $clm_val, 'after');
        $this->db->where("del_yn = 'N'");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by("clm_val");

        $query = $this->db->get();

        $result_rows = $query->num_rows();

        if ($prcs_cls == 'rowcnt') {
            $result = $result_rows;

            if ($result == 0) {
                info_log("get_item_list", "No Data Found!(clm_nm=" . $clm_nm . ", clm_val=" . $clm_val . ")");
            }
        } else {
            if ($result_rows == 1 && strncmp($prcs_cls, "single", 6) == 0) {
                $result = $query->row();  // 단건, 객체 $result->board_id
            } else {
                $result = $query->result();  // 객체 $result->board_id
                //$result = $query->result_array();  배열 $result['board_id']
            }

            if (!$result) {
                if ($result_rows == 0) {
                    info_log("get_item_list", "No Data Found!(clm_nm=" . $clm_nm . ", clm_val=" . $clm_val . ")");
                } else {
                    info_log("get_item_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_item_list", "[SQL ERR] 메타 항목 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_list($clm_nm, $mst_yn=null, $clm_val=null)
    {
        $this->db->select("clm_val
                          ,clm_val_nm
                          ,othr_info
                          ");
        $this->db->from('tba003i00');

        //if (strncmp($clm_nm, "RSV_CHNL_CLS", 12) == 0 || strncmp($clm_nm, "EXPNS_CHNL_CLS", 14) == 0
        // || strncmp($clm_nm, "CLSS", 4) == 0          || strncmp($clm_nm, "GST_CLS", 7) == 0
        // || strncmp($clm_nm, "COST_CLS", 4) == 0
        //   )
        //{
        //     $t_db_no = array($_SESSION['db_no'], '0000000000');
        //     $this->db->where_in('db_no', $t_db_no);
        //}
        //else
        //{
        //    $this->db->where("db_no = ", $_SESSION['db_no']);
        //}

        if (strncmp($mst_yn, "Y", 1) == 0) {
            $t_db_no = array($_SESSION['db_no'], '0000000000');
            $this->db->where_in('db_no', $t_db_no);
        } else {
            $this->db->where("db_no = ", $_SESSION['db_no']);
        }

        $this->db->where("clm_nm = ", $clm_nm);

        if (strncmp($clm_nm, "EXPNS_CLS", 9) == 0 || strncmp($clm_nm, "IO_TR_CLS", 9) == 0 || strncmp($clm_nm, "USR", 3) == 0) {
            $this->db->like('clm_val', $clm_val, 'after');
        }

        $this->db->where("del_yn = ", 'N');

        if (strncmp($clm_nm, "EXPNS_CLS", 9) == 0) {
            $this->db->order_by("concat(case when othr_info is null then concat(clm_val, '1') else concat(othr_info, '2') end, clm_val_nm)");
        } else {
            $this->db->order_by("ifnull(orderby_info, clm_val)");
        }

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        $result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']

        if (!$result) {
            if ($query->num_rows() == 0) {
                info_log("get_list", "No Data Found!(clm_nm=" . $clm_nm . ", clm_val=" . $clm_val . ")");
            } else {
                info_log("get_list", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_list", "[SQL ERR] 메타 항목 리스트 조회 오류!");
            }
        }

        return $result;
    }


    public function get_incm_smmry($sum_cls, $stnd_yymm, $prcs_cls=null, $offset=null, $limit=null)
    {
        // 전년 동월/기간 일자 조회
        $sql = "select  date_format(ADDDATE(concat(?, '01'), INTERVAL -13 month), '%Y%m')  last_yr_bf_mon
                       ,date_format(ADDDATE(concat(?, '01'), INTERVAL -12 month), '%Y%m')  last_yr_mon
                       ,date_format(ADDDATE(concat(?, '01'), INTERVAL   1 month), '%Y%m')  this_yr_af_mon";

        $query = $this->db->query($sql, array($stnd_yymm, $stnd_yymm, $stnd_yymm));

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_incm_smmry/last_yr_mon", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_incm_smmry/last_yr_mon", "[SQL ERR] 전년 동월/기간 일자 조회 오류!");
        }

        $last_yr_bf_mon = $result->last_yr_bf_mon;
        $last_yr_mon    = $result->last_yr_mon;
        $this_yr_af_mon = $result->this_yr_af_mon;

        info_log("get_incm_smmry/last_yr_mon", "last_yr_bf_mon    = " . $last_yr_bf_mon);
        info_log("get_incm_smmry/last_yr_mon", "last_yr_mon       = " . $last_yr_mon);
        info_log("get_incm_smmry/last_yr_mon", "this_yr_af_mon    = " . $this_yr_af_mon);

        $result ='';

        $sql = "select  a.sum_cls
                       ,b.clm_val_nm
                       ,a.rsv_rt
                       ,a.this_year_mon_sell_amt
                       ,a.last_year_mon_sell_amt
                       ,a.this_year_mon_amt
                       ,a.last_year_mon_amt
                       ,a.this_year_amt
                       ,a.last_year_amt
                  from  (
                         select  a.sum_cls         sum_cls
                                ,case when ? = '1' then b.hsrm_rsv_rt
                                      when ? = '2' then b.chnl_rsv_rt
                                 end  rsv_rt
                                ,sum(b.this_year_mon_sell_amt)   this_year_mon_sell_amt
                                ,sum(b.last_year_mon_sell_amt)   last_year_mon_sell_amt
                                ,sum(a.this_year_mon_amt     )   this_year_mon_amt
                                ,sum(a.last_year_mon_amt     )   last_year_mon_amt
                                ,sum(a.this_year_amt         )   this_year_amt
                                ,sum(a.last_year_amt         )   last_year_amt
                           from  (
                                 select  case when ? = '1' then a.hsrm_cls
                                              when ? = '2' then a.chnl_cls
                                         end   sum_cls
                                        ,sum(a.last_year_mon_amt)  last_year_mon_amt
                                        ,sum(a.this_year_mon_amt)  this_year_mon_amt
                                        ,sum(a.last_year_amt)      last_year_amt
                                        ,sum(a.this_year_amt)      this_year_amt
                                   from  (
                                         select  case when c.addtnl_info = '2' then '2'
                                                      else '1'
                                                 end chnl_cls
                                                ,b.hsrm_cls
                                                ,a.tr_dt
                                                ,(case when a.tr_dt like concat(?, '%') then c.othr_info * a.amt else 0 end)    last_year_mon_amt
                                                ,(case when a.tr_dt like concat(?, '%') then c.othr_info * a.amt else 0 end)    this_year_mon_amt
                                                ,(case when a.tr_dt like concat(substr(?, 1, 4), '%') then c.othr_info * a.amt else 0 end)    last_year_amt
                                                ,(case when a.tr_dt like concat(substr(?, 1, 4), '%') then c.othr_info * a.amt else 0 end)    this_year_amt
                                           from  tba006l00  a
                                                ,tba005l00  b
                                                ,tba003i00  c
                                          where  a.db_no = ?
                                            and  a.tr_dt between concat(substr(?, 1, 4), '0101') and concat(substr(?, 1, 4), '1231')
                                            and  a.amt > 0
                                            and  a.del_yn = 'N'
                                            and  b.db_no = a.db_no
                                            and  b.rsv_srno = a.rsv_srno
                                            and  c.db_no in ('0000000000', a.db_no)
                                            and  c.clm_nm = 'tr_cls'
                                            and  c.clm_val = a.tr_cls
                                         )  a
                                  group by  case when ? = '1' then a.hsrm_cls
                                                 when ? = '2' then a.chnl_cls
                                            end
                                 )  a
                                ,(
                                  select  case when ? = '1' then a.hsrm_cls
                                               when ? = '2' then a.rsv_chnl_cls
                                          end  sum_cls
                                         ,truncate((sum(case when a.dt like concat(?, '%') then 1 else 0 end)
                                                    / convert(date_format(last_day(concat(?, '01')), '%d'), decimal)) * 100, 2)  hsrm_rsv_rt
                                         ,truncate((sum(case when a.dt like concat(?, '%') then 1 else 0 end)
                                                    / (convert(date_format(last_day(concat(?, '01')), '%d'), decimal) * 2)) * 100, 2)  chnl_rsv_rt
                                         ,sum(case when a.dt like concat(?, '%') then a.amt_per_dt else 0 end)  last_year_mon_sell_amt
                                         ,sum(case when a.dt like concat(?, '%') then a.amt_per_dt else 0 end)  this_year_mon_sell_amt
                                    from  (
                                           select  a.rsv_srno
                                                  ,a.hsrm_cls
                                                  ,a.rsv_chnl_cls
                                                  ,a.srt_dt
                                                  ,a.end_dt
                                                  ,a.amt
                                                  ,b.dt
                                                  ,count(b.dt) over (partition by a.rsv_srno)  dt_cnt
                                                  ,truncate(a.amt / count(b.dt) over (partition by a.rsv_srno), 0)  amt_per_dt
                                             from  tba005l00  a
                                                  ,tba004l00  b
                                            where  a.db_no = ?
                                              and  a.srt_dt >= concat(?, '01')
                                              and  a.end_dt <= concat(?, '31')
                                              and  a.cncl_yn = 'N'
                                              and  b.dt between a.srt_dt and a.end_dt
                                          )  a
                                   group by  case when ? = '1' then a.hsrm_cls
                                                  when ? = '2' then a.rsv_chnl_cls
                                             end
                                 )  b
                          where  b.sum_cls = a.sum_cls
                          group by  a.sum_cls
                          with rollup
                          limit  ?, ?
                        ) a  left join  tba003i00  b
                                    on  b.db_no in ('0000000000', ?)
                                   and  b.clm_nm = case when ? = '1' then 'hsrm_cls'
                                                        when ? = '2' then 'rsv_chnl_cls'
                                                   end
                                   and  b.clm_val = a.sum_cls
                   order by  ifnull(a.sum_cls, '99')";

        $query = $this->db->query($sql, array($sum_cls
                                             ,$sum_cls
                                             ,$sum_cls
                                             ,$sum_cls
                                             ,$last_yr_mon
                                             ,$stnd_yymm
                                             ,$last_yr_mon
                                             ,$stnd_yymm
                                             ,$_SESSION['db_no']
                                             ,$last_yr_mon
                                             ,$stnd_yymm
                                             ,$sum_cls
                                             ,$sum_cls
                                             ,$sum_cls
                                             ,$sum_cls
                                             ,$stnd_yymm
                                             ,$stnd_yymm
                                             ,$stnd_yymm
                                             ,$stnd_yymm
                                             ,$last_yr_mon
                                             ,$stnd_yymm
                                             ,$_SESSION['db_no']
                                             ,$last_yr_bf_mon
                                             ,$this_yr_af_mon
                                             ,$sum_cls
                                             ,$sum_cls
                                             ,$offset
                                             ,$limit
                                             ,$_SESSION['db_no']
                                             ,$sum_cls
                                             ,$sum_cls
                                             ));

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_incm_smmry", "No Data Found!(sum_cls=[" . $sum_cls . "], stnd_yymm=[" . $stnd_yymm . "])");
            return;
        } else {
            if ($prcs_cls == 'data') {
                //info_log("get_incm_smmry", "last_query  = [" . $this->db->last_query() . "]");
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_incm_smmry", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_incm_smmry", "[SQL ERR] 수익 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_incm_list($stnd_yymm, $rsv_chnl_cls=null, $prcs_cls=null, $offset=null, $limit=null)
    {
        $sql = "select  a.tr_dt
                       ,concat(substr(a.tr_dt, 1, 4), '-', substr(a.tr_dt, 5, 2), '-', substr(a.tr_dt, 7, 2))    stnd_tr_dt
                       ,a.rsv_srno
                       ,a.rsv_chnl_cls
                       ,a.tr_srno
                       ,a.gst_nm
                       ,a.tr_cls_nm
                       ,a.amt
                       ,a.acnt_bal
                  from  (
                         select  a.tr_dt
                                ,a.tr_srno
                                ,c.rsv_srno
                                ,c.rsv_chnl_cls
                                ,d.gst_nm
                                ,case when a.othr_withdraw_yn = 'Y' then concat(b.clm_val_nm, '(타계좌출금)')
                                      else b.clm_val_nm
                                 end    tr_cls_nm
                                ,sum(b.othr_info * a.amt)    amt
                                ,sum(case when a.othr_withdraw_yn = 'N' then b.othr_info * a.amt else 0 end)    acnt_bal
                           from  tba006l00  a
                                ,tba003i00  b
                                ,tba005l00  c
                                ,tba007l00  d
                          where  a.db_no = ?
                            and  a.tr_dt between concat(?, '01') and concat(?, '31')
                            and  a.amt > 0
                            and  a.del_yn = 'N'
                            and  b.db_no in ('0000000000', a.db_no)
                            and  b.clm_nm = 'TR_CLS'
                            and  b.clm_val = a.tr_cls
                            and  case when b.addtnl_info = '2' then '2'
                                      else '1'
                                 end like ?
                            and  c.db_no = a.db_no
                            and  c.rsv_srno = a.rsv_srno
                            and  d.db_no = c.db_no
                            and  d.gst_no = c.gst_no
                          group by  a.tr_dt
                                   ,b.clm_val_nm
                                   ,d.gst_nm
                          with rollup
                       )  a
                where  ((a.tr_dt is null
                         and  a.tr_cls_nm is null
                         and  a.gst_nm is null)
                        or
                        (a.tr_dt is not null
                         and  a.tr_cls_nm is not null
                         and  a.gst_nm is not null))
                order by  ifnull(a.tr_dt, '99999999'), a.tr_srno";

        if (isset($limit) && isset($offset)) {
            $sql = $sql . " limit " .  $offset . ", " . $limit;
        }

        $query = $this->db->query($sql, array($_SESSION['db_no']
                                             ,$stnd_yymm
                                             ,$stnd_yymm
                                             ,$rsv_chnl_cls
                                             ));

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_incm_list", "No Data Found!(stnd_yymm=" . $stnd_yymm . ")");
            return;
        } else {
            if ($prcs_cls == 'data') {
                //info_log("get_incm_list", "last_query  = [" . $this->db->last_query() . "]");
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_incm_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_incm_list", "[SQL ERR] 일별 수익 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_usr_tkn($usr_no, $idntfr)
    {
        $this->db->select('a.usr_no
                          ,a.usr_id
                          ,b.tkn
                          ,b.expired_ymdh
                          ,ifnull(c.usr_no, a.usr_no)  db_no
                          ');
        $this->db->from('tba001i00  a');
        $this->db->join('tba001i01  b', 'b.usr_no = a.usr_no', 'left');
        $this->db->join('tba001i02  c', 'c.shr_usr_no = a.usr_no', 'left');
        $this->db->where('a.usr_no = ', $usr_no);
        $this->db->where('b.idntfr  = ', $idntfr);

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        //$sql_result = $this->db->error();

        //info_log("get_usr_tkn", "num_rows = [" . (string)$query->num_rows() . "]");

        if (!$result) {
            if ($query->num_rows() == 0) {
                info_log("get_usr_tkn", "No Data Found(usr_no=" . $usr_no . ", idntfr=" . $idntfr . ")");
            } else {
                info_log("get_usr_tkn", "last_query  = [" . $this->db->last_query() . "]");
                info_log("get_usr_tkn", "[SQL ERR] 사용자 토큰 조회 오류!");
            }
        }

        return $result;
    }


    public function get_pay_rcv_dt($srt_dt)
    {
        //echo "srt_dt = " . $srt_dt . "<br>";

        //지급일 = 입실일 + 1(휴일 여부 무관)
        //입금일 = 당일 포함 평일 3일 후 입금(도이치뱅크)
        //2017.09.15 지급은행 변경으로 입금일 변경
        //입금일 = 지급일 + 1(농협) = 입실일 + 2
        //$sql = "select  date_format(adddate(str_to_date(?, '%Y%m%d'), 1), '%Y%m%d')  pay_dt
        //               ,dt                                                           rcv_dt
        //          from  (
        //                 select  dt
        //                        ,dt_cls
        //                        ,@rnum := @rnum + 1  rnum
        //                   from  tba004l00  a
        //                        ,(
        //                          select  @rnum := 0
        //                         )  b
        //                   where  dt > ?
        //                     and  dt <= date_format(adddate(str_to_date(?, '%Y%m%d'), 10), '%Y%m%d')
        //                     and  dt not in (
        //                                     select  dt
        //                                       from  tba004l00
        //                                      where  dt > ?
        //                                        and  dt <= date_format(adddate(str_to_date(?, '%Y%m%d'), 10), '%Y%m%d')
        //                                        and  dt_cls != '1'
        //                                    )
        //                    and  dt_cls = '1'
        //                  order by  dt
        //                )  a
        //         where  rnum = 3";
        //
        //$query = $this->db->query($sql, array($srt_dt, $srt_dt, $srt_dt, $srt_dt, $srt_dt));


        $sql = "select  date_format(adddate(str_to_date(?, '%Y%m%d'), 1), '%Y%m%d')  pay_dt
                       ,date_format(adddate(str_to_date(?, '%Y%m%d'), 2), '%Y%m%d')  rcv_dt";

        $query = $this->db->query($sql, array($srt_dt, $srt_dt));

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_pay_rcv_dt", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_pay_rcv_dt", "[SQL ERR] 수령일 조회 오류!");
        }

        return $result;
    }


    public function insert_tba005l00($arr_data)
    {
        // 중복 등록 여부 확인
        //$sql = "select  count(*)  cnt
        //          from  tba005l00
        //         where  db_no = ?
        //           and  hsrm_cls = ?
        //           and  (
        //                 srt_dt between ? and ?
        //                 or
        //                 end_dt between ? and ?
        //                )
        //           and  cncl_yn = 'N'";
        //
        //$query = $this->db->query($sql, array($_SESSION['db_no'], $arr_data['hsrm_cls'], $arr_data['srt_dt'], $arr_data['end_dt'], $arr_data['srt_dt'], $arr_data['end_dt']));

        // 중복 등록 여부 확인
        $sql = "select  count(*)  cnt
                  from  tba004l00  a
                       ,(
                         select  srt_dt
                                ,end_dt
                           from  tba005l00
                          where  db_no = ?
                            and  hsrm_cls = ?
                            and  srt_dt between date_format(adddate(?, -30), '%Y%m%d') and date_format(adddate(?, 30), '%Y%m%d')
                            and  cncl_yn = 'N'
                        )  b
                where  a.dt between b.srt_dt and b.end_dt
                  and  a.dt between ? and ?";

        $query = $this->db->query($sql, array($_SESSION['db_no'], $arr_data['hsrm_cls'], $arr_data['srt_dt'], $arr_data['end_dt'], $arr_data['srt_dt'], $arr_data['end_dt']));

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("insert_tba005l00/dup_chk", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("insert_tba005l00/dup_chk", "[SQL ERR] 예약 중복 여부 체크 오류!");
        } else {
            info_log("insert_tba005l00/dup_chk", "hsrm_cls = " . $arr_data['hsrm_cls'] . " ,srt_dt = " . $arr_data['srt_dt'] . " , end_dt = " . $arr_data['end_dt']);
            info_log("insert_tba005l00/dup_chk", "result_cnt = " . $result->cnt);
            //info_log("insert_tba005l00/dup_chk", "query_cnt = " . $query->num_rows());

            if ($result->cnt > 0) {
                alert_log("insert_tba005l00/dup_chk", "예약기간에 이미 등록된 예약이 존재합니다!");
            }
        }

        $i_data = array('db_no'        => $_SESSION['db_no']
                       ,'rsv_srno'     => $arr_data['rsv_srno']
                       ,'cnfm_dt'      => $arr_data['cnfm_dt']
                       ,'hsrm_cls'     => $arr_data['hsrm_cls']
                       ,'srt_dt'       => $arr_data['srt_dt']
                       ,'end_dt'       => $arr_data['end_dt']
                       ,'gst_no'       => $arr_data['gst_no']
                       ,'amt'          => $arr_data['amt']
                       ,'deposit'      => $arr_data['deposit']
                       ,'rsv_chnl_cls' => $arr_data['rsv_chnl_cls']
                       ,'gst_desc'     => $arr_data['gst_desc']
                       ,'adlt_cnt'     => $arr_data['adlt_cnt']
                       ,'chld_cnt'     => $arr_data['chld_cnt']
                       ,'gst_cls'      => $arr_data['gst_cls']
                       ,'evnt_id'      => $arr_data['evnt_id']
                       ,'memo'         => $arr_data['memo']
                       ,'cncl_yn'      => $arr_data['cncl_yn']
                       ,'cncl_dt'      => $arr_data['cncl_dt']
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

        $result = $this->db->insert('tba005l00', $i_data);

        return $result;
    }


    public function get_rsvt_info($rsv_srno = '')
    {
        //google 캘린더의 end-date 는 exclusive(start-dt 는 inclusive) 하므로 end_dt + 1 일자를 리턴
        $this->db->select("a.cnfm_dt
                          ,concat(substr(a.cnfm_dt, 1, 4), '-', substr(a.cnfm_dt, 5, 2), '-', substr(a.cnfm_dt, 7, 2))  stnd_cnfm_dt
                          ,a.hsrm_cls
                          ,c.clm_val_nm  hsrm_cls_nm
                          ,a.srt_dt
                          ,concat(substr(a.srt_dt, 1, 4), '-', substr(a.srt_dt, 5, 2), '-', substr(a.srt_dt, 7, 2))  stnd_srt_dt
                          ,a.end_dt
                          ,concat(substr(a.end_dt, 1, 4), '-', substr(a.end_dt, 5, 2), '-', substr(a.end_dt, 7, 2))  stnd_end_dt
                          ,adddate(a.end_dt, 1)                                                                      stnd_g_end_dt
                          ,a.gst_no
                          ,b.gst_nm
                          ,a.amt
                          ,a.deposit
                          ,a.rsv_chnl_cls
                          ,d.clm_val_nm  rsv_chnl_cls_nm
                          ,case when a.rsv_chnl_cls = '1' then '[BL]'
                                when a.rsv_chnl_cls = '2' then '[AB]'
                           end  g_rsv_chnl_cls
                          ,a.gst_desc
                          ,a.adlt_cnt
                          ,a.chld_cnt
                          ,a.gst_cls
                          ,a.evnt_id
                          ,a.memo
                          ,a.cncl_yn
                          ,a.cncl_dt
                          ");
        $this->db->from('tba005l00  a');
        $this->db->from('tba007l00  b');
        $this->db->from('tba003i00  c');
        $this->db->from('tba003i00  d');
        $this->db->where("a.db_no     = ", $_SESSION['db_no']);
        $this->db->where("a.rsv_srno  = ", $rsv_srno);
        $this->db->where("b.db_no     = a.db_no");
        $this->db->where("b.gst_no    = a.gst_no");
        $this->db->where("c.db_no     = a.db_no");
        $this->db->where("c.clm_nm    = 'HSRM_CLS'");
        $this->db->where("c.clm_val   = a.hsrm_cls");
        $this->db->where("d.db_no     = '0000000000'");
        $this->db->where("d.clm_nm    = 'RSV_CHNL_CLS'");
        $this->db->where("d.clm_val   = a.rsv_chnl_cls");
        $this->db->order_by("a.rsv_srno DESC");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_rsvt_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_rsvt_info", "[SQL ERR] 예약정보 조회 오류!");
        }

        return $result;
    }


    public function get_rsvt_list($stnd_yymm, $hsrm_cls, $rsv_chnl_cls, $view_cls, $prcs_cls=null, $offset=null, $limit=null)
    {
        if (strncmp($hsrm_cls, 'all', 3) == 0) {
            $hsrm_cls = '';
        }

        if (strncmp($rsv_chnl_cls, 'all', 3) == 0) {
            $rsv_chnl_cls = '';
        }

        $this->db->select("a.rsv_srno
                          ,b.clm_val_nm    hsrm_cls_nm
                          ,concat(substr(a.srt_dt, 1, 4), '-', substr(a.srt_dt, 5, 2), '-', substr(a.srt_dt, 7, 2))  srt_dt
                          ,concat(substr(a.cnfm_dt, 1, 4), '-', substr(a.cnfm_dt, 5, 2), '-', substr(a.cnfm_dt, 7, 2))  cnfm_dt
                          ,d.gst_nm
                          ,c.clm_val_nm  rsv_chnl_cls_nm
                          ");
        $this->db->from('tba005l00  a');
        $this->db->from('tba003i00  b');
        $this->db->from('tba003i00  c');
        $this->db->from('tba007l00  d');
        $this->db->where('a.db_no = ', $_SESSION['db_no']);

        if (strncmp($view_cls, '1', 1) == 0) {
            $this->db->like('a.srt_dt', $stnd_yymm, 'after');
        } elseif (strncmp($view_cls, '2', 1) == 0) {
            $this->db->like('a.cnfm_dt', $stnd_yymm, 'after');
        }

        $this->db->like('a.hsrm_cls', $hsrm_cls, 'after');
        $this->db->like('a.rsv_chnl_cls', $rsv_chnl_cls, 'after');
        $this->db->where("a.cncl_yn = 'N'");
        $this->db->where("b.db_no = a.db_no");
        $this->db->where("b.clm_nm = 'HSRM_CLS'");
        $this->db->where("b.clm_val = a.hsrm_cls");
        $this->db->where("c.db_no = '0000000000'");
        $this->db->where("c.clm_nm = 'RSV_CHNL_CLS'");
        $this->db->where("c.clm_val = a.rsv_chnl_cls");
        $this->db->where("d.db_no  = a.db_no");
        $this->db->where("d.gst_no = a.gst_no");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        if (strncmp($view_cls, '1', 1) == 0) {
            $this->db->order_by('a.srt_dt, a.hsrm_cls, a.rsv_srno');
        } elseif (strncmp($view_cls, '2', 1) == 0) {
            $this->db->order_by('a.cnfm_dt, a.hsrm_cls, a.rsv_srno');
        }

        $query = $this->db->get();

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_rsvt_list", "No Data Found!(stnd_yymm=" . $stnd_yymm . ", hsrm_cls=" . $hsrm_cls . ")");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_rsvt_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_rsvt_list", "[SQL ERR] 예약 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function update_tba005l00_1($arr_data)
    {
        // 중복 등록 여부 확인
        $sql = "select  count(*)  cnt
                  from  tba004l00  a
                       ,(
                         select  srt_dt
                                ,end_dt
                           from  tba005l00
                          where  db_no = ?
                            and  rsv_srno != ?
                            and  hsrm_cls = ?
                            and  srt_dt between date_format(adddate(?, -30), '%Y%m%d') and date_format(adddate(?, 30), '%Y%m%d')
                            and  cncl_yn = 'N'
                        )  b
                where  a.dt between b.srt_dt and b.end_dt
                  and  a.dt between ? and ?";

        $query = $this->db->query($sql, array($_SESSION['db_no'], $arr_data['rsv_srno'], $arr_data['hsrm_cls'], $arr_data['srt_dt'], $arr_data['end_dt'], $arr_data['srt_dt'], $arr_data['end_dt']));

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("update_tba005l00_1/dup_chk", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("update_tba005l00_1/dup_chk", "[SQL ERR] 예약 중복 여부 체크 오류!");
        } else {
            info_log("update_tba005l00_1/dup_chk", "hsrm_cls = " . $arr_data['hsrm_cls'] . " ,srt_dt = " . $arr_data['srt_dt'] . " , end_dt = " . $arr_data['end_dt']);
            info_log("update_tba005l00_1/dup_chk", "result_cnt = " . $result->cnt);
            //info_log("insert_tba005l00/dup_chk", "query_cnt = " . $query->num_rows());

            if ($result->cnt > 0) {
                alert_log("update_tba005l00_1/dup_chk", "예약기간에 이미 등록된 예약이 존재합니다!");
            }
        }

        $u_data = array('cnfm_dt'      => $arr_data['cnfm_dt']
                       ,'hsrm_cls'     => $arr_data['hsrm_cls']
                       ,'srt_dt'       => $arr_data['srt_dt']
                       ,'end_dt'       => $arr_data['end_dt']
                       ,'gst_no'       => $arr_data['gst_no']
                       ,'amt'          => $arr_data['amt']
                       ,'deposit'      => $arr_data['deposit']
                       ,'rsv_chnl_cls' => $arr_data['rsv_chnl_cls']
                       ,'gst_desc'     => $arr_data['gst_desc']
                       ,'adlt_cnt'     => $arr_data['adlt_cnt']
                       ,'chld_cnt'     => $arr_data['chld_cnt']
                       ,'gst_cls'      => $arr_data['gst_cls']
                       ,'memo'         => $arr_data['memo']
                       ,'cncl_yn'      => $arr_data['cncl_yn']
                       ,'cncl_dt'      => $arr_data['cncl_dt']
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

        $this->db->where('db_no   = ', $_SESSION['db_no']);
        $this->db->where('rsv_srno = ', $arr_data['rsv_srno']);

        $result = $this->db->update('tba005l00', $u_data);

        return $result;
    }


    public function update_tba005l00_2($arr_data, $prcs_cls=null)
    {
        $u_data = array(
                        'cncl_yn'      => $arr_data['cncl_yn']
                       ,'cncl_dt'      => $arr_data['cncl_dt']
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

        $this->db->where('db_no   = ', $_SESSION['db_no']);
        $this->db->where('rsv_srno = ', $arr_data['rsv_srno']);

        $result = $this->db->update('tba005l00', $u_data);

        return $result;
    }


    public function update_tba005l00_3($arr_data)
    {
        $u_data = array(
                        'evnt_id'     => $arr_data['evnt_id']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $this->db->where('db_no   = ', $_SESSION['db_no']);
        $this->db->where('rsv_srno = ', $arr_data['rsv_srno']);

        $result = $this->db->update('tba005l00', $u_data);

        return $result;
    }


    public function insert_tba006l00($arr_data)
    {
        $i_data = array('db_no'            => $_SESSION['db_no']
                       ,'tr_srno'          => $arr_data['tr_srno']
                       ,'rsv_srno'         => $arr_data['rsv_srno']
                       ,'tr_dt'            => $arr_data['tr_dt']
                       ,'tr_cls'           => $arr_data['tr_cls']
                       ,'tr_chnl_cls'      => $arr_data['tr_chnl_cls']
                       ,'amt'              => $arr_data['amt']
                       ,'memo'             => $arr_data['memo']
                       ,'othr_withdraw_yn' => $arr_data['othr_withdraw_yn']
                       ,'expns_srno'       => $arr_data['expns_srno']
                       ,'mnpl_usr_no'      => $_SESSION['usr_no']
                       ,'mnpl_ip'          => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'        => date("YmdHis")
                       );

        $result = $this->db->insert('tba006l00', $i_data);

        return $result;
    }


    public function get_etc_incm_list($stnd_yymm=null, $prcs_cls=null, $offset=null, $limit=null)
    {
        // 2020.02.01  조건 제외
        // $this->db->where("b.cncl_yn  = 'N'");

        $this->db->select("a.tr_srno
                          ,a.rsv_srno
                          ,concat(substr(a.tr_dt, 1, 4), '-', substr(a.tr_dt, 5, 2), '-', substr(a.tr_dt, 7, 2))  tr_dt
                          ,d.gst_nm
                          ,a.tr_cls
                          ,c.clm_val_nm  tr_cls_nm
                          ,a.amt
                          ,c.addtnl_info
                          ");
        $this->db->from('tba006l00  a');
        $this->db->from('tba005l00  b');
        $this->db->from('tba003i00  c');
        $this->db->from('tba007l00  d');
        $this->db->where('a.db_no = ', $_SESSION['db_no']);
        $this->db->like('a.tr_dt', $stnd_yymm, 'after');
        $this->db->where("a.del_yn  = 'N'");
        $this->db->where("b.db_no   = a.db_no");
        $this->db->where("b.rsv_srno = a.rsv_srno");
        $this->db->where("c.db_no   in ('0000000000', a.db_no)");
        $this->db->where("c.clm_nm = 'TR_CLS'");
        $this->db->where("c.clm_val = a.tr_cls");

        $this->db->where("c.addtnl_info = '9'");

        // 2021.05.16. AB 입금일자는 수익 상세조회에서 수정하도록 기능 변경
        //if (strcmp($view_cls, "1") == 0)
        //{
        //    $this->db->where("c.addtnl_info = '2'");
        //}
        //else if (strcmp($view_cls, "2") == 0)
        //{
        //    $this->db->where("c.addtnl_info = '9'");
        //}
        //else
        //{
        //}
        // 2021.05.16. AB 입금일자는 수익 상세조회에서 수정하도록 기능 변경


        $this->db->where("d.db_no   = a.db_no");
        $this->db->where("d.gst_no  = b.gst_no");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by('a.tr_dt, a.tr_cls');

        $query = $this->db->get();
        $result = $query->num_rows();

        //info_log("get_etc_incm_list", "last_query  = [" . $this->db->last_query() . "]");

        if ($result == 0) {
            info_log("get_etc_incm_list", "No Data Found!(stnd_yymm=" . $stnd_yymm . ")");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_etc_incm_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_etc_incm_list", "[SQL ERR] 기타 거래 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_etc_incm_info($tr_srno)
    {
        //,concat(substr(b.rcv_dt, 1, 4), '-', substr(b.rcv_dt, 5, 2), '-', substr(b.rcv_dt, 7, 2))  stnd_rcv_dt

        //google 캘린더의 end-date 는 exclusive(start-dt 는 inclusive) 하므로 end_dt + 1 일자를 리턴
        $this->db->select("a.tr_dt
                          ,concat(substr(a.tr_dt, 1, 4), '-', substr(a.tr_dt, 5, 2), '-', substr(a.tr_dt, 7, 2))  stnd_tr_dt
                          ,b.cncl_dt
                          ,concat(substr(b.cncl_dt, 1, 4), '-', substr(b.cncl_dt, 5, 2), '-', substr(b.cncl_dt, 7, 2))  stnd_cncl_dt
                          ,a.tr_cls
                          ,a.tr_chnl_cls
                          ,a.amt
                          ,a.memo
                          ,a.othr_withdraw_yn
                          ,a.expns_srno
                          ");
        $this->db->from('tba006l00  a');
        $this->db->from('tba005l00  b');
        $this->db->where("a.db_no   = ", $_SESSION['db_no']);
        $this->db->where("a.tr_srno  = ", $tr_srno);
        $this->db->where("b.db_no    = a.db_no");
        $this->db->where("b.rsv_srno = a.rsv_srno");
        $this->db->order_by("a.tr_srno DESC");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_etc_incm_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_etc_incm_info", "[SQL ERR] 기타 거래 조회 오류!");
        }

        return $result;
    }


    public function update_tba006l00_1($arr_data)
    {
        $u_data = array(
                        'rsv_srno'         => $arr_data['rsv_srno']
                       ,'tr_dt'            => $arr_data['tr_dt']
                       ,'tr_cls'           => $arr_data['tr_cls']
                       ,'tr_chnl_cls'      => $arr_data['tr_chnl_cls']
                       ,'amt'              => $arr_data['amt']
                       ,'memo'             => $arr_data['memo']
                       ,'othr_withdraw_yn' => $arr_data['othr_withdraw_yn']
                       ,'expns_srno'       => $arr_data['expns_srno']
                       ,'mnpl_usr_no'      => $_SESSION['usr_no']
                       ,'mnpl_ip'          => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'        => date("YmdHis")
                       );

        $this->db->where('db_no   = ', $_SESSION['db_no']);
        $this->db->where('tr_srno  = ', $arr_data['tr_srno']);

        $result = $this->db->update('tba006l00', $u_data);

        return $result;
    }


    public function update_tba006l00_2($arr_data)
    {
        $u_data = array(
                        'del_yn'       => 'Y'
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

        $this->db->where('db_no   = ', $_SESSION['db_no']);
        $this->db->where('tr_srno  = ', $arr_data['tr_srno']);

        $result = $this->db->update('tba006l00', $u_data);

        return $result;
    }


    public function update_tba006l00_3($arr_data)
    {
        $u_data = array(
                        'tr_dt'            => $arr_data['tr_dt']
                       ,'amt'              => $arr_data['amt']
                       ,'memo'             => $arr_data['memo']
                       ,'othr_withdraw_yn' => $arr_data['othr_withdraw_yn']
                       ,'expns_srno'       => $arr_data['expns_srno']
                       ,'mnpl_usr_no'      => $_SESSION['usr_no']
                       ,'mnpl_ip'          => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'        => date("YmdHis")
                       );

        $this->db->where('db_no   = ', $_SESSION['db_no']);
        $this->db->where('tr_srno  = ', $arr_data['tr_srno']);

        $result = $this->db->update('tba006l00', $u_data);

        return $result;
    }


//    public function delete_tba006l00($arr_data)
//    {
//        $this->db->where('db_no   = ', $_SESSION['db_no'] );
//        $this->db->where('tr_srno  = ', $arr_data['tr_srno'] );
//
//        $result = $this->db->delete('tba006l00');
//
//        if (!$result)
//        {
//            info_log("delete_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
//            alert_log("delete_tba006l00", "SQL ERR!");
//        }
//
//        $prcs_cnt = $this->db->affected_rows();
//
//        if ($prcs_cnt != 1)
//        {
//            info_log("delete_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
//            alert_log("delete_tba006l00", "Update Cnt ERR!");
//        }
//
//        return $result;
//    }


    public function get_expns_cost_cls_smmry($stnd_yymm=null, $prcs_cls=null, $offset=null, $limit=null)
    {
        $t_db_no = '0000000000';

        $sql = "select  a.db_no
                       ,a.cost_cls
                       ,a.clm_val_nm  cost_cls_nm
                       ,case when a.cost_cls is null then concat(?, '/ALL')
                             else a.key_val
                        end  key_val
                       ,a.amt
                  from  (
                         select  a.db_no
                                ,a.cost_cls
                                ,b.clm_val_nm
                                ,concat(?, '/', a.cost_cls)  key_val
                                ,sum(a.amt)                        amt
                           from  tbb001l00  a
                                ,tba003i00  b
                                ,tba003i00  c
                          where  a.db_no = ?
                            and  a.expns_dt like concat(?, '%')
                            and  a.ssamzi_yn = 'N'
                            and  a.del_yn = 'N'
                            and  b.db_no = '0000000000'
                            and  b.clm_nm = 'COST_CLS'
                            and  b.clm_val = a.cost_cls
                            and  c.db_no in ('0000000000', ?)
                            and  c.clm_nm = 'EXPNS_CHNL_CLS'
                            and  c.clm_val = a.expns_chnl_cls
                            and  c.del_yn = 'N'
                            and  c.othr_info != '3'
                          group by  a.cost_cls
                           with rollup
                        )  a
                 order by  ifnull(a.db_no, '0000000000'), a.cost_cls";

        if (isset($limit) && isset($offset)) {
            $sql = $sql . " limit " .  $offset . ", " . $limit;
        }

        $query = $this->db->query($sql, array($stnd_yymm, $stnd_yymm, $_SESSION['db_no'], $stnd_yymm, $_SESSION['db_no']));

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_expns_cost_cls_smmry", "No Data Found!");
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_expns_cost_cls_smmry", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_expns_cost_cls_smmry", "[SQL ERR] 지출 고정/변동비 요약 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_expns_cls_list($prcs_cls=null, $offset=null, $limit=null)
    {
        $this->db->select("case when othr_info is null then clm_val
                                else null
                           end                                                 as clm_val_l
                          ,case when othr_info is null then clm_val_nm
                                else null
                           end                                                 as clm_val_nm_l
                          ,case when othr_info is null then null
                                else clm_val
                           end                                                 as clm_val_s
                          ,case when othr_info is null then null
                                else clm_val_nm
                           end                                                 as clm_val_nm_s
                          ,ifnull(othr_info, clm_val)                          as clm_order
                         ", false);
        $this->db->from('tba003i00');
        $this->db->where('db_no = ', $_SESSION['db_no']);
        $this->db->where("clm_nm  = 'EXPNS_CLS'");
        $this->db->where("del_yn  = 'N'");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by('ifnull(othr_info, clm_val)');
        $this->db->order_by("case when othr_info is null then '1' else '2' end");
        $this->db->order_by('clm_val_nm');

        $query = $this->db->get();

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_expns_itm_list", "No Data Found!");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_expns_itm_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_expns_itm_list", "[SQL ERR] 지출 항목 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_expns_chnl_smmry($stnd_yymm=null, $prcs_cls=null, $offset=null, $limit=null)
    {
        $t_db_no = '0000000000';

        $sql = "select  a.db_no
                       ,a.expns_chnl_cls
                       ,a.expns_chnl_cls_nm
                       ,case when a.db_no is null and expns_chnl_cls is null then concat(?, '/AZ')
                             when a.db_no is not null and expns_chnl_cls is null then concat(?, '/BZ')
                             else a.key_val
                        end  key_val
                       ,case when expns_chnl_cls is null then a.amt - ifnull(a.chnl_othr_3_amt, 0) - ifnull(a.ssamzi_amt, 0)
                             else a.amt
                        end  amt
                  from  (
                         select  b.db_no
                                ,a.expns_chnl_cls
                                ,b.clm_val_nm                      expns_chnl_cls_nm
                                ,concat(?, '/', a.expns_chnl_cls)  key_val
                                ,sum(case when b.othr_info = '3' then a.amt end)  chnl_othr_3_amt
                                ,sum(case when a.ssamzi_yn = 'Y' then a.amt end)  ssamzi_amt
                                ,sum(a.amt)                        amt
                           from  tbb001l00  a
                                ,tba003i00  b
                          where  a.db_no = ?
                            and  a.expns_dt like concat(?, '%')
                            and  a.del_yn = 'N'
                            and  b.db_no in (?, ?)
                            and  b.clm_nm = 'EXPNS_CHNL_CLS'
                            and  b.del_yn = 'N'
                            and  b.clm_val = a.expns_chnl_cls
                          group by  b.db_no, a.expns_chnl_cls
                           with rollup
                        )  a
                 where  (ifnull(a.db_no, '9999999999') != '0000000000'
                         or
                         a.expns_chnl_cls is not null
                        )
                 order by  ifnull(a.db_no, '0000000000'), ifnull(a.expns_chnl_cls, '00')";

        if (isset($limit) && isset($offset)) {
            $sql = $sql . " limit " .  $offset . ", " . $limit;
        }

        $query = $this->db->query($sql, array($stnd_yymm, $stnd_yymm, $stnd_yymm, $_SESSION['db_no'], $stnd_yymm, $_SESSION['db_no'], $t_db_no));

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_expns_chnl_smmry", "No Data Found!");
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_expns_chnl_smmry", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_expns_chnl_smmry", "[SQL ERR] 지출 매체 요약 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_expns_cls_smmry($stnd_yymm=null, $prcs_cls=null, $offset=null, $limit=null)
    {
        $sql = "select  case when a.expns_cls is not null then null
                             when a.othr_info = '00000' then '합 계'
                             else b.clm_val_nm
                        end  upp_cls_nm
                       ,c.clm_val_nm
                       ,concat(?, '/', a.expns_cls)  key_val
                       ,case when a.expns_cls is not null then a.amt
                             when a.othr_info = '00000' then a.amt - ifnull(a.chnl_othr_3_amt, 0) - ifnull(a.ssamzi_amt, 0)
                             else a.amt - ifnull(a.chnl_othr_3_amt, 0)
                        end  amt
                  from  (
                        select  ifnull(b.othr_info, '00000')  othr_info
                               ,a.expns_cls
                               ,sum(case when c.othr_info = '3' then a.amt end)  chnl_othr_3_amt
                               ,sum(case when a.ssamzi_yn = 'Y' then a.amt end)  ssamzi_amt
                               ,sum(a.amt)  amt
                               ,b.clm_nm
                               ,a.db_no
                          from  tbb001l00  a
                               ,tba003i00  b
                               ,tba003i00  c
                         where  a.db_no = ?
                           and  a.expns_dt like concat(?, '%')
                           and  a.del_yn = 'N'
                           and  b.db_no = a.db_no
                           and  b.clm_nm = 'EXPNS_CLS'
                           and  b.del_yn = 'N'
                           and  b.clm_val = a.expns_cls
                           and  c.db_no in ('0000000000', ?)
                           and  c.clm_nm = 'EXPNS_CHNL_CLS'
                           and  c.del_yn = 'N'
                           and  c.clm_val = a.expns_chnl_cls
                         group by  b.othr_info
                                  ,a.expns_cls
                         with rollup
                        )  a left join tba003i00  c on  c.db_no  = a.db_no
                                                   and  c.clm_nm  = a.clm_nm
                                                   and  c.clm_val = a.expns_cls
                             left join tba003i00  b on  b.db_no  = a.db_no
                                                   and  b.clm_nm  = a.clm_nm
                                                   and  b.clm_val = a.othr_info
                order by  concat(case when a.expns_cls is null then concat(a.othr_info, '1')
                                      else concat(a.othr_info, '2')
                                 end), a.amt desc";

        if (isset($limit) && isset($offset)) {
            $sql = $sql . " limit " .  $offset . ", " . $limit;
        }

        $query = $this->db->query($sql, array($stnd_yymm, $_SESSION['db_no'], $stnd_yymm, $_SESSION['db_no']));

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_expns_cls_smmry", "No Data Found!");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_expns_cls_smmry", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_expns_cls_smmry", "[SQL ERR] 지출 항목 요약 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function insert_tbb001l00($arr_data)
    {
        $i_data = array('db_no'         => $_SESSION['db_no']
                       ,'expns_srno'     => $arr_data['expns_srno']
                       ,'expns_dt'       => $arr_data['expns_dt']
                       ,'expns_chnl_cls' => $arr_data['expns_chnl_cls']
                       ,'expns_cls'      => $arr_data['expns_cls']
                       ,'memo'           => $arr_data['memo']
                       ,'whr_to_buy'     => $arr_data['whr_to_buy']
                       ,'ssamzi_yn'      => $arr_data['ssamzi_yn']
                       ,'cost_cls'       => $arr_data['cost_cls']
                       ,'amt'            => $arr_data['amt']
                       ,'del_yn'         => 'N'
                       ,'mnpl_usr_no'    => $_SESSION['usr_no']
                       ,'mnpl_ip'        => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'      => date("YmdHis")
                       );

        $result = $this->db->insert('tbb001l00', $i_data);

        return $result;
    }


    public function get_expns_dtl_list($stnd_yymm, $cost_cls=null, $chnl_cls=null, $cls=null, $prcs_cls=null, $offset=null, $limit=null)
    {
        if (strncmp($chnl_cls, "AZ", 2) == 0) {
            unset($chnl_cls);
            $t_db_no = array($_SESSION['db_no'], '0000000000');
        }
        if (strncmp($chnl_cls, "BZ", 2) == 0) {
            unset($chnl_cls);
            $t_db_no = array($_SESSION['db_no']);
        } else {
            $t_db_no = array($_SESSION['db_no'], '0000000000');
        }

        $this->db->select("concat(substr(a.expns_dt, 1, 4), '-', substr(a.expns_dt, 5, 2), '-', substr(a.expns_dt, 7, 2))  stnd_expns_dt
                          ,a.expns_chnl_cls
                          ,b.clm_val_nm                               expns_chnl_cls_nm
                          ,a.expns_cls
                          ,c.clm_val_nm  expns_cls_nm
                          ,case when char_length(a.memo) <= 7 then a.memo
                                when char_length(a.memo) >  7 then concat(substr(a.memo, 1, 6), '...')
                           end  memo
                          ,memo  memo_whole
                          ,case when char_length(a.whr_to_buy) <= 5 then a.whr_to_buy
                                when char_length(a.whr_to_buy) >  5 then concat(substr(a.whr_to_buy, 1, 4), '...')
                           end  whr_to_buy
                          ,a.amt
                          ,a.expns_srno
                          ");
        $this->db->from('tbb001l00  a');
        $this->db->from('tba003i00  b');
        $this->db->from('tba003i00  c');
        $this->db->where("a.db_no = ", $_SESSION['db_no']);
        $this->db->like('a.expns_dt', $stnd_yymm, 'after');
        $this->db->like("ifnull(a.cost_cls, 'Z')", $cost_cls, 'after');
        $this->db->like('a.expns_chnl_cls', $chnl_cls, 'after');
        $this->db->like('a.expns_cls', $cls, 'after');
        $this->db->where("a.del_yn = 'N'");
        $this->db->where_in('b.db_no', $t_db_no);
        $this->db->where("b.clm_nm = 'EXPNS_CHNL_CLS'");
        $this->db->where("b.clm_val = a.expns_chnl_cls");
        $this->db->where("c.db_no = a.db_no");
        $this->db->where("c.clm_nm = 'EXPNS_CLS'");
        $this->db->where("c.clm_val = a.expns_cls");
        $this->db->order_by("a.expns_dt, a.expns_srno");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();   // Produces: SELECT title, content, date FROM mytable
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_expns_dtl_list", "No Data Found!");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_expns_dtl_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_expns_dtl_list", "[SQL ERR] 지출 상세 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_expns_info($expns_srno)
    {
        $this->db->select("a.expns_dt
                          ,concat(substr(a.expns_dt, 1, 4), '-', substr(a.expns_dt, 5, 2), '-', substr(a.expns_dt, 7, 2))  stnd_expns_dt
                          ,a.expns_chnl_cls
                          ,a.expns_cls
                          ,a.memo
                          ,a.whr_to_buy
                          ,a.ssamzi_yn
                          ,a.cost_cls
                          ,a.amt
                          ");
        $this->db->from('tbb001l00  a');
        $this->db->where("a.db_no   = ", $_SESSION['db_no']);
        $this->db->where("a.expns_srno  = ", $expns_srno);
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_expns_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_expns_info", "[SQL ERR] 지출 정보 조회 오류!");
        }

        return $result;
    }


    public function update_tbb001l00_1($arr_data)
    {
        $u_data = array(
                        'expns_dt'       => $arr_data['expns_dt']
                       ,'expns_chnl_cls' => $arr_data['expns_chnl_cls']
                       ,'expns_cls'      => $arr_data['expns_cls']
                       ,'memo'           => $arr_data['memo']
                       ,'whr_to_buy'     => $arr_data['whr_to_buy']
                       ,'ssamzi_yn'      => $arr_data['ssamzi_yn']
                       ,'cost_cls'       => $arr_data['cost_cls']
                       ,'amt'            => $arr_data['amt']
                       ,'mnpl_usr_no'    => $_SESSION['usr_no']
                       ,'mnpl_ip'        => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'      => date("YmdHis")
                       );

        $this->db->where('db_no      = ', $_SESSION['db_no']);
        $this->db->where('expns_srno  = ', $arr_data['expns_srno']);

        $result = $this->db->update('tbb001l00', $u_data);

        return $result;
    }


    public function update_tbb001l00_2($arr_data)
    {
        $u_data = array(
                        'del_yn'         => 'Y'
                       ,'mnpl_usr_no'    => $_SESSION['usr_no']
                       ,'mnpl_ip'        => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'      => date("YmdHis")
                       );

        $this->db->where('db_no      = ', $_SESSION['db_no']);
        $this->db->where('expns_srno  = ', $arr_data['expns_srno']);

        $result = $this->db->update('tbb001l00', $u_data);

        return $result;
    }


    public function get_rsvt_cncl_list($stnd_yymm=null, $hsrm_cls=null, $prcs_cls=null, $offset=null, $limit=null)
    {
        if (strncmp($hsrm_cls, 'all', 3) == 0) {
            $hsrm_cls = '';
        }

        $sql = "select  a.rsv_srno
                       ,a.tr_srno
                       ,a.hsrm_cls
                       ,a.hsrm_cls_nm
                       ,a.cncl_dt
                       ,a.srt_dt
                       ,a.gst_nm
                       ,a.rsv_chnl_cls
                       ,a.rsv_chnl_cls_nm
                       ,a.refund_amt
                  from  (
                         select  a.rsv_srno
                                ,d.tr_srno
                                ,a.hsrm_cls
                                ,b.clm_val_nm hsrm_cls_nm
                                ,concat(substr(a.cncl_dt, 1, 4), '-', substr(a.cncl_dt, 5, 2), '-', substr(a.cncl_dt, 7, 2))  cncl_dt
                                ,concat(substr(a.srt_dt, 1, 4), '-', substr(a.srt_dt, 5, 2), '-', substr(a.srt_dt, 7, 2))  srt_dt
                                ,e.gst_nm
                                ,a.rsv_chnl_cls
                                ,c.clm_val_nm rsv_chnl_cls_nm
                                ,sum(case when d.tr_cls = '03' then d.amt else 0 end)    refund_amt
                           from  tba005l00 a, tba003i00 b, tba003i00 c, tba006l00 d, tba007l00 e
                          where  a.db_no = ?
                            and  a.cncl_dt like concat(?, '%')
                            and  a.hsrm_cls like concat(?, '%')
                            and  a.cncl_yn = 'Y'
                            and  b.db_no = a.db_no
                            and  b.clm_nm = 'hsrm_cls'
                            and  b.clm_val = a.hsrm_cls
                            and  c.db_no = '0000000000'
                            and  c.clm_nm = 'rsv_chnl_cls'
                            and  c.clm_val = a.rsv_chnl_cls
                            and  d.db_no = a.db_no
                            and  d.rsv_srno = a.rsv_srno
                            and  d.del_yn = 'N'
                            and  d.tr_cls in ('03', '04')
                            and  e.db_no = a.db_no
                            and  e.gst_no = a.gst_no
                          group by  a.rsv_srno
                                   ,d.tr_srno
                                   ,a.hsrm_cls
                                   ,b.clm_val_nm
                                   ,concat(substr(a.cncl_dt, 1, 4), '-', substr(a.cncl_dt, 5, 2), '-', substr(a.cncl_dt, 7, 2))
                                   ,concat(substr(a.srt_dt, 1, 4), '-', substr(a.srt_dt, 5, 2), '-', substr(a.srt_dt, 7, 2))
                                   ,e.gst_nm
                                   ,a.rsv_chnl_cls
                                   ,c.clm_val_nm
                           with rollup
                        )  a
                 where  a.rsv_chnl_cls_nm is not null or a.rsv_srno is null
                 order by  case when a.rsv_srno is null then '99991231' else a.cncl_dt end
                          ,a.rsv_chnl_cls
                          ,a.hsrm_cls
                          ,a.tr_srno
                limit ?, ?";


        $query = $this->db->query($sql, array($_SESSION['db_no']
                                              ,$stnd_yymm
                                              ,$hsrm_cls
                                              ,$offset
                                              ,$limit));

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_rsvt_cncl_list", "No Data Found!(hsrm_cls=[" . $hsrm_cls . "], stnd_yymm=[" . $stnd_yymm . "])");
            return;
        } else {
            if ($prcs_cls == 'data') {
                //info_log("get_incm_smmry", "last_query  = [" . $this->db->last_query() . "]");
                  $result = $query->result();  // 객체 $result->board_id

                  if (!$result) {
                      info_log("get_rsvt_cncl_list", "last_query  = [" . $this->db->last_query() . "]");
                      alert_log("get_rsvt_cncl_list", "[SQL ERR] 예약 취소 리스트 조회 오류!");
                  }
            }
        }

        return $result;
    }


    public function get_rsv_term($hsrm_cls, $srt_dt, $end_dt, $extend_yn)
    {

        // 2020.07.18. 예약 후 일정 변경 문의시에도 가격 조회가 가능하도록 기능 추가
        if (strncmp($extend_yn, "Y", 1) != 0) {
            // 중복 등록 여부 확인
            $sql = "select  count(*)  cnt
                      from  tba004l00  a
                           ,(
                             select  srt_dt
                                    ,end_dt
                               from  tba005l00
                              where  db_no = ?
                                and  hsrm_cls = ?
                                and  srt_dt between date_format(adddate(?, -30), '%Y%m%d') and date_format(adddate(?, 30), '%Y%m%d')
                                and  cncl_yn = 'N'
                            )  b
                    where  a.dt between b.srt_dt and b.end_dt
                      and  a.dt between ? and ?";

            $query = $this->db->query($sql, array($_SESSION['db_no'], $hsrm_cls, $srt_dt, $end_dt, $srt_dt, $end_dt));

            //$result = $query->result();  // 객체 $result->board_id
            //$result = $query->result_array();  //배열 $result['board_id']
            $result = $query->row();  // 단건, 객체 $result->board_id

            if (!$result) {
                info_log("get_rsv_term/dup_chk", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_rsv_term/dup_chk", "[SQL ERR] 예약 중복 여부 체크 오류!");
            } else {
                info_log("get_rsv_term/dup_chk", "hsrm_cls = " . $hsrm_cls . " ,srt_dt = " . $srt_dt . " , end_dt = " . $end_dt);
                info_log("get_rsv_term/dup_chk", "result_cnt = " . $result->cnt);
                //info_log("insert_tba005l00/dup_chk", "query_cnt = " . $query->num_rows());

                if ($result->cnt > 0) {
                    alert_log("insert_tba005l00/dup_chk", "예약기간에 이미 등록된 예약이 존재합니다!", "/rsvt/prc");
                }
            }
        }



        //dayofweek
        //1. 일요일, 2. 월요일... 7.토요일
        $sql = "select  (@rownum := @rownum + 1) rnum
                       ,a.dt
                       ,date_format(ADDDATE(a.dt, 1), '%Y%m%d')  add_dt_1
                       ,case when DAYOFWEEK(a.dt) = 1 then '일'
                             when DAYOFWEEK(a.dt) = 2 then '월'
                             when DAYOFWEEK(a.dt) = 3 then '화'
                             when DAYOFWEEK(a.dt) = 4 then '수'
                             when DAYOFWEEK(a.dt) = 5 then '목'
                             when DAYOFWEEK(a.dt) = 6 then '금'
                             when DAYOFWEEK(a.dt) = 7 then '토'
                        end  dayofweek
                  from  tba004l00  a
                       ,(select @rownum := 0)  z
                 where  a.dt between ? and ?
                 order by  a.dt";

        $query = $this->db->query($sql, array($srt_dt, $end_dt));

        $result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        //$result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_rsv_term", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_rsv_term", "[SQL ERR] 예약 기간 조회 오류!");
        }

        return $result;
    }


    public function get_dt_prc($stnd_dt, $hsrm_cls)
    {

        //info_log("get_dt_prc", "stnd_dt  = [" . $stnd_dt . "]");

        // 성수기
        //case when a.season_cls = '1' then 22
        // 준성수기
        //     when a.season_cls = '2' then 19
        // 일반 주말(금, 토)
        //     when a.season_cls = '3' and dayofweek(a.dt) in ('6', '7')       then 19
        // 일반 공휴일 전일 또는 공휴일
        //     when a.season_cls = '3' and (b.bef_dt is not null or a.dt_cls = '3') then 19
        // 일반 평일
        //     when a.season_cls = '3' and dayofweek(a.dt) not in ('6', '7')   then 17
        // 비수기 주말(금, 토)
        //     when a.season_cls = '4' and dayofweek(a.dt) in ('6', '7')       then 17
        // 비수기 공휴일 전일 또는 공휴일
        //     when a.season_cls = '4' and (b.bef_dt is not null or a.dt_cls = '3') then 17
        // 비수기 평일
        //     when a.season_cls = '4' and dayofweek(a.dt) not in ('6', '7')   then 15
        //end  prc

        //2019.08.16  가격조회 로직 수정
        //            기존 일자정보에서 시즌정보를 직접 입력/조회하던 방식에서 별도의 시즌정보를 가지는 것으로 변경
        //$sql = "select  a.dt
        //               ,a.season_cls
        //               ,a.dt_cls
        //               ,b.bef_dt
        //               ,case when a.season_cls = '1' then '성수기'
        //                     when a.season_cls = '2' then '준성수기'
        //                     when a.season_cls = '3' and dayofweek(a.dt) in ('6', '7')            then '평수기 주말'
        //                     when a.season_cls = '3' and (b.bef_dt is not null or a.dt_cls = '3') then '평수기 공휴일/공휴일 전일'
        //                     when a.season_cls = '3' and dayofweek(a.dt) not in ('6', '7')        then '평수기 평일'
        //                     when a.season_cls = '4' and dayofweek(a.dt) in ('6', '7')            then '비수기 주말'
        //                     when a.season_cls = '4' and (b.bef_dt is not null or a.dt_cls = '3') then '비수기 공휴일/공휴일 전일'
        //                     when a.season_cls = '4' and dayofweek(a.dt) not in ('6', '7')        then '비수기 평일'
        //                end  prc_cls
        //               ,case when a.season_cls = '1' then '성수기'
        //                     when a.season_cls = '2' then '준성수기'
        //                     when a.season_cls = '3' and dayofweek(a.dt) in ('6', '7')            then '주말'
        //                     when a.season_cls = '3' and (b.bef_dt is not null or a.dt_cls = '3') then '주말'
        //                     when a.season_cls = '3' and dayofweek(a.dt) not in ('6', '7')        then '평일'
        //                     when a.season_cls = '4' and dayofweek(a.dt) in ('6', '7')            then '주말'
        //                     when a.season_cls = '4' and (b.bef_dt is not null or a.dt_cls = '3') then '주말'
        //                     when a.season_cls = '4' and dayofweek(a.dt) not in ('6', '7')        then '평일'
        //                end  prc_cls_desc
        //               ,case when a.season_cls = '1' then 22
        //                     when a.season_cls = '2' then 19
        //                     when a.season_cls = '3' and dayofweek(a.dt) in ('6', '7')       then 19
        //                     when a.season_cls = '3' and (b.bef_dt is not null or a.dt_cls = '3') then 19
        //                     when a.season_cls = '3' and dayofweek(a.dt) not in ('6', '7')   then 17
        //                     when a.season_cls = '4' and dayofweek(a.dt) in ('6', '7')       then 17
        //                     when a.season_cls = '4' and (b.bef_dt is not null or a.dt_cls = '3') then 17
        //                     when a.season_cls = '4' and dayofweek(a.dt) not in ('6', '7')   then 15
        //                end  prc
        //          from  tba004l00  a  left outer join (
        //                                               select  date_format(adddate(z.dt, -1), '%Y%m%d')  bef_dt
        //                                                 from  tba004l00  z
        //                                                where  z.dt_cls = '3'
        //                                              )  b  on b.bef_dt = a.dt
        //         where  a.dt = ?";
        //
        //$query = $this->db->query($sql, array($stnd_dt));


        $sql = "select  a.dt
                       ,a.season_cls
                       ,a.dt_cls
                       ,a.holyday_bef_dt_yn
                       ,case when a.season_cls = '1' then '성수기'
                             when a.season_cls = '2' then '준성수기'
                             when a.season_cls = '3' and dayofweek(a.dt) in ('6', '7')                 then '평수기 주말'
                             when a.season_cls = '3' and (a.holyday_bef_dt_yn = 'Y' or a.dt_cls = '3') then '평수기 공휴일 전일/공휴일'
                             when a.season_cls = '3' and dayofweek(a.dt) not in ('6', '7')             then '평수기 평일'
                             when a.season_cls = '4' and dayofweek(a.dt) in ('6', '7')                 then '비수기 주말'
                             when a.season_cls = '4' and (a.holyday_bef_dt_yn = 'Y' or a.dt_cls = '3') then '비수기 공휴일 전일/공휴일'
                             when a.season_cls = '4' and dayofweek(a.dt) not in ('6', '7')             then '비수기 평일'
                        end  prc_cls
                       ,case when a.season_cls = '1' then '성수기'
                             when a.season_cls = '2' then '준성수기'
                             when a.season_cls = '3' and dayofweek(a.dt) in ('6', '7')                 then '주말'
                             when a.season_cls = '3' and (a.holyday_bef_dt_yn = 'Y' or a.dt_cls = '3') then '주말'
                             when a.season_cls = '3' and dayofweek(a.dt) not in ('6', '7')             then '평일'
                             when a.season_cls = '4' and dayofweek(a.dt) in ('6', '7')                 then '주말'
                             when a.season_cls = '4' and (a.holyday_bef_dt_yn = 'Y' or a.dt_cls = '3') then '주말'
                             when a.season_cls = '4' and dayofweek(a.dt) not in ('6', '7')             then '평일'
                        end  prc_cls_desc
                       ,case when a.season_cls = '1'                                                   then b.season_fee + c.hsrm_fee
                             when a.season_cls = '2'                                                   then b.season_fee + c.hsrm_fee
                             when a.season_cls = '3' and dayofweek(a.dt) in ('6', '7')                 then b.season_fee + c.hsrm_fee + b.holyday_fee
                             when a.season_cls = '3' and (a.holyday_bef_dt_yn = 'Y' or a.dt_cls = '3') then b.season_fee + c.hsrm_fee + b.holyday_fee
                             when a.season_cls = '3' and dayofweek(a.dt) not in ('6', '7')             then b.season_fee + c.hsrm_fee
                             when a.season_cls = '4' and dayofweek(a.dt) in ('6', '7')                 then b.season_fee + c.hsrm_fee + b.holyday_fee
                             when a.season_cls = '4' and (a.holyday_bef_dt_yn = 'Y' or a.dt_cls = '3') then b.season_fee + c.hsrm_fee + b.holyday_fee
                             when a.season_cls = '4' and dayofweek(a.dt) not in ('6', '7')             then b.season_fee + c.hsrm_fee
                        end  prc
                       ,b.cntnu_dscnt
                  from  (
                         select  a.dt
                                ,a.dt_cls
                                ,ifnull(b.season_cls, case when substr(a.dt, 5, 2) between '01' and '03' then '4'
                                                           when substr(a.dt, 5, 2) between '04' and '10' then '3'
                                                           when substr(a.dt, 5, 2) between '11' and '12' then '4'
                                                      end)  season_cls
                                ,case when c.holyday_bef_dt is null then  'N' else 'Y' end  holyday_bef_dt_yn
                           from  tba004l00  a  left outer join (
                                                                select  ?              dt
                                                                        ,z.season_cls
                                                                  from  tba008l00  z
                                                                 where  z.db_no = ?
                                                                   and  ? between z.srt_dt and z.end_dt
                                                               )  b  on b.dt = a.dt
                                               left outer join (
                                                                select  z.dt                                      holyday
                                                                       ,date_format(adddate(z.dt, -1), '%Y%m%d')  holyday_bef_dt
                                                                  from  tba004l00  z
                                                                 where  z.dt_cls = '3'
                                                               )  c  on c.holyday_bef_dt = a.dt
                          where  a.dt = ?
                        )  a
                       ,(
                         select  clm_val                  season_cls
                                ,ifnull(addtnl_info,  0)  season_fee
                                ,ifnull(addtnl_info2, 0)  holyday_fee
                                ,ifnull(addtnl_info3, 0)  cntnu_dscnt
                           from  tba003i00
                          where  db_no = ?
                            and  clm_nm = 'SEASON_CLS'
                        )  b
                       ,(
                          select  clm_val                 hsrm_cls
                                 ,ifnull(addtnl_info, 0)  hsrm_fee
                            from  tba003i00
                           where  db_no = ?
                             and  clm_nm = 'HSRM_CLS'
                             and  clm_val = ?
                        )  c
                 where  b.season_cls = a.season_cls";

        $query = $this->db->query($sql, array($stnd_dt, $_SESSION['db_no'], $stnd_dt, $stnd_dt, $_SESSION['db_no'], $_SESSION['db_no'], $hsrm_cls));

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_dt_prc", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_dt_prc", "[SQL ERR] 가격 조회 오류!");
        }

        return $result;
    }


    public function get_gst_list($gst_nm=null, $prcs_cls=null, $offset=null, $limit=null)
    {
        $this->db->select("a.gst_no
                          ,a.gst_nm
                          ,concat(substr(a.phone_num, 1, 3), '-', substr(a.phone_num, 4, 4), '-', substr(a.phone_num, 8, 4))  phone_num
                          ");
        $this->db->from('tba007l00  a');
        $this->db->where('a.db_no = ', $_SESSION['db_no']);
        $this->db->like('a.gst_nm', $gst_nm);
        $this->db->where("a.del_yn  = 'N'");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by('a.gst_nm');

        $query = $this->db->get();
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_gst_list", "No Data Found!(gst_nm=" . $gst_nm . ")");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_gst_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_gst_list", "[SQL ERR] 고객 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function insert_tba007l00($arr_data)
    {
        $i_data = array('db_no'       => $_SESSION['db_no']
                       ,'gst_no'      => $arr_data['gst_no']
                       ,'gst_nm'      => $arr_data['gst_nm']
                       ,'phone_num'   => $arr_data['phone_num']
                       ,'memo'        => $arr_data['memo']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $result = $this->db->insert('tba007l00', $i_data);

        return $result;
    }


    public function update_tba007l00_1($arr_data)
    {
        //print_r($u_data);
        //exit;
        $u_data = array('gst_nm'      => $arr_data['gst_nm']
                       ,'phone_num'   => $arr_data['phone_num']
                       ,'memo'        => $arr_data['memo']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $this->db->where('db_no   = ', $_SESSION['db_no']);
        $this->db->where('gst_no  = ', $arr_data['gst_no']);

        $result = $this->db->update('tba007l00', $u_data);

        return $result;
    }


    public function update_tba007l00_2($arr_data)
    {
        $u_data = array('del_yn'       => 'Y'
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $this->db->where('db_no   = ', $_SESSION['db_no']);
        $this->db->where('gst_no  = ', $arr_data['gst_no']);

        $result = $this->db->update('tba007l00', $u_data);

        return $result;
    }


    public function get_gst_info($gst_no)
    {
        //2018.10.19. 010 prefix 로 고정 처리
        //,a.phone_num >> ,substr(a.phone_num, 4, 8)  phone_num

        $this->db->select("a.gst_nm
                          ,substr(a.phone_num, 4, 8)  phone_num
                          ,concat(substr(a.phone_num, 1, 3), '-', substr(a.phone_num, 4, 4), '-', substr(a.phone_num, 8, 4))  phone_num_fm
                          ,a.memo
                          ");
        $this->db->from('tba007l00  a');
        $this->db->where('a.db_no  = ', $_SESSION['db_no']);
        $this->db->where('a.gst_no = ', $gst_no);

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            $result_rows = $query->num_rows();
            if ($result_rows == 0) {
                alert_log("get_gst_info", "존재하지않는 고객번호 입니다!(" . $gst_no . ")");
            } else {
                info_log("get_gst_info", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_gst_info", "[SQL ERR] 고객정보 조회 오류!");
            }
        }

        return $result;
    }


    public function get_gst_dup_chk($gst_nm, $phone_num)
    {
        $this->db->select('count(*) cnt
                          ');
        $this->db->from('tba007l00  a');
        $this->db->where('a.db_no  = ', $_SESSION['db_no']);
        $this->db->where('a.gst_nm = ', $gst_nm);
        //$this->db->where('a.phone_num  = ', $phone_num);
        $this->db->where("a.del_yn  = 'N'");

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            $result_rows = $query->num_rows();
            if ($result_rows == 0) {
                alert_log("get_gst_dup_chk", "존재하지 않는 고객 입니다!(" . $gst_nm . ")");
            } else {
                info_log("get_gst_dup_chk", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_gst_dup_chk", "[SQL ERR] 고객정보 중복 조회 오류!");
            }
        }

        return $result;
    }


    public function get_gst_dtl_list($gst_no=null, $prcs_cls=null, $offset=null, $limit=null)
    {
        $this->db->select("c.clm_val_nm  hsrm_cls_nm
                          ,concat(substr(b.srt_dt, 1, 4), '-', substr(b.srt_dt, 5, 2), '-', substr(b.srt_dt, 7, 2))  srt_dt
                          ,concat(substr(b.end_dt, 1, 4), '-', substr(b.end_dt, 5, 2), '-', substr(b.end_dt, 7, 2))  end_dt
                          ,b.cncl_yn
                          ,ifnull(b.gst_desc, concat('성인:', b.adlt_cnt, ' 자녀:', b.chld_cnt)) gst_desc
                          ,b.rsv_srno
                          ");
        $this->db->from('tba007l00  a');
        $this->db->from('tba005l00  b');
        $this->db->from('tba003i00  c');
        $this->db->where('a.db_no = ', $_SESSION['db_no']);
        $this->db->where('a.gst_no', $gst_no);
        $this->db->where("a.del_yn  = 'N'");
        $this->db->where('b.db_no  = a.db_no');
        $this->db->where('b.gst_no = a.gst_no');
        $this->db->where("c.db_no   = b.db_no");
        $this->db->where("c.clm_nm  = 'HSRM_CLS'");
        $this->db->where("c.clm_val = b.hsrm_cls");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by('b.srt_dt, b.hsrm_cls');

        $query = $this->db->get();
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_gst_dtl_list", "No Data Found!(gst_no=" . $gst_no . ")");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_gst_dtl_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_gst_dtl_list", "[SQL ERR] 고객 상세 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_gst_rsvt_list($gst_no)
    {
        $this->db->select('rsv_srno
                          ,hsrm_cls
                          ,evnt_id
                          ');
        $this->db->from('tba005l00  a');
        $this->db->where('a.db_no  = ', $_SESSION['db_no']);
        $this->db->where('a.gst_no = ', $gst_no);
        $this->db->where("a.cncl_yn  = 'N'");

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        $result = $query->result();  // 객체 $result->board_id
        //$result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            $result_rows = $query->num_rows();
            if ($result_rows == 0) {
                info_log("get_gst_rsvt_list", "예약건 미존재 고객!(" . $gst_no . ")");
            } else {
                info_log("get_gst_rsvt_list", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_gst_rsvt_list", "[SQL ERR] 고객정보 중복 조회 오류!");
            }
        }

        return $result;
    }


    public function insert_update_tbb002l00($arr_data)
    {
        //$i_data = array('db_no'       => $_SESSION['db_no']
        //               ,'tr_yymm'     => $arr_data['tr_yymm']
        //               ,'io_tr_cls'   => $arr_data['io_tr_cls']
        //               ,'tr_dt'       => $arr_data['tr_dt']
        //               ,'amt'         => $arr_data['amt']
        //               ,'mnpl_usr_no' => $_SESSION['usr_no']
        //               ,'mnpl_ip'     => $_SESSION['ip_addr']
        //               ,'mnpl_ymdh'   => date("YmdHis")
        //               );
        //
        //$result = $this->db->insert('tbb002l00', $i_data);
        //
        //return $result;


        $sql = "insert into tbb002l00 values (?
                                             ,?
                                             ,?
                                             ,?
                                             ,?
                                             ,?
                                             ,?
                                             ,?
                                             ,?
                                             ) on duplicate key update tr_dt = ?, memo = ?, amt = ?, mnpl_usr_no = ?, mnpl_ip = ?, mnpl_ymdh = ?";

        $query = $this->db->query($sql, array($_SESSION['db_no']
                                             ,$arr_data['tr_yymm']
                                             ,$arr_data['io_tr_cls']
                                             ,$arr_data['tr_dt']
                                             ,$arr_data['memo']
                                             ,$arr_data['amt']
                                             ,$_SESSION['usr_no']
                                             ,$_SESSION['ip_addr']
                                             ,date("YmdHis")
                                             ,$arr_data['tr_dt']
                                             ,$arr_data['memo']
                                             ,$arr_data['amt']
                                             ,$_SESSION['usr_no']
                                             ,$_SESSION['ip_addr']
                                             ,date("YmdHis")));

        //$result = $query->row();  // 단건, 객체 $result->board_id

        $result = $query;

        if (!$result) {
            info_log("insert_update_tbb002l00", "입출금거래 입력 오류!");
            info_log("insert_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
            return $result;
        }

        return 1;
    }


    // 2020.03.14 잔고 입력/수정 로직 주석 처리
    // 잔고 관리 없이 차후에는 수입/지출만 관리하도록 변경
    //2020.09.06. 미사용 삭제(bal_update_tbb003l00 으로 변경)
    //public function bal_update_tbb002l00($arr_data)
    //{
    //
    //    $sql = "select  (a.amt - b.amt + c.amt)  bal    -- 전월 잔액 - 당월 현금지출 + 당월 지출
    //                   ,b.amt                    cash_expns_amt
    //                   ,d.amt                    cur_bal
    //                   ,e.amt                    cur_cash_expns_amt
    //                   ,date_format(last_day(concat(?, '01')), '%Y%m%d')  last_dt
    //              from  (
    //                     select  ifnull(sum(amt), 0)  amt
    //                       from  tbb002l00
    //                      where  db_no = ?
    //                        and  tr_yymm = date_format(adddate(last_day(concat(?, '01')), INTERVAL -1 month), '%Y%m')
    //                        and  io_tr_cls = '000'
    //                    )  a    -- 전월잔고
    //                   ,(
    //                     select  ifnull(sum(x.amt), 0)  amt
    //                       from  (
    //                              select  amt
    //                                from  tbb001l00
    //                               where  db_no = ?
    //                                 and  expns_dt like concat(?, '%')
    //                                 and  del_yn = 'N'
    //                                 and  expns_chnl_cls = '01'  -- 현금
    //                                 and  ssamzi_yn = 'N'
    //                                 and  expns_dt >= '20181201'
    //                               union all
    //                              select  amt
    //                                from  tbb002l00
    //                               where  db_no = ?
    //                                 and  tr_yymm = ?
    //                                 and  io_tr_cls = '201'
    //                                 and  tr_yymm < '201812'
    //                             )  x
    //                    )  b
    //                   ,(
    //                     select  ifnull(sum(case when io_tr_cls like '1%' then 1
    //                                             when io_tr_cls like '2%' then -1
    //                                        end * amt
    //                                        ), 0)  amt
    //                       from  tbb002l00
    //                      where  db_no = ?
    //                        and  tr_yymm = ?
    //                        and  io_tr_cls not in ('000', '201')  -- 잔고, 현금지출 제외
    //                    )  c
    //                   ,(
    //                     select  ifnull(sum(amt), 0)  amt
    //                       from  tbb002l00
    //                      where  db_no = ?
    //                        and  tr_yymm = ?
    //                        and  io_tr_cls = '000'
    //                    )  d    -- 당월 잔고
    //                   ,(
    //                     select  ifnull(sum(amt), 0)  amt
    //                       from  tbb002l00
    //                      where  db_no = ?
    //                        and  tr_yymm = ?
    //                        and  io_tr_cls = '201'
    //                    )  e";
    //
    //    $query = $this->db->query($sql, array($arr_data['tr_yymm']
    //                                         ,$_SESSION['db_no']
    //                                         ,$arr_data['tr_yymm']
    //                                         ,$_SESSION['db_no']
    //                                         ,$arr_data['tr_yymm']
    //                                         ,$_SESSION['db_no']
    //                                         ,$arr_data['tr_yymm']
    //                                         ,$_SESSION['db_no']
    //                                         ,$arr_data['tr_yymm']
    //                                         ,$_SESSION['db_no']
    //                                         ,$arr_data['tr_yymm']
    //                                         ,$_SESSION['db_no']
    //                                         ,$arr_data['tr_yymm']
    //                                         ));
    //
    //    //$result = $query->result();  // 객체 $result->board_id
    //    $result = $query->row();  // 단건, 객체 $result->board_id
    //
    //    if (!$result)
    //    {
    //        $result_rows = $query->num_rows();
    //        if ($result_rows == 0)
    //        {
    //            info_log("bal_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //            alert_log("bal_update_tbb002l00", "잔고 조회 데이터 없음!(" . $arr_data['tr_yymm'] . ")");
    //        }
    //        else
    //        {
    //            info_log("bal_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //            alert_log("bal_update_tbb002l00", "[SQL ERR] 잔고 조회 오류!");
    //        }
    //    }
    //
    //    //info_log("bal_update_tbb002l00", "result->bal                = [" . $result->bal                . "]");
    //    //info_log("bal_update_tbb002l00", "result->cur_bal            = [" . $result->cur_bal            . "]");
    //    //info_log("bal_update_tbb002l00", "result->cash_expns_amt     = [" . $result->cash_expns_amt     . "]");
    //    //info_log("bal_update_tbb002l00", "result->cur_cash_expns_amt = [" . $result->cur_cash_expns_amt . "]");
    //    //info_log("bal_update_tbb002l00", "result->last_dt            = [" . $result->last_dt            . "]");
    //
    //    $bal                = $result->bal               ;
    //    $cur_bal            = $result->cur_bal           ;
    //    $cash_expns_amt     = $result->cash_expns_amt    ;
    //    $cur_cash_expns_amt = $result->cur_cash_expns_amt;
    //    $last_dt            = $result->last_dt           ;
    //
    //    info_log("bal_update_tbb002l00", "arr_data['tr_yymm'] = [" . $arr_data['tr_yymm']        . "]");
    //    info_log("bal_update_tbb002l00", "bal                 = [" . $bal                . "]");
    //    info_log("bal_update_tbb002l00", "cur_bal             = [" . $cur_bal            . "]");
    //    info_log("bal_update_tbb002l00", "cash_expns_amt      = [" . $cash_expns_amt     . "]");
    //    info_log("bal_update_tbb002l00", "cur_cash_expns_amt  = [" . $cur_cash_expns_amt . "]");
    //    info_log("bal_update_tbb002l00", "last_dt             = [" . $last_dt            . "]");
    //
    //
    //    if (strncmp($arr_data['tr_yymm'], "201812", 6) >= 0 && $cash_expns_amt != $cur_cash_expns_amt)
    //    {
    //        info_log("bal_update_tbb002l00", "현금지출 입력/수정!");
    //
    //        // 현금지출 입력
    //        $sql = "insert into tbb002l00 values (?
    //                                             ,?
    //                                             ,?
    //                                             ,?
    //                                             ,?
    //                                             ,?
    //                                             ,?
    //                                             ,?
    //                                             ,?
    //                                             ) on duplicate key update amt = ?, mnpl_usr_no = ?, mnpl_ip = ?, mnpl_ymdh = ?";
    //
    //        $query = $this->db->query($sql, array($_SESSION['db_no']
    //                                             ,$arr_data['tr_yymm']
    //                                             ,"201"
    //                                             ,$last_dt
    //                                             ,NULL
    //                                             ,$cash_expns_amt
    //                                             ,$_SESSION['usr_no']
    //                                             ,$_SESSION['ip_addr']
    //                                             ,date("YmdHis")
    //                                             ,$cash_expns_amt
    //                                             ,"Batch"
    //                                             ,"Batch"
    //                                             ,date("YmdHis")));
    //
    //        $result = $query;
    //
    //        if (!$result)
    //        {
    //            info_log("bal_update_tbb002l00", "현금지출 입력 오류!");
    //            info_log("bal_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //            return $result;
    //        }
    //    }
    //
    //    //info_log("!!! bal_update_tbb002l00", "result->bal                = [" . $result->bal                . "]");
    //    //info_log("!!! bal_update_tbb002l00", "result->cur_bal            = [" . $result->cur_bal            . "]");
    //
    //    // 2020.03.14. 잔고 입력/수정 부분 주석처리
    //    //if ($bal != $cur_bal)
    //    //{
    //    //    info_log("bal_update_tbb002l00", "잔고 입력/수정!");
    //    //
    //    //    // 당월 잔고 입력
    //    //    $sql = "insert into tbb002l00 values (?
    //    //                                         ,?
    //    //                                         ,?
    //    //                                         ,?
    //    //                                         ,?
    //    //                                         ,?
    //    //                                         ,?
    //    //                                         ,?
    //    //                                         ,?
    //    //                                         ) on duplicate key update amt = ?, mnpl_usr_no = ?, mnpl_ip = ?, mnpl_ymdh = ?";
    //    //
    //    //    $query = $this->db->query($sql, array($_SESSION['db_no']
    //    //                                         ,$arr_data['tr_yymm']
    //    //                                         ,"000"
    //    //                                         ,$last_dt
    //    //                                         ,NULL
    //    //                                         ,$bal
    //    //                                         ,$_SESSION['usr_no']
    //    //                                         ,$_SESSION['ip_addr']
    //    //                                         ,date("YmdHis")
    //    //                                         ,$bal
    //    //                                         ,$_SESSION['usr_no']
    //    //                                         ,$_SESSION['ip_addr']
    //    //                                         ,date("YmdHis")));
    //    //
    //    //    //$result = $query->row();  // 단건, 객체 $result->board_id
    //    //
    //    //    $result = $query;
    //    //
    //    //    if (!$result)
    //    //    {
    //    //        info_log("bal_update_tbb002l00", "당월 잔고 입력 오류!");
    //    //        info_log("bal_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //    //        return $result;
    //    //    }
    //    //}
    //
    //
    //    // 2020.05.03
    //    // 수입항목 생성
    //    // 과거 데이터 보호를 위해 2020.04월 이후부터 생성
    //    if (strncmp($arr_data['tr_yymm'], "202004", 6) >= 0)
    //    {
    //        // step1. 기존 입력 데이터 삭제
    //        $this->db->where('db_no     = ', $_SESSION['db_no']    );
    //        $this->db->where('tr_yymm   = ', $arr_data['tr_yymm']  );
    //        $this->db->like('io_tr_cls', '1', 'after');
    //
    //        $result = $this->db->delete('tbb002l00');
    //
    //        //info_log("bal_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //        //info_log("bal_update_tbb002l00", "last_query return  = [" . $result . "]");
    //
    //        // step2. 신규 데이터 입력
    //        $sql = "insert into tbb002l00
    //                select  ?
    //                       ,substr(dt, 1, 6)
    //                       ,case when incm_cls = '9' then concat('1', lpad(row_number() over(order by incm_cls, dt, incm_srno), 2, '0'))
    //                             else concat('10', incm_cls)
    //                        end
    //                       ,dt
    //                       ,memo
    //                       ,amt
    //                       ,'Batch'
    //                       ,'Batch'
    //                       ,now()
    //                  from  tbb003l00
    //                 where  db_no = ?
    //                   and  dt like concat(?, '%')";
    //
    //        $query = $this->db->query($sql, array($_SESSION['db_no']
    //                                             ,$_SESSION['db_no']
    //                                             ,$arr_data['tr_yymm']
    //                                             ));
    //
    //        $result = $query;
    //        //$result = $query->result();  // 객체 $result->board_id
    //        //$result = $query->row();  // 단건, 객체 $result->board_id
    //
    //        if (!$result)
    //        {
    //            $result_rows = $query->num_rows();
    //            if ($result_rows == 0)
    //            {
    //                info_log("bal_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //                info_log("bal_update_tbb002l00", "수입 조회 데이터 없음!(" . $arr_data['tr_yymm'] . ")");
    //            }
    //            else
    //            {
    //                info_log("bal_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //                alert_log("bal_update_tbb002l00", "[SQL ERR] 수입 조회 오류!");
    //            }
    //        }
    //    }
    //
    //    return 1;
    //}


    public function bal_chk_tbb003l00($arr_data)
    {
        $sql = "select  a.cash_expns_amt
                       ,b.cur_cash_expns_amt
                       ,b.cur_io_tr_srno
                       ,date_format(last_day(concat(?, '01')), '%Y%m%d')  last_dt
                  from  (
                         select  ifnull(sum(amt), 0)  cash_expns_amt
                           from  tbb001l00
                          where  db_no = ?
                            and  expns_dt like concat(?, '%')
                            and  del_yn = 'N'
                            and  expns_chnl_cls = '01'  -- 현금
                            and  ssamzi_yn = 'N'
                        )  a
                       ,(
                         select  ifnull(sum(amt), 'N')    cur_cash_expns_amt
                                ,io_tr_srno               cur_io_tr_srno
                           from  tbb003l00
                          where  db_no = ?
                            and  dt like concat(?, '%')
                            and  io_tr_cls = '201'
                            and  del_yn = 'N'
                        )  b";

        $query = $this->db->query($sql, array($arr_data['tr_yymm']
                                             ,$_SESSION['db_no']
                                             ,$arr_data['tr_yymm']
                                             ,$_SESSION['db_no']
                                             ,$arr_data['tr_yymm']
                                             ));

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            $result_rows = $query->num_rows();
            if ($result_rows == 0) {
                info_log("bal_chk_tbb003l00", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("bal_chk_tbb003l00", "현금 지출 합계금액 조회 데이터 없음!(" . $arr_data['tr_yymm'] . ")");
            } else {
                info_log("bal_chk_tbb003l00", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("bal_chk_tbb003l00", "[SQL ERR] 현금 지출 합계금액 조회 오류!");
            }
        }

        return $result;
    }


    public function delete_tbb002l00($arr_data)
    {
        $this->db->where('db_no     = ', $_SESSION['db_no']);
        $this->db->where('tr_yymm   = ', $arr_data['tr_yymm']);
        $this->db->where('io_tr_cls = ', $arr_data['io_tr_cls']);

        $result = $this->db->delete('tbb002l00');

        return $result;
    }

    public function get_io_tr_smmry($stnd_yymm=null)
    {
        //case when a.io_tr_cls = '19' then concat(b.clm_val_nm, '(', a.memo, ')') else b.clm_val_nm end  clm_val_nm

        $sql = "select  a.groupby_key
                       ,a.io_tr_cls
                       ,a.clm_val_nm   io_tr_nm
                       ,a.order_key    order_key
                       ,a.io_tr_srno
                       ,a.memo
                       ,a.amt
                  from  (
                        select  substr(order_key, 1, 1)  groupby_key
                               ,a.order_key
                               ,a.io_tr_cls
                               ,a.clm_val_nm
                               ,a.io_tr_srno
                               ,a.memo
                               ,sum(a.amt)               amt
                          from  (
                                select  concat(a.io_tr_cls, a.dt, a.io_tr_srno)  order_key
                                       ,a.io_tr_cls
                                       ,case when length(a.memo) > 0 then concat(b.clm_val_nm, '(', a.memo, ')')
                                             else b.clm_val_nm
                                        end    clm_val_nm
                                       ,a.io_tr_srno
                                       ,a.memo
                                       ,a.amt
                                  from  tbb003l00  a
                                       ,tba003i00  b
                                 where  a.db_no = ?
                                   and  a.dt like concat(?, '%')
                                   and  a.del_yn = 'N'
                                   and  b.db_no = a.db_no
                                   and  b.clm_nm = 'IO_TR_CLS'
                                   and  b.clm_val = a.io_tr_cls
                                 union all
                                select  '900'     order_key
                                       ,'ZZZ'     io_tr_cls
                                       ,'순손익'  clm_val_nm
                                       ,'ZZZ'     io_tr_srno
                                       ,NULL      memo
                                       ,sum(case when a.io_tr_cls like '1%' then a.amt end)
                                      - sum(case when a.io_tr_cls like '2%' then a.amt end)  amt
                                  from  tbb003l00  a
                                       ,tba003i00  b
                                 where  a.db_no = ?
                                   and  a.dt like concat(?, '%')
                                   and  a.del_yn = 'N'
                                   and  b.db_no = a.db_no
                                   and  b.clm_nm = 'IO_TR_CLS'
                                   and  b.clm_val = a.io_tr_cls
                                )  a
                         group by  substr(a.order_key, 1, 1)
                                  ,a.order_key
                                  ,a.clm_val_nm
                        with rollup
                        )  a
                 where  a.groupby_key is not null
                   and  (
                         a.groupby_key is not null
                         and
                         a.order_key is not null
                         and
                         a.clm_val_nm is not null
                        )
                    or  (
                         a.groupby_key in ('1', '2')
                         and
                         a.order_key is null
                         and
                         a.clm_val_nm is null
                        )
              order by  a.groupby_key
                       ,a.order_key";


        $query = $this->db->query($sql, array($_SESSION['db_no']
                                              ,$stnd_yymm
                                              ,$_SESSION['db_no']
                                              ,$stnd_yymm
                                              ));

        $result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        //$result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_io_tr_smmry", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_io_tr_smmry", "[SQL ERR] 입출금 조회 오류!");
        }

        return $result;
    }


    //2020.09.06. 미사용 삭제 처리(tba002l00 >> tba003l00 으로 변경 처리)
    //public function get_io_tr_info($stnd_yymm=NULL, $io_tr_cls=NULL)
    //{
    //    //google 캘린더의 end-date 는 exclusive(start-dt 는 inclusive) 하므로 end_dt + 1 일자를 리턴
    //    $this->db->select("a.tr_yymm
    //                      ,a.io_tr_cls
    //                      ,a.tr_dt
    //                      ,concat(substr(a.tr_dt, 1, 4), '-', substr(a.tr_dt, 5, 2), '-', substr(a.tr_dt, 7, 2))  stnd_tr_dt
    //                      ,a.memo
    //                      ,a.amt
    //                      ");
    //    $this->db->from('tbb002l00  a');
    //    $this->db->where("a.db_no     = ", $_SESSION['db_no']);
    //    $this->db->where("a.tr_yymm   = ", $stnd_yymm);
    //    $this->db->where("a.io_tr_cls = ", $io_tr_cls);
    //    $this->db->limit(1);
    //
    //    $query = $this->db->get();
    //
    //    //$result = $query->result();  // 객체 $result->board_id
    //    //$result = $query->result_array();  //배열 $result['board_id']
    //    $result = $query->row();  // 단건, 객체 $result->board_id
    //
    //    if (!$result)
    //    {
    //        info_log("get_io_tr_info", "last_query  = [" . $this->db->last_query() . "]");
    //        alert_log("get_io_tr_info", "[SQL ERR] 입출금거래 조회 오류!");
    //    }
    //
    //    return $result;
    //
    //}


    public function get_dt_dtl($stnd_dt=null)
    {
        //google 캘린더의 end-date 는 exclusive(start-dt 는 inclusive) 하므로 end_dt + 1 일자를 리턴
        $this->db->select("a.dt
                          ,a.dt_cls
                          ,b.clm_val_nm
                          ");
        $this->db->from('tba004l00  a');
        $this->db->from('tba003i00  b');
        $this->db->where("a.dt      = ", $stnd_dt);
        $this->db->where("b.db_no   = '0000000000'");
        $this->db->where("b.clm_nm  = 'DT_CLS'");
        $this->db->where("b.clm_val = a.dt_cls");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_dt_dtl", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_dt_dtl", "[SQL ERR] 일자 상세 조회 오류!");
        }

        return $result;
    }


    public function update_tba004l00_1($arr_data)
    {
        $u_data = array('dt_cls'    => $arr_data['dt_cls']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );
        $this->db->where('dt = ', $arr_data['dt']);

        $result = $this->db->update('tba004l00', $u_data);

        return $result;
    }


    public function insert_tba008l00($arr_data)
    {
        $i_data = array('db_no'       => $_SESSION['db_no']
                       ,'season_srno' => $arr_data['season_srno']
                       ,'srt_dt'      => $arr_data['srt_dt']
                       ,'end_dt'      => $arr_data['end_dt']
                       ,'season_cls'  => $arr_data['season_cls']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $result = $this->db->insert('tba008l00', $i_data);

        return $result;
    }


    public function get_chckin_list($stnd_dt, $prcs_cls, $offset=null, $limit=null)
    {
        $this->db->select("a.gst_no
                          ,b.gst_nm
                          ,substr(b.phone_num, 8, 4)  phone_num
                          ,a.hsrm_cls
                          ,c.clm_val_nm    hsrm_nm
                          ,ifnull(a.gst_desc, concat('성인:', a.adlt_cnt, ' 자녀:', a.chld_cnt)) gst_desc
                          ,a.memo
                          ,DATEDIFF(a.end_dt, a.srt_dt) + 1  stay_days
                          ");
        $this->db->from('tba005l00  a');
        $this->db->from('tba007l00  b');
        $this->db->from('tba003i00  c');
        $this->db->where('a.db_no = ', $_SESSION['db_no']);
        $this->db->where("a.cncl_yn = 'N'");
        $this->db->where('a.srt_dt = ', $stnd_dt);
        $this->db->where("b.db_no = a.db_no");
        $this->db->where("b.gst_no = a.gst_no");
        $this->db->where("c.db_no = a.db_no");
        $this->db->where("c.clm_nm = 'HSRM_CLS'");
        $this->db->where("c.clm_val = a.hsrm_cls");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by('a.hsrm_cls');

        $query = $this->db->get();

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_chckin_list", "No Data Found!(stnd_dt=" . $stnd_dt . ")");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_chckin_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_chckin_list", "[SQL ERR] 체크인 고객 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_expns_srch_list($memo, $view_cls, $prcs_cls=null, $offset=null, $limit=null)
    {
        $sql = "select  a.stnd_dt
                       ,a.memo
                       ,a.memo_whole
                       ,a.amt
                       ,a.io_cls
                       ,a.io_cls_nm
                       ,a.srno
                       ,a.key_val
                  from  (
                        select  concat(substr(a.expns_dt, 1, 4), '-', substr(a.expns_dt, 5, 2), '-', substr(a.expns_dt, 7, 2))  stnd_dt
                               ,case when char_length(a.memo) <= 7 then a.memo
                                     when char_length(a.memo) >  7 then concat(substr(a.memo, 1, 6), '...')
                                end  memo
                               ,a.memo  memo_whole
                               ,a.amt
                               ,'2'    io_cls
                               ,'지출'  io_cls_nm
                               ,a.expns_srno    srno
                               ,a.expns_srno    key_val
                          from  tbb001l00  a
                         where  a.db_no = ?
                           and  a.memo like concat('%', ?, '%')
                           and  a.del_yn = 'N'
                           and  '2' = ?
                         union all
                        select  concat(substr(b.dt, 1, 4), '-', substr(b.dt, 5, 2), '-', substr(b.dt, 7, 2))  stnd_dt
                               ,case when char_length(b.memo) <= 7 then b.memo
                                     when char_length(b.memo) >  7 then concat(substr(b.memo, 1, 6), '...')
                                end  memo
                               ,b.memo  memo_whole
                               ,b.amt
                               ,'1'    io_cls
                               ,'수입'  io_cls_nm
                               ,b.io_tr_srno    srno
                               ,b.io_tr_srno    key_val
                          from  tbb003l00  b
                         where  b.db_no = ?
                           and  b.io_tr_cls like '1%'
                           and  b.memo like concat('%', ?, '%')
                           and  b.del_yn = 'N'
                           and  '1' = ?
                        )  a
                 order by  a.stnd_dt desc, a.io_cls, a.srno";

        if (isset($limit) && isset($offset)) {
            $sql = $sql . " limit " .  $offset . ", " . $limit;
        }

        //info_log("get_expns_srch_list", "sql  = [" . $sql . "]");
        //info_log("get_expns_srch_list", "db_no  = [" . $_SESSION['db_no'] . "]");
        //$memo = '%' . $memo . '%';
        //info_log("get_expns_srch_list", "memo  = [" . $memo . "]");

        $query = $this->db->query($sql, array($_SESSION['db_no']
                                             ,$memo
                                             ,$view_cls
                                             ,$_SESSION['db_no']
                                             ,$memo
                                             ,$view_cls));

        //info_log("get_expns_srch_list", "query  = [" . $query . "]");

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_expns_srch_list", "No Data Found!(memo = " . $memo . ")");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_expns_srch_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_expns_srch_list", "[SQL ERR] 수입/지출 조회 오류!");
                }
            }
        }

        return $result;
    }



    public function insert_tbb003l00($arr_data)
    {
        $i_data = array('db_no'       => $_SESSION['db_no']
                       ,'io_tr_srno'  => $arr_data['io_tr_srno']
                       ,'dt'          => $arr_data['dt']
                       ,'io_tr_cls'   => $arr_data['io_tr_cls']
                       ,'memo'        => $arr_data['memo']
                       ,'amt'         => $arr_data['amt']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $result = $this->db->insert('tbb003l00', $i_data);

        return $result;
    }


    public function get_io_tr_list($stnd_yymm, $prcs_cls=null, $offset=null, $limit=null)
    {
        $this->db->select("a.io_tr_srno
                          ,concat(substr(a.dt, 1, 4), '-', substr(a.dt, 5, 2), '-', substr(a.dt, 7, 2))  stnd_dt
                          ,b.clm_val_nm    incm_cls_nm
                          ,case when char_length(a.memo) <= 15 then a.memo
                                when char_length(a.memo) >  15 then concat(substr(a.memo, 1, 14), '...')
                           end  memo
                          ,memo  memo_whole
                          ,amt
                          ");
        $this->db->from('tbb003l00  a');
        $this->db->from('tba003i00  b');
        $this->db->where("a.db_no = ", $_SESSION['db_no']);
        $this->db->like('a.dt', $stnd_yymm, 'after');
        $this->db->where("a.del_yn = 'N'");
        $this->db->where("b.db_no = a.db_no");
        $this->db->where("b.clm_nm = 'IO_TR_CLS'");
        $this->db->where("b.clm_val = a.io_tr_cls");
        $this->db->order_by("a.dt, a.io_tr_srno");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();   // Produces: SELECT title, content, date FROM mytable
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_io_tr_list", "No Data Found!");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_io_tr_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_io_tr_list", "[SQL ERR] 수입 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_io_tr_info($io_tr_srno)
    {
        //google 캘린더의 end-date 는 exclusive(start-dt 는 inclusive) 하므로 end_dt + 1 일자를 리턴
        $this->db->select("a.io_tr_srno
                          ,concat(substr(a.dt, 1, 4), '-', substr(a.dt, 5, 2), '-', substr(a.dt, 7, 2))  stnd_dt
                          ,a.io_tr_cls    io_tr_cls
                          ,b.clm_val_nm   io_tr_cls_nm
                          ,a.memo
                          ,a.amt
                          ");
        $this->db->from('tbb003l00  a');
        $this->db->from('tba003i00  b');
        $this->db->where("a.db_no     = ", $_SESSION['db_no']);
        $this->db->where("a.io_tr_srno = ", $io_tr_srno);
        $this->db->where("b.db_no     = a.db_no");
        $this->db->where("b.clm_nm    = 'IO_TR_CLS'");
        $this->db->where("b.clm_val   = a.io_tr_cls");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_io_tr_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_io_tr_info", "[SQL ERR] 입출금 거래 조회 오류!");
        }

        return $result;
    }


    public function update_tbb003l00_1($arr_data)
    {
        $u_data = array(
                        'dt'           => $arr_data['dt']
                       ,'io_tr_cls'    => $arr_data['io_tr_cls']
                       ,'memo'         => $arr_data['memo']
                       ,'amt'          => $arr_data['amt']
                       ,'del_yn'       => 'N'
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

        $this->db->where('db_no       = ', $_SESSION['db_no']);
        $this->db->where('io_tr_srno  = ', $arr_data['io_tr_srno']);

        $result = $this->db->update('tbb003l00', $u_data);

        return $result;
    }


    public function update_tbb003l00_2($arr_data)
    {
        $u_data = array(
                        'del_yn'       => 'Y'
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

        $this->db->where('db_no      = ', $_SESSION['db_no']);
        $this->db->where('io_tr_srno = ', $arr_data['io_tr_srno']);

        $result = $this->db->update('tbb003l00', $u_data);

        return $result;
    }


    public function insert_tbc001l00($arr_data)
    {
        $i_data = array('dt'          => $arr_data['dt']
                       ,'io_cls'      => $arr_data['io_cls']
                       ,'emp_no'      => $arr_data['emp_no']
                       ,'memo'        => $arr_data['memo']
                       ,'amt'         => $arr_data['amt']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $result = $this->db->insert('tbc001l00', $i_data);

        return $result;
    }


    public function get_alba_io_list($stnd_yymm, $io_cls, $prcs_cls=null, $offset=null, $limit=null)
    {
        $this->db->select("a.srno
                          ,concat(substr(a.dt, 1, 4), '-', substr(a.dt, 5, 2), '-', substr(a.dt, 7, 2))  stnd_dt
                          ,b.clm_val_nm    io_cls_nm
                          ,c.clm_val_nm    emp_no_nm
                          ,case when char_length(a.memo) <= 13 then a.memo
                                when char_length(a.memo) >  13 then concat(substr(a.memo, 1, 12), '...')
                           end  memo
                          ,memo  memo_whole
                          ,amt
                          ");
        $this->db->from('tbc001l00  a');
        $this->db->from('tba003i00  b');
        $this->db->from('tba003i00  c');
        $this->db->like('a.dt', $stnd_yymm, 'after');
        $this->db->like('a.io_cls', $io_cls, 'after');
        $this->db->where("a.del_yn = 'N'");
        $this->db->where("b.db_no = ", $_SESSION['db_no']);
        $this->db->where("b.clm_nm = 'IO_CLS'");
        $this->db->where("b.clm_val = a.io_cls");
        $this->db->where("c.db_no = ", $_SESSION['db_no']);
        $this->db->where("c.clm_nm = 'USR'");
        $this->db->where("c.clm_val = a.emp_no");
        $this->db->order_by("a.dt, a.srno");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();   // Produces: SELECT title, content, date FROM mytable
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_alba_list", "No Data Found!");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_alba_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_alba_list", "[SQL ERR] 알바 입출금 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_alba_io_info($srno)
    {
        //google 캘린더의 end-date 는 exclusive(start-dt 는 inclusive) 하므로 end_dt + 1 일자를 리턴
        $this->db->select("a.srno
                          ,concat(substr(a.dt, 1, 4), '-', substr(a.dt, 5, 2), '-', substr(a.dt, 7, 2))  stnd_dt
                          ,a.io_cls      io_cls
                          ,b.clm_val_nm  io_cls_nm
                          ,a.emp_no      emp_no
                          ,c.clm_val_nm  emp_no_nm
                          ,a.memo
                          ,a.amt
                          ");
        $this->db->from('tbc001l00  a');
        $this->db->from('tba003i00  b');
        $this->db->from('tba003i00  c');
        $this->db->where("a.srno = ", $srno);
        $this->db->where("b.db_no     = ", $_SESSION['db_no']);
        $this->db->where("b.clm_nm    = 'IO_CLS'");
        $this->db->where("b.clm_val   = a.io_cls");
        $this->db->where("c.db_no     = ", $_SESSION['db_no']);
        $this->db->where("c.clm_nm    = 'USR'");
        $this->db->where("c.clm_val   = a.emp_no");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_alba_io_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_alba_io_info", "[SQL ERR] 알바 입출금 조회 오류!");
        }

        return $result;
    }


    public function update_tbc001l00_1($arr_data)
    {
        $u_data = array('dt'          => $arr_data['dt']
                       ,'io_cls'      => $arr_data['io_cls']
                       ,'emp_no'      => $arr_data['emp_no']
                       ,'memo'        => $arr_data['memo']
                       ,'amt'         => $arr_data['amt']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $this->db->where('srno  = ', $arr_data['srno']);

        $result = $this->db->update('tbc001l00', $u_data);

        return $result;
    }


    public function update_tbc001l00_2($arr_data)
    {
        $u_data = array(
                        'del_yn'       => 'Y'
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

        $this->db->where('srno  = ', $arr_data['srno']);

        $result = $this->db->update('tbc001l00', $u_data);

        return $result;
    }


    public function get_alba_io_smmry($prcs_cls=null, $offset=null, $limit=null)
    {
        $this->db->select("b.clm_val_nm  emp_no_nm
                          ,sum(case when a.io_cls = '1' then a.amt else 0 end)   incm_amt
                          ,sum(case when a.io_cls = '2' then a.amt else 0 end)   expn_amt
                          ,sum(case when a.io_cls = '1' then a.amt
                                    when a.io_cls = '2' then a.amt * -1
                               end)   blnc_amt
                          ");
        $this->db->from('tbc001l00  a');
        $this->db->from('tba003i00  b');
        $this->db->where("a.del_yn = 'N'");
        $this->db->where("b.db_no = ", $_SESSION['db_no']);
        $this->db->where("b.clm_nm = 'USR'");
        $this->db->where("b.clm_val = a.emp_no");
        $this->db->group_by("b.clm_val_nm");
        $this->db->order_by("a.emp_no");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();   // Produces: SELECT title, content, date FROM mytable
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_alba_io_smmry", "No Data Found!");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_alba_io_smmry", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_alba_io_smmry", "[SQL ERR] 알바 입출금 잔액 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_season_list($stnd_yy, $prcs_cls=null, $offset=null, $limit=null)
    {
        $sql = "select  a.season_srno
                       ,concat(substr(a.srt_dt, 1, 4), '-', substr(a.srt_dt, 5, 2), '-', substr(a.srt_dt, 7, 2))  srt_dt
                       ,concat(substr(a.end_dt, 1, 4), '-', substr(a.end_dt, 5, 2), '-', substr(a.end_dt, 7, 2))  end_dt
                       ,b.clm_val_nm    season_cls_nm
                  from  tba008l00  a
                       ,tba003i00  b
                 where  a.db_no = ?
                   and  (a.srt_dt like concat(?, '%')
                         or
                         a.end_dt like concat(?, '%'))
                   and  a.del_yn = 'N'
                   and  b.db_no = a.db_no
                   and  b.clm_nm = 'SEASON_CLS'
                   and  b.clm_val = a.season_cls
                 order by  a.srt_dt";

        if (isset($limit) && isset($offset)) {
            $sql = $sql . " limit " .  $offset . ", " . $limit;
        }

        $query = $this->db->query($sql, array($_SESSION['db_no'], $stnd_yy, $stnd_yy));

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_season_list", "last_query  = [" . $this->db->last_query() . "]");
            info_log("get_season_list", "No Data Found!");
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_season_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_season_list", "[SQL ERR] 시즌 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_season_info($season_srno)
    {
        //google 캘린더의 end-date 는 exclusive(start-dt 는 inclusive) 하므로 end_dt + 1 일자를 리턴
        $this->db->select("a.season_srno
                          ,concat(substr(a.srt_dt, 1, 4), '-', substr(a.srt_dt, 5, 2), '-', substr(a.srt_dt, 7, 2))  stnd_srt_dt
                          ,concat(substr(a.end_dt, 1, 4), '-', substr(a.end_dt, 5, 2), '-', substr(a.end_dt, 7, 2))  stnd_end_dt
                          ,a.season_cls
                          ,b.clm_val_nm    season_cls_nm
                          ");
        $this->db->from('tba008l00  a');
        $this->db->from('tba003i00  b');
        $this->db->where("a.db_no     = ", $_SESSION['db_no']);
        $this->db->where("a.season_srno = ", $season_srno);
        $this->db->where("a.del_yn = 'N'");
        $this->db->where("b.db_no     = a.db_no");
        $this->db->where("b.clm_nm    = 'SEASON_CLS'");
        $this->db->where("b.clm_val   = a.season_cls");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_season_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_season_info", "[SQL ERR] 시증 정보 조회 오류!");
        }

        return $result;
    }

    public function update_tba008l00_1($arr_data)
    {
        $u_data = array('srt_dt'      => $arr_data['srt_dt']
                       ,'end_dt'      => $arr_data['end_dt']
                       ,'season_cls'  => $arr_data['season_cls']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $this->db->where('season_srno  = ', $arr_data['season_srno']);

        $result = $this->db->update('tba008l00', $u_data);

        return $result;
    }


    public function update_tba008l00_2($arr_data)
    {
        $u_data = array(
                        'del_yn'       => 'Y'
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

        $this->db->where('season_srno  = ', $arr_data['season_srno']);

        $result = $this->db->update('tba008l00', $u_data);

        return $result;
    }


    public function insert_tbb005l00($arr_data)
    {
        $i_data = array('db_no'           => $_SESSION['db_no']
                       ,'fix_expns_srno'  => $arr_data['fix_expns_srno']
                       ,'trnsfr_day'      => $arr_data['trnsfr_day']
                       ,'expns_day'       => $arr_data['expns_day']
                       ,'expns_nm'        => $arr_data['expns_nm']
                       ,'expns_day'       => $arr_data['expns_day']
                       ,'expns_chnl_cls'  => $arr_data['expns_chnl_cls']
                       ,'sttlmt_yn'       => $arr_data['sttlmt_yn']
                       ,'expns_cls'       => $arr_data['expns_cls']
                       ,'whr_to_buy'      => $arr_data['whr_to_buy']
                       ,'amt'             => $arr_data['amt']
                       ,'memo'            => $arr_data['memo']
                       ,'bank'            => $arr_data['bank']
                       ,'ac_no'           => $arr_data['ac_no']
                       ,'rel_ac_no'       => $arr_data['rel_ac_no']
                       ,'del_yn'          => 'N'
                       ,'mnpl_usr_no'     => $_SESSION['usr_no']
                       ,'mnpl_ip'         => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'       => date("YmdHis")
                       );
        
        $i_data = array_filter($i_data);

        $result = $this->db->insert('tbb005l00', $i_data);

        info_log("insert_tbb005l00", "last_query  = [" . $this->db->last_query() . "]");

        return $result;
    }


    public function get_fix_expns_info($fix_expns_srno)
    {
        $this->db->select("a.fix_expns_srno
                          ,a.expns_nm
                          ,a.trnsfr_day
                          ,a.io_tr_cls
                          ,a.expns_day
                          ,a.expns_chnl_cls
                          ,a.sttlmt_yn
                          ,ifnull(a.expns_cls, 'NULL')   expns_cls
                          ,a.whr_to_buy
                          ,a.amt
                          ,a.memo
                          ,a.bank
                          ,a.ac_no
                          ,a.rel_ac_no
                          ");
        $this->db->from('tbb005l00  a');
        $this->db->where("a.db_no   = ", $_SESSION['db_no']);
        $this->db->where("a.fix_expns_srno  = ", $fix_expns_srno);
        $this->db->where("a.del_yn  = 'N'");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_fix_expns_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_fix_expns_info", "[SQL ERR] 고정지출 정보 조회 오류!");
        }

        return $result;
    }


    public function update_tbb005l00_1($arr_data)
    {
        $u_data = array(
                        'expns_day'       => $arr_data['expns_day']
                       ,'expns_nm'        => $arr_data['expns_nm']
                       ,'trnsfr_day'      => $arr_data['trnsfr_day']
                       ,'expns_day'       => $arr_data['expns_day']
                       ,'expns_chnl_cls'  => $arr_data['expns_chnl_cls']
                       ,'sttlmt_yn'       => $arr_data['sttlmt_yn']
                       ,'expns_cls'       => $arr_data['expns_cls']
                       ,'whr_to_buy'      => $arr_data['whr_to_buy']
                       ,'amt'             => $arr_data['amt']
                       ,'memo'            => $arr_data['memo']
                       ,'bank'            => $arr_data['bank']
                       ,'ac_no'           => $arr_data['ac_no']
                       ,'rel_ac_no'       => $arr_data['rel_ac_no']
                       ,'del_yn'          => 'N'
                       ,'mnpl_usr_no'     => $_SESSION['usr_no']
                       ,'mnpl_ip'         => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'       => date("YmdHis")
                       );

        $this->db->where('db_no      = ', $_SESSION['db_no']);
        $this->db->where('fix_expns_srno  = ', $arr_data['fix_expns_srno']);

        $result = $this->db->update('tbb005l00', $u_data);

        return $result;
    }


    public function update_tbb005l00_2($arr_data)
    {
        $u_data = array(
                        'del_yn'         => 'Y'
                       ,'mnpl_usr_no'    => $_SESSION['usr_no']
                       ,'mnpl_ip'        => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'      => date("YmdHis")
                       );

        $this->db->where('db_no      = ', $_SESSION['db_no']);
        $this->db->where('fix_expns_srno  = ', $arr_data['fix_expns_srno']);

        $result = $this->db->update('tbb005l00', $u_data);

        return $result;
    }


    public function get_fix_expns_list($view_cls, $prcs_cls=null, $offset=null, $limit=null)
    {
        $this->db->select("a.fix_expns_srno
                          ,a.expns_nm
                          ,a.trnsfr_day
                          ,a.expns_day
                          ,a.expns_chnl_cls
                          ,b.clm_val_nm    expns_chnl_cls_nm
                          ,a.expns_cls
                          ,ifnull(c.clm_val_nm, '선택없음')    expns_cls_nm
                          ,a.whr_to_buy
                          ,a.amt
                          ,a.memo
                          ,a.bank
                          ,d.clm_val_nm    bank_nm
                          ,a.ac_no
                          ,a.rel_ac_no
                          ,row_number() over (PARTITION BY CONCAT(a.trnsfr_day, a.ac_no) ORDER BY a.trnsfr_day, a.ac_no, a.expns_day, a.fix_expns_srno)    rownum
                          ,count(a.fix_expns_srno) over (PARTITION BY  a.trnsfr_day, a.ac_no)  rowspan_val
                          ,sum(a.amt) over (PARTITION BY a.trnsfr_day, a.ac_no)               sum_amt
                          ");
        $this->db->from('tbb005l00  a');
        $this->db->join('tba003i00  b', "b.clm_val = a.expns_chnl_cls and b.db_no   IN ('0000000000', a.db_no) and b.clm_nm  = 'EXPNS_CHNL_CLS'", 'left outer');
        $this->db->join('tba003i00  c', "c.clm_val = a.expns_cls and c.db_no   IN ('0000000000', a.db_no) and c.clm_nm  = 'EXPNS_CLS'", 'left outer');
        $this->db->join('tba003i00  d', "d.clm_val = a.bank and d.db_no   IN ('0000000000', a.db_no) and d.clm_nm  = 'BANK'", 'left outer');
        $this->db->where("a.db_no   = ", $_SESSION['db_no']);
        $this->db->where("a.del_yn = 'N'");

        if (strcmp($view_cls, "1") == 0) {
            $this->db->where("a.sttlmt_yn = 'N'");
        } else {
            $this->db->where("(a.expns_chnl_cls = '01'");
            $this->db->or_where("a.sttlmt_yn = 'Y')");
        }

        $this->db->order_by("a.trnsfr_day, a.ac_no, a.expns_day, a.fix_expns_srno");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();   // Produces: SELECT title, content, date FROM mytable
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_fix_expns_list", "last_query  = [" . $this->db->last_query() . "]");
            info_log("get_fix_expns_list", "No Data Found!");
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_fix_expns_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_fix_expns_list", "[SQL ERR] 고정지출 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_io_tr_cls_dup_chk($dt, $io_tr_cls)
    {
        $this->db->select('ifnull(count(*), 0) cnt
                          ');
        $this->db->from('tbb003l00  a');
        $this->db->where('a.db_no  = ', $_SESSION['db_no']);
        $this->db->like('a.dt', substr($dt, 0, 6), 'after');
        $this->db->where('a.io_tr_cls  = ', $io_tr_cls);
        $this->db->where("a.del_yn  = 'N'");

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            $result_rows = $query->num_rows();
            if ($result_rows == 0) {
                alert_log("get_io_tr_cls_dup_chk", "해당월 입출금거래분류 미등록!(" . $dt . "/" . $io_tr_cls . ")");
            } else {
                info_log("get_io_tr_cls_dup_chk", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_io_tr_cls_dup_chk", "[SQL ERR] 입출금거래분류 중복 조회 오류!");
            }
        }

        return $result;
    }


    public function get_fix_expns_withdraw_dup_chk($io_tr_cls)
    {
        $this->db->select('ifnull(count(*), 0) cnt
                          ');
        $this->db->from('tbb005l00  a');
        $this->db->where('a.db_no  = ', $_SESSION['db_no']);
        $this->db->where('a.io_tr_cls  = ', $io_tr_cls);
        $this->db->where("a.sttlmt_yn = 'Y'");
        $this->db->where("a.del_yn    = 'N'");

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            $result_rows = $query->num_rows();
            if ($result_rows == 0) {
                alert_log("get_fix_expns_withdraw_dup_chk", "고정지출 출금건 미존재!(" . $expns_chnl_cls . ")");
            } else {
                info_log("get_fix_expns_withdraw_dup_chk", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_fix_expns_withdraw_dup_chk", "[SQL ERR] 고정지출 출금 중복 조회 오류!");
            }
        }

        return $result;
    }


    public function get_tr_srno($rsv_srno, $tr_dt, $tr_cls)
    {
        $this->db->select('a.tr_srno');
        $this->db->from('tba006l00  a');
        $this->db->where('a.db_no     = ', $_SESSION['db_no']);
        $this->db->where('a.rsv_srno  = ', $rsv_srno);
        $this->db->where('a.tr_dt     = ', $tr_dt);
        $this->db->where('a.tr_cls    = ', $tr_cls);
        $this->db->where("a.del_yn    = 'N'");

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            $result_rows = $query->num_rows();
            if ($result_rows == 0) {
                //alert_log("get_tr_srno", "기타거래 거래일련번호 미존재!(" . $rsv_srno . ")");
                info_log("get_tr_srno", "기타거래 거래일련번호 미존재!(" . $rsv_srno . ")");

                return $result_rows;
            } else {
                info_log("get_tr_srno", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_tr_srno", "[SQL ERR] 기타거래 거래일련번호 조회 오류!");

                return $result;
            }
        }

        return $result;
    }


    public function get_tr_list($rsv_srno)
    {
        $db_no = array('0000000000', $_SESSION['db_no']);

        $this->db->select("a.rsv_srno
                          ,concat(substr(a.tr_dt, 1, 4), '-', substr(a.tr_dt, 5, 2), '-', substr(a.tr_dt, 7, 2))  dtfm_tr_dt
                          ,a.tr_cls
                          ,b.clm_val_nm
                          ,(b.othr_info * a.amt)   as amt
                          ");
        $this->db->from('tba006l00  a');
        $this->db->from('tba003i00  b');
        $this->db->where("a.db_no = ", $_SESSION['db_no']);
        $this->db->where("a.rsv_srno = ", $rsv_srno);
        $this->db->where_in("b.db_no", $db_no);
        $this->db->where("b.clm_nm = 'TR_CLS'");
        $this->db->where("b.clm_val = a.tr_cls");
        $this->db->order_by("a.tr_srno");

        $query = $this->db->get();   // Produces: SELECT title, content, date FROM mytable
        $result = $query->num_rows();

        if ($result == 0) {
            //info_log("get_tr_list", "last_query  = [" . $this->db->last_query() . "]");
            info_log("get_tr_list", "No Data Found!");
        } else {
            $result = $query->result();  // 객체 $result->board_id

            if (!$result) {
                info_log("get_tr_list", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_tr_list", "[SQL ERR] 기타 거래 리스트 조회 오류!");
            }
        }

        return $result;
    }


    public function insert_tbc002l00($arr_data)
    {
        $i_data = array('usr'       => $arr_data['usr']
                       ,'dt'        => $arr_data['dt']
                       ,'time_cls'  => $arr_data['time_cls']
                       ,'time'      => $arr_data['time']
                       ,'memo'      => $arr_data['memo']
                       ,'mnpl_ymdh' => date("YmdHis")
                       );

        $result = $this->db->insert('tbc002l00', $i_data);

        //info_log("insert_tbc002l00", "last_query  = [" . $this->db->last_query() . "]");

        return $result;
    }


    public function get_time_mng_list($dt_fr, $dt_to, $usr, $time_cls, $prcs_cls=null, $offset=null, $limit=null)
    {
        $sql = "SELECT  a.srno
                       ,concat(substr(a.dt, 1, 4), '-', substr(a.dt, 5, 2), '-', substr(a.dt, 7, 2))  stnd_dt
                       ,b.clm_val_nm usr_nm
                       ,c.clm_val_nm time_cls_nm
                       ,c.othr_info
                       ,a.time
                       ,case when char_length(a.memo) <= 13 then a.memo
                             when char_length(a.memo) >  13 then concat(substr(a.memo, 1, 12), '...')
                        end  memo, memo memo_whole
                  FROM  tbc002l00 a
                       ,tba003i00 b
                       ,tba003i00 c
                 WHERE  a.dt between ? and ?
                   AND  a.usr LIKE concat(?, '%') ESCAPE '!'
                   AND  a.time_cls LIKE concat(?, '%') ESCAPE '!'
                   AND  a.del_yn = 'N'
                   AND  b.db_no = ?
                   AND  b.clm_nm = 'USR'
                   AND  b.clm_val = a.usr
                   AND  c.db_no = b.db_no
                   AND  c.clm_nm = 'TIME_CLS'
                   AND  c.clm_val = a.time_cls
                 UNION ALL
                SELECT  99999         srno
                       ,'9999-12-31'  stnd_dt
                       ,'합계'        usr_nm
                       ,case when b.othr_info = '1' then '게임'
                             when b.othr_info = '2' then '유튜브'
                             when b.othr_info = '3' then '들엄시민'
                             when b.othr_info = '4' then '인터넷'
                        end   time_cls_nm
                       ,b.othr_info
                       ,SUM(a.time)  time
                       ,null
                       ,null
                  FROM  tbc002l00  a
                       ,tba003i00  b
                 WHERE  a.dt between ? and ?
                   AND  a.usr LIKE concat(?, '%') ESCAPE '!'
                   AND  a.time_cls LIKE concat(?, '%') ESCAPE '!'
                   AND  a.del_yn = 'N'
                   AND  b.db_no = ?
                   AND  b.clm_nm = 'TIME_CLS'
                   AND  b.clm_val = a.time_cls
                 GROUP BY  b.othr_info
                 ORDER BY  stnd_dt, srno, othr_info";

        if (isset($limit) && isset($offset)) {
            $sql = $sql . " limit " .  $offset . ", " . $limit;
        }

        $query = $this->db->query($sql, array($dt_fr
                                             ,$dt_to
                                             ,$usr
                                             ,$time_cls
                                             ,$_SESSION['db_no']
                                             ,$dt_fr
                                             ,$dt_to
                                             ,$usr
                                             ,$time_cls
                                             ,$_SESSION['db_no']));

        $result = $query->num_rows();
        if ($result == 0) {
            info_log("get_time_mng_list", "No Data Found!");
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_time_mng_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_time_mng_list", "[SQL ERR] 아이들 시간관리 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_time_mng_info($srno)
    {
        $this->db->select("a.srno
                          ,a.usr
                          ,a.dt
                          ,concat(substr(a.dt, 1, 4), '-', substr(a.dt, 5, 2), '-', substr(a.dt, 7, 2))  stnd_dt
                          ,a.time_cls
                          ,a.time
                          ,a.memo
                          ");
        $this->db->from('tbc002l00  a');
        $this->db->where("a.srno  = ", $srno);
        $this->db->where("a.del_yn  = 'N'");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_time_mng_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_time_mng_info", "[SQL ERR] 아이들 시간관리 정보 조회 오류!");
        }

        return $result;
    }


    public function update_tbc002l00_1($arr_data)
    {
        $u_data = array('usr'       => $arr_data['usr']
                       ,'dt'        => $arr_data['dt']
                       ,'time_cls'  => $arr_data['time_cls']
                       ,'time'      => $arr_data['time']
                       ,'memo'      => $arr_data['memo']
                       ,'mnpl_ymdh' => date("YmdHis")
                       );

        $this->db->where('srno  = ', $arr_data['srno']);

        $result = $this->db->update('tbc002l00', $u_data);

        return $result;
    }


    public function update_tbc002l00_2($arr_data)
    {
        $u_data = array(
                        'del_yn'         => 'Y'
                       ,'mnpl_ymdh'      => date("YmdHis")
                       );

        $this->db->where('srno  = ', $arr_data['srno']);

        $result = $this->db->update('tbc002l00', $u_data);

        return $result;
    }


    public function get_week_fr_to($stnd_dt)
    {
        $sql = "SELECT DATE_FORMAT(DATE_SUB(?, interval WEEKDAY(?) DAY), '%Y%m%d')        dt_fr
                      ,DATE_FORMAT(DATE_ADD(?, interval (6 - WEEKDAY(?)) DAY), '%Y%m%d')  dt_to";

        $query = $this->db->query($sql, array($stnd_dt
                                             ,$stnd_dt
                                             ,$stnd_dt
                                             ,$stnd_dt));

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_week_fr_to", "last_query  = [" . $this->db->last_query() . "]");
            info_log("get_week_fr_to", "No Data Found!");
        } else {
            $result = $query->result();  // 객체 $result->board_id

            if (!$result) {
                info_log("get_week_fr_to", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_week_fr_to", "[SQL ERR] 주 시작일, 종료일 조회 오류!");
            }
        }

        return $result;
    }

    public function insert_tbb006i00($arr_data)
    {
        $i_data = array('db_no'         => $_SESSION['db_no']
                       ,'ac_srno'       => $arr_data['ac_srno']
                       ,'ac_no'         => $arr_data['ac_no']
                       ,'bank'          => $arr_data['bank']
                       ,'ac_owner'      => $arr_data['ac_owner']
                       ,'ac_cls'        => $arr_data['ac_cls']
                       ,'primary_yn'    => $arr_data['primary_yn']
                       ,'srt_dt'        => $arr_data['srt_dt']
                       ,'end_dt'        => $arr_data['end_dt']
                       ,'amt'           => $arr_data['amt']
                       ,'memo'          => $arr_data['memo']
                       ,'mnpl_usr_no'   => $_SESSION['usr_no']
                       ,'mnpl_ip'       => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'     => date("YmdHis")
                       );

        $result = $this->db->insert('tbb006i00', $i_data);

        //info_log("insert_tbc002l00", "last_query  = [" . $this->db->last_query() . "]");

        return $result;
    }

    public function update_tbb006i00_1($arr_data)
    {
        $u_data = array('ac_no'         => $arr_data['ac_no']
                       ,'bank'          => $arr_data['bank']
                       ,'ac_owner'      => $arr_data['ac_owner']
                       ,'ac_cls'        => $arr_data['ac_cls']
                       ,'primary_yn'    => $arr_data['primary_yn']
                       ,'srt_dt'        => $arr_data['srt_dt']
                       ,'end_dt'        => $arr_data['end_dt']
                       ,'amt'           => $arr_data['amt']
                       ,'memo'          => $arr_data['memo']
                       ,'mnpl_usr_no'   => $_SESSION['usr_no']
                       ,'mnpl_ip'       => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'     => date("YmdHis")
                       );

        $this->db->where('db_no    = ', $_SESSION['db_no']);
        $this->db->where('ac_srno  = ', $arr_data['ac_srno']);

        $result = $this->db->update('tbb006i00', $u_data);

        return $result;
    }


    public function update_tbb006i00_2($arr_data)
    {
        $u_data = array(
                        'del_yn'        => 'Y'
                       ,'mnpl_usr_no'   => $_SESSION['usr_no']
                       ,'mnpl_ip'       => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'     => date("YmdHis")
                       );

        $this->db->where('db_no    = ', $_SESSION['db_no']);
        $this->db->where('ac_srno  = ', $arr_data['ac_srno']);

        $result = $this->db->update('tbb006i00', $u_data);

        return $result;
    }


    public function get_ac_list($prcs_cls=null, $offset=null, $limit=null)
    {
        $db_no = array('0000000000', $_SESSION['db_no']);

        $this->db->select("a.ac_srno
                          ,a.ac_owner
                          ,b.clm_val_nm    ac_owner
                          ,a.ac_cls
                          ,c.clm_val_nm    ac_cls_nm
                          ,a.bank
                          ,d.clm_val_nm    bank_nm
                          ,a.ac_no
                          ,a.primary_yn
                          ,concat(substr(a.srt_dt, 1, 4), '-', substr(a.srt_dt, 5, 2), '-', substr(a.srt_dt, 7, 2))  srt_dt
                          ,concat(substr(a.end_dt, 1, 4), '-', substr(a.end_dt, 5, 2), '-', substr(a.end_dt, 7, 2))  end_dt
                          ,a.amt
                          ,a.memo
                          ,a.del_yn");
        $this->db->from('tbb006i00  a');
        $this->db->from('tba003i00  b');
        $this->db->from('tba003i00  c');
        $this->db->from('tba003i00  d');
        $this->db->where("a.db_no = ", $_SESSION['db_no']);
        $this->db->where("a.del_yn = 'N'");
        $this->db->where_in("b.db_no", $db_no);
        $this->db->where("b.clm_nm = 'USR'");
        $this->db->where("b.clm_val = a.ac_owner");
        $this->db->where_in("c.db_no", $db_no);
        $this->db->where("c.clm_nm = 'AC_CLS'");
        $this->db->where("c.clm_val = a.ac_cls");
        $this->db->where_in("d.db_no", $db_no);
        $this->db->where("d.clm_nm = 'BANK'");
        $this->db->where("d.clm_val = a.bank");
        $this->db->order_by("a.ac_owner, a.ac_cls, a.bank, a.ac_no");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();   // Produces: SELECT title, content, date FROM mytable
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_ac_list", "No Data Found!");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_ac_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_ac_list", "[SQL ERR] 계좌 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_ac_info($ac_srno)
    {
        $db_no = array('0000000000', $_SESSION['db_no']);

        $this->db->select("a.ac_srno
                          ,a.ac_no
                          ,a.bank
                          ,a.ac_owner
                          ,b.clm_val_nm    ac_owner_nm
                          ,a.ac_cls
                          ,c.clm_val_nm    ac_cls_nm
                          ,a.primary_yn
                          ,concat(substr(a.srt_dt, 1, 4), '-', substr(a.srt_dt, 5, 2), '-', substr(a.srt_dt, 7, 2))  srt_dt
                          ,concat(substr(a.end_dt, 1, 4), '-', substr(a.end_dt, 5, 2), '-', substr(a.end_dt, 7, 2))  end_dt
                          ,a.amt
                          ,a.memo
                          ,a.del_yn");
        $this->db->from('tbb006i00  a');
        $this->db->from('tba003i00  b');
        $this->db->from('tba003i00  c');
        $this->db->where("a.db_no = ", $_SESSION['db_no']);
        $this->db->where("a.ac_srno = ", $ac_srno);
        $this->db->where("a.del_yn = 'N'");
        $this->db->where_in("b.db_no", $db_no);
        $this->db->where("b.clm_nm = 'USR'");
        $this->db->where("b.clm_val = a.ac_owner");
        $this->db->where_in("c.db_no", $db_no);
        $this->db->where("c.clm_nm = 'AC_CLS'");
        $this->db->where("c.clm_val = a.ac_cls");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_ac_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_ac_info", "[SQL ERR] 계좌 정보 조회 오류!");
        }
        return $result;
    }

    public function get_ac_primary_dup_chk()
    {
        $this->db->select('ifnull(count(*), 0) cnt
                          ');
        $this->db->from('tbb006i00  a');
        $this->db->where('a.db_no  = ', $_SESSION['db_no']);
        $this->db->where("a.primary_yn  = 'Y'");
        $this->db->where("a.del_yn  = 'N'");

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_ac_cls_dup_chk", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_ac_cls_dup_chk", "[SQL ERR] 주거래계좌 중복 조회 오류!");
        }

        return $result;
    }

    public function get_ac_bal($stnd_yymm, $prcs_cls=null, $offset=null, $limit=null)
    {
        $stnd_dt = $stnd_yymm . '31';

        $sql = "select  a.ac_srno
                       ,a.ac_no
                       ,a.bank
                       ,a.bank_nm
                       ,a.ac_owner
                       ,a.ac_owner_nm
                       ,a.ac_cls
                       ,a.ac_cls_nm
                       ,a.balance
                 from  (select  a.ac_srno
                               ,a.ac_no
                               ,a.bank
                               ,b.clm_val_nm   bank_nm
                               ,a.ac_owner
                               ,c.clm_val_nm   ac_owner_nm
                               ,a.ac_cls
                               ,d.clm_val_nm   ac_cls_nm
                               ,a.amt
                               ,a.io_amt
                               ,sum(a.balance) balance
                          from  (
                                select  a.ac_srno
                                       ,a.ac_no
                                       ,a.bank
                                       ,a.ac_owner
                                       ,a.ac_cls
                                       ,a.amt
                                       ,ifnull(sum(b.in_amt) - sum(b.out_amt), 0)          io_amt
                                       ,a.amt + ifnull(sum(b.in_amt) - sum(b.out_amt), 0)  balance
                                  from  tbb006i00  a
                                        left join
                                        (select  a.db_no
                                                ,a.dt
                                		        ,case when a.io_tr_cls like '1%' then a.amt end  in_amt
                                                ,case when a.io_tr_cls like '2%' then a.amt end  out_amt
                                         from  tbb003l00  a
                                        where  a.del_yn = 'N')  b  on b.db_no = a.db_no and b.dt >= a.srt_dt and b.dt <= ?
                                 where  a.db_no = ?
                                   and  a.ac_cls = '1'
                                   and  a.srt_dt <= ?
                                   and  a.primary_yn = 'Y'
                                   and  a.del_yn = 'N'
                                 group by  a.ac_srno
                                          ,a.ac_no
                                          ,a.ac_cls
                                          ,a.amt
                                 union all
                                select  a.ac_srno
                                       ,a.ac_no
                                       ,a.bank
                                       ,a.ac_owner
                                       ,a.ac_cls
                                       ,a.amt
                                       ,0         io_amt
                                       ,a.amt     balance
                                  from  tbb006i00  a
                                 where  a.db_no = ?
                                   and  a.ac_cls = '2'
                                   and  a.srt_dt <= ?
                                   and  a.del_yn = 'N'
                                 union all
                                select  a.ac_srno
                                       ,a.ac_no
                                       ,a.bank
                                       ,a.ac_owner
                                       ,a.ac_cls
                                       ,a.amt
                                       ,ifnull(c.saving_amt, 0)           io_amt
                                       ,a.amt + ifnull(c.saving_amt, 0)   balance
                                  from  tbb006i00  a
                                        left join
                                        (select  a.db_no
                                		        ,a.rel_ac_no
                                                ,sum(b.amt)       saving_amt
                                           from  tbb005l00  a
                                                ,tbb001l00  b
                                          where  a.db_no = ?
                                            and  a.del_yn = 'N'
                                            and  a.expns_cls = '20120'
                                            and  b.db_no = a.db_no
                                            and  b.expns_cls = a.expns_cls
                                            and  b.fix_expns_srno = a.fix_expns_srno
                                            and  b.del_yn = 'N'
                                          group by  a.db_no
                                                   ,a.rel_ac_no)  c on c.db_no = a.db_no and c.rel_ac_no = a.ac_no
                                 where  a.ac_cls = '3'
                                   and  a.srt_dt <= ?
                                   and  a.del_yn = 'N'
                                )  a
                               ,tba003i00  b
                               ,tba003i00  c
                               ,tba003i00  d
                        where  b.db_no = ?
                          and  b.clm_nm = 'BANK'
                          and  b.clm_val = a.bank
                          and  c.db_no = b.db_no
                          and  c.clm_nm = 'USR'
                          and  c.clm_val = a.ac_owner
                          and  d.db_no = '0000000000'
                          and  d.clm_nm = 'AC_CLS'
                          and  d.clm_val = a.ac_cls
                          group by  a.ac_srno
                                   ,a.ac_no
                                   ,a.bank
                                   ,b.clm_val_nm
                                   ,a.ac_owner
                                   ,c.clm_val_nm
                                   ,a.ac_cls
                                   ,d.clm_val_nm
                                  with rollup
                       )  a
               where  a.ac_cls_nm is not null or ac_srno is NULL
               order by ifnull(a.ac_owner, '99')
                       ,a.ac_cls
                       ,a.ac_srno";

        if (isset($limit) && isset($offset)) {
            $sql = $sql . " limit " .  $offset . ", " . $limit;
        }

        $query = $this->db->query($sql, array($stnd_dt
                                             ,$_SESSION['db_no']
                                             ,$stnd_dt
                                             ,$_SESSION['db_no']
                                             ,$stnd_dt
                                             ,$_SESSION['db_no']
                                             ,$stnd_dt
                                             ,$_SESSION['db_no'] ));

        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_ac_balance", "last_query  = [" . $this->db->last_query() . "]");
            info_log("get_ac_balance", "No Data Found!");
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_ac_balance", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_ac_balance", "[SQL ERR] 계좌 잔고 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_continue_rsvt_yn($rsv_srno)
    {
        // 2022.03.17. 예약연장 예약건 여부 확인
        $sql = "SELECT  concat(substr(a.cnfm_dt, 5, 2), '/', substr(a.cnfm_dt, 7, 2))  stnd_cnfm_dt
                       ,truncate(a.amt / 10000, 0) amt
                       ,a.hsrm_cls
                       ,b.srt_dt
                       ,a.end_dt
                       ,concat(substr(b.srt_dt, 1, 4), '-', substr(b.srt_dt, 5, 2), '-', substr(b.srt_dt, 7, 2))  stnd_srt_dt
                       ,concat(substr(a.g_end_dt, 1, 4), '-', substr(a.g_end_dt, 5, 2), '-', substr(a.g_end_dt, 7, 2))  stnd_g_end_dt
                       ,DATEDIFF(a.g_end_dt, a.srt_dt)  stay_cnt
                  FROM  (SELECT  db_no
                                ,cnfm_dt
                                ,gst_no
                                ,hsrm_cls
                                ,srt_dt
                                ,end_dt
                                ,date_format(adddate(srt_dt, -1), '%Y%m%d')  bef_end_dt
                                ,date_format(ADDDATE(end_dt,  1), '%Y%m%d')  g_end_dt
                                ,amt
                           FROM  tba005l00
                          WHERE  db_no = ?
                            AND  rsv_srno = ?
                        )  a
                        ,tba005l00  b
                  WHERE  b.db_no = a.db_no
                    AND  b.gst_no = a.gst_no
                    AND  b.hsrm_cls = a.hsrm_cls
                    AND  b.end_dt = a.bef_end_dt";

        $query = $this->db->query($sql, array($_SESSION['db_no']
                                            , $rsv_srno));

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        info_log("get_continue_rsvt_yn", "query_cnt = " . $query->num_rows());

        if ($query->num_rows() == 0)
        {
            info_log("get_ac_list", "No Data Found!");
            return;
        }
        else
        {
            if (!$result) {
                info_log("get_continue_rsvt_yn", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("get_continue_rsvt_yn", "[SQL ERR] 예약 연장 여부 체크 SQL 오류!");
            } else {
                info_log("get_continue_rsvt_yn", "rsv_srno = " . $rsv_srno);
                info_log("get_continue_rsvt_yn", "query_cnt = " . $query->num_rows());
            }
        }

        return $result;
    }

    /*=====================================================================================================================*/
    /* staym REST Api
    /*=====================================================================================================================*/
    public function check_auth_client($client_id, $auth_key)
    {
        $l_client_id = "staym";
        $l_auth_key  = "*7E172853D0E2BC2AC1695048864F4EF3416101A1";

        //info_log("check_auth_client", "l_client_id = [" . $l_client_id . "]");
        //info_log("check_auth_client", "client_id   = [" . $client_id . "]");
        //info_log("check_auth_client", "l_auth_key  = [" . $l_auth_key . "]");
        //info_log("check_auth_client", "auth_key    = [" . $auth_key . "]");

        if ($client_id == $l_client_id && $auth_key == $l_auth_key) {
            return true;
        } else {
            return false;
        }
    }
}
