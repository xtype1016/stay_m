<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
    수입(income) 조회
*/

class Time_mng extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $this->load->model('stay_m');
        $this->load->library('form_validation');
        //$this->load->library('session');
        $this->load->helper('url');
        //$this->load->helper('cookie');
        //$this->load->helper('My_alert_log');

        $usr_no = get_cookie('usr_no');
        $idntfr = get_cookie('idntfr');

        if (!isset($_SESSION['usr_no']) && strlen($usr_no) > 0 && strlen($idntfr) > 0) {
            info_log("time_mng", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->smmry();
        redirect('time_mng/list', 'refresh');
    }

    /*
        사이트 헤더, 푸터 추가
    */
    public function _remap($method, $params = array())
    {
        // Header
        $this->load->view('header_v');

        if (method_exists($this, $method)) {
            //$this->{"{$method}"}();
            call_user_func_array(array($this, $method), $params);
        }

        // footer
        $this->load->view('footer_v');
    }


    public function list()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("time_mng/list", "================================================================================");
        info_log("time_mng/list", "아이들 시간관리 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = $page_url = '';

        $stnd_dt = str_replace('-', '', $this->uri->segment(3));
        $usr = $this->uri->segment(4);
        $time_cls = $this->uri->segment(5);
        $view_cls = $this->uri->segment(6);

        //info_log("time_mng/list", "stnd_yymm = [" . $stnd_yymm . "]");

        $uri_segment = 8;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_dt)) {
            //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
            $stnd_dt = date("Ymd");
        }

        if (empty($view_cls)) {
            $view_cls = '1';
        }

        //info_log("time_mng/list", "stnd_dt   = [" . $stnd_dt . "]");
        //info_log("time_mng/list", "usr       = [" . $usr . "]");
        //info_log("time_mng/list", "time_cls  = [" . $time_cls . "]");
        //info_log("time_mng/list", "view_cls  = [" . $view_cls . "]");

        if (strcmp($view_cls, "1") == 0) {
            $dt_fr = $stnd_dt;
            $dt_to = $stnd_dt;
        } elseif (strcmp($view_cls, "2") == 0) {
            $result = $this->stay_m->get_week_fr_to($stnd_dt);
            $dt_fr = $result[0]->dt_fr;
            $dt_to = $result[0]->dt_to;
        } else {
            alert_log("time_mng/list", "일별 또는 주별을 선택해 주십시요!");
        }

        if (empty($usr)) {
            $usr = "all";
        }

        if (empty($time_cls)) {
            $time_cls = "all";
        }

        $bef_uri = uri_string();
        info_log("time_mng/list", "bef_uri  = [" . $bef_uri . "]");
        // bef_list 세션데이터 초기화
        unset($_SESSION['bef_uri']);
        $this->session->set_userdata('bef_uri', $bef_uri);

        // Pagination 용 주소
        $page_url = '/' . $stnd_dt . '/' . $usr . '/' . $time_cls . '/' . $view_cls;

        $data['usr']      = $usr;
        $data['time_cls'] = $time_cls;

        if (strcmp($usr, "all") == 0) {
            $usr = "%";
        }

        if (strcmp($time_cls, "all") == 0) {
            $time_cls = "%";
        }


        // 페이지네이션 설정
        $config['base_url']         = '/time_mng/list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_time_mng_list($dt_fr, $dt_to, $usr, $time_cls, 'rowcnt');         // 표시할 게시물 총 수
        $config['per_page']         = 5;             // 한 페이지에 표시할 게시물 수
        $config['uri_segment']      = $uri_segment;  // 페이지번호가 위치한 세그먼트
        $config['num_links']        = 3;             // 선택된 페이지번호 좌우로 몇 개의 숫자 링크를 보여줄 지 설정
        $config['use_page_numbers'] = true;          // 링크를 1, 2, 3 으로 표기
        $config['first_link']       = '<<';          // 처음으로 링크 생성
        $config['last_link']        = '>>';          // 끝으로 링크 생성
        $config['next_link']        = '>';
        $config['prev_link']        = '<';

        $config['full_tag_open']    = "<nav><ul class='pagination pagination-sm'>";
        $config['full_tag_close']   = '</ul></nav>';
        $config['num_tag_open']     = '<li>';
        $config['num_tag_close']    = '</li>';
        $config['cur_tag_open']     = "<li class='active'><a href='#'>";
        $config['cur_tag_close']    = '</a></li>';
        $config['next_tag_open']    = '<li>';
        $config['next_tag_close']   = '</li>';
        $config['prev_tag_open']    = '<li>';
        $config['prev_tag_close']   = '</li>';
        $config['first_tag_open']   = '<li>';
        $config['first_tag_close']  = '</li>';
        $config['last_tag_open']    = '<li>';
        $config['last_tag_close']   = '</li>';

        // 페이지네이션 초기화
        $this->pagination->initialize($config);

        // 페이징 링크 생성
        $data['pagination'] = $this->pagination->create_links();

        // 게시물 목록을 불러오기 위한 offset, limit 값 가져오기
        //$page = $this->uri->segment(3, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        $page = $this->uri->segment($uri_segment, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        $start = ($page - 1) * $config['per_page'];
        $limit = $config['per_page'];

        if ($config['total_rows'] > 0) {
            $data['time_mng_list'] = $this->stay_m->get_time_mng_list($dt_fr, $dt_to, $usr, $time_cls, 'data', $start, $limit);
        }

        $data['stnd_dt'] = substr($stnd_dt, 0, 4) . "-" . substr($stnd_dt, 4, 2) . "-" . substr($stnd_dt, 6, 2);

        $usr_list = $this->stay_m->get_list('USR', 'Y', '2');

        $t_usr_list = new stdClass();
        $t_usr_list->clm_nm     = 'USR';
        $t_usr_list->clm_val    = 'all';
        $t_usr_list->clm_val_nm = '전체';
        $t_usr_list->othr_info  = '';

        array_unshift($usr_list, $t_usr_list);

        $data['usr_list'] = $usr_list;

        $time_cls_list = $this->stay_m->get_list('TIME_CLS', 'Y');

        $t_time_cls_list = new stdClass();
        $t_time_cls_list->clm_nm     = 'TIME_CLS';
        $t_time_cls_list->clm_val    = 'all';
        $t_time_cls_list->clm_val_nm = '전체';
        $t_time_cls_list->othr_info  = '';

        array_unshift($time_cls_list, $t_time_cls_list);

        $data['time_cls_list'] = $time_cls_list;

        $data['view_cls'] = $view_cls;

        info_log("time_mng/list", "아이들 시간관리 조회 완료!");
        info_log("time_mng/list", "================================================================================");

        $this->load->view('time_mng_list_v', $data);
    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST) {
            info_log("time_mng/ins/", "================================================================================");
            info_log("time_mng/ins/", "아이들 시간관리 입력 시작!");

            $this->form_validation->set_rules('usr', '사용자', 'required');
            $this->form_validation->set_rules('dt', '일자', 'required');
            $this->form_validation->set_rules('time_cls', '시간구분', 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == true) {
                (int)$time = str_replace(',', '', $this->input->post('time', 'TRUE'));

                // 금액 입력 확인
                if ($time <= 0) {
                    alert_log("time_mng/ins", "시간이 0보다 작거나 같습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0) {
                    $memo = $this->input->post('memo', 'TRUE');
                } else {
                    $memo = null;
                }

                $this->db->trans_begin();

                $i_data = array('usr'      => $this->input->post('usr', 'TRUE')
                               ,'dt'       => str_replace('-', '', $this->input->post('dt', 'TRUE'))
                               ,'time_cls' => $this->input->post('time_cls', 'TRUE')
                               ,'time'     => $time
                               ,'memo'     => trim($memo)
                               );

                $result = $this->stay_m->insert_tbc002l00($i_data);

                if ($result) {
                    $this->db->trans_commit();

                    info_log("time_mng/ins/", "아이들 시간관리 입력 완료!");
                    info_log("time_mng/ins/", "================================================================================");

                    $ins_cls = $this->uri->segment(3);
                    //info_log("time_mng/ins", "ins_cls = [" . $ins_cls . "]");

                    if (strncmp($ins_cls, "r", 1) == 0) {
                        redirect(base_url("time_mng/ins"));
                    } else {
                        redirect(base_url("time_mng/list"));
                    }
                } else {
                    info_log("time_mng/ins/insert_tbc002l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("time_mng/ins/insert_tbc002l00", "[SQL ERR] 아이들 시간관리 입력 오류!");
                }
            } else {
                $this->time_mng_reg_v('i');
            }
        } else {
            $this->time_mng_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST) {
            info_log("time_mng/upd/", "================================================================================");
            info_log("time_mng/upd/", "아이들 시간관리 수정 시작!");

            $this->form_validation->set_rules('usr', '사용자', 'required');
            $this->form_validation->set_rules('dt', '일자', 'required');
            $this->form_validation->set_rules('time_cls', '시간구분', 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == true) {
                (int)$time = str_replace(',', '', $this->input->post('time', 'TRUE'));

                // 금액 입력 확인
                if ($time <= 0) {
                    alert_log("time_mng/ins", "시간이 0보다 작거나 같습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0) {
                    $memo = $this->input->post('memo', 'TRUE');
                } else {
                    $memo = null;
                }

                $this->db->trans_begin();

                $srno = $this->uri->segment(3);

                $u_data = array('srno'    => $srno
                               ,'usr'      => $this->input->post('usr', 'TRUE')
                               ,'dt'       => str_replace('-', '', $this->input->post('dt', 'TRUE'))
                               ,'time_cls' => $this->input->post('time_cls', 'TRUE')
                               ,'time'     => $time
                               ,'memo'     => trim($memo)
                               );

                $result = $this->stay_m->update_tbc002l00_1($u_data);

                if ($result) {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1) {
                        info_log("time_mng/upd/update_tbc002l00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("time_mng/upd/update_tbc002l00_1", "[SQL ERR] 아이들 시간관리 수정 처리 오류![" . $prcs_cnt . "]!");
                    } else {
                        $this->db->trans_commit();

                        info_log("time_mng/upd/", "아이들 시간관리 수정 완료!");

                        $redirect_url = $_SESSION['bef_uri'];
                        info_log("time_mng/upd/", "redirect_url  = [" . $redirect_url . "]");

                        if (isset($redirect_url)) {
                            redirect(base_url($redirect_url));
                        } else {
                            redirect(base_url("time_mng/list"));
                        }

                        info_log("time_mng/upd/", "================================================================================");
                    }
                } else {
                    info_log("time_mng/upd/update_tbc002l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("time_mng/upd/update_tbc002l00_1", "[SQL ERR] 아이들 시간관리 수정 오류!");
                }
            } else {
                $this->time_mng_reg_v('u');
            }
        } else {
            $this->time_mng_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST) {
            info_log("time_mng/del/", "================================================================================");
            info_log("time_mng/del/", "아이들 시간관리 삭제 시작!");

            $this->form_validation->set_rules('usr', '사용자', 'required');
            $this->form_validation->set_rules('dt', '일자', 'required');
            $this->form_validation->set_rules('time_cls', '시간구분', 'required');

            if ($this->form_validation->run() == true) {
                $this->db->trans_begin();

                $srno = $this->uri->segment(3);

                $u_data = array('srno'     => $srno
                           );

                $result = $this->stay_m->update_tbc002l00_2($u_data);

                if ($result) {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1) {
                        info_log("time_mng/del/update_tbc002l00_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("time_mng/del/update_tbc002l00_2", "[SQL ERR] 아이들 시간관리 삭제 처리 오류![" . $prcs_cnt . "]!");
                        info_log("time_mng/del/update_tbc002l00_2", "================================================================================");
                    } else {
                        $this->db->trans_commit();

                        info_log("time_mng/del/", "아이들 시간관리 삭제 완료!");
                        info_log("time_mng/del/", "================================================================================");

                        $redirect_url = $_SESSION['bef_uri'];
                        info_log("time_mng/del/", "redirect_url  = [" . $redirect_url . "]");

                        if (isset($redirect_url)) {
                            redirect(base_url($redirect_url));
                        } else {
                            redirect(base_url("time_mng/list"));
                        }
                    }
                } else {
                    info_log("time_mng/del/update_tbc002l00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("time_mng/del/update_tbc002l00_2", "[SQL ERR] 아이들 시간관리 삭제 오류!");
                    info_log("time_mng/del/update_tbc002l00_2", "================================================================================");
                }
            } else {
                $this->time_mng_reg_v('u');
            }
        } else {
            $this->time_mng_reg_v('u');
        }
    }


    public function time_mng_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        ////$data['time_mng_cls_list']       = $this->stay_m->get_list('time_mng_CLS');
        //$time_mng_cls_result = $this->stay_m->get_list('time_mng_CLS');
        //
        //$i = -1;
        //foreach($time_mng_cls_result as $e_cls_list)
        //{
        //    if (!isset($e_cls_list->othr_info))
        //    {
        //        $i = $i + 1;
        //        $time_mng_large_cls[$i] = $e_cls_list->clm_val_nm;
        //    }
        //    else
        //    {
        //        $time_mng_cls[$time_mng_large_cls[$i]][$e_cls_list->clm_val] = $e_cls_list->clm_val_nm;
        //    }
        //}
        ////print_r($time_mng_cls);
        //$data['time_mng_cls_list'] = $time_mng_cls;
        //
        //$data['time_mng_chnl_cls_list']  = $this->stay_m->get_list('time_mng_CHNL_CLS', 'Y');
        //
        //$data['cost_cls_list']  = $this->stay_m->get_list('COST_CLS', 'Y');

        //print_r($data['time_mng_chnl_cls_list']);
        //print_r($data['cost_cls_list']);

        $data['time_cls_list'] = $this->stay_m->get_list('TIME_CLS', 'Y');
        $data['usr_list']      = $this->stay_m->get_list('USR', 'Y', '2');

        if (strncmp($prcs_cls, "u", 1) == 0) {
            $data['view'] = $this->stay_m->get_time_mng_info($this->uri->segment(3));
        }

        $this->load->view('time_mng_reg_v', $data);
    }
}
