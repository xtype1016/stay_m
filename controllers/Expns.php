<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    예약관리 등록 컨트롤러
*/
class Expns extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $this->load->model('stay_m');
        $this->load->library('form_validation');
        //$this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('cookie');
        //$this->load->helper('My_alert_log');
        $this->load->library('user_agent');

        $usr_no = get_cookie('usr_no');
        $idntfr = get_cookie('idntfr');

        if (!isset($_SESSION['usr_no']) && strlen($usr_no) > 0 && strlen($idntfr) > 0)
        {
            info_log("expns", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->expns_smmry();
        redirect('expns/expns_smmry','refresh');
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


    public function smmry()
    {
        info_log("expns/smmry/", "================================================================================");
        info_log("expns/smmry/", "지출 요약 조회 시작!");

        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        // 검색 변수 초기화
        $stnd_yymm = $page_url = $view_cls = '';

        $view_cls  = $this->uri->segment(3);
        $stnd_yymm = str_replace('-', '', $this->uri->segment(4));

        if (strlen($view_cls) == 0)
        {
            $view_cls = '2';
        }

        if (empty($stnd_yymm))
        {
            $stnd_yymm = date("Ym");
        }

        info_log("expns/smmry/", "view_cls  = [" . $view_cls . "]");
        info_log("expns/smmry/", "stnd_yymm = [" . $stnd_yymm . "]");

        $expns_smmry_uri = uri_string();
        info_log("expns/smmry/", "expns_smmry_uri  = [" . $expns_smmry_uri . "]");
        // bef_list 세션데이터 초기화
        unset($_SESSION['expns_smmry_uri']);
        $this->session->set_userdata('expns_smmry_uri', $expns_smmry_uri);

        $uri_segment = 6;          // 페이지 번호가 위치한 세그먼트

        // Pagination 용 주소
        $page_url = '/' . $view_cls . '/' . $stnd_yymm;

        // 페이지네이션 설정
        $config['base_url']         = '/expns/smmry/' . $page_url . '/page/';

        if (strncmp($view_cls, "1", 1) == 0)
        {
            $config['total_rows']       = $this->stay_m->get_expns_cost_cls_smmry($stnd_yymm, 'rowcnt');         // 표시할 게시물 총 수
        }
        else if (strncmp($view_cls, "2", 1) == 0)
        {
            $config['total_rows']       = $this->stay_m->get_expns_chnl_smmry($stnd_yymm, 'rowcnt');         // 표시할 게시물 총 수
        }
        else if (strncmp($view_cls, "3", 1) == 0)
        {
            $config['total_rows']       = $this->stay_m->get_expns_cls_smmry($stnd_yymm, 'rowcnt');         // 표시할 게시물 총 수
        }
        else
        {
            alert_log("expns/smmry/", "구분값 오류!($view_cls = " . $view_cls . ")" );
        }

        $config['per_page']         = 6;             // 한 페이지에 표시할 게시물 수
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
        //$page = $this->uri->segment(3, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        $page = $this->uri->segment($uri_segment, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        $start = ($page - 1) * $config['per_page'];
        $limit = $config['per_page'];

        if ($config['total_rows'] > 0)
        {
            if (strncmp($view_cls, "1", 1) == 0)
            {
                $data['expns_cost_cls_smmry'] = $this->stay_m->get_expns_cost_cls_smmry($stnd_yymm, 'data', $start, $limit);
            }
            else if (strncmp($view_cls, "2", 1) == 0)
            {
                $data['expns_chnl_smmry'] = $this->stay_m->get_expns_chnl_smmry($stnd_yymm, 'data', $start, $limit);
            }
            else if (strncmp($view_cls, "3", 1) == 0)
            {
                $data['expns_cls_smmry'] = $this->stay_m->get_expns_cls_smmry($stnd_yymm, 'data', $start, $limit);
            }
        }

        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);
        $data['view_cls'] = $view_cls;

        get_list_url();

        info_log("expns/smmry/", "지출 요약 조회 종료!");
        info_log("expns/smmry/", "================================================================================");

        if (strncmp($view_cls, "1", 1) == 0)
        {
            $this->load->view('expns_cost_cls_smmry_v', $data);
        }
        else if (strncmp($view_cls, "2", 1) == 0)
        {
            $this->load->view('expns_chnl_smmry_v', $data);
        }
        else if (strncmp($view_cls, "3", 1) == 0)
        {
            $this->load->view('expns_cls_smmry_v', $data);
        }

    }


    public function dtl()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("expns/dtl/", "================================================================================");
        info_log("expns/dtl/", "지출 상세 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = $page_url = '';
        $call_cls       = "";
        $cost_cls       = "";
        $expns_chnl_cls = "";
        $expns_cls      = "";

        $view_cls = $this->uri->segment(3);
        $stnd_yymm = $this->uri->segment(4);

        if (strncmp($view_cls, "1", 1) == 0)
        {
            $cost_cls = $this->uri->segment(5);
        }
        else if (strncmp($view_cls, "2", 1) == 0)
        {
            $expns_chnl_cls = $this->uri->segment(5);
        }
        else if (strncmp($view_cls, "3", 1) == 0)
        {
            $expns_cls = $this->uri->segment(5);
        }

        info_log("expns/dtl/", "view_cls       = [" . $view_cls . "]");
        info_log("expns/dtl/", "stnd_yymm      = [" . $stnd_yymm . "]");
        info_log("expns/dtl/", "cost_cls       = [" . $cost_cls . "]");
        info_log("expns/dtl/", "expns_chnl_cls = [" . $expns_chnl_cls . "]");
        info_log("expns/dtl/", "expns_cls      = [" . $expns_cls . "]");

        $expns_dtl_uri = uri_string();
        info_log("expns/dtl/", "expns_dtl_uri  = [" . $expns_dtl_uri . "]");
        // bef_list 세션데이터 초기화
        unset($_SESSION['expns_dtl_uri']);
        $this->session->set_userdata('expns_dtl_uri', $expns_dtl_uri);

        $uri_segment = 7;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_yymm))
        {
            $stnd_yymm = date("Ym");
        }

        // Pagination 용 주소
        $page_url = '/' . $view_cls . '/' . $stnd_yymm . '/' . $this->uri->segment(5);

        // 페이지네이션 설정
        $config['base_url']         = '/expns/dtl/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_expns_dtl_list($stnd_yymm, $cost_cls, $expns_chnl_cls, $expns_cls, 'rowcnt');         // 표시할 게시물 총 수
        $config['per_page']         = 7;             // 한 페이지에 표시할 게시물 수
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
        //$page = $this->uri->segment(3, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        $page = $this->uri->segment($uri_segment, 1);
        $start = ($page - 1) * $config['per_page'];
        $limit = $config['per_page'];

        if ($config['total_rows'] > 0)
        {
            $data['expns_dtl_list'] = $this->stay_m->get_expns_dtl_list($stnd_yymm, $cost_cls, $expns_chnl_cls, $expns_cls, 'data', $start, $limit);
            //print_r($data['expns_chnl_smmry']);
        }

        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);
        $data['view_cls'] = $view_cls;

        info_log("expns/dtl/", "지출 상세 조회 완료!");
        info_log("expns/dtl/", "================================================================================");

        $this->load->view('expns_dtl_list_v', $data);

    }


    public function srch()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("expns/srch/", "================================================================================");
        info_log("expns/srch/", "수입/지출 검색 조회 시작!");

        // 검색 변수 초기화
        $memo = $page_url = '';

        $memo = urldecode($this->uri->segment(3));
        $view_cls = urldecode($this->uri->segment(4));
        $uri_segment = 6;          // 페이지 번호가 위치한 세그먼트

        //info_log("expns/srch", "memo = [" . $memo . "]");
        //info_log("expns/srch", "view_cls = [" . $view_cls . "]");

        if (empty($memo))
        {
            $data['total_rows'] = -1;
            $this->load->view('expns_srch_list_v', $data);
            info_log("expns/srch/", "memo empty!");
            info_log("expns/srch/", "수입/지출 검색 최초 조회 완료!");
            info_log("expns/srch/", "================================================================================");
            return;
        }

        //info_log("expns/srch", "segment = [" . $this->uri->segment(5) . "]");

        // Pagination 용 주소
        $page_url = '/' . $memo . '/' . $view_cls . '/';

        // 페이지네이션 설정
        $config['base_url']         = '/expns/srch/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_expns_srch_list($memo, $view_cls, 'rowcnt');         // 표시할 게시물 총 수
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
            $data['expns_srch_list'] = $this->stay_m->get_expns_srch_list($memo, $view_cls, 'data', $start, $limit);
        }

        $data['total_rows'] = $config['total_rows'];
        $data['memo'] = $memo;
        $data['view_cls'] = $view_cls;

        info_log("expns/srch/", "수입/지출 검색 조회 완료!");
        info_log("expns/srch/", "================================================================================");

        $this->load->view('expns_srch_list_v', $data);

    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("expns/ins/", "================================================================================");
            info_log("expns/ins/", "지출 입력 시작!");

            $this->form_validation->set_rules('expns_dt'  , '거래일', 'required');
            $this->form_validation->set_rules('memo'      , '메모'  , 'required');
            $this->form_validation->set_rules('whr_to_buy', '구입처', 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                if ($amt <= 0)
                {
                    alert_log("rsvt/cncl", "지출금액이 0보다 작거나 같습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                if (strlen($this->input->post('ssamzi_yn', 'TRUE')) > 0)
                {
                    $ssamzi_yn = $this->input->post('ssamzi_yn', 'TRUE');
                }
                else
                {
                    $ssamzi_yn = 'N';
                }

                //info_log("expns/ins", "ssamzi_yn = [" . $this->input->post('ssamzi_yn', 'TRUE') . "]");

                $this->db->trans_begin();

                $expns_srno = $this->stay_m->get_clm_sr_val('EXPNS_SRNO');

                $i_data = array('expns_srno'     => $expns_srno
                               ,'expns_dt'       => str_replace('-', '', $this->input->post('expns_dt', 'TRUE'))
                               ,'expns_chnl_cls' => $this->input->post('expns_chnl_cls'               , 'TRUE')
                               ,'expns_cls'      => $this->input->post('expns_cls'                    , 'TRUE')
                               ,'memo'           => trim($memo)
                               ,'whr_to_buy'     => trim($this->input->post('whr_to_buy'              , 'TRUE'))
                               ,'ssamzi_yn'      => $ssamzi_yn
                               ,'cost_cls'       => $this->input->post('cost_cls'                     , 'TRUE')
                               ,'amt'            => $amt
                               );

                $result = $this->stay_m->insert_tbb001l00($i_data);

                if (!$result)
                {
                    info_log("expns/ins/insert_tbb001l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("expns/ins/insert_tbb001l00", "[SQL ERR] 지출 입력 오류!");
                }


                $this->db->trans_commit();

                $view_cls    = $this->uri->segment(3);
                $ins_cls     = $this->uri->segment(4);

                info_log("expns/ins/", "view_cls = [" . $view_cls . "]");
                info_log("expns/ins/", "ins_cls  = [" . $ins_cls . "]");

                if (isset($ins_cls) && strncmp($ins_cls, "r", 1) == 0)
                {
                    $redirect_url = "expns/ins/" . $view_cls . '/' . $ins_cls;
                }
                else
                {
                    $expns_dt = str_replace('-', '', $this->input->post('expns_dt', 'TRUE'));
                    $stnd_yymm = substr($expns_dt, 0, 6);
                    $redirect_url = "expns/smmry/" . $view_cls . "/" . $stnd_yymm;
                }

                info_log("expns/ins/", "redirect_url  = [" . $redirect_url . "]");
                info_log("expns/ins/", "base_url  = [" . base_url() . "]");
                info_log("expns/ins/", "지출 입력 완료!");
                info_log("expns/ins/", "================================================================================");

                redirect(base_url($redirect_url));
            }
            else
            {
                $this->expns_reg_v('i');
            }
        }
        else
        {
            $this->expns_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("expns/upd/", "================================================================================");
            info_log("expns/upd/", "지출 수정 시작!");

            $this->form_validation->set_rules('expns_dt'  , '거래일', 'required');
            $this->form_validation->set_rules('memo'      , '메모'  , 'required');
            $this->form_validation->set_rules('whr_to_buy', '구입처', 'required');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                if ($amt <= 0)
                {
                    alert_log("rsvt/cncl", "지출금액이 0보다 작거나 같습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                if (strlen($this->input->post('ssamzi_yn', 'TRUE')) > 0)
                {
                    $ssamzi_yn = $this->input->post('ssamzi_yn', 'TRUE');
                }
                else
                {
                    $ssamzi_yn = 'N';
                }

                $this->db->trans_begin();

                $expns_srno = $this->uri->segment(3);

                $u_data = array('expns_srno'     => $expns_srno
                               ,'expns_dt'       => str_replace('-', '', $this->input->post('expns_dt', 'TRUE'))
                               ,'expns_chnl_cls' => $this->input->post('expns_chnl_cls'               , 'TRUE')
                               ,'expns_cls'      => $this->input->post('expns_cls'                    , 'TRUE')
                               ,'memo'           => trim($memo)
                               ,'whr_to_buy'     => trim($this->input->post('whr_to_buy'              , 'TRUE'))
                               ,'ssamzi_yn'      => $ssamzi_yn
                               ,'cost_cls'       => $this->input->post('cost_cls'                     , 'TRUE')
                               ,'amt'            => $amt
                               );

                $result = $this->stay_m->update_tbb001l00_1($u_data);

                if (!$result)
                {
                    info_log("expns/upd/update_tbb001l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("expns/upd/update_tbb001l00_1", "[SQL ERR] 지출 수정 오류!");
                }

                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $this->db->affected_rows();
                if ($prcs_cnt != 1)
                {
                    info_log("expns/upd/update_tbb001l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("expns/upd/update_tbb001l00_1", "[SQL ERR] 지출 수정 처리 오류![" . $prcs_cnt . "]!");
                }

                $this->db->trans_commit();

                //$temp_segment = explode('/', $_SESSION['expns_dtl_uri']);
                //
                //info_log("expns/upd/", "temp_segment[2]  = [" . $temp_segment[2] . "]");
                //info_log("expns/upd/", "temp_segment[4]  = [" . $temp_segment[4] . "]");
                //
                //$expns_dt = str_replace('-', '', $this->input->post('expns_dt', 'TRUE'));
                //$stnd_yymm = substr($expns_dt, 0, 6);
                //$redirect_url = "expns/dtl/" . $temp_segment[2] . "/" . $stnd_yymm . "/" . $temp_segment[4];
                $redirect_url = $_SESSION['expns_dtl_uri'];
                info_log("expns/upd", "redirect_url  = [" . $redirect_url . "]");

                info_log("expns/upd/", "지출 수정 완료!");
                info_log("expns/upd/", "================================================================================");

                redirect(base_url($redirect_url));
            }
            else
            {
                $this->expns_reg_v('u');
            }
        }
        else
        {
            $this->expns_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("expns/del/", "================================================================================");
            info_log("expns/del/", "지출 삭제 시작!");

            $this->db->trans_begin();

            $expns_srno = $this->uri->segment(3);
            info_log("expns/del/", "expns_srno = [" . $expns_srno . "]");

            $u_data = array('expns_srno'     => $expns_srno
                           );

            $result = $this->stay_m->update_tbb001l00_2($u_data);

            if (!$result)
            {
                info_log("expns/del/update_tbb001l00_2", "last_query  = [" . $this->db->last_query() . "]");
                $this->db->trans_rollback();
                alert_log("expns/del/update_tbb001l00_2", "[SQL ERR] 지출 삭제 오류!");
            }

            // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
            $prcs_cnt = $this->db->affected_rows();
            if ($prcs_cnt != 1)
            {
                info_log("expns/del/update_tbb001l00_2", "last_query  = [" . $this->db->last_query() . "]");
                $this->db->trans_rollback();
                alert_log("expns/del/update_tbb001l00_2", "[SQL ERR] 지출 삭제 건수 오류![" . $prcs_cnt . "]!");
            }
            else
            {
                $this->db->trans_commit();

                $redirect_url = $_SESSION['expns_dtl_uri'];
                info_log("expns/del/", "redirect_url  = [" . $redirect_url . "]");

                info_log("expns/del/", "지출 삭제 완료!");
                info_log("expns/del/", "================================================================================");

                redirect(base_url($redirect_url));
            }
        }
    }


    public function expns_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        //$data['expns_cls_list']       = $this->stay_m->get_list('EXPNS_CLS');
        $expns_cls_result = $this->stay_m->get_list('EXPNS_CLS');

        $i = -1;
        foreach($expns_cls_result as $e_cls_list)
        {
            if (!isset($e_cls_list->othr_info))
            {
                $i = $i + 1;
                $expns_large_cls[$i] = $e_cls_list->clm_val_nm;
            }
            else
            {
                $expns_cls[$expns_large_cls[$i]][$e_cls_list->clm_val] = $e_cls_list->clm_val_nm;
            }
        }
        //print_r($expns_cls);
        $data['expns_cls_list'] = $expns_cls;
        $data['expns_chnl_cls_list']  = $this->stay_m->get_list('EXPNS_CHNL_CLS', 'Y');
        $data['cost_cls_list']  = $this->stay_m->get_list('COST_CLS', 'Y');

        //print_r($data['expns_chnl_cls_list']);
        //print_r($data['cost_cls_list']);

        if (strncmp($prcs_cls, "i", 1) == 0)
        {
            $data['call_cls'] = $this->uri->segment(3);
            $data['ins_cls']  = $this->uri->segment(4);
        }

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            //info_log("expns/expns_reg_v", "this->uri->segment(4) = [" . $this->uri->segment(4) . "]");
            $data['view'] = $this->stay_m->get_expns_info($this->uri->segment(3));
        }

        $this->load->view('expns_reg_v', $data);
    }


}