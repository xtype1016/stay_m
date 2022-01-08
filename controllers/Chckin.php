<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    고객 관리 등록 컨트롤러
*/
class Chckin extends CI_Controller
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
        $this->load->library('googleapi');

        $usr_no = get_cookie('usr_no');
        $idntfr = get_cookie('idntfr');

        if (!isset($_SESSION['usr_no']) && strlen($usr_no) > 0 && strlen($idntfr) > 0)
        {
            info_log("chckin", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->list();
        redirect('chckin/list','refresh');
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


    public function list()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("chckin/list", "================================================================================");
        info_log("chckin/list", "체크인 고객 리스트 조회 시작!");

        // 검색 변수 초기화
        $uri_segment = 4;          // 페이지 번호가 위치한 세그먼트

        //2019.12.27. 검색시 변수전달 Get >> Post 변경
        $stnd_dt = str_replace('-', '', $this->input->post('stnd_dt', 'TRUE'));
        if (empty($stnd_dt))
        {
            $stnd_dt = date("Ymd");
        }

        $page_url = '';

        //info_log("chckin/list", "stnd_dt  = [" . $stnd_dt . "]");

        // 페이지네이션 설정
        $config['base_url']         = '/chckin/list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_chckin_list($stnd_dt, 'rowcnt');         // 표시할 게시물 총 수
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
            $data['chckin_list'] = $this->stay_m->get_chckin_list($stnd_dt, 'data', $start, $limit);
            //info_log("gst/list/get_item_list", "last_query  = [" . $this->db->last_query() . "]");
        }

        $data['total_rows'] = $config['total_rows'];


        $data['stnd_dt'] = substr($stnd_dt, 0, 4) . "-" . substr($stnd_dt, 4, 2) . "-" . substr($stnd_dt, 6, 2);

        //info_log("chckin/list/get_chckin_list", "data['stnd_dt']  = [" . $data['stnd_dt'] . "]");

        info_log("chckin/list", "체크인 고객 리스트 조회 완료!");
        info_log("chckin/list", "================================================================================");

        $this->load->view('chckin_list_v', $data);
    }


}