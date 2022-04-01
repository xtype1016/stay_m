<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
    수입(income) 조회
*/

class Asset extends CI_Controller
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
            info_log("Asset", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->smmry();
        redirect('asset/ac_list', 'refresh');
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


    public function ac_list()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("asset/ac_list", "================================================================================");
        info_log("asset/ac_list", "계좌 리스트 조회 시작!");

        // 검색 변수 초기화
        $page_url = '';

        $uri_segment = 4;          // 페이지 번호가 위치한 세그먼트

        $bef_uri = uri_string();
        info_log("asset/ac_list", "bef_uri  = [" . $bef_uri . "]");
        // bef_list 세션데이터 초기화
        unset($_SESSION['bef_uri']);
        $this->session->set_userdata('bef_uri', $bef_uri);

        // Pagination 용 주소
        $page_url = '';

        // 페이지네이션 설정
        $config['base_url']         = '/asset/ac_list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_ac_list('rowcnt');         // 표시할 게시물 총 수
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
            $data['ac_list'] = $this->stay_m->get_ac_list('data', $start, $limit);
        }

        info_log("asset/ac_list", "계좌 리스트 조회 완료!");
        info_log("asset/ac_list", "================================================================================");

        $this->load->view('ac_list_v', $data);
    }


    public function ac_bal()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("asset/ac_bal", "================================================================================");
        info_log("asset/ac_bal", "계좌 잔고 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = '';

        $stnd_yymm = str_replace('-', '', $this->uri->segment(3));

        if (empty($stnd_yymm))
        {
            $stnd_yymm = date("Ym");
        }

        $page_url = '';

        $uri_segment = 5;          // 페이지 번호가 위치한 세그먼트

        $bef_uri = uri_string();
        info_log("asset/ac_bal", "bef_uri  = [" . $bef_uri . "]");
        // bef_list 세션데이터 초기화
        unset($_SESSION['bef_uri']);
        $this->session->set_userdata('bef_uri', $bef_uri);

        // Pagination 용 주소
        $page_url = '/' . $stnd_yymm;

        // 입출금거래 현금지출합계금액 ins/upd
        cash_bal_ins_upd($stnd_yymm);

        // 페이지네이션 설정
        $config['base_url']         = '/asset/ac_bal/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_ac_bal($stnd_yymm, 'rowcnt');         // 표시할 게시물 총 수
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
            $data['ac_bal_list'] = $this->stay_m->get_ac_bal($stnd_yymm, 'data', $start, $limit);
        }

        info_log("asset/ac_bal", "계좌 잔고 조회 완료!");
        info_log("asset/ac_bal", "================================================================================");

        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);

        $this->load->view('ac_bal_list_v', $data);
    }


    public function ac_ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST) {
            info_log("asset/ac_ins/", "================================================================================");
            info_log("asset/ac_ins/", "계좌정보 입력 시작!");

            $this->form_validation->set_rules('ac_no', '계좌번호', 'required');

            if ($this->form_validation->run() == true) {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                if ($amt < 0) {
                    alert_log("asset/ac_ins", "금액이 0보다 작습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0) {
                    $memo = $this->input->post('memo', 'TRUE');
                } else {
                    $memo = null;
                }

                $ac_cls = $this->input->post('ac_cls', 'TRUE');

                if (strcmp($ac_cls, '1') == 0) {
                    $srt_dt = str_replace('-', '', $this->input->post('srt_dt', 'TRUE'));
                    $end_dt = '99991231';
                } else {
                    $srt_dt = str_replace('-', '', $this->input->post('srt_dt', 'TRUE'));
                    $end_dt = str_replace('-', '', $this->input->post('end_dt', 'TRUE'));
                }

                $primary_yn = $this->input->post('primary_yn', 'TRUE');

                if (strcmp($primary_yn, 'Y') == 0) {
                    $dup_chk = $this->stay_m->get_ac_primary_dup_chk();

                    info_log("asset/ac_ins/", "dup_chk cnt = [" . $dup_chk->cnt . "]");

                    if ($dup_chk->cnt >= 1) {
                        alert_log("asset/ac_ins/", "주거래계좌로 등록된 건이 있습니다. 주거래계좌는 하나만 등록 가능합니다!!");
                    }
                }

                $this->db->trans_begin();

                $ac_srno = $this->stay_m->get_clm_sr_val('AC_SRNO');

                $i_data = array('ac_srno'    => $ac_srno
                               ,'ac_no'      => $this->input->post('ac_no', 'TRUE')
                               ,'bank'       => $this->input->post('bank', 'TRUE')
                               ,'ac_owner'   => $this->input->post('ac_owner', 'TRUE')
                               ,'ac_cls'     => $ac_cls
                               ,'primary_yn' => $primary_yn
                               ,'srt_dt'     => $srt_dt
                               ,'end_dt'     => $end_dt
                               ,'amt'        => $amt
                               ,'memo'       => trim($memo)
                               );

                $result = $this->stay_m->insert_tbb006i00($i_data);

                if ($result) {
                    $this->db->trans_commit();

                    info_log("asset/ac_ins/", "계좌정보 입력 완료!");
                    info_log("asset/ac_ins/", "================================================================================");

                    $ins_cls = $this->uri->segment(3);
                    //info_log("asset/ins", "ins_cls = [" . $ins_cls . "]");

                    if (strncmp($ins_cls, "r", 1) == 0) {
                        redirect(base_url("asset/ac_ins"));
                    } else {
                        redirect(base_url("asset/ac_list"));
                    }
                } else {
                    info_log("asset/ac_ins/insert_tbb006i00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("asset/ac_ins/insert_tbb006i00", "[SQL ERR] 계좌정보 입력 오류!");
                }
            } else {
                $this->ac_reg_v('i');
            }
        } else {
            $this->ac_reg_v('i');
        }
    }


    public function ac_upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST) {
            info_log("asset/ac_upd/", "================================================================================");
            info_log("asset/ac_upd/", "계좌정보 수정 시작!");

            $this->form_validation->set_rules('ac_srno', '계좌 일련번호', 'required');
            $this->form_validation->set_rules('ac_no', '계좌번호', 'required');

            if ($this->form_validation->run() == true) {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                if ($amt < 0) {
                    alert_log("asset/ac_upd", "금액이 0보다 작습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0) {
                    $memo = $this->input->post('memo', 'TRUE');
                } else {
                    $memo = null;
                }

                $ac_cls = $this->input->post('ac_cls', 'TRUE');

                if (strcmp($ac_cls, '1') == 0) {
                    $srt_dt = str_replace('-', '', $this->input->post('srt_dt', 'TRUE'));
                    $end_dt = '99991231';
                } else {
                    $srt_dt = str_replace('-', '', $this->input->post('srt_dt', 'TRUE'));
                    $end_dt = str_replace('-', '', $this->input->post('end_dt', 'TRUE'));
                }

                $primary_yn = $this->input->post('primary_yn', 'TRUE');
                $ori_primary_yn = $this->input->post('ori_primary_yn', 'TRUE');
                
                info_log("asset/ac_ins/", "ori_primary_yn = [" . $ori_primary_yn . "]");

                if (strcmp($ori_primary_yn, 'N') == 0 && strcmp($primary_yn, 'Y') == 0) {
                    $dup_chk = $this->stay_m->get_ac_primary_dup_chk();

                    info_log("asset/ac_ins/", "dup_chk cnt = [" . $dup_chk->cnt . "]");

                    if ($dup_chk->cnt >= 1) {
                        alert_log("asset/ac_ins/", "주거래계좌로 등록된 건이 있습니다. 주거래계좌는 하나만 등록 가능합니다!!");
                    }
                }

                $this->db->trans_begin();

                $u_data = array('ac_srno'    => $this->input->post('ac_srno', 'TRUE')
                               ,'ac_no'      => $this->input->post('ac_no', 'TRUE')
                               ,'bank'       => $this->input->post('bank', 'TRUE')
                               ,'ac_owner'   => $this->input->post('ac_owner', 'TRUE')
                               ,'ac_cls'     => $ac_cls
                               ,'primary_yn' => $primary_yn
                               ,'srt_dt'     => $srt_dt
                               ,'end_dt'     => $end_dt
                               ,'amt'        => $amt
                               ,'memo'       => trim($memo)
                               );

                $result = $this->stay_m->update_tbb006i00_1($u_data);

                if ($result) {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1) {
                        info_log("asset/ac_upd/update_tbb006i00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("asset/ac_upd/update_tbb006i00_1", "[SQL ERR] 계좌정보 수정 처리 오류![" . $prcs_cnt . "]!");
                    } else {
                        $this->db->trans_commit();

                        info_log("asset/ac_upd/", "계좌정보 수정 완료!");
                        info_log("asset/ac_upd/", "================================================================================");

                        $redirect_url = $_SESSION['bef_uri'];
                        info_log("asset/ac_upd/", "redirect_url  = [" . $redirect_url . "]");

                        if (isset($redirect_url)) {
                            redirect(base_url($redirect_url));
                        } else {
                            redirect(base_url("asset/ac_list"));
                        }
                    }
                } else {
                    info_log("asset/ac_upd/update_tbb006i00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("asset/ac_upd/update_tbb006i00_1", "[SQL ERR] 계좌정보 수정 오류!");
                }
            } else {
                $this->ac_reg_v('u');
            }
        } else {
            $this->ac_reg_v('u');
        }
    }


    public function ac_del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST) {
            info_log("asset/ac_del/", "================================================================================");
            info_log("asset/ac_del/", "계좌정보 삭제 시작!");

            $this->form_validation->set_rules('ac_srno', '계좌 일련번호', 'required');
            $this->form_validation->set_rules('ac_no', '계좌번호', 'required');

            if ($this->form_validation->run() == true) {
                $this->db->trans_begin();

                $ac_srno = $this->input->post('ac_srno', 'TRUE');

                $u_data = array('ac_srno'     => $ac_srno
                           );

                $result = $this->stay_m->update_tbb006i00_2($u_data);

                if ($result) {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1) {
                        info_log("asset/ac_del/update_tbb006i00_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("asset/ac_del/update_tbb006i00_2", "[SQL ERR] 계좌정보 삭제 처리 오류![" . $prcs_cnt . "]!");
                        info_log("asset/ac_del/update_tbb006i00_2", "================================================================================");
                    } else {
                        $this->db->trans_commit();

                        info_log("asset/ac_del/", "계좌정보 삭제 완료!");
                        info_log("asset/ac_del/", "================================================================================");

                        $redirect_url = $_SESSION['bef_uri'];
                        info_log("asset/ac_del/", "redirect_url  = [" . $redirect_url . "]");

                        if (isset($redirect_url)) {
                            redirect(base_url($redirect_url));
                        } else {
                            redirect(base_url("asset/ac_list"));
                        }
                    }
                } else {
                    info_log("asset/ac_del/update_tbb006i00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("asset/ac_del/update_tbb006i00_2", "[SQL ERR] 계좌정보 삭제 오류!");
                    info_log("asset/ac_del/update_tbb006i00_2", "================================================================================");
                }
            } else {
                $this->Asset_reg_v('u');
            }
        } else {
            $this->Asset_reg_v('u');
        }
    }


    public function ac_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        $ac_cls = $this->input->post('ac_cls', 'TRUE');
        if (empty($ac_cls)) {
            $ac_cls = '1';
        }
        $data['ac_cls'] = $ac_cls;

        $data['ac_cls_list']  = $this->stay_m->get_list('AC_CLS', 'Y');
        $data['usr_list']  = $this->stay_m->get_list('USR', 'Y');
        $data['bank_list']  = $this->stay_m->get_list('BANK', 'Y');

        if (strncmp($prcs_cls, "u", 1) == 0) {
            $data['view'] = $this->stay_m->get_ac_info($this->uri->segment(3));
        }

        $this->load->view('ac_reg_v', $data);
    }
}
