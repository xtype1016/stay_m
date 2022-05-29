<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    예약관리 등록 컨트롤러
*/
class Io_tr extends CI_Controller
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

        if (!isset($_SESSION['usr_no']) && strlen($usr_no) > 0 && strlen($idntfr) > 0)
        {
            info_log("io_tr", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->list();
        redirect('io_tr/cls_list','refresh');
    }


    /*
        사이트 헤더, 푸터 추가
    */
    public function _remap($method, $params = array())
    {
        // Header
        $this->load->view('header_v');

        if (method_exists($this, $method))
        {
            //$this->{"{$method}"}();
            call_user_func_array(array($this, $method), $params);
        }

        // footer
        $this->load->view('footer_v');
    }


    public function cls_list()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        // 검색 변수 초기화
        $page_url = '';
        // Pagination 용 주소
        $page_url = '/';

        $uri_segment = 4;          // 페이지 번호가 위치한 세그먼트

        // 페이지네이션 설정
        $config['base_url']         = '/io_tr/cls_list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_item_list('IO_TR_CLS', '', 'rowcnt');         // 표시할 게시물 총 수
        $config['per_page']         = 5;             // 한 페이지에 표시할 게시물 수
        $config['uri_segment']      = $uri_segment;  // 페이지번호가 위치한 세그먼트
        $config['num_links']        = 3;             // 선택된 페이지번호 좌우로 몇 개의 숫자 링크를 보여줄 지 설정
        $config['use_page_numbers'] = TRUE;          // 링크를 1, 2, 3 으로 표기
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
        $page = $this->uri->segment($uri_segment, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        $start = ($page - 1) * $config['per_page'];
        $limit = $config['per_page'];

        if ($config['total_rows'] > 0)
        {
            $data['io_tr_list'] = $this->stay_m->get_item_list('IO_TR_CLS', '', 'data', $start, $limit);
        }

        $this->load->view('io_tr_cls_list_v', $data);
    }


    public function cls_ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->form_validation->set_rules('clm_val_nm', '입출금거래명'  , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $tr_cls = $this->input->post('tr_cls'  , 'TRUE');
                $t_tr_cls = $this->stay_m->get_clm_sr_val('IO_TR_CLS' . $tr_cls);

                $io_tr_cls = $tr_cls . $t_tr_cls;

                $i_data = array('clm_nm'      => 'IO_TR_CLS'
                               ,'clm_val'     => $io_tr_cls
                               ,'clm_val_nm'  => trim($this->input->post('clm_val_nm'  , 'TRUE'))
                               ,'othr_info'   => $tr_cls
                               );

                $result = $this->stay_m->insert_tba003i00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();

                    info_log("io_tr/ins/insert_tba003i00", "입출금거래구분 입력 완료!");
                    info_log("io_tr/ins/insert_tba003i00", "================================================================================");

                    redirect(base_url("/io_tr/cls_list"));
                }
                else
                {
                    info_log("io_tr/ins/insert_tba003i00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("io_tr/ins/insert_tba003i00", "[SQL ERR] 입출금거래구분 입력 오류!");
                }
            }
            else
            {
                $this->io_tr_cls_reg_v('i');
            }
        }
        else
        {
            $this->io_tr_cls_reg_v('i');
        }
    }


    public function cls_upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->form_validation->set_rules('clm_val_nm', '입출금거래명'  , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $u_data = array('clm_nm'      => 'IO_TR_CLS'
                               ,'clm_val'     => $this->uri->segment(3)
                               ,'clm_val_nm'  => trim($this->input->post('clm_val_nm'  , 'TRUE'))
                               ,'othr_info'   => NULL
                               );

                $result = $this->stay_m->update_tba003i00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("io_tr/upd/update_tba003i00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("io_tr/upd/update_tba003i00_1", "[SQL ERR] 입출금거래구분 수정 건수 오류![" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("io_tr/upd/update_tba003i00_1", "입출금거래구분 수정 완료!");
                        info_log("io_tr/upd/update_tba003i00_1", "================================================================================");

                        redirect(base_url("/io_tr/cls_list"));
                    }
                }
                else
                {
                    info_log("io_tr/upd/update_tba003i00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("io_tr/upd/update_tba003i00_1", "[SQL ERR] 입출금거래구분 수정 오류!");
                }
            }
            else
            {
                $this->io_tr_cls_reg_v('u');
            }
        }
        else
        {
            $this->io_tr_cls_reg_v('u');
        }
    }


    public function cls_del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->db->trans_begin();

            $u_data = array('clm_nm'    => 'IO_TR_CLS'
                           ,'clm_val'   => $this->uri->segment(3)
                           );

            $result = $this->stay_m->update_tba003i00_2($u_data);

            if ($result)
            {
                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $this->db->affected_rows();
                if ($prcs_cnt != 1)
                {
                    info_log("io_tr/del/update_tba003i00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("io_tr/del/update_tba003i00_2", "[SQL ERR] 입출금거래구분 삭제 건수 오류![" . $prcs_cnt . "]!");
                }
                else
                {
                    $this->db->trans_commit();

                    info_log("io_tr/del/update_tba003i00_2", "입출금거래구분 삭제 수정 완료!");
                    info_log("io_tr/del/update_tba003i00_2", "================================================================================");

                    redirect(base_url("/io_tr/cls_list"));
                }
            }
            else
            {
                info_log("io_tr/del/update_tba003i00_2", "last_query  = [" . $this->db->last_query() . "]");
                $this->db->trans_rollback();
                alert_log("io_tr/del/update_tba003i00_2", "[SQL ERR] 입출금거래구분 삭제 오류!");
            }
        }
    }


    //2020.09.06. tbb002l00 사용중지, tbb003l00으로 일원화
    //public function ins()
    //{
    //    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    //
    //    if ($_POST)
    //    {
    //        info_log("io_tr/ins/", "================================================================================");
    //        info_log("io_tr/ins/", "입출금거래 입력 처리 시작!");
    //
    //        $this->form_validation->set_rules('tr_dt'     , '거래일자'    , 'required');
    //        $this->form_validation->set_rules('io_tr_cls' , '입출거래분류', 'required');
    //        //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');
    //
    //        if ($this->form_validation->run() == TRUE)
    //        {
    //            $tr_dt = str_replace('-', '', $this->input->post('tr_dt', 'TRUE'));
    //
    //            $this->db->trans_begin();
    //
    //            $i_data = array('tr_yymm'    => substr($tr_dt, 0, 6)
    //                           ,'io_tr_cls'  => $this->input->post('io_tr_cls', 'TRUE')
    //                           ,'tr_dt'      => $tr_dt
    //                           ,'memo'       => $this->input->post('memo', 'TRUE')
    //                           ,'amt'        => str_replace(',', '', $this->input->post('amt', 'TRUE'))
    //                           );
    //
    //            $result = $this->stay_m->insert_update_tbb002l00($i_data);
    //
    //            if ($result)
    //            {
    //                // 2020.03.14 미사용 처리
    //                // 잔고 관리 없이 차후에는 수입/지출만 관리하도록 변경
    //                //$i_data = array('tr_yymm' => substr($tr_dt, 0, 6)
    //                //               );
    //                //
    //                //$result = $this->stay_m->bal_update_tbb002l00($i_data);
    //                //
    //                //if ($result)
    //                //{
    //                //    info_log("io_tr/ins/bal_update_tbb002l00", "입출금거래 현금지출/잔고 입력수정 정상 처리!");
    //                //
    //                //    $this->db->trans_commit();
    //                //
    //                //    info_log("io_tr/ins/", "입출금거래 입력 처리 종료!");
    //                //    info_log("io_tr/ins/", "================================================================================");
    //                //
    //                //    redirect(base_url("io_tr/smmry"));
    //                //}
    //                //else
    //                //{
    //                //    info_log("io_tr/ins/bal_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //                //    $this->db->trans_rollback();
    //                //    alert_log("io_tr/ins/bal_update_tbb002l00", "[SQL ERR] 입출금거래 현금지출/잔고 입력수정 처리 오류!");
    //                //}
    //                // 2020.03.14 미사용 처리
    //                // 잔고 관리 없이 차후에는 수입/지출만 관리하도록 변경
    //
    //                $this->db->trans_commit();
    //
    //                info_log("io_tr/ins/", "입출금거래 입력 처리 종료!");
    //                info_log("io_tr/ins/", "================================================================================");
    //
    //                redirect(base_url("io_tr/smmry"));
    //            }
    //            else
    //            {
    //                info_log("io_tr/ins/insert_tba005l00", "last_query  = [" . $this->db->last_query() . "]");
    //                $this->db->trans_rollback();
    //                alert_log("io_tr/ins/insert_tba005l00", "[SQL ERR] 입출금거래 입력 처리 오류!");
    //            }
    //        }
    //        else
    //        {
    //            $this->io_tr_reg_v('i');
    //        }
    //    }
    //    else
    //    {
    //        $this->io_tr_reg_v('i');
    //    }
    //}
    //
    //
    //public function upd()
    //{
    //    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    //
    //    if ($_POST)
    //    {
    //        info_log("io_tr/ins/", "================================================================================");
    //        info_log("io_tr/ins/", "입출금거래 수정 처리 시작!");
    //
    //        $this->form_validation->set_rules('tr_dt'       , '거래일자'    , 'required');
    //        $this->form_validation->set_rules('io_tr_cls_u' , '입출거래분류', 'required');
    //        //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');
    //
    //        if ($this->form_validation->run() == TRUE)
    //        {
    //            $tr_dt = str_replace('-', '', $this->input->post('tr_dt', 'TRUE'));
    //
    //            $this->db->trans_begin();
    //
    //            $i_data = array('tr_yymm'    => substr($tr_dt, 0, 6)
    //                           ,'io_tr_cls'  => $this->input->post('io_tr_cls_u', 'TRUE')
    //                           ,'tr_dt'      => $tr_dt
    //                           ,'memo'       => $this->input->post('memo', 'TRUE')
    //                           ,'amt'        => str_replace(',', '', $this->input->post('amt', 'TRUE'))
    //                           );
    //
    //            $result = $this->stay_m->insert_update_tbb002l00($i_data);
    //
    //            if ($result)
    //            {
    //                // 2020.03.14 미사용 처리
    //                // 잔고 관리 없이 차후에는 수입/지출만 관리하도록 변경
    //                //$i_data = array('tr_yymm' => substr($tr_dt, 0, 6)
    //                //               );
    //                //
    //                //$result = $this->stay_m->bal_update_tbb002l00($i_data);
    //                //
    //                //if ($result)
    //                //{
    //                //    info_log("io_tr/upd/bal_update_tbb002l00", "입출금거래 현금지출/잔고 입력수정 정상 처리!");
    //                //
    //                //    $this->db->trans_commit();
    //                //
    //                //    info_log("io_tr/upd", "입출금거래 입력 처리 종료!");
    //                //    info_log("io_tr/upd", "================================================================================");
    //                //
    //                //    redirect(base_url("io_tr/smmry"));
    //                //}
    //                //else
    //                //{
    //                //    info_log("io_tr/upd/bal_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //                //    $this->db->trans_rollback();
    //                //    alert_log("io_tr/upd/bal_update_tbb002l00", "[SQL ERR] 입출금거래 현금지출/잔고 입력수정 처리 오류!");
    //                //}
    //                // 2020.03.14 미사용 처리
    //                // 잔고 관리 없이 차후에는 수입/지출만 관리하도록 변경
    //
    //                $this->db->trans_commit();
    //
    //                info_log("io_tr/upd", "입출금거래 수정 처리 종료!");
    //                info_log("io_tr/upd", "================================================================================");
    //
    //                redirect(base_url("io_tr/smmry"));
    //            }
    //            else
    //            {
    //                info_log("io_tr/upd/insert_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //                $this->db->trans_rollback();
    //                alert_log("io_tr/upd/insert_update_tbb002l00", "[SQL ERR] 입출금거래 수정 처리 오류!");
    //            }
    //        }
    //        else
    //        {
    //            $this->io_tr_reg_v('u');
    //        }
    //    }
    //    else
    //    {
    //        $this->io_tr_reg_v('u');
    //    }
    //}
    //
    //
    //public function del()
    //{
    //    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    //
    //    if ($_POST)
    //    {
    //        info_log("io_tr/del", "================================================================================");
    //        info_log("io_tr/del", "입출금거래 삭제 처리 시작!");
    //
    //        $this->form_validation->set_rules('tr_dt'       , '거래일자'    , 'required');
    //        $this->form_validation->set_rules('io_tr_cls_u' , '입출거래분류', 'required');
    //        //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');
    //
    //        if ($this->form_validation->run() == TRUE)
    //        {
    //            $tr_dt = str_replace('-', '', $this->input->post('tr_dt', 'TRUE'));
    //
    //            $this->db->trans_begin();
    //
    //            $d_data = array('tr_yymm'    => substr($tr_dt, 0, 6)
    //                           ,'io_tr_cls'  => $this->input->post('io_tr_cls_u', 'TRUE')
    //                           );
    //
    //            $result = $this->stay_m->delete_tbb002l00($d_data);
    //
    //            if ($result)
    //            {
    //                // 2020.03.14 미사용 처리
    //                // 잔고 관리 없이 차후에는 수입/지출만 관리하도록 변경
    //                //$i_data = array('tr_yymm' => substr($tr_dt, 0, 6)
    //                //               );
    //                //
    //                //$result = $this->stay_m->bal_update_tbb002l00($i_data);
    //                //
    //                //if ($result)
    //                //{
    //                //    info_log("io_tr/upd/bal_update_tbb002l00", "입출금거래 현금지출/잔고 입력수정 정상 처리!");
    //                //
    //                //    $this->db->trans_commit();
    //                //
    //                //    info_log("io_tr/upd", "입출금거래 입력 처리 종료!");
    //                //    info_log("io_tr/upd", "================================================================================");
    //                //
    //                //    redirect(base_url("io_tr/smmry"));
    //                //}
    //                //else
    //                //{
    //                //    info_log("io_tr/upd/bal_update_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //                //    $this->db->trans_rollback();
    //                //    alert_log("io_tr/upd/bal_update_tbb002l00", "[SQL ERR] 입출금거래 현금지출/잔고 입력수정 처리 오류!");
    //                //}
    //                // 2020.03.14 미사용 처리
    //                // 잔고 관리 없이 차후에는 수입/지출만 관리하도록 변경
    //
    //                $this->db->trans_commit();
    //
    //                info_log("io_tr/del", "입출금거래 삭제 처리 정상 종료!");
    //                info_log("io_tr/del", "================================================================================");
    //
    //                redirect(base_url("io_tr/smmry"));
    //            }
    //            else
    //            {
    //                info_log("io_tr/del/delete_tbb002l00", "last_query  = [" . $this->db->last_query() . "]");
    //                $this->db->trans_rollback();
    //                alert_log("io_tr/del/delete_tbb002l00", "[SQL ERR] 입출금거래 삭제 처리 오류!");
    //            }
    //        }
    //        else
    //        {
    //            $this->io_tr_reg_v('u');
    //        }
    //    }
    //    else
    //    {
    //        $this->io_tr_reg_v('u');
    //    }
    //}

    public function smmry()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("io_tr/smmry/", "================================================================================");
        info_log("io_tr/smmry/", "입출금거래 요약 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = $page_url = '';
        $stnd_yymm = str_replace('-', '', $this->uri->segment(3));

        // Pagination 용 주소
        $page_url = '/';

        $uri_segment = 4;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_yymm))
        {
            //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
            $stnd_yymm = date("Ym");
            $view_cls = "1";
         }

        // 입출금거래 현금지출합계금액 ins/upd
        cash_bal_ins_upd($stnd_yymm);

        if ($stnd_yymm >= "202205")
        {
            bl_bal_ins_upd($stnd_yymm);
        }

        // 페이지네이션 설정
        $config['base_url']         = '/io_tr/smmry/' . $page_url . '/page/';
        //$config['total_rows']       = $this->stay_m->get_item_list('IO_TR_CLS', '', 'rowcnt');         // 표시할 게시물 총 수
        $config['total_rows']       = 20;
        $config['per_page']         = 20;             // 한 페이지에 표시할 게시물 수
        $config['uri_segment']      = $uri_segment;  // 페이지번호가 위치한 세그먼트
        $config['num_links']        = 3;             // 선택된 페이지번호 좌우로 몇 개의 숫자 링크를 보여줄 지 설정
        $config['use_page_numbers'] = TRUE;          // 링크를 1, 2, 3 으로 표기
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
        $page = $this->uri->segment($uri_segment, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        //$start = ($page - 1) * $config['per_page'];
        //$limit = $config['per_page'];

        if ($config['total_rows'] > 0)
        {
            $data['io_tr_smmry'] = $this->stay_m->get_io_tr_smmry($stnd_yymm);
        }

        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);

        $this->load->view('io_tr_smmry_v', $data);

        info_log("io_tr/smmry/", "입출금거래 요약 조회 완료!");
        info_log("io_tr/smmry/", "================================================================================");
    }


    //public function list()
    //{
    //    //$this->output->enable_profiler(TRUE);
    //    $this->load->library('pagination');
    //
    //    info_log("io_tr/list/", "================================================================================");
    //    info_log("io_tr/list/", "입출금 리스트 조회 시작!");
    //
    //    // 검색 변수 초기화
    //    $stnd_yymm = $page_url = '';
    //
    //    $stnd_yymm = str_replace('-', '', $this->uri->segment(3));
    //    $rsv_chnl_cls = $this->uri->segment(4);
    //
    //    $uri_segment = 5;          // 페이지 번호가 위치한 세그먼트
    //
    //    if (empty($stnd_yymm))
    //    {
    //        //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
    //        $stnd_yymm = date("Ym");
    //    }
    //
    //    $stnd_dt = date("Ymd");
    //
    //    //info_log("io_tr/list", "rsv_chnl_cls = [" . $rsv_chnl_cls . "]");
    //
    //    // Pagination 용 주소
    //    $page_url = '/' . $stnd_yymm;
    //
    //    // 페이지네이션 설정
    //    $config['base_url']         = '/io_tr/list/' . $page_url . '/page/';
    //    $config['total_rows']       = $this->stay_m->get_io_tr_list($stnd_yymm, 'rowcnt');         // 표시할 게시물 총 수
    //    $config['per_page']         = 5;             // 한 페이지에 표시할 게시물 수
    //    $config['uri_segment']      = $uri_segment;  // 페이지번호가 위치한 세그먼트
    //    $config['num_links']        = 3;             // 선택된 페이지번호 좌우로 몇 개의 숫자 링크를 보여줄 지 설정
    //    $config['use_page_numbers'] = TRUE;          // 링크를 1, 2, 3 으로 표기
    //    $config['first_link']       = '<<';          // 처음으로 링크 생성
    //    $config['last_link']        = '>>';          // 끝으로 링크 생성
    //    $config['next_link']        = '>';
    //    $config['prev_link']        = '<';
    //
    //    $config['full_tag_open']    = "<nav><ul class='pagination pagination-sm'>";
    //    $config['full_tag_close']   = '</ul></nav>';
    //    $config['num_tag_open']     = '<li>';
    //    $config['num_tag_close']    = '</li>';
    //    $config['cur_tag_open']     = "<li class='active'><a href='#'>";
    //    $config['cur_tag_close']    = '</a></li>';
    //    $config['next_tag_open']    = '<li>';
    //    $config['next_tag_close']   = '</li>';
    //    $config['prev_tag_open']    = '<li>';
    //    $config['prev_tag_close']   = '</li>';
    //    $config['first_tag_open']   = '<li>';
    //    $config['first_tag_close']  = '</li>';
    //    $config['last_tag_open']    = '<li>';
    //    $config['last_tag_close']   = '</li>';
    //
    //    // 페이지네이션 초기화
    //    $this->pagination->initialize($config);
    //
    //    // 페이징 링크 생성
    //    $data['pagination'] = $this->pagination->create_links();
    //
    //    // 게시물 목록을 불러오기 위한 offset, limit 값 가져오기
    //    //$page = $this->uri->segment(3, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
    //    $page = $this->uri->segment($uri_segment, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
    //    $start = ($page - 1) * $config['per_page'];
    //    $limit = $config['per_page'];
    //
    //    if ($config['total_rows'] > 0)
    //    {
    //        $data['io_tr_list'] = $this->stay_m->get_io_tr_list($stnd_yymm, 'data', $start, $limit);
    //    }
    //
    //    $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);
    //
    //    $data['view_cls'] = $view_cls;
    //
    //    $this->load->view('io_tr_list_v', $data);
    //
    //    info_log("io_tr/list/", "입출금 리스트 조회 완료!");
    //    info_log("io_tr/list/", "================================================================================");
    //
    //}


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("io_tr/ins/", "================================================================================");
            info_log("io_tr/ins/", "입출금 거래 입력 시작!");

            $this->form_validation->set_rules('dt'      , '일자'    , 'required');
            $this->form_validation->set_rules('io_tr_cls', '수입구분', 'required');
            //$this->form_validation->set_rules('memo'    , '메모'    , 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                if ($amt <= 0)
                {
                    alert_log("io_tr/ins/", "금액이 0보다 작거나 같습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }



                //입출금거래분류별 건수 체크
                //199 기타수익을 제외하고는 분류별로 동일월에는 한건의 데이터만 존재 가능
                $io_tr_cls = $this->input->post('io_tr_cls', 'TRUE');
                info_log("io_tr/ins/", "io_tr_cls = [" . $io_tr_cls . "]");

                if (strcmp($io_tr_cls, "199") !=0)
                {
                    $dup_chk = $this->stay_m->get_io_tr_cls_dup_chk(str_replace('-', '', $this->input->post('dt', 'TRUE')), $io_tr_cls);

                    info_log("io_tr/ins/", "dup_chk cnt = [" . $dup_chk->cnt . "]");

                    //2021.09.15 수정
                    //탐나는전 충전의 경우 민석/인선 구분 입력할 수 있도록 2건 입력 가능하도록 수정
                    if (strcmp($io_tr_cls, "206") == 0 )
                    {
                        if ($dup_chk->cnt >= 2)
                        {
                            alert_log("io_tr/ins/", "동일 입출금거래분류 존재! 추가 입력 불가! [" . str_replace('-', '', $this->input->post('dt', 'TRUE')) . "/" . $io_tr_cls . "]");
                        }
                    }
                    else
                    {
                        if ($dup_chk->cnt > 0)
                        {
                            alert_log("io_tr/ins/", "동일 입출금거래분류 존재! 추가 입력 불가! [" . str_replace('-', '', $this->input->post('dt', 'TRUE')) . "/" . $io_tr_cls . "]");
                        }
                    }
                }

                $this->db->trans_begin();

                $io_tr_srno = $this->stay_m->get_clm_sr_val('IO_TR_SRNO');

                $i_data = array('io_tr_srno' => $io_tr_srno
                               ,'dt'         => str_replace('-', '', $this->input->post('dt', 'TRUE'))
                               ,'io_tr_cls'  => $io_tr_cls
                               ,'memo'       => trim($memo)
                               ,'amt'        => $amt
                               );

                $result = $this->stay_m->insert_tbb003l00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();

                    info_log("io_tr/ins/", "입출금 거래 입력 완료!");
                    info_log("io_tr/ins/", "================================================================================");

                    $ins_cls = $this->uri->segment(3);
                    //info_log("io_tr/ins/", "ins_cls = [" . $ins_cls . "]");

                    if (strncmp($ins_cls, "r", 1) == 0)
                    {
                        redirect(base_url("io_tr/ins"));
                    }
                    else
                    {
                        redirect(base_url("io_tr/smmry"));
                    }
                }
                else
                {
                    info_log("io_tr/ins/insert_tbb003l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("io_tr/ins/insert_tbb003l00", "[SQL ERR] 입출금 거래 입력 오류!");
                }
            }
            else
            {
                $this->incm_reg_v('i');
            }
        }
        else
        {
            $this->incm_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("io_tr/upd/", "================================================================================");
            info_log("io_tr/upd/", "입출금 거래 수정 시작!");

            $this->form_validation->set_rules('dt'      , '일자'    , 'required');
            $this->form_validation->set_rules('io_tr_cls_hid', '수입구분', 'required');
            //$this->form_validation->set_rules('memo'    , '메모'    , 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                if ($amt <= 0)
                {
                    alert_log("io_tr/upd/", "금액이 0보다 작거나 같습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                //입출금거래분류별 건수 체크
                //199 기타수익을 제외하고는 분류별로 동일월에는 한건의 데이터만 존재 가능
                $io_tr_cls = $this->input->post('io_tr_cls', 'TRUE');
                info_log("io_tr/upd/", "io_tr_cls = [" . $io_tr_cls . "]");

                if (strcmp($io_tr_cls, "199") !=0)
                {
                    $dup_chk = $this->stay_m->get_io_tr_cls_dup_chk(str_replace('-', '', $this->input->post('dt', 'TRUE')), $io_tr_cls);

                    info_log("io_tr/upd/", "dup_chk cnt = [" . $dup_chk->cnt . "]");

                    if ($dup_chk->cnt > 0)
                    {
                        alert_log("io_tr/upd/", "동일 입출금거래분류 존재! 추가 입력 불가! [" . str_replace('-', '', $this->input->post('dt', 'TRUE')) . "/" . $io_tr_cls . "]");
                    }
                }

                $this->db->trans_begin();

                $io_tr_srno = $this->uri->segment(3);

                $u_data = array('io_tr_srno' => $io_tr_srno
                               ,'dt'         => str_replace('-', '', $this->input->post('dt', 'TRUE'))
                               ,'io_tr_cls'  => $this->input->post('io_tr_cls_hid', 'TRUE')
                               ,'memo'       => trim($memo)
                               ,'amt'        => $amt
                               );

                $result = $this->stay_m->update_tbb003l00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("io_tr/upd/update_tbb003l00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("io_tr/upd/update_tbb003l00_1", "[SQL ERR] 입출금 거래 수정 처리 오류![" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("io_tr/upd/", "입출금 거래 수정 완료!");
                        info_log("io_tr/upd/", "================================================================================");

                        redirect(base_url("io_tr/smmry"));
                    }
                }
                else
                {
                    info_log("io_tr/upd/update_tbb003l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("io_tr/upd/update_tbb003l00_1", "[SQL ERR] 입출금 거래 수정 오류!");
                }
            }
            else
            {
                $this->incm_reg_v('u');
            }
        }
        else
        {
            $this->incm_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("io_tr/del/", "================================================================================");
            info_log("io_tr/del/", "입출금 거래 삭제 시작!");

            $this->form_validation->set_rules('dt'      , '일자'    , 'required');
            $this->form_validation->set_rules('io_tr_cls_hid', '수입구분', 'required');
            $this->form_validation->set_rules('memo'    , '메모'    , 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                $this->db->trans_begin();

                $io_tr_srno = $this->uri->segment(3);

                $u_data = array('io_tr_srno'     => $io_tr_srno
                           );

                $result = $this->stay_m->update_tbb003l00_2($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("io_tr/del/update_tbb003l00_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("io_tr/del/update_tbb003l00_2", "[SQL ERR] 입출금 거래 삭제 처리 오류![" . $prcs_cnt . "]!");
                        info_log("io_tr/del/update_tbb003l00_2", "================================================================================");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("io_tr/del/", "입출금 거래 삭제 완료!");
                        info_log("io_tr/del/", "================================================================================");

                        redirect(base_url("io_tr/smmry"));
                    }
                }
                else
                {
                    info_log("io_tr/del/update_tbb003l00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("io_tr/del/update_tbb003l00_2", "[SQL ERR] 입출금 거래 삭제 오류!");
                    info_log("io_tr/del/update_tbb003l00_2", "================================================================================");
                }
            }
            else
            {
                $this->incm_reg_v('u');
            }
        }
        else
        {
            $this->incm_reg_v('u');
        }
    }


    public function incm_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        ////$data['io_tr_cls_list']       = $this->stay_m->get_list('io_tr_cls');
        //$io_tr_cls_result = $this->stay_m->get_list('io_tr_cls');
        //
        //$i = -1;
        //foreach($io_tr_cls_result as $e_cls_list)
        //{
        //    if (!isset($e_cls_list->othr_info))
        //    {
        //        $i = $i + 1;
        //        $incm_large_cls[$i] = $e_cls_list->clm_val_nm;
        //    }
        //    else
        //    {
        //        $io_tr_cls[$incm_large_cls[$i]][$e_cls_list->clm_val] = $e_cls_list->clm_val_nm;
        //    }
        //}
        ////print_r($io_tr_cls);
        //$data['io_tr_cls_list'] = $io_tr_cls;
        //
        //$data['incm_chnl_cls_list']  = $this->stay_m->get_list('incm_CHNL_CLS', 'Y');
        //
        //$data['cost_cls_list']  = $this->stay_m->get_list('COST_CLS', 'Y');

        //print_r($data['incm_chnl_cls_list']);
        //print_r($data['cost_cls_list']);

        $data['io_cls_list']  = $this->stay_m->get_list('IO_CLS', 'N');
        $data['io_tr_cls_list_1']  = $this->stay_m->get_list('IO_TR_CLS', 'N', '1');
        $data['io_tr_cls_list_2']  = $this->stay_m->get_list('IO_TR_CLS', 'N', '2');

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            //info_log("io_tr/incm_reg_v", "this->uri->segment(4) = [" . $this->uri->segment(4) . "]");
            //$data['view'] = $this->stay_m->get_incm_info($this->uri->segment(3));
            $data['view'] = $this->stay_m->get_io_tr_info($this->uri->segment(3));
        }

        //$this->load->view('incm_reg_v', $data);
        $this->load->view('io_tr_reg_v', $data);
    }


    public function io_tr_cls_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;
        $data['IO_TR_CLS'] = $this->uri->segment(3);

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $data['view'] = $this->stay_m->get_item_list('IO_TR_CLS', $this->uri->segment(3), 'single');
        }

        $this->load->view('io_tr_cls_reg_v', $data);
    }


    public function io_tr_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $data['stnd_yymm'] = str_replace('-', '', $this->uri->segment(3));
            $data['io_tr_cls'] = $this->uri->segment(4);
            $data['view'] = $this->stay_m->get_io_tr_info($data['stnd_yymm'], $data['io_tr_cls']);
        }

        $data['io_tr_cls_list1'] = json_decode(json_encode($this->stay_m->get_list('IO_TR_CLS','', '1')), true);
        $data['io_tr_cls_list2'] = json_decode(json_encode($this->stay_m->get_list('IO_TR_CLS','', '2')), true);

        //$data['io_tr_cls_list_1'] = $this->stay_m->get_list('IO_TR_CLS','','1');
        $data['io_tr_cls_list_2'] = $this->stay_m->get_list('IO_TR_CLS','','2');

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        $this->load->view('io_tr_reg_v', $data);
    }

}
