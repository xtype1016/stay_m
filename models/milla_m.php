<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Milla_m extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function get_clm_sr_val($mt_nm)
    {
        $db_no = "";
        $ip_addr = "";
        $nxt_val  = "";

        //if (strncmp($mt_nm, "USR_NO", 6) == 0) {
        //    $db_no = '0000000000';
        //} else {
        //    $db_no = $_SESSION['db_no'];
        //}

        if (isset($_SESSION['usr_no']) > 0) {
            $usr_no = $_SESSION['usr_no'];
        } else {
            $usr_no = '0000000';
        }

        if (isset($_SESSION['ip_addr']) > 0) {
            $ip_addr = $_SESSION['ip_addr'];
        } else {
            $ip_addr = get_ip();
        }

        $this->db->select('mt_nm
                          ,mt_val
                          ');
        $this->db->from('milla002i00');
        $this->db->where('mt_nm =', $mt_nm);

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$q_result = $query->result();  // 객체 $result->board_id
        //$q_result = $query->result_array();  //배열 $result['board_id']
        $q_result = $query->row();  // 단건, 객체 $result->board_id

        if (!$q_result) {
            if ($query->num_rows() == 0) {
                // initial value setting

                if (strcmp($mt_nm, "USR_NO") == 0) {
                    $nxt_val = '0000001';
                } else if (strcmp($mt_nm, "CMPNY_CLS") == 0) {
                    $nxt_val = '01';
                } else if (strncmp($mt_nm, "CTGR_CLS", 8) == 0) {
                    $nxt_val = '001';
                //} elseif (strncmp($mt_nm, "HSRM_CLS", 8) == 0) {
                //    $nxt_val = '01';
                //} elseif (strncmp($mt_nm, "TR_CLS", 6) == 0) {
                //    $nxt_val = '21';
                //} elseif (strncmp($mt_nm, "EXPNS_CLS", 9) == 0) {
                //    $nxt_val = '01';
                //} elseif (strncmp($mt_nm, "EXPNS_CHNL_CLS", 14) == 0) {
                //    $nxt_val = '02';
                } else {
                    $nxt_val = '1';
                    //echo "초기값이 정의되지 않았습니다![" . $mt_nm . "]<br>";
                    //alert_log("milla/get_clm_sr_val", "초기값이 정의되지 않았습니다![" . $mt_nm . "]");
                }

                info_log("milla/get_clm_sr_val/initial", "mt_nm    = [" . $mt_nm . "]");
                info_log("milla/get_clm_sr_val/initial", "nxt_val  = [" . $nxt_val . "]");

                $i_data = array( 'mt_nm'       => $mt_nm
                                ,'mt_val'      => $nxt_val
                                ,'mnpl_usr_no' => $usr_no
                                ,'mnpl_ip'     => $ip_addr
                                ,'mnpl_ymdh'   => date("YmdHis")
                               );

                $i_result = $this->db->insert('milla002i00', $i_data);

                if (!$i_result) {
                    //$sql_result = $this->db->error();

                    info_log("milla/get_clm_sr_val", "last_query  = [" . $this->db->last_query() . "]");
                    //info_log("milla/get_clm_sr_val", "sqlcode  = [" . $sql_result['code'] . "]");
                    //info_log("milla/get_clm_sr_val", "sqlcode  = [" . $sql_result['message'] . "]");
                    alert_log("milla/get_clm_sr_val", "초기값 생성 오류!(INSERT)[" . $mt_nm . "]");
                }
            } else {
                info_log("milla/get_clm_sr_val", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("milla/get_clm_sr_val", "[SQL ERR] 사용자 일련번호 조회 오류!");
            }
        } else {
            //print_r ($q_result);
            //echo "q_result->mt_val = [" . $q_result[0]->mt_val . "]<br>";

            if (strcmp($mt_nm, "USR_NO") == 0) {
                $nxt_val = sprintf("%07d", (int)$q_result->mt_val + 1);
            } else if (strcmp($mt_nm, "CMPNY_CLS") || strncmp($mt_nm, "LCTGR_CLS", 9) == 0) {
                $nxt_val = sprintf("%02d", (int)$q_result->mt_val + 1);
            } else if (strncmp($mt_nm, "CTGR_CLS", 8) == 0) {
                $nxt_val = sprintf("%03d", (int)$q_result->mt_val + 1);
            }
            //} elseif (strncmp($mt_nm, "TR_CLS", 6) == 0          || strncmp($mt_nm, "EXPNS_CLS", 6) == 0 ||
            //         strncmp($mt_nm, "EXPNS_CHNL_CLS", 14) == 0 || strncmp($mt_nm, "HSRM_CLS", 8) == 0  ||
            //         strncmp($mt_nm, "IO_TR_CLS", 9) == 0
            //        ) {
            //    if (strncmp($q_result->mt_val, "99", 2) == 0) {
            //        alert_log("milla/get_clm_sr_val", "더 이상 생성할 수 없습니다!(" .  $mt_nm . " 최대값 도달)");
            //    } else {
            //        $nxt_val = sprintf("%02d", (int)$q_result->mt_val + 1);
            //    }

            //    info_log("milla/get_clm_sr_val", "nxt_val  = [" . $nxt_val . "]");
            //}
            //else if (strncmp($mt_nm, "EXPNS_CLS", 9) == 0)
            //{
            //    if (strncmp($q_result->mt_val, "9999", 2) == 0)
            //    {
            //        alert_log("milla/get_clm_sr_val", "더 이상 생성할 수 없습니다!(" .  $mt_nm . " 최대값 도달)");
            //    }
            //    else
            //    {
            //        $nxt_val = sprintf("%04d", (int)$q_result->mt_val + 1);
            //    }
            //
            //    //info_log("milla/get_clm_sr_val", "nxt_val  = [" . $nxt_val . "]");
            //}
            else {
                //$nxt_val = sprintf("%d", (int)$q_result->mt_val + 1);
                $nxt_val = sprintf("%03d", (int)$q_result->mt_val + 1);
            }

            info_log("milla/get_clm_sr_val", "mt_nm    = [" . $mt_nm . "]");
            info_log("milla/get_clm_sr_val", "nxt_val  = [" . $nxt_val . "]");

            $u_data = array(
                            'mt_val'      => $nxt_val
                           ,'mnpl_usr_no' => $_SESSION['usr_no']
                           ,'mnpl_ip'     => $ip_addr
                           ,'mnpl_ymdh'   => date("YmdHis")
                           );
            $this->db->where('mt_nm =', $mt_nm);

            $u_result = $this->db->update('milla002i00', $u_data);

            if (!$u_result) {
                info_log("milla/get_clm_sr_val", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("milla/get_clm_sr_val", "다음 일련번호 생성 오류!(UPDATE CHK1)[" . $mt_nm . "]");
            }

            $prcs_cnt = $this->db->affected_rows();

            if ($prcs_cnt != 1) {
                info_log("milla/get_clm_sr_val", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("milla/get_clm_sr_val", "다음 일련번호 생성 오류!(Update Cnt ERR! prcs_cnt=, " . $prcs_cnt . ")");
            }
        }

        return $nxt_val;
    }

    public function get_usr_dup_chk($usr_id)
    {
        $this->db->select('count(*) cnt
                          ');
        $this->db->from('milla001i00  a');
        $this->db->where('a.usr_id = ', $usr_id);

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            if ($query->num_rows() == 0) {
                info_log("milla/get_usr_dup_chk", "No Data Found!(mt_nm=" . $mt_nm . ", mt_val=" . $mt_val . ")");
            } else {
                info_log("milla/get_usr_dup_chk", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("milla/get_usr_dup_chk", "[SQL ERR] User ID 조회 오류!");
            }
        }

        return $result;
    }


    public function insert_milla001i00($arr_data)
    {
        $i_data = array('usr_no'     => $arr_data['usr_no']
                       ,'usr_id'     => $arr_data['usr_id']
                       ,'pswd'       => $arr_data['pswd']
                       ,'mnpl_ip'    => $arr_data['ip_addr']
                       ,'mnpl_ymdh'  => date("YmdHis")
                       );

        $result = $this->db->insert('milla001i00', $i_data);

        return $result;
    }


    public function get_usr_info($usr_id)
    {
        //$input_client_id = $this->input->get_request_header('Client-ID', TRUE);

        $this->db->select("a.usr_no
                          ,a.usr_id
                          ,a.pswd
                          ,'0000001'  db_no
                          ");
        $this->db->from('milla001i00  a');
        $this->db->where('a.usr_id = ', $usr_id);

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            $result_rows = $query->num_rows();
            if ($result_rows == 0) {
                alert_log("milla/get_usr_info", "존재하지않는 사용자ID 입니다!(" . $usr_id . ")");

            //if ($client_id == $input_client_id)
                //{
                //    return array('status' => 204,'message' => 'Username not found.');
                //}
                //else
                //{
                //    alert_log("milla/get_usr_info", "존재하지않는 사용자ID 입니다!(" . $usr_id . ")");
                //}
            } else {
                info_log("milla/get_usr_info", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("milla/get_usr_info", "[SQL ERR] 사용자 정보 조회 오류!");
            }
        }

        return $result;
    }

    public function insert_milla001i01($arr_data)
    {
        //$i_data = array('usr_no'       => $arr_data['usr_no']
        //               ,'idntfr'       => $arr_data['idntfr']
        //               ,'tkn'          => $arr_data['tkn']
        //               ,'mnpl_ip'      => $arr_data['ip_addr']
        //               ,'mnpl_ymdh'    => date("YmdHis")
        //               ,'expired_ymdh' => date("YmdHis", strtotime("+7 days"))
        //               );
        //
        //$result = $this->db->insert('milla001i01', $i_data);

        $sql = "insert into milla001i01 values (?
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
            info_log("milla/insert_milla001i01", "last_query  = [" . $this->db->last_query() . "]");
            return $result;
        }

        return $result;
    }


    public function update_milla001i01_1($arr_data)
    {
        $u_data = array('tkn'          => $arr_data['tkn']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       ,'expired_ymdh' => date("YmdHis", strtotime("+7 days"))
                       );
        $this->db->where('usr_no = ', $arr_data['usr_no']);
        $this->db->where('idntfr = ', $arr_data['idntfr']);

        $result = $this->db->update('milla001i01', $u_data);

        return $result;
    }


    public function delete_milla001i01_1($arr_data)
    {
        $this->db->where('usr_no = ', $arr_data['usr_no']);
        $this->db->where('idntfr = ', $arr_data['idntfr']);

        $result = $this->db->delete('milla001i01');

        return $result;
    }


    public function delete_milla001i01_2($usr_no)
    {
        $this->db->where('usr_no = ', $usr_no);
        $this->db->where('expired_ymdh <= adddate(now(), -8)');

        $result = $this->db->delete('milla001i01');

        return $result;
    }


    public function insert_milla001i03($arr_data)
    {
        $i_data = array('usr_no'     => $arr_data['usr_no']
                       ,'idntfr'     => $arr_data['idntfr']
                       ,'result'     => $arr_data['result']
                       ,'login_ip'   => $arr_data['ip_addr']
                       ,'login_ymdh' => date("YmdHis")
                       );

        $result = $this->db->insert('milla001i03', $i_data);

        return $result;
    }

    public function get_usr_tkn($usr_no, $idntfr)
    {
        $this->db->select("a.usr_no
                          ,a.usr_id
                          ,b.tkn
                          ,b.expired_ymdh
                          ,'0000001'  db_no
                          ");
        $this->db->from('milla001i00  a');
        $this->db->join('milla001i01  b', 'b.usr_no = a.usr_no', 'left');
        $this->db->where('a.usr_no = ', $usr_no);
        $this->db->where('b.idntfr  = ', $idntfr);

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        //$sql_result = $this->db->error();

        //info_log("milla/get_usr_tkn", "num_rows = [" . (string)$query->num_rows() . "]");

        if (!$result) {
            if ($query->num_rows() == 0) {
                info_log("milla/get_usr_tkn", "No Data Found(usr_no=" . $usr_no . ", idntfr=" . $idntfr . ")");
            } else {
                info_log("milla/get_usr_tkn", "last_query  = [" . $this->db->last_query() . "]");
                info_log("milla/get_usr_tkn", "[SQL ERR] 사용자 토큰 조회 오류!");
            }
        }

        return $result;
    }

    public function insert_milla003i00($arr_data)
    {
        $i_data = array('mt_nm'       => $arr_data['mt_nm']
                       ,'mt_val'      => $arr_data['mt_val']
                       ,'mt_kor_nm'   => $arr_data['mt_kor_nm']
                       ,'addtn_info'  => $arr_data['addtn_info']
                       ,'othr_info'   => $arr_data['othr_info']
                       ,'order_info'  => $arr_data['order_info']
                       ,'memo'        => $arr_data['memo']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $result = $this->db->insert('milla003i00', $i_data);

        return $result;
    }

    public function get_meta_list($mt_nm, $mt_val, $prcs_cls, $offset=null, $limit=null)
    {
        $this->db->select("a.mt_nm
                          ,a.mt_val
                          ,a.mt_kor_nm
                          ,a.addtn_info
                          ,a.othr_info
                          ,a.order_info
                          ,a.memo
                          ");
        $this->db->from('milla003i00  a');
        $this->db->where("a.del_yn = 'N'");
        $this->db->where("a.mt_nm = ", $mt_nm);
        $this->db->like('a.mt_val', $mt_val, 'after');
        $this->db->order_by("ifnull(a.order_info, a.mt_val)");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();   // Produces: SELECT title, content, date FROM mytable
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_meta_list", "No Data Found!");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_meta_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_meta_list", "[SQL ERR] 메타 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }

    public function get_ctgr_list($cmpny_cls, $mt_nm, $othr_info, $prcs_cls, $offset=null, $limit=null)
    {

        $join_cndtn = "b.mt_nm = 'LCTGR_CLS_" . $cmpny_cls . "' and b.mt_val = a.othr_info";

        //info_log("get_ctgr_list", "join_cndtn = [" . $join_cndtn . "]");

        $this->db->select("a.mt_nm
                          ,a.mt_val
                          ,a.mt_kor_nm
                          ,a.addtn_info
                          ,a.othr_info
                          ,a.order_info
                          ,a.memo
                          ,b.mt_val     lctgr_cls
                          ,b.mt_kor_nm  lctgr_nm
                          ,b.addtn_info cmpny_cls
                          ");
        $this->db->from('milla003i00  a');
        $this->db->join('milla003i00  b', $join_cndtn, 'left');
        $this->db->where("a.del_yn = 'N'");
        $this->db->where("a.mt_nm = ", $mt_nm);
        $this->db->where("a.othr_info = ", $othr_info);
        $this->db->order_by("ifnull(a.order_info, a.mt_val)");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();   // Produces: SELECT title, content, date FROM mytable
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_ctgr_list", "No Data Found!");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_ctgr_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_ctgr_list", "[SQL ERR] 분류 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }


    public function get_meta_info($cmpny_cls, $mt_nm, $mt_val)
    {
        $join_cndtn = "b.mt_nm = 'LCTGR_CLS_" . $cmpny_cls . "' and b.mt_val = a.othr_info";

        //info_log("get_meta_info", "join_cndtn = [" . $join_cndtn . "]");

        $this->db->select("a.mt_nm
                          ,a.mt_val
                          ,a.mt_kor_nm
                          ,a.addtn_info
                          ,a.othr_info
                          ,a.order_info
                          ,a.memo
                          ,b.mt_val     lctgr_cls
                          ,b.mt_kor_nm  lctgr_nm
                          ,b.addtn_info cmpny_cls
                          ");
        $this->db->from('milla003i00  a');
        $this->db->join('milla003i00  b', $join_cndtn, 'left');
        $this->db->where("a.mt_nm  = ", $mt_nm);
        $this->db->where("a.mt_val = ", $mt_val);
        $this->db->where("a.del_yn    = 'N'");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_meta_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_meta_info", "[SQL ERR] 메타 조회 오류!");
        }

        return $result;
    }

    public function get_ctgr_use_chk($addtn_info)
    {
        $this->db->select('count(*) cnt
                          ');
        $this->db->from('milla004l00  a');
        $this->db->like('a.itm_cd', $addtn_info, 'after');

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            if ($query->num_rows() == 0) {
                info_log("milla/get_ctgr_use_chk", "No Data Found!(addtn_info = " . $addtn_info . ")");
            } else {
                info_log("milla/get_ctgr_use_chk", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("milla/get_ctgr_use_chk", "[SQL ERR] 분류코드 사용여부 조회 오류!");
            }
        }

        return $result;
    }

    public function update_milla003i00_1($arr_data)
    {
        $u_data = array('mt_kor_nm'   => $arr_data['mt_kor_nm']
                       ,'addtn_info'  => $arr_data['addtn_info']
                       ,'othr_info'   => $arr_data['othr_info']
                       ,'order_info'  => $arr_data['order_info']
                       ,'memo'        => $arr_data['memo']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $this->db->where('mt_nm   = ', $arr_data['mt_nm']);
        $this->db->where('mt_val  = ', $arr_data['mt_val']);

        $result = $this->db->update('milla003i00', $u_data);

        return $result;
    }

    public function update_milla003i00_2($arr_data)
    {
        $u_data = array(
                        'del_yn'       => 'Y'
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

       $this->db->where('mt_nm   = ', $arr_data['ori_mt_nm']);
       $this->db->where('mt_val  = ', $arr_data['ori_mt_val']);

        $result = $this->db->update('milla003i00', $u_data);

        return $result;
    }

    public function insert_milla004l00($arr_data)
    {
        $i_data = array('itm_cd'      => $arr_data['itm_cd']
                       ,'itm_nm'      => $arr_data['itm_nm']
                       ,'color_nm'    => $arr_data['color_nm']
                       ,'material'    => $arr_data['material']
                       ,'size'        => $arr_data['size']
                       ,'wt'          => $arr_data['wt']
                       ,'in_prc'      => $arr_data['in_prc']
                       ,'ot_prc'      => $arr_data['ot_prc']
                       ,'memo'        => $arr_data['memo']
                       ,'sku_id'      => $arr_data['sku_id']
                       ,'bar_cd'      => $arr_data['bar_cd']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $result = $this->db->insert('milla004l00', $i_data);

        return $result;
    }

    public function get_itm_list($cmpny_cls, $lctgr_cls, $ctgr_cls, $prcs_cls, $offset=null, $limit=null)
    {

        $lctgr_nm = 'LCTGR_CLS_' . $cmpny_cls;
        $ctgr_nm  = 'CTGR_CLS_' . $cmpny_cls;

        if (strcmp($lctgr_cls, 'all') == 0)
        {
            $lctgr_cls = '';
        }

        if (strcmp($ctgr_cls, 'all') == 0)
        {
            $ctgr_cls = '';
        }

        $this->db->select("c.addtn_info  cmpny_cls
                          ,a.itm_cd
                          ,a.old_itm_cd
                          ,a.itm_nm
                          ,a.color_nm
                          ,a.material
                          ,a.size
                          ,a.wt
                          ,a.in_prc
                          ,a.ot_prc
                          ,a.memo
                          ,a.sku_id
                          ,a.bar_cd
                          ");
        $this->db->from('milla004l00  a');
        $this->db->from('milla003i00  b');
        $this->db->from('milla003i00  c');
        $this->db->where("a.del_yn = 'N'");
        $this->db->where("b.mt_nm  = ", $ctgr_nm);
        $this->db->where("b.addtn_info = substr(a.itm_cd, 1, 4)");
        $this->db->where("c.mt_nm  = ", $lctgr_nm);
        $this->db->where("c.mt_val = b.othr_info");
        $this->db->where("c.addtn_info = ", $cmpny_cls);
        $this->db->like('c.mt_val', $lctgr_cls, 'after');
        $this->db->like('b.mt_val', $ctgr_cls, 'after');
        $this->db->order_by("a.itm_cd");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();   // Produces: SELECT title, content, date FROM mytable
        $result = $query->num_rows();

        if ($result == 0) {
            info_log("get_itm_list", "No Data Found!");
            return;
        } else {
            if ($prcs_cls == 'data') {
                $result = $query->result();  // 객체 $result->board_id

                if (!$result) {
                    info_log("get_itm_list", "last_query  = [" . $this->db->last_query() . "]");
                    alert_log("get_itm_list", "[SQL ERR] 상품 리스트 조회 오류!");
                }
            }
        }

        return $result;
    }

    public function get_itm_info($itm_cd)
    {
        $this->db->select("a.itm_cd
                          ,a.old_itm_cd
                          ,right(b.mt_nm, 2)  cmpny_cls
                          ,b.othr_info        lctgr_cls
                          ,b.mt_val           ctgr_cls
                          ,a.itm_nm
                          ,a.color_nm
                          ,a.material
                          ,a.size
                          ,a.wt
                          ,a.in_prc
                          ,a.ot_prc
                          ,a.memo
                          ,a.sku_id
                          ,a.bar_cd
                          ");
        $this->db->from('milla004l00  a');
        $this->db->from('milla003i00  b');
        $this->db->where("a.itm_cd    = ", $itm_cd);
        $this->db->where("a.del_yn    = 'N'");
        $this->db->where("b.addtn_info = substr(a.itm_cd, 1, 4)");
        $this->db->limit(1);

        $query = $this->db->get();

        //$result = $query->result();  // 객체 $result->board_id
        //$result = $query->result_array();  //배열 $result['board_id']
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            info_log("get_itm_info", "last_query  = [" . $this->db->last_query() . "]");
            alert_log("get_itm_info", "[SQL ERR] 상품 조회 오류!");
        }

        return $result;
    }

    public function update_milla004l00_1($arr_data)
    {
        $u_data = array('itm_nm'      => $arr_data['itm_nm']
                       ,'color_nm'    => $arr_data['color_nm']
                       ,'material'    => $arr_data['material']
                       ,'size'        => $arr_data['size']
                       ,'wt'          => $arr_data['wt']
                       ,'in_prc'      => $arr_data['in_prc']
                       ,'ot_prc'      => $arr_data['ot_prc']
                       ,'memo'        => $arr_data['memo']
                       ,'sku_id'      => $arr_data['sku_id']
                       ,'bar_cd'      => $arr_data['bar_cd']
                       ,'mnpl_usr_no' => $_SESSION['usr_no']
                       ,'mnpl_ip'     => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'   => date("YmdHis")
                       );

        $this->db->where('itm_cd     = ', $arr_data['itm_cd']);

        $result = $this->db->update('milla004l00', $u_data);

        return $result;
    }

    public function update_milla004l00_2($arr_data)
    {
        $u_data = array(
                        'del_yn'       => 'Y'
                       ,'mnpl_usr_no'  => $_SESSION['usr_no']
                       ,'mnpl_ip'      => $_SESSION['ip_addr']
                       ,'mnpl_ymdh'    => date("YmdHis")
                       );

        $this->db->where('itm_cd     = ', $arr_data['itm_cd']);

        $result = $this->db->update('milla004l00', $u_data);

        return $result;
    }

    public function get_ctgr_exist_chk($addtn_info)
    {
        $this->db->select('count(*) cnt
                          ');
        $this->db->from('milla003i00  a');
        $this->db->like('a.addtn_info', $addtn_info, 'after');

        $query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

        //$result = $query->result();  // 객체 $result->board_id
        $result = $query->row();  // 단건, 객체 $result->board_id

        if (!$result) {
            if ($query->num_rows() == 0) {
                info_log("milla/get_ctgr_exist_chk", "No Data Found!(addtn_info = " . $addtn_info . ")");
            } else {
                info_log("milla/get_ctgr_exist_chk", "last_query  = [" . $this->db->last_query() . "]");
                alert_log("milla/get_ctgr_exist_chk", "[SQL ERR] 분류코드 사용여부 조회 오류!");
            }
        }

        return $result;
    }

}
