<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    수입(income) 조회
*/

Class Incm extends CI_Controller
{
    function __construct()
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
            info_log("incm", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->smmry();
        redirect('incm/smmry','refresh');
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
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("incm/smmry/", "================================================================================");
        info_log("incm/smmry/", "수입 요약 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = $page_url = '';

        //2019.12.27. 검색시 변수전달 Get >> Post 변경
        //$stnd_yymm = str_replace('-', '', $this->uri->segment(3));
        //$view_cls  = $this->uri->segment(4);
        if (empty($this->uri->segment(3)))
        {
            $stnd_yymm = str_replace('-', '', $this->input->post('stnd_yymm', 'TRUE'));
            $view_cls  = $this->input->post('view_cls', 'TRUE');
        }
        else
        {
            $stnd_yymm = str_replace('-', '', $this->uri->segment(3));
            $view_cls  = $this->uri->segment(4);
        }

        $uri_segment = 6;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_yymm))
        {
            //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
            $stnd_yymm = date("Ym");
            $view_cls = "1";
         }

        //info_log("incm/smmry", "view_cls = [" . $view_cls . "]");

        $stnd_dt = date("Ymd");

        // Pagination 용 주소
        $page_url = '/' . $stnd_yymm . '/' . $stnd_dt;

        // 페이지네이션 설정
        $config['base_url']         = '/incm/smmry/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_incm_smmry($view_cls, $stnd_yymm, 'rowcnt', 0, 100);         // 표시할 게시물 총 수

        //if (strncmp($view_cls, "1", 1) == 0)
        //{
        //    $config['total_rows']       = $this->stay_m->get_incm_smmry($stnd_yymm, $stnd_dt, 'rowcnt', 0, 100);         // 표시할 게시물 총 수
        //}
        //else if (strncmp($view_cls, "2", 1) == 0)
        //{
        //    $config['total_rows']       = $this->stay_m->get_incm_smmry2($stnd_yymm, $stnd_dt, 'rowcnt', 0, 100);         // 표시할 게시물 총 수
        //}
        //else
        //{
        //    info_log("incm/smmry", "Error! view_cls = [" . $view_cls . "]");
        //    alert_log("incm/smmry", "변수(view_cls) 오류!");
        //}

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
        //$page = $this->uri->segment(3, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        $page = $this->uri->segment($uri_segment, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        $start = ($page - 1) * $config['per_page'];
        $limit = $config['per_page'];

        if ($config['total_rows'] > 0)
        {
            $data['incm_smmry'] = $this->stay_m->get_incm_smmry($view_cls, $stnd_yymm, 'data', $start, $limit);
        }

        //if ($config['total_rows'] > 0)
        //{
        //    if (strncmp($view_cls, "1", 1) == 0)
        //    {
        //        $data['incm_smmry'] = $this->stay_m->get_incm_smmry($stnd_yymm, $stnd_dt, 'data', $start, $limit);
        //    }
        //    else if (strncmp($view_cls, "2", 1) == 0)
        //    {
        //        $data['incm_smmry'] = $this->stay_m->get_incm_smmry2($stnd_yymm, $stnd_dt, 'data', $start, $limit);
        //    }
        //}

        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);

        $data['view_cls'] = $view_cls;

        info_log("incm/smmry/", "수입 요약 조회 완료!");
        info_log("incm/smmry/", "================================================================================");

        $this->load->view('incm_smmry_v', $data);
    }


    public function list()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("incm/list/", "================================================================================");
        info_log("incm/list/", "수입 리스트 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = $page_url = '';

        $stnd_yymm = str_replace('-', '', $this->uri->segment(3));
        $view_cls = $this->uri->segment(4);

        $uri_segment = 6;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_yymm))
        {
            //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
            $stnd_yymm = date("Ym");
            $view_cls = "0";
        }

        if (strcmp($view_cls, "0") == 0)
        {
            $rsv_chnl_cls = "%";
        }
        else
        {
            $rsv_chnl_cls = $view_cls;
        }

        info_log("incm/list", "rsv_chnl_cls = [" . $rsv_chnl_cls . "]");

        // Pagination 용 주소
        $page_url = '/' . $stnd_yymm . '/' . $this->uri->segment(4);

        // 페이지네이션 설정
        $config['base_url']         = '/incm/list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_incm_list($stnd_yymm, $rsv_chnl_cls, 'rowcnt');         // 표시할 게시물 총 수
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
        //$page = $this->uri->segment(3, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        $page = $this->uri->segment($uri_segment, 1);  // 3번째 세그먼트 값을 가져오되 없는 경우 1
        $start = ($page - 1) * $config['per_page'];
        $limit = $config['per_page'];

        if ($config['total_rows'] > 0)
        {
            $data['incm_list'] = $this->stay_m->get_incm_list($stnd_yymm, $rsv_chnl_cls, 'data', $start, $limit);
        }

        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);

        $data['view_cls'] = $view_cls;

        $bef_url = uri_string();
        info_log("incm/list/", "bef_url        = [" . $bef_url . "]");
        // bef_url 세션데이터 초기화
        unset($_SESSION['bef_url']);
        $this->session->set_userdata('bef_url', $bef_url);

        info_log("incm/list/", "수입 리스트 조회 완료!");
        info_log("incm/list/", "================================================================================");

        $this->load->view('incm_list_v', $data);
    }

}