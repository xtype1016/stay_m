<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    예약관리 등록 컨트롤러
*/
class Rsvt extends CI_Controller
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
            info_log("rsvt", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->ins();
        redirect('rsvt/ins','refresh');
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

        info_log("rsvt/list/", "================================================================================");
        info_log("rsvt/list/", "예약 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = $page_url = '';

        //2019.12.27. 검색시 변수전달 Get >> Post 변경
        //$stnd_yymm    = str_replace('-', '', $this->uri->segment(3));
        //$hsrm_cls     = $this->uri->segment(4);
        //$rsv_chnl_cls = $this->uri->segment(5);

        //2020.01.11 검색조건 수정
        $stnd_yymm    = str_replace('-', '', $this->uri->segment(3));
        $hsrm_cls     = $this->uri->segment(4);
        $rsv_chnl_cls = $this->uri->segment(5);
        $view_cls     = $this->uri->segment(6);

        $uri_segment = 8;          // 페이지 번호가 위치한 세그먼트

        //info_log("rsvt/list", "stnd_yymm    = [" . $stnd_yymm . "]");
        //info_log("rsvt/list", "hsrm_cls     = [" . $hsrm_cls . "]");
        //info_log("rsvt/list", "rsv_chnl_cls = [" . $rsv_chnl_cls . "]");

        //$uri_segment = 7;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_yymm))
        {
            //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
            $stnd_yymm = date("Ym");
            //echo "NOT POST AF stnd_yymm = " . $stnd_yymm . "<BR>";
            //echo "Check Point02!!!<br>";
        }

        if (empty($hsrm_cls))
        {
            $hsrm_cls = 'all';
        }

        if (empty($rsv_chnl_cls))
        {
            $rsv_chnl_cls = 'all';
        }

        if (empty($view_cls))
        {
            $view_cls = '1';
        }

        $bef_url = uri_string();
        //info_log("rsvt/list/", "bef_url        = [" . $bef_url . "]");
        // bef_url 세션데이터 초기화
        unset($_SESSION['bef_url']);
        $this->session->set_userdata('bef_url', $bef_url);


        // Pagination 용 주소
        $page_url = '/' . $stnd_yymm . '/' . $hsrm_cls . '/' . $rsv_chnl_cls . '/' . $view_cls;

        // 페이지네이션 설정
        $config['base_url']         = '/rsvt/list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_rsvt_list($stnd_yymm, $hsrm_cls, $rsv_chnl_cls, $view_cls, 'rowcnt');         // 표시할 게시물 총 수
        $config['per_page']         = 5;             // 한 페이지에 표시할 게시물 수
        $config['uri_segment']      = $uri_segment;  // 페이지번호가 위치한 세그먼트
        $config['num_links']        = 3;             // 선택된 페이지번호 좌우로 몇 개의 숫자 링크를 보여줄 지 설정
        $config['use_page_numbers'] = TRUE;          // 링크를 1, 2, 3 으로 표기
        $config['first_link']       = '<<';          // 처음으로 링크 생성
        $config['last_link']        = '>>';          // 끝으로 링크 생성
        $config['next_link']        = '>';
        $config['prev_link']        = '<';

        $config['full_tag_open']    = "<ul class='pagination pagination-sm'>";
        $config['full_tag_close']   = '</ul>';
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
            $data['rsvt_list'] = $this->stay_m->get_rsvt_list($stnd_yymm, $hsrm_cls, $rsv_chnl_cls, $view_cls, 'data', $start, $limit);
        }
        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);
        $data['hsrm_cls']  = $hsrm_cls;

        $t_hsrm_cls_list = new stdClass();
        $t_hsrm_cls_list->clm_nm     = 'HSRM_CLS';
        $t_hsrm_cls_list->clm_val    = 'all';
        $t_hsrm_cls_list->clm_val_nm = '전체';
        $t_hsrm_cls_list->othr_info  = '';

        $hsrm_cls_list = $this->stay_m->get_list('HSRM_CLS');

        array_unshift($hsrm_cls_list, $t_hsrm_cls_list);

        //print_r($data['rsvt_list']);
        //print_r($hsrm_cls_list);
        //info_log("rsvt/list", "count(rsvt_list)  = [" . count($data['rsvt_list']) . "]");

        $data['hsrm_cls_list'] = $hsrm_cls_list;

        $data['rsv_chnl_cls']  = $rsv_chnl_cls;

        $t_rsv_chnl_cls_list = new stdClass();
        $t_rsv_chnl_cls_list->clm_nm     = 'RSV_CHNL_CLS';
        $t_rsv_chnl_cls_list->clm_val    = 'all';
        $t_rsv_chnl_cls_list->clm_val_nm = '전체';
        $t_rsv_chnl_cls_list->othr_info  = '';

        $rsv_chnl_cls_list = $this->stay_m->get_list('RSV_CHNL_CLS', 'Y');

        array_unshift($rsv_chnl_cls_list, $t_rsv_chnl_cls_list);

        $data['view_cls']  = $view_cls;

        //print_r($data['rsvt_list']);
        //print_r($hsrm_cls_list);
        //info_log("rsvt/list", "count(rsvt_list)  = [" . count($data['rsvt_list']) . "]");

        $data['rsv_chnl_cls_list'] = $rsv_chnl_cls_list;

        info_log("rsvt/list/", "예약 조회 완료!");
        info_log("rsvt/list/", "================================================================================");

        $this->load->view('rsvt_list_v', $data);
    }


    public function cncl_list()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("rsvt/cncl_list", "================================================================================");
        info_log("rsvt/cncl_list", "예약 취소 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = $page_url = '';

        $stnd_yymm = str_replace('-', '', $this->uri->segment(3));
        $hsrm_cls  = $this->uri->segment(4);

        $uri_segment = 6;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_yymm))
        {
            //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
            $stnd_yymm = date("Ym");
            //echo "NOT POST AF stnd_yymm = " . $stnd_yymm . "<BR>";
            //echo "Check Point02!!!<br>";
        }

        if (empty($hsrm_cls))
        {
            $hsrm_cls = 'all';
        }

        //info_log("rsvt/cncl_list", "Test1");

        // Pagination 용 주소
        $page_url = '/' . $stnd_yymm . '/' . $hsrm_cls;

        // 페이지네이션 설정
        $config['base_url']         = '/rsvt/cncl_list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_rsvt_cncl_list($stnd_yymm, $hsrm_cls, 'rowcnt', 0, 100);         // 표시할 게시물 총 수
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
            $data['cncl_list'] = $this->stay_m->get_rsvt_cncl_list($stnd_yymm, $hsrm_cls, 'data', $start, $limit);
        }

        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);
        $data['hsrm_cls']  = $hsrm_cls;

        $t_hsrm_cls_list = new stdClass();
        $t_hsrm_cls_list->clm_nm     = 'HSRM_CLS';
        $t_hsrm_cls_list->clm_val    = 'all';
        $t_hsrm_cls_list->clm_val_nm = '전체';
        $t_hsrm_cls_list->othr_info  = '';

        $hsrm_cls_list = $this->stay_m->get_list('HSRM_CLS');

        array_unshift($hsrm_cls_list, $t_hsrm_cls_list);

        //print_r($data['rsvt_list']);
        //print_r($hsrm_cls_list);
        //info_log("rsvt/list", "count(rsvt_list)  = [" . count($data['rsvt_list']) . "]");

        $data['hsrm_cls_list'] = $hsrm_cls_list;

        info_log("rsvt/cncl_list", "예약 취소 조회 완료!");
        info_log("rsvt/cncl_list", "================================================================================");

        $this->load->view('rsvt_cncl_list_v', $data);

    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("rsvt/ins", "================================================================================");
            info_log("rsvt/ins", "예약 입력 처리 시작!");

            $this->form_validation->set_rules('cnfm_dt'     , '예약확정일자', 'required');
            $this->form_validation->set_rules('hsrm_cls'    , '숙소'        , 'required');
            $this->form_validation->set_rules('srt_dt'      , '시작일자'    , 'required');
            $this->form_validation->set_rules('end_dt'      , '종료일자'    , 'required');
            if (strncmp($_SESSION['usr_no'], '0000000005', 10) != 0)
            {
                $this->form_validation->set_rules('gst_no'      , '고객번호'    , 'required');
            }
            else
            {
                $this->form_validation->set_rules('gst_nm'      , '고객명'    , 'required');
            }
            $this->form_validation->set_rules('rsv_chnl_cls', '예약채널'    , 'required');
            //$this->form_validation->set_rules('gst_desc'    , '고객구성상세', 'required');
            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                $cnfm_dt = str_replace('-', '', $this->input->post('cnfm_dt', 'TRUE'));
                $srt_dt  = str_replace('-', '', $this->input->post('srt_dt' , 'TRUE'));
                $end_dt  = str_replace('-', '', $this->input->post('end_dt' , 'TRUE'));

                // 예약확정일이 시작일보다 작으면 오류
                if (strncmp($cnfm_dt, $srt_dt, 8) > 0)
                {
                    alert_log("rsvt/ins", "예약확정일자가 시작일보다 늦습니다!");
                }

                // 시작일이 종료일보다 작으면 오류
                if (strncmp($srt_dt, $end_dt, 8) > 0)
                {
                    alert_log("rsvt/ins", "시작일자가 종료일보다 늦습니다!");
                }

                // 시작일과 종료일이 동일한 경우 오류
                // 2021.05.05. 1박 예약 등록 가능하도록 해당 체크 주석처리
                //if (strncmp($srt_dt, $end_dt, 8) == 0)
                //{
                //    alert_log("rsvt/ins", "시작일자와 종료일자가 같습니다!");
                //}
                // 2021.05.05. 1박 예약 등록 가능하도록 해당 체크 주석처리

                if ($this->input->post('gst_desc', 'TRUE') != '')
                {
                    $gst_desc = trim($this->input->post('gst_desc', 'TRUE'));
                }
                else
                {
                    $gst_desc = NULL;
                }

                if ($this->input->post('memo', 'TRUE') != '')
                {
                    $memo = trim($this->input->post('memo', 'TRUE'));
                }
                else
                {
                    $memo = NULL;
                }

                $gst_no = $this->input->post('gst_no', 'TRUE');

                $this->db->trans_begin();

                $rsv_srno = $this->stay_m->get_clm_sr_val('RSV_SRNO');

                $i_data = array('rsv_srno'     => $rsv_srno
                               ,'cnfm_dt'      => $cnfm_dt
                               ,'hsrm_cls'     => $this->input->post('hsrm_cls'                    , 'TRUE')
                               ,'srt_dt'       => $srt_dt
                               ,'end_dt'       => $end_dt
                               ,'gst_no'       => $gst_no
                               ,'amt'          => str_replace(',', '', $this->input->post('amt'    , 'TRUE'))
                               ,'deposit'      => str_replace(',', '', $this->input->post('deposit', 'TRUE'))
                               ,'rsv_chnl_cls' => $this->input->post('rsv_chnl_cls'                , 'TRUE')
                               ,'gst_desc'     => $gst_desc
                               ,'adlt_cnt'     => $this->input->post('adlt_cnt'                    , 'TRUE')
                               ,'chld_cnt'     => $this->input->post('chld_cnt'                    , 'TRUE')
                               ,'gst_cls'      => $this->input->post('gst_cls'                     , 'TRUE')
                               ,'evnt_id'      => NULL
                               ,'memo'         => $memo
                               ,'cncl_yn'      => 'N'
                               ,'cncl_dt'      => NULL
                               );

                $result = $this->stay_m->insert_tba005l00($i_data);

                if (!$result)
                {
                    info_log("rsvt/ins/insert_tba005l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/ins/insert_tba005l00", "[SQL ERR] 예약 입력 처리 오류!");
                }

                $rsv_chnl_cls = $this->input->post('rsv_chnl_cls'           , 'TRUE') ;
                $deposit = str_replace(',', '', $this->input->post('deposit', 'TRUE'));

                info_log("rsvt/ins", "rsv_chnl_cls = [" . $rsv_chnl_cls . "]");
                info_log("rsvt/ins", "deposit      = [" . $deposit . "]");

                //======================================================================================================
                // 2020.12.30 예약금 입금 거래내역 생성 BEGIN
                // AB의 경우도 생성.
                if (strcmp($rsv_chnl_cls, "1") == 0)
                {
                    $tr_cls = "01";
                    $tr_memo = "BL 예약금 입금";
                    $tr_dt = $cnfm_dt;
                }
                else if (strcmp($rsv_chnl_cls, "2") == 0)
                {
                    $tr_cls = "02";
                    $tr_memo = "AB 입금";

                    $result = $this->stay_m->get_pay_rcv_dt($srt_dt);
                    if (!$result)
                    {
                        info_log("rsvt/ins/get_pay_rcv_dt", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("rsvt/ins/get_pay_rcv_dt", "AB 입금일자 조회 오류!");
                    }
                    $tr_dt = $result->rcv_dt;
                }
                //2022.12.11 네이버 채널 추가 Begin
                else if (strcmp($rsv_chnl_cls, "3") == 0)
                {
                    $tr_cls = "03";
                    $tr_memo = "네이버 예약금 입금";
                    $tr_dt = $cnfm_dt;
                }
                else
                {
                    info_log("rsvt/ins/예약금 입금", "rsv_chnl_cls  = [" . $rsv_chnl_cls . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/ins/예약금 입금", "[SQL ERR] 미존재 예약 채널!");
                }
                //2022.12.11 네이버 채널 추가 End

                $tr_srno = $this->stay_m->get_clm_sr_val('TR_SRNO');

                info_log("rsvt/ins/예약금 입금/", "tr_srno   = [" . $tr_srno . "]");
                info_log("rsvt/ins/예약금 입금/", "tr_cls    = [" . $tr_cls . "]");
                info_log("rsvt/ins/예약금 입금/", "tr_dt     = [" . $tr_dt . "]");

                $i_data = array('tr_srno'          => $tr_srno
                               ,'rsv_srno'         => $rsv_srno
                               ,'tr_dt'            => $tr_dt
                               ,'tr_cls'           => $tr_cls
                               ,'tr_chnl_cls'      => '2'
                               ,'amt'              => str_replace(',', '', $this->input->post('amt'    , 'TRUE'))
                               ,'memo'             => $tr_memo
                               ,'othr_withdraw_yn' => 'N'
                               ,'expns_srno'       => NULL
                               );

                $result = $this->stay_m->insert_tba006l00($i_data);

                if (!$result)
                {
                    info_log("rsvt/ins/insert_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/ins/insert_tba006l00", "예약 거래 입력 오류!");
                }
                // 2020.12.30 예약금 입금 거래내역 생성 END
                //======================================================================================================


                //======================================================================================================
                // 2020.09.22 보증금 입금 추가 BEGIN
                // 블로그 예약 AND 보증금 존재시 보증금 입금 처리
                if (strcmp($rsv_chnl_cls, "1") == 0 && $deposit > 0)
                {
                    $tr_srno = $this->stay_m->get_clm_sr_val('TR_SRNO');

                    info_log("rsvt/ins/보증금 입금/", "tr_srno     = [" . $tr_srno . "]");

                    $i_data = array('tr_srno'          => $tr_srno
                                   ,'rsv_srno'         => $rsv_srno
                                   ,'tr_dt'            => $cnfm_dt
                                   ,'tr_cls'           => '31'
                                   ,'tr_chnl_cls'      => '2'
                                   ,'amt'              => $deposit
                                   ,'memo'             => 'BL 예약 보증금 입금'
                                   ,'othr_withdraw_yn' => 'N'
                                   ,'expns_srno'       => NULL
                                   );

                    $result = $this->stay_m->insert_tba006l00($i_data);

                    if (!$result)
                    {
                        info_log("rsvt/ins/insert_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("rsvt/ins/insert_tba006l00", "BL 예약 (보증금 입금) 입력 오류!");
                    }
                }
                // 2020.09.22 보증금 입금 추가 END
                //======================================================================================================

                //info_log("rsvt/ins", "[" . base_url() . "]");

                // 구글 캘린더 처리
                //if (strncmp("http://192.168.1.51", base_url(), 19) == 0)
                if (strcmp("https://xsvr.duckdns.org/", base_url()) == 0)
                {
                    //$this->auth('ins', $rsv_srno, '', '');
                    g_auth('ins', $rsv_srno, '', '', '');
                }

                $this->db->trans_commit();

                info_log("rsvt/ins", "rsv_srno = [" . $rsv_srno . "]");
                info_log("rsvt/ins", "예약 입력 처리 종료!");
                info_log("rsvt/ins", "================================================================================");

                //redirect(base_url("rsvt/list"));
                info_log("rsvt/ins", "redirect cnfm_msg!");
                redirect(base_url("rsvt/cnfm_msg/" . $rsv_srno));
            }
            else
            {
                $this->rsvt_reg_v('i');
            }
        }
        else
        {
            $this->rsvt_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("rsvt/upd", "================================================================================");
            info_log("rsvt/upd", "예약 수정 처리 시작!");

            $this->form_validation->set_rules('cnfm_dt'     , '예약확정일자', 'required');
            $this->form_validation->set_rules('hsrm_cls'    , '숙소'        , 'required');
            $this->form_validation->set_rules('srt_dt'      , '시작일자'    , 'required');
            $this->form_validation->set_rules('end_dt'      , '종료일자'    , 'required');
            $this->form_validation->set_rules('gst_no'      , '고객번호'    , 'required');
            $this->form_validation->set_rules('rsv_chnl_cls', '예약채널'    , 'required');
            //$this->form_validation->set_rules('gst_desc'    , '고객구성상세', 'required');
            $this->form_validation->set_rules('rsv_srno'    , '예약일련번호', 'required');
            //$this->form_validation->set_rules('evnt_id'     , '이벤트ID'    , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $cnfm_dt = str_replace('-', '', $this->input->post('cnfm_dt', 'TRUE'));
                $srt_dt  = str_replace('-', '', $this->input->post('srt_dt' , 'TRUE'));
                $end_dt  = str_replace('-', '', $this->input->post('end_dt' , 'TRUE'));

                // 예약확정일이 시작일보다 작으면 오류
                if (strncmp($cnfm_dt, $srt_dt, 8) > 0)
                {
                    alert_log("rsvt/upd", "예약확정일자가 시작일보다 늦습니다!");
                }

                // 시작일이 종료일보다 작으면 오류
                if (strncmp($srt_dt, $end_dt, 8) > 0)
                {
                    alert_log("rsvt/upd", "시작일자가 종료일보다 늦습니다!");
                }

                if ($this->input->post('gst_desc', 'TRUE') != '')
                {
                    $gst_desc = trim($this->input->post('gst_desc', 'TRUE'));
                }
                else
                {
                    $gst_desc = NULL;
                }

                if ($this->input->post('memo', 'TRUE') != '')
                {
                    $memo = trim($this->input->post('memo', 'TRUE'));
                }
                else
                {
                    $memo = NULL;
                }

                $this->db->trans_begin();

                $rsv_srno     = $this->input->post('rsv_srno', 'TRUE');
                $amt          = str_replace(',', '', $this->input->post('amt'    , 'TRUE'));
                $rsv_chnl_cls = $this->input->post('rsv_chnl_cls'           , 'TRUE');
                $deposit      = str_replace(',', '', $this->input->post('deposit', 'TRUE'));

                info_log("rsvt/upd", "rsv_srno     = [" . $rsv_srno . "]");

                // 2018.01.07
                // 숙소 변경시 캘린더 삭제 오류 발생하여 기존 숙소정보를 쿠키에 저장하도록 수정
                $rsvt_info = $this->stay_m->get_rsvt_info($rsv_srno);

                info_log("rsvt/upd", "bef_hsrm_cls      = [" . $rsvt_info->hsrm_cls  . "]");
                info_log("rsvt/upd", "aft_hsrm_cls      = [" . $this->input->post('hsrm_cls', 'TRUE') . "]");

                $u_data = array('rsv_srno'     => $rsv_srno
                               ,'cnfm_dt'      => $cnfm_dt
                               ,'hsrm_cls'     => $this->input->post('hsrm_cls'                    , 'TRUE')
                               ,'srt_dt'       => $srt_dt
                               ,'end_dt'       => $end_dt
                               ,'gst_no'       => $this->input->post('gst_no'                      , 'TRUE')
                               ,'amt'          => $amt
                               ,'deposit'      => $deposit
                               ,'rsv_chnl_cls' => $rsv_chnl_cls
                               ,'gst_desc'     => $gst_desc
                               ,'adlt_cnt'     => $this->input->post('adlt_cnt'                    , 'TRUE')
                               ,'chld_cnt'     => $this->input->post('chld_cnt'                    , 'TRUE')
                               ,'gst_cls'      => $this->input->post('gst_cls'                     , 'TRUE')
                               ,'memo'         => $memo
                               ,'cncl_yn'      => 'N'
                               ,'cncl_dt'      => NULL
                               );

                $result = $this->stay_m->update_tba005l00_1($u_data);

                if (!$result)
                {
                    info_log("rsvt/upd/update_tba005l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/upd/update_tba005l00_1", "[SQL ERR] 예약 수정 오류!");
                }

                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $this->db->affected_rows();
                if ($prcs_cnt != 1)
                {
                    info_log("rsvt/upd/update_tba005l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/upd/update_tba005l00_1", "[SQL ERR] 예약 수정 건수 오류![" . $prcs_cnt . "]!");
                }

                info_log("rsvt/upd", "rsv_chnl_cls = [" . $rsv_chnl_cls . "]");
                info_log("rsvt/upd", "bef_deposit  = [" . $rsvt_info->deposit  . "]");
                info_log("rsvt/upd", "deposit      = [" . $deposit . "]");

                //======================================================================================================
                // 2020.12.30 거래내역 생성 BEGIN
                info_log("rsvt/upd", "bef_amt = [" . $rsvt_info->amt  . "]");
                info_log("rsvt/upd", "aft_amt = [" . $amt . "]");

                if (strcmp($rsvt_info->rsv_chnl_cls, "1") == 0)
                {
                    $bf_tr_cls = "01";
                    $bef_tr_dt = $rsvt_info->cnfm_dt;
                }
                else if (strcmp($rsvt_info->rsv_chnl_cls, "2") == 0)
                {
                    $bf_tr_cls = "02";
                    $qry_result = $this->stay_m->get_pay_rcv_dt($rsvt_info->srt_dt);
                    if (!$qry_result)
                    {
                        info_log("rsvt/upd/get_pay_rcv_dt1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("rsvt/upd/get_pay_rcv_dt1", "AB 입금일자 조회 오류!");
                    }

                    $bef_tr_dt = $qry_result->rcv_dt;
                }
                else if (strcmp($rsvt_info->rsv_chnl_cls, "3") == 0)
                {
                    $bf_tr_cls = "03";
                    $bef_tr_dt = $rsvt_info->cnfm_dt;
                }
                else
                {
                    info_log("rsvt/upd/", "rsv_chnl_cls  = [" . $rsvt_info->rsv_chnl_cls . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/upd/", "미존재 예약채널!");
                }

                info_log("rsvt/upd", "bef_tr_dt = [" . $bef_tr_dt . "]");

                $qry_result = $this->stay_m->get_tr_srno($rsv_srno, $bef_tr_dt, $bf_tr_cls);
                if (!$qry_result)
                {
                    info_log("rsvt/upd/get_tr_srno", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/upd/get_tr_srno", "거래일련번호 조회 오류!");
                }

                $tr_srno = $qry_result->tr_srno;
                info_log("rsvt/upd", "tr_srno    = [" . $tr_srno . "]");

                if (strcmp($rsv_chnl_cls, "1") == 0)
                {
                    $tr_cls = "01";
                    $tr_memo = "BL 예약금 입금";
                    $tr_dt = $cnfm_dt;
                }
                else if (strcmp($rsv_chnl_cls, "2") == 0)
                {
                    $tr_cls = "02";
                    $tr_memo = "AB 입금";

                    $qry_result = $this->stay_m->get_pay_rcv_dt($srt_dt);
                    if (!$qry_result)
                    {
                        info_log("rsvt/upd/get_pay_rcv_dt2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("rsvt/upd/get_pay_rcv_dt2", "AB 입금일자 조회 오류!");
                    }
                    $tr_dt = $qry_result->rcv_dt;
                }
                else if (strcmp($rsv_chnl_cls, "3") == 0)
                {
                    $tr_cls = "03";
                    $tr_memo = "네이버 예약금 입금";
                    $tr_dt = $cnfm_dt;
                }
                else
                {
                    info_log("rsvt/upd/", "rsv_chnl_cls!!  = [" . $rsv_chnl_cls . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/upd/", "미존재 예약채널!");
                }

                info_log("rsvt/upd", "tr_dt = [" . $tr_dt . "]");

                $u_data = array('tr_srno'          => $tr_srno
                               ,'rsv_srno'         => $rsv_srno
                               ,'tr_dt'            => $tr_dt
                               ,'tr_cls'           => $tr_cls
                               ,'tr_chnl_cls'      => '2'
                               ,'amt'              => str_replace(',', '', $this->input->post('amt'    , 'TRUE'))
                               ,'memo'             => $tr_memo
                               ,'othr_withdraw_yn' => 'N'
                               ,'expns_srno'       => NULL
                               );

                $result = $this->stay_m->update_tba006l00_1($u_data);

                if (!$result)
                {
                    info_log("rsvt/cncl/insert_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/cncl/insert_tba006l00", "예약 취소 거래 입력 오류!");
                }
                // 2020.12.30 거래내역 생성 END
                //======================================================================================================


                //======================================================================================================
                // 2020.09.22 보증금 로직 추가 BEGIN
                // 블로그 예약의 경우 보증금 존재시 보증금 입금 처리
                // 2020.12.30. 보증금 > 0 조건 변경 > 보증금 수정시
                // 과거 데이터는 이미 변경하였으므로 보증금 > 0 로직을 유지할 필요가 없어짐
                //if (strcmp($rsv_chnl_cls, "1") == 0 && $deposit > 0)
                if (strcmp($rsv_chnl_cls, "1") == 0 && (int)$rsvt_info->deposit != (int)$deposit)
                {
                    //======================================================================================================
                    // 2020.12.30. BEGIN
                    // 과거 데이터는 이미 변경하였으므로 보증금 > 0 로직을 유지할 필요가 없어짐
                    //
                    //$qry_result = $this->stay_m->get_tr_srno($rsv_srno, $cnfm_dt, '31');
                    //info_log("rsvt/upd/get_tr_srno", "tr_srno     = [" . $qry_result->tr_srno . "]");
                    //
                    //if (!isset($qry_result->tr_srno))
                    //{
                    //    // 프로그램 수정 전의 과거 예약건 수정시 보증금 등록건이 없으므로 일련번호 채번
                    //    if ($qry_result == 0)
                    //    {
                    //        $tr_srno = $this->stay_m->get_clm_sr_val('TR_SRNO');
                    //        info_log("rsvt/upd/get_clm_sr_val", "tr_srno     = [" . $tr_srno . "]");
                    //
                    //        $i_data = array('tr_srno'          => $tr_srno
                    //                       ,'rsv_srno'         => $rsv_srno
                    //                       ,'tr_dt'            => $cnfm_dt
                    //                       ,'tr_cls'           => '31'
                    //                       ,'tr_chnl_cls'      => '2'
                    //                       ,'amt'              => $deposit
                    //                       ,'memo'             => 'BL 예약 보증금 입금'
                    //                       ,'othr_withdraw_yn' => 'N'
                    //                       );
                    //
                    //        $result = $this->stay_m->insert_tba006l00($i_data);
                    //
                    //        if (!$result)
                    //        {
                    //            info_log("rsvt/upd/insert_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
                    //            $this->db->trans_rollback();
                    //            alert_log("rsvt/upd/insert_tba006l00", "BL 예약 (보증금 입금) 입력 오류!");
                    //        }
                    //    }
                    //}
                    //else
                    //{
                    //    $tr_srno = $qry_result->tr_srno;
                    //
                    //    $u_data = array('tr_srno'          => $tr_srno
                    //                   ,'rsv_srno'         => $rsv_srno
                    //                   ,'tr_dt'            => $cnfm_dt
                    //                   ,'tr_cls'           => '31'
                    //                   ,'tr_chnl_cls'      => '2'
                    //                   ,'amt'              => $deposit
                    //                   ,'memo'             => 'BL 예약 보증금 입금'
                    //                   ,'othr_withdraw_yn' => 'N'
                    //                   );
                    //
                    //    $result = $this->stay_m->update_tba006l00_1($u_data);
                    //
                    //    if (!$result)
                    //    {
                    //        info_log("rsvt/upd/insert_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
                    //        $this->db->trans_rollback();
                    //        alert_log("rsvt/upd/insert_tba006l00", "BL 예약 (보증금 입금) 입력 오류!");
                    //    }
                    //}
                    // 2020.12.30 END
                    //======================================================================================================

                    $qry_result = $this->stay_m->get_tr_srno($rsv_srno, $cnfm_dt, '31');
                    info_log("rsvt/upd/get_tr_srno", "tr_srno     = [" . $qry_result->tr_srno . "]");

                    $tr_srno = $qry_result->tr_srno;

                    $u_data = array('tr_srno'          => $tr_srno
                                   ,'rsv_srno'         => $rsv_srno
                                   ,'tr_dt'            => $cnfm_dt
                                   ,'tr_cls'           => '31'
                                   ,'tr_chnl_cls'      => '2'
                                   ,'amt'              => $deposit
                                   ,'memo'             => 'BL 예약 보증금 입금'
                                   ,'othr_withdraw_yn' => 'N'
                                   ,'expns_srno'       => NULL
                                   );

                    $result = $this->stay_m->update_tba006l00_1($u_data);

                    if (!$result)
                    {
                        info_log("rsvt/upd/insert_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("rsvt/upd/insert_tba006l00", "BL 예약 (보증금 입금) 입력 오류!");
                    }

                }
                // 2020.09.22 보증금 로직 추가 END
                //======================================================================================================

                // 구글 캘린더 처리
                //if (strncmp("http://www.stayingm.co.kr", base_url(), 25) == 0)
                //info_log("rsvt/upd", "base_url  = [" . base_url() . "]");
                if (strcmp("https://xsvr.duckdns.org/", base_url()) == 0)
                {
                    //info_log("rsvt/upd", "Check Point 00!");
                    //$this->auth('upd', $this->input->post('rsv_srno', 'TRUE'), $rsvt_info->hsrm_cls, $this->input->post('evnt_id', 'TRUE'));
                    g_auth('upd', $this->input->post('rsv_srno', 'TRUE'), $rsvt_info->hsrm_cls, $this->input->post('evnt_id', 'TRUE'), '');
                }

                $this->db->trans_commit();

                info_log("rsvt/upd/", "예약 수정 완료!");
                info_log("rsvt/upd/", "================================================================================");

                $redirect_url = $_SESSION['bef_url'];
                info_log("rsvt/upd/", "redirect_url  = [" . $redirect_url . "]");

                if (isset($redirect_url))
                {
                    redirect(base_url($redirect_url));
                }
                else
                {
                    redirect(base_url("rsvt/list"));
                }

            }
            else
            {
                $this->rsvt_reg_v('u');
            }
        }
        else
        {
            $this->rsvt_reg_v('u');
        }
    }


    public function cncl()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("rsvt/cncl", "================================================================================");
            info_log("rsvt/cncl", "예약 취소 처리 시작!");

            $this->form_validation->set_rules('rsv_srno'      , '예약일련번호', 'required');
            $this->form_validation->set_rules('b_rsv_chnl_cls', '예약채널구분', 'required');
            //$this->form_validation->set_rules('b_rcv_dt'      , '입금일자'    , 'required');
            //$this->form_validation->set_rules('evnt_id'     , '이벤트ID'    , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                //if ($amt <= 0)
                //{
                //    alert_log("rsvt/cncl", "환불금액 또는 취소 후 입금액이 0보다 작거나 같습니다!");
                //}

                // tba005l00 cncl_yn, cncl_dt update
                $cncl_dt = date("Y-m-d");

                $this->db->trans_begin();

                //info_log("rsvt/cncl", $this->input->post('rsv_srno', 'TRUE'));

                $b_rsv_chnl_cls = $this->input->post('b_rsv_chnl_cls', 'TRUE');
                info_log("rsvt/cncl", "b_rsv_chnl_cls = [" . $b_rsv_chnl_cls . "]");

                //2018.05.21.
                //예약채널이 A&B 인 경우 폼에서 입력한 입금일자 사용. 이외는 기존 입금일자로 업데이트
                //2020.12.30
                //tba005l00 pay_dt, rcv_dt 컬럼 삭제로 해당로직 불필요
                //info_log("rsvt/cncl", $this->input->post('b_rcv_dt', 'TRUE'));
                //
                //if (strncmp($b_rsv_chnl_cls, "1", 1) == 0)
                //{
                //    $rcv_dt = $this->input->post('b_rcv_dt', 'TRUE');
                //}
                //else if (strncmp($b_rsv_chnl_cls, "2", 1) == 0)
                //{
                //    $rcv_dt = NULL;
                //}

                $u_data = array('rsv_srno'     => $this->input->post('rsv_srno', 'TRUE')
                               ,'cncl_yn'      => 'Y'
                               ,'cncl_dt'      => str_replace('-', '', $this->input->post('cncl_dt', 'TRUE'))
                               );

                $result = $this->stay_m->update_tba005l00_2($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("rsvt/cncl/update_tba005l00_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("rsvt/cncl/update_tba005l00_2", "[SQL ERR] 예약 취소 건수 오류[" . $prcs_cnt . "]!");
                    }
                    //update_tba005l00_2 정상 처리시
                    else
                    {
                        if (strlen($this->input->post('othr_withdraw_yn', 'TRUE')) > 0)
                        {
                            $othr_withdraw_yn = $this->input->post('othr_withdraw_yn', 'TRUE');
                            $expns_srno = $this->stay_m->get_clm_sr_val('EXPNS_SRNO');
                        }
                        else
                        {
                            $othr_withdraw_yn = 'N';
                            $expns_srno = NULL;
                        }


                        // tba006l00 insert
                        $tr_srno = $this->stay_m->get_clm_sr_val('TR_SRNO');

                        info_log("rsvt/cncl", "tr_srno     = [" . $tr_srno . "]");

                        //==============================================================================
                        //2020.12.30. AB의 경우 예상 입금일로 거래일자 설정
                        if (strncmp($b_rsv_chnl_cls, "1", 1) == 0 || strncmp($b_rsv_chnl_cls, "3", 1) == 0)
                        {
                            $tr_dt = str_replace('-', '', $this->input->post('cncl_dt', 'TRUE'));
                        }
                        else if (strncmp($b_rsv_chnl_cls, "2", 1) == 0)
                        {
                            $tr_dt = str_replace('-', '', $this->input->post('b_rcv_dt', 'TRUE'));

                            // 기존 AB 입금 취소 처리
                            $qry_result = $this->stay_m->get_tr_srno($this->input->post('rsv_srno', 'TRUE'), $tr_dt, '02');
                            if (!$qry_result)
                            {
                                info_log("rsvt/cncl/get_tr_srno", "last_query  = [" . $this->db->last_query() . "]");
                                $this->db->trans_rollback();
                                alert_log("rsvt/cncl/get_tr_srno", "거래일련번호 조회 오류!");
                            }

                            $bef_tr_srno = $qry_result->tr_srno;
                            info_log("rsvt/cncl/get_tr_srno", "tr_dt = [" . $tr_dt . "]");
                            info_log("rsvt/cncl/get_tr_srno", "bef_tr_srno = [" . $qry_result->tr_srno . "]");

                            $u_data = array('tr_srno'          => $bef_tr_srno);

                            $result = $this->stay_m->update_tba006l00_2($u_data);

                            if (!$result)
                            {
                                info_log("rsvt/cncl/update_tba006l00_2", "last_query  = [" . $this->db->last_query() . "]");
                                $this->db->trans_rollback();
                                alert_log("rsvt/cncl/update_tba006l00_2", "AB 기존 거래 취소 처리 오류!");
                            }
                        }
                        else
                        {
                            info_log("rsvt/cncl/", "b_rsv_chnl_cls  = [" . $b_rsv_chnl_cls . "]");
                            $this->db->trans_rollback();
                            alert_log("rsvt/cncl/", "미존재 예약채널!");
                        }

                        info_log("rsvt/cncl", "tr_dt     = [" . $tr_dt . "]");
                        //2020.12.30. AB의 경우 예상 입금일로 거래일자 설정
                        //==============================================================================

                        $i_data = array('tr_srno'          => $tr_srno
                                       ,'rsv_srno'         => $this->input->post('rsv_srno', 'TRUE')
                                       ,'tr_dt'            => $tr_dt
                                       ,'tr_cls'           => $this->input->post('tr_cls'  , 'TRUE')
                                       ,'tr_chnl_cls'      => '2'
                                       ,'amt'              => $amt
                                       ,'memo'             => trim($this->input->post('memo'  , 'TRUE'))
                                       ,'othr_withdraw_yn' => $othr_withdraw_yn
                                       ,'expns_srno'       => $expns_srno
                                       );

                        $result = $this->stay_m->insert_tba006l00($i_data);

                        if (!$result)
                        {
                            info_log("rsvt/cncl/insert_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
                            $this->db->trans_rollback();
                            alert_log("rsvt/cncl/insert_tba006l00", "예약 취소 거래 입력 오류!");
                        }

                        $deposit = $this->input->post('deposit', 'TRUE');
                        info_log("rsvt/cncl", "deposit      = [" . $deposit . "]");

                        // 보증금 존재시 보증금 환불 처리
                        // 2020.12.30 BL 예약인 경우 조건에 추가
                        //if ($deposit > 0)
                        if (strcmp($b_rsv_chnl_cls, "1") == 0 && $deposit > 0)
                        {
                            $tr_srno = $this->stay_m->get_clm_sr_val('TR_SRNO');

                            info_log("rsvt/cncl", "tr_srno     = [" . $tr_srno . "]");

                            $i_data = array('tr_srno'          => $tr_srno
                                           ,'rsv_srno'         => $this->input->post('rsv_srno', 'TRUE')
                                           ,'tr_dt'            => str_replace('-', '', $this->input->post('cncl_dt', 'TRUE'))
                                           ,'tr_cls'           => '32'
                                           ,'tr_chnl_cls'      => '2'
                                           ,'amt'              => $deposit
                                           ,'memo'             => '예약 취소 보증금 환불'
                                           ,'othr_withdraw_yn' => $othr_withdraw_yn
                                           );

                            $result = $this->stay_m->insert_tba006l00($i_data);

                            if (!$result)
                            {
                                info_log("rsvt/cncl/insert_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
                                $this->db->trans_rollback();
                                alert_log("rsvt/cncl/insert_tba006l00", "예약 취소 거래(보증금 환불) 입력 오류!");
                            }
                        }


                        //2021.09.09. 취소 처리시 타계좌 출금의 경우 지출 입력 처리 Begin
                        if (strcmp($othr_withdraw_yn, 'Y') == 0)
                        {
                            $i_data = array('expns_srno'     => $expns_srno
                                           ,'expns_dt'       => str_replace('-', '', $this->input->post('cncl_dt', 'TRUE'))
                                           ,'expns_chnl_cls' => '01'
                                           ,'expns_cls'      => '20210'
                                           ,'memo'           => '예약취소 환불(생활비 계좌 출금)'
                                           ,'whr_to_buy'     => $this->input->post('gst_nm', 'TRUE')
                                           ,'ssamzi_yn'      => 'N'
                                           ,'cost_cls'       => '1'
                                           ,'amt'            => $amt
                                           );

                            $result = $this->stay_m->insert_tbb001l00($i_data);

                            if (!$result)
                            {
                                info_log("rsvt/cncl/insert_tbb001l00", "last_query  = [" . $this->db->last_query() . "]");
                                $this->db->trans_rollback();
                                alert_log("rsvt/cncl/insert_tbb001l00", "[SQL ERR][예약취소 환불] 지출 입력 오류!");
                            }

                            // 입출금거래 현금지출합계금액 ins/upd
                            $stnd_yymm = substr(str_replace('-', '', $this->input->post('cncl_dt', 'TRUE')), 0, 6);
                            cash_bal_ins_upd($stnd_yymm);

                        }
                        //2021.09.09. 취소 처리시 타계좌 출금의 경우 지출 입력 처리 End

                        info_log("rsvt/cncl", "rsv_srno     = [" . $this->input->post('rsv_srno', 'TRUE') . "]");

                        // 2018.01.07
                        // 숙소 변경시 캘린더 삭제 오류 발생하여 기존 숙소정보를 저장하도록 수정
                        $rsvt_info = $this->stay_m->get_rsvt_info($this->input->post('rsv_srno', 'TRUE'));

                        info_log("rsvt/cncl", "bef_hsrm_cls = [" . $rsvt_info->hsrm_cls  . "]");

                        // 구글 캘린더 처리
                        //if (strncmp("http://www.stayingm.co.kr", base_url(), 25) == 0)
                        if (strcmp("https://xsvr.duckdns.org/", base_url()) == 0)
                        {
                            //$this->auth('cncl', $this->input->post('rsv_srno', 'TRUE'), $rsvt_info->hsrm_cls, $this->input->post('evnt_id', 'TRUE'));
                            g_auth('cncl', $this->input->post('rsv_srno', 'TRUE'), $rsvt_info->hsrm_cls, $this->input->post('evnt_id', 'TRUE'), '');
                        }

                        $this->db->trans_commit();

                        info_log("rsvt/cncl", "예약 취소 완료!");
                        info_log("rsvt/cncl", "================================================================================");

                        redirect(base_url("rsvt/cncl_list"));

                    }
                }
                else
                {
                    info_log("rsvt/cncl/update_tba005l00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/cncl/update_tba005l00_2", "예약 취소 오류!");
                }
            }
            else
            {
                $this->rsvt_cncl_v('c');
            }
        }
        else
        {
            $this->rsvt_cncl_v('c');
        }
    }


    public function cncl_upd()
    {
        // 취소일자, 입금일자(A&B), 환불금액(또는 취소 후 입금금액) 수정만 가능
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("rsvt/cncl_upd", "================================================================================");
            info_log("rsvt/cncl_upd", "예약 취소건 수정 처리 시작!");

            $this->form_validation->set_rules('rsv_srno'      , '예약일련번호', 'required');
            $this->form_validation->set_rules('b_rsv_chnl_cls', '예약채널구분', 'required');
            //$this->form_validation->set_rules('b_rcv_dt'      , '입금일자'    , 'required');
            //$this->form_validation->set_rules('evnt_id'     , '이벤트ID'    , 'required');

            //info_log("rsvt/cncl", "b_rsv_chnl_cls = [" . $this->input->post('b_rsv_chnl_cls', 'TRUE') . "]");
            //info_log("rsvt/cncl", "b_rcv_dt = [" . $this->input->post('b_rcv_dt', 'TRUE') . "]");

            if ($this->form_validation->run() == TRUE)
            {
                //info_log("rsvt/cncl", "Check Point00!");

                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                //if ($amt <= 0)
                //{
                //    alert_log("rsvt/cncl", "환불금액 또는 취소 후 입금액이 0보다 작거나 같습니다!");
                //}

                // tba005l00 cncl_yn, cncl_dt update
                $this->db->trans_begin();

                //info_log("rsvt/cncl", $this->input->post('rsv_srno', 'TRUE'));

                $b_rsv_chnl_cls = $this->input->post('b_rsv_chnl_cls', 'TRUE');

                //2018.05.21.
                //예약채널이 A&B 인 경우 폼에서 입력한 입금일자 사용. 이외는 기존 입금일자로 업데이트
                //2020.12.30
                //tba005l00 pay_dt, rcv_dt 컬럼 삭제로 해당로직 불필요
                //if (strncmp($b_rsv_chnl_cls, "1", 1) == 0)
                //{
                //    $rcv_dt = $this->input->post('b_rcv_dt', 'TRUE');
                //}
                //else if (strncmp($b_rsv_chnl_cls, "2", 1) == 0)
                //{
                //    $rcv_dt = str_replace('-', '', $this->input->post('rcv_dt', 'TRUE'));
                //}

                $u_data = array('rsv_srno'     => $this->input->post('rsv_srno', 'TRUE')
                               ,'cncl_yn'      => 'Y'
                               ,'cncl_dt'      => str_replace('-', '', $this->input->post('cncl_dt', 'TRUE'))
                               );

                $result = $this->stay_m->update_tba005l00_2($u_data, 'u');

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("rsvt/cncl_upd/update_tba005l00_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("rsvt/cncl_upd/update_tba005l00_2", "[SQL ERR] 예약 취소 수정 건수 오류[" . $prcs_cnt . "]!");
                    }
                    //update_tba005l00_2 정상 처리시
                    else
                    {
                        //==============================================================================
                        //2020.12.30. AB의 경우 예상 입금일로 거래일자 설정
                        if (strncmp($b_rsv_chnl_cls, "1", 1) == 0 || strncmp($b_rsv_chnl_cls, "3", 1) == 0)
                        {
                            $tr_dt = str_replace('-', '', $this->input->post('cncl_dt', 'TRUE'));
                        }
                        else if (strncmp($b_rsv_chnl_cls, "2", 1) == 0)
                        {
                            $tr_dt = str_replace('-', '', $this->input->post('rcv_dt', 'TRUE'));
                        }

                        info_log("rsvt/cncl", "tr_dt     = [" . $tr_dt . "]");
                        //2020.12.30. AB의 경우 예상 입금일로 거래일자 설정
                        //==============================================================================


                        //2021.09.10
                        //expns_srno 존재 여부 확인 BEGIN
                        $tr_srno = $this->input->post('tr_srno', 'TRUE');
                        $expns_info = $this->stay_m->get_etc_incm_info($tr_srno);
                        info_log("rsvt/cncl_upd/", "expns_srno  = [" . $expns_info->expns_srno . "]");
                        //expns_srno 존재 여부 확인 END


                        //2021.09.10
                        //타계좌 출금 여부 BEGIN
                        if (strlen($this->input->post('othr_withdraw_yn', 'TRUE')) > 0)
                        {
                            $othr_withdraw_yn = $this->input->post('othr_withdraw_yn', 'TRUE');
                            if (strlen($expns_info->expns_srno) > 0)
                            {
                                $expns_srno = $expns_info->expns_srno;
                            }
                            else
                            {
                                $expns_srno = $this->stay_m->get_clm_sr_val('EXPNS_SRNO');
                            }
                        }
                        else
                        {
                            $othr_withdraw_yn = 'N';
                            $expns_srno = NULL;
                        }
                        //타계좌 출금 여부 END

                        if (strlen($this->input->post('memo'  , 'TRUE')) == 0) {
                            $memo = NULL;
                        }
                        else {
                            $memo = $this->input->post('memo'  , 'TRUE');
                        }
                        
                        // tba006l00 update
                        $u_data = array('tr_srno'          => $tr_srno
                                       ,'rsv_srno'         => $this->input->post('rsv_srno', 'TRUE')
                                       ,'tr_dt'            => $tr_dt
                                       ,'amt'              => str_replace(',', '', $this->input->post('amt', 'TRUE'))
                                       ,'memo'             => $memo
                                       ,'othr_withdraw_yn' => $othr_withdraw_yn
                                       ,'expns_srno'       => $expns_srno
                                       );

                        $result = $this->stay_m->update_tba006l00_3($u_data);

                        if ($result)
                        {
                            // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                            $prcs_cnt = $this->db->affected_rows();
                            if ($prcs_cnt != 1)
                            {
                                info_log("rsvt/cncl_upd/update_tba006l00_3", "last_query  = [" . $this->db->last_query() . "]");
                                $this->db->trans_rollback();
                                alert_log("rsvt/cncl_upd/update_tba006l00_3", "[SQL ERR] 예약 취소 거래 수정 건수 오류![" . $prcs_cnt . "]!");
                            }
                            else
                            {
                                //2021.09.10. 수정시 타계좌 출금 로직
                                //1. 기존 입력 데이터 존재여부 확인
                                //2. 타계좌 출금 'Y' 변경시
                                //    - 기존 데이터 존재시 금액 UPDATE 처리
                                //    - 기존 데이터 미존재시 INSERT 처리
                                //3. 타계좌 출금 'N' 변경시
                                //    - 기존 데이터 존재시 tbb001l00 DELETE 처리
                                //    - 기존 데이터 미존재시 아무 처리 없음

                                if (strcmp($othr_withdraw_yn, 'Y') == 0)
                                {
                                    if (strlen($expns_info->expns_srno) > 0)
                                    {
                                        $u_data = array('expns_srno'     => $expns_srno
                                                       ,'expns_dt'       => str_replace('-', '', $this->input->post('cncl_dt', 'TRUE'))
                                                       ,'expns_chnl_cls' => '01'
                                                       ,'expns_cls'      => '20210'
                                                       ,'memo'           => '예약취소 환불(생활비 계좌 출금)'
                                                       ,'whr_to_buy'     => $this->input->post('gst_nm', 'TRUE')
                                                       ,'ssamzi_yn'      => 'N'
                                                       ,'cost_cls'       => '1'
                                                       ,'amt'            => $amt
                                                       );

                                        $result = $this->stay_m->update_tbb001l00_1($u_data);

                                        if (!$result)
                                        {
                                            info_log("rsvt/cncl/update_tbb001l00_1", "last_query  = [" . $this->db->last_query() . "]");
                                            $this->db->trans_rollback();
                                            alert_log("rsvt/cncl/update_tbb001l00_1", "[SQL ERR][예약취소 환불] 지출 수정 오류!");
                                        }

                                        // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                                        $prcs_cnt = $this->db->affected_rows();
                                        if ($prcs_cnt != 1)
                                        {
                                            info_log("rsvt/cncl/update_tbb001l00_1", "last_query  = [" . $this->db->last_query() . "]");
                                            $this->db->trans_rollback();
                                            alert_log("rsvt/cncl/update_tbb001l00_1", "[SQL ERR][예약취소 환불] 지출 수정 처리 오류![" . $prcs_cnt . "]!");
                                        }
                                    }
                                    else {
                                        $i_data = array('expns_srno'     => $expns_srno
                                                       ,'expns_dt'       => str_replace('-', '', $this->input->post('cncl_dt', 'TRUE'))
                                                       ,'expns_chnl_cls' => '01'
                                                       ,'expns_cls'      => '20210'
                                                       ,'memo'           => '예약취소 환불(생활비 계좌 출금)'
                                                       ,'whr_to_buy'     => $this->input->post('gst_nm', 'TRUE')
                                                       ,'ssamzi_yn'      => 'N'
                                                       ,'cost_cls'       => '1'
                                                       ,'amt'            => $amt
                                                       );

                                        $result = $this->stay_m->insert_tbb001l00($i_data);

                                        if (!$result)
                                        {
                                            info_log("rsvt/cncl/insert_tbb001l00", "last_query  = [" . $this->db->last_query() . "]");
                                            $this->db->trans_rollback();
                                            alert_log("rsvt/cncl/insert_tbb001l00", "[SQL ERR][예약취소 환불] 지출 입력 오류!");
                                        }
                                    }

                                    // 입출금거래 현금지출합계금액 ins/upd
                                    $stnd_yymm = substr(str_replace('-', '', $this->input->post('cncl_dt', 'TRUE')), 0, 6);
                                    cash_bal_ins_upd($stnd_yymm);

                                }
                                else if (strcmp($othr_withdraw_yn, 'N') == 0)
                                {
                                    if (strlen($expns_info->expns_srno) > 0)
                                    {
                                        $u_data = array('expns_srno'     => $expns_info->expns_srno
                                                       );

                                        $result = $this->stay_m->update_tbb001l00_2($u_data);

                                        if (!$result)
                                        {
                                            info_log("rsvt/cncl_upd/update_tbb001l00_2", "last_query  = [" . $this->db->last_query() . "]");
                                            $this->db->trans_rollback();
                                            alert_log("rsvt/cncl_upd/update_tbb001l00_2", "[SQL ERR][예약취소 환불] 지출 삭제 오류!");
                                        }

                                    }
                                }

                                $this->db->trans_commit();

                                info_log("rsvt/cncl_upd", "예약 취소 수정 완료!");
                                info_log("rsvt/cncl_upd", "================================================================================");

                                redirect(base_url("rsvt/cncl_list"));
                            }
                        }
                        else
                        {
                            info_log("rsvt/cncl_upd/update_tba006l00_3", "last_query  = [" . $this->db->last_query() . "]");
                            $this->db->trans_rollback();
                            alert_log("rsvt/cncl_upd/update_tba006l00_3", "[SQL ERR] 예약 취소 거래 수정 오류!");
                        }

                    }
                }
                else
                {
                    info_log("rsvt/cncl_upd/update_tba005l00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("rsvt/cncl_upd/update_tba005l00_2", "[SQL ERR] 예약 취소 수정 오류!");
                }
            }
            else
            {
                //info_log("rsvt/cncl", "Check Point01!");
                $this->rsvt_cncl_v('u');
            }
        }
        else
        {
            //info_log("rsvt/cncl", "Check Point02!");
            $this->rsvt_cncl_v('u');
        }
    }


    public function prc()
    {
        // 기간별 가격 조회
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        //2018.10.27. get 방식으로 변경후 폼 검증 주석처리
        //$this->form_validation->set_rules('hsrm_cls'  , '숙소'        , 'required');
        //$this->form_validation->set_rules('srt_dt'    , '시작일자'    , 'required');
        //$this->form_validation->set_rules('end_dt'    , '종료일자'    , 'required');
        //$this->form_validation->set_rules('gst_num'   , '숙박인원'    , 'required');

        //if ($this->form_validation->run() == TRUE)
        //{
        info_log("rsvt/prc", "================================================================================");
        info_log("rsvt/prc", "가격 조회 시작!");

        //2019.12.27. 검색시 변수전달 Get >> Post 변경
        //$hsrm_cls   = $this->input->get('hsrm_cls', 'TRUE');
        //$srt_dt     = str_replace('-', '', $this->input->get('srt_dt', 'TRUE'));
        //$end_dt     = str_replace('-', '', $this->input->get('end_dt', 'TRUE'));
        //$gst_num    = $this->input->get('gst_num', 'TRUE');
        //$revisit_yn = $this->input->get('revisit_yn', 'TRUE');

        $hsrm_cls    = $this->input->post('hsrm_cls', 'TRUE');
        $srt_dt      = str_replace('-', '', $this->input->post('srt_dt', 'TRUE'));
        $end_dt      = str_replace('-', '', $this->input->post('end_dt', 'TRUE'));
        $gst_num1    = $this->input->post('gst_num1', 'TRUE');
        $gst_num2    = $this->input->post('gst_num2', 'TRUE');
        $revisit_yn  = $this->input->post('revisit_yn', 'TRUE');
        (int)$discount_rt = str_replace(',', '', $this->input->post('discount_rt', 'TRUE'));
        $deposit     = $this->input->post('deposit', 'TRUE');
        $extend_yn   = $this->input->post('extend_yn', 'TRUE');

        $gst_num = $gst_num1 + $gst_num2;

        $log_lvl = "r";

        if (strlen($hsrm_cls) != 0)
        {
            info_log("rsvt/prc", "hsrm_cls     = [" . $hsrm_cls . "]");
            info_log("rsvt/prc", "srt_dt       = [" . $srt_dt . "]");
            info_log("rsvt/prc", "end_dt       = [" . $end_dt . "]");
            info_log("rsvt/prc", "gst_num      = [" . $gst_num  . "]");
            info_log("rsvt/prc", "gst_num1     = [" . $gst_num1 . "]");
            info_log("rsvt/prc", "gst_num2     = [" . $gst_num2 . "]");
            info_log("rsvt/prc", "revisit_yn   = [" . $revisit_yn . "]");
            info_log("rsvt/prc", "discount_rt  = [" . $discount_rt . "]");
            info_log("rsvt/prc", "deposit      = [" . $deposit . "]");
            info_log("rsvt/prc", "extend_yn    = [" . $extend_yn . "]");

            $rsv_dt = $this->stay_m->get_rsv_term($hsrm_cls, $srt_dt, $end_dt, $extend_yn);

            $days_cnt = count($rsv_dt);
            $loop_times = 0;

            $total_prc = 0;
            $dtl_prc = "";
            $dtl_desc_prc = "";

            $info_msg = "";
            $info_msg2 = "";

            if (strncmp($revisit_yn, "Y", 1) == 0)
            {
                $info_msg = "안녕하세요.\n머무른채에 재방문 예약문의 주셔서 감사합니다. ^^\n\n";
            }
            else
            {
                $info_msg = "안녕하세요.\n머무른채에 예약문의 주셔서 감사합니다. ^^\n\n";
            }

            $season_discnt = "";
            $discnt = "";
            $prcs_cnt = 0;
            $arr_prc_cls_desc = [];
            $extra_num_desc = "";
            $revisit_desc = "";
            $arr_prcs_yn = "";
            $arr_prc_cls_cnt = 0;

            $prc_cls_desc_msg = "";

            info_log("rsvt/prc", "days_cnt       = [" . $days_cnt . "]", $log_lvl);

            foreach ($rsv_dt as $r_dt)
            {
                $loop_times = $loop_times + 1;

                info_log("rsvt/prc", "loop_times = [" . $loop_times . "]", $log_lvl);
                info_log("rsvt/prc", "rnum       = [" . $r_dt->rnum . "]", $log_lvl);
                info_log("rsvt/prc", "r_dt->dt   = [" . $r_dt->dt . "]"  , $log_lvl);

                $dt_prc = $this->stay_m->get_dt_prc($r_dt->dt, $hsrm_cls);

                //print_r ($dt_prc);

                //info_log("rsvt/prc", "dt         = [" . $dt_prc->dt . "]");
                info_log("rsvt/prc", "dt_cls      = [" . $dt_prc->dt_cls . "]"     , $log_lvl);
                info_log("rsvt/prc", "season_cls  = [" . $dt_prc->season_cls . "]" , $log_lvl);
                info_log("rsvt/prc", "prc         = [" . $dt_prc->prc . "]"        , $log_lvl);
                info_log("rsvt/prc", "cntnu_dscnt = [" . $dt_prc->cntnu_dscnt . "]", $log_lvl);

                // 2022.12.25 사흘동 요금 = 이틀동 요금 + 10,000원 추가, 숙소별 로직 위치 이동
                //2022.11.28 숙소 평수 안내 삭제
                if (strncmp($hsrm_cls, "01", 2) == 0)
                {
                    //$hsrm_cls_nm = "19평 이틀동";
                    $hsrm_cls_nm = "이틀동";
                }
                else if (strncmp($hsrm_cls, "02", 2) == 0)
                {
                    //$hsrm_cls_nm = "21평 사흘동";
                    $hsrm_cls_nm = "사흘동";
                    $dt_prc->prc = $dt_prc->prc + 1;
                }
                else
                {
                    alert_log("rsvt/prc", "[PRCS ERR] 숙소구분 오류[" . $hsrm_cls . "]!");
                }

                info_log("rsvt/prc", "사흘동 prc    = [" . $dt_prc->prc . "]"        , $log_lvl);

                // 2022.12.14 2박 추가요금 체계 삭제
                // 2021.05.05. 1박의 경우 3만원, 2박의 경우 1박당 2만원 추가 요금 부가
                //if ($days_cnt == 1)
                //{
                //    $dt_prc->prc = $dt_prc->prc + 3;
                //    info_log("rsvt/prc", "prc 1박 = [" . $dt_prc->prc . "]"        , $log_lvl);
                //}
                //else if ($days_cnt == 2)
                //{
                //    $dt_prc->prc = $dt_prc->prc + 2;
                //    info_log("rsvt/prc", "prc 2박 = [" . $dt_prc->prc . "]"        , $log_lvl);
                //}
                // 2021.05.05. 1박의 경우 3만원, 2박의 경우 1박당 2만원 추가 요금 부가


                // 연박할인 제외
                // 숙박 1일차 || 준성수기 || 공휴일 || 성수기의 경우 숙박 3일차 이전
                // 2019.07.11. 성수기의 경우 숙박기간 중 공휴일 포함시 연박할인 제외 하지 않음
                // 2021.04.13. 성수기 연박할인 삭제
                // 숙박 1일차 || 준성수기 || 공휴일 || 성수기
                //2022.12.14. 연박할인은 3박 부터 적용 추가
                //if ($r_dt->rnum == 1 || $dt_prc->season_cls == '2' || ($dt_prc->dt_cls == '3') || ($dt_prc->season_cls == '1'))
                if ($r_dt->rnum == 1 || $r_dt->rnum == 2 || $dt_prc->season_cls == '2' || ($dt_prc->dt_cls == '3') || ($dt_prc->season_cls == '1'))
                {
                    $tmp_prc = $dt_prc->prc;
                    info_log("rsvt/prc", "tmp_prc1      = [" . $tmp_prc . "]", $log_lvl);

                    $tmp_dtl_prc = $tmp_prc . "([" . $dt_prc->prc_cls . "])";
                }
                // 성수기 연박할인
                // 숙박 3일차 이후
                // 2021.04.13. 성수기 연박할인 삭제
                /* 성수기 연박할인 로직 삭제 Begin
                =================================================================================================================
                else if ($dt_prc->season_cls == '1' && $r_dt->rnum >= 3)
                {
                    $tmp_prc = $dt_prc->prc - $dt_prc->cntnu_dscnt;
                    info_log("rsvt/prc", "tmp_prc2      = [" . $tmp_prc . "]", $log_lvl);

                    $tmp_dtl_prc = $tmp_prc . "([" . $dt_prc->prc_cls . "] [성수기 연박할인(-" . $dt_prc->cntnu_dscnt . ")])";
                    $season_discnt = "Y";
                }
                =================================================================================================================
                성수기 연박할인 로직 삭제 End
                */
                // 연박할인
                else
                {
                    $tmp_prc = $dt_prc->prc - $dt_prc->cntnu_dscnt;
                    info_log("rsvt/prc", "tmp_prc3      = [" . $tmp_prc . "]", $log_lvl);

                    $tmp_dtl_prc = $tmp_prc . "([" . $dt_prc->prc_cls . "] [연박할인(-" . $dt_prc->cntnu_dscnt . ")])";
                    $discnt = "Y";
                }

                if ($r_dt->rnum == 1)
                {
                    $srt_dayofweek = $r_dt->dayofweek;
                    $arr_prc_cls_desc[] = $dt_prc->prc_cls_desc;
                }

                //info_log("rsvt/prc", "prc_cls        = [" . $dt_prc->prc_cls . "]");

                $arr_prcs_yn = "N";
                $loop_cnt    = 0;
                $arr_prc_cls_cnt = count($arr_prc_cls_desc);

                foreach($arr_prc_cls_desc as $t_prc_cls_desc)
                {
                    $loop_cnt = $loop_cnt + 1;

                    if (strcmp($t_prc_cls_desc, $dt_prc->prc_cls_desc) == 0)
                    {
                        $arr_prcs_yn = "Y";
                    }

                    //info_log("rsvt/prc", "======================================================");
                    //info_log("rsvt/prc", "loop_cnt       = [" . $loop_cnt . "]");
                    //info_log("rsvt/prc", "t_prc_cls_desc = [" . $t_prc_cls_desc . "]");
                    //info_log("rsvt/prc", "prc_cls_desc   = [" . $dt_prc->prc_cls_desc . "]");
                    //info_log("rsvt/prc", "arr_prcs_yn    = [" . $arr_prcs_yn . "]");

                    if (($arr_prc_cls_cnt == $loop_cnt) && (strncmp($arr_prcs_yn, "N", 1) == 0))
                    {
                        $arr_prc_cls_desc[] = $dt_prc->prc_cls_desc;
                        //info_log("rsvt/prc", "Check Point 00!");
                    }

                    //info_log("rsvt/prc", "======================================================");

                }

                //print_r($arr_prc_cls_desc);

                $total_prc = $total_prc + $tmp_prc;

                if ($loop_times == $days_cnt)
                {
                    $dtl_prc = $dtl_prc . $tmp_prc;
                    $dtl_desc_prc = $dtl_desc_prc . $tmp_dtl_prc;
                    $end_dayofweek = $r_dt->dayofweek;
                    $end_dt_1      = $r_dt->add_dt_1;

                    $bef_dtl_prc = $dtl_prc;
                    $bef_total_prc = $total_prc;

                    // 추가인원
                    if ($gst_num > 4)
                    {
                        $tmp_prc = ($gst_num - 4) * 3 * $days_cnt;

                        $tmp_dtl_prc = $tmp_prc . "([추가인원(3)])";

                        $total_prc = $total_prc + $tmp_prc;

                        $dtl_prc = $dtl_prc . " + " . $tmp_prc;
                        $dtl_desc_prc = $dtl_desc_prc . " + " . $tmp_dtl_prc;

                        $extra_num_desc = "기본정원 4인 외에 " . ($gst_num - 4) . "분이 계셔서 추가요금이 "
                                        . "발생합니다.\n\n"
                                        . "추가요금은 한분당 1박에 3만원으로\n"
                                        . ($gst_num - 4) . "인 * 3만원 * " . $days_cnt . "박 = " . $tmp_prc . " 만원입니다.\n\n"
                                        . "총 요금은 " . $bef_total_prc . " + " . $tmp_prc . " = " . $total_prc . " 만원입니다.\n\n"
                                        . "추가인원분께는 추가침구세트(더블사이즈 요와 이불, 베개)와 수건, 식기가 준비됩니다.\n\n";

                    }

                    // 재방문 할인
                    if (strncmp($revisit_yn, "Y", 1) == 0)
                    {
                        $tmp_prc = 1 * $days_cnt;

                        $tmp_dtl_prc = $tmp_prc . "([재방문할인(1)])";

                        $total_prc = $total_prc - $tmp_prc;

                        $dtl_prc = $dtl_prc . " - " . $tmp_prc;
                        $dtl_desc_prc = $dtl_desc_prc . " - " . $tmp_dtl_prc;

                        //$bef_total_prc = $total_prc;

                        $revisit_desc = "여기에 다시 찾아주시는 고객님께 감사한 마음으로 1박에 1만원씩 총 " . $tmp_prc . " 만원 할인해 드리려 합니다.\n\n"
                                        . "할인된 요금은 " . ($total_prc + $tmp_prc) . " - " . $tmp_prc . " = " . $total_prc . " 만원입니다.\n\n";
                    }

                    $dtl_prc = $dtl_prc . " = " . $total_prc;
                    $dtl_prc_expr = $bef_dtl_prc . " = " . $bef_total_prc;
                    $dtl_desc_prc = $dtl_desc_prc . " = " . $total_prc;
                }
                else
                {
                    $dtl_prc = $dtl_prc . $tmp_prc . " + ";
                    $dtl_desc_prc = $dtl_desc_prc . $tmp_dtl_prc . " + ";
                }

                info_log("rsvt/prc", "dtl_prc        = [" . $dtl_prc . "]", $log_lvl);
            }

            $std_srt_dt = substr($srt_dt, 0, 4) . "/" . substr($srt_dt, 4, 2) . "/" . substr($srt_dt, 6, 2);
            // 2019.04.16. 종료일을 체크아웃 기준으로 변경
            //$std_end_dt = substr($end_dt, 0, 4) . "/" . substr($end_dt, 4, 2) . "/" . substr($end_dt, 6, 2);
            $std_end_dt = substr($end_dt_1, 0, 4) . "/" . substr($end_dt_1, 4, 2) . "/" . substr($end_dt_1, 6, 2);

            $arr_prc_cls_desc_cnt = count($arr_prc_cls_desc);

            foreach($arr_prc_cls_desc as $t_prc_cls_desc)
            {
                $prc_cls_desc_msg = $prc_cls_desc_msg . $t_prc_cls_desc;

                $prcs_cnt = $prcs_cnt + 1;

                if ($prcs_cnt >= 1 && $prcs_cnt < $arr_prc_cls_desc_cnt)
                {
                    $prc_cls_desc_msg = $prc_cls_desc_msg . ", ";
                }

                if ($prcs_cnt == $arr_prc_cls_desc_cnt)
                {
                    if (strncmp($season_discnt, "Y", 1) == 0)
                    {
                        $prc_cls_desc_msg = $prc_cls_desc_msg . ", 성수기 연박할인";
                    }

                    if (strncmp($discnt, "Y", 1) == 0)
                    {
                        $prc_cls_desc_msg = $prc_cls_desc_msg . ", 연박할인";
                    }
                }
            }

            if ($gst_num > 4)
            {
                $tmp_txt = "에\n";
            }
            else
            {
                $tmp_txt = "입니다.\n\n";
            }

            if ($gst_num2 == 0)
            {
                $gst_num_text = "성인 " . $gst_num1 . "분,";
            }
            else
            {
                $gst_num_text = "성인 " . $gst_num1 . "분,  자녀 " . $gst_num2 . "분,";
            }

            $info_msg = $info_msg . "문의주신 " . $std_srt_dt . " ~ " . $std_end_dt . " (" . $srt_dayofweek . "~" . $end_dayofweek . ") " . $days_cnt . "박, \n"
                                  . $hsrm_cls_nm . "에 " . $gst_num_text . " 예약 가능하십니다.\n\n"
                                  . "객실 요금은 " . $prc_cls_desc_msg . " 적용으로 \n" . $dtl_prc_expr . " 만원" . $tmp_txt
                                  . $extra_num_desc
                                  . $revisit_desc
                                  ;

            $acnt_info_msg = "아래의 계좌로 6시간 이내에 입금하시고 예약자 성함과 함께 입금완료 문자 주시면, "
                           . "확인 후 예약확정 문자를 다시 드립니다.\n\n"
                           . "토스뱅크 1000-1841-2808\n"
                           . "예금주 강민석\n\n"
                           . "감사합니다:)";


            // 할인 적용시
            if ($discount_rt > 0)
            {
                info_log("rsvt/prc", "할인 적용건!", $log_lvl);

                $bef_total_prc = $total_prc;
                $total_prc = round(($bef_total_prc * (100 - $discount_rt) / 100));
                //$total_prc = ceil(($bef_total_prc * (100 - $discount_rt) / 100));

                info_log("rsvt/prc", "bef_total_prc    = [" . $bef_total_prc . "]", $log_lvl);
                info_log("rsvt/prc", "total_prc        = [" . $total_prc . "]", $log_lvl);

                if ($deposit > 0)
                {
                    $deposit_msg = "만원에 보증금 " . $deposit . "만원을 더해서 총 " . ($total_prc + $deposit) . "만원입니다. \n\n";
                }
                else
                {
                    $deposit_msg = "만원입니다. \n\n";
                }

                $info_msg2 = "장기 숙박 할인 조건 안내 \n\n"
                           . "현재 " . $days_cnt . "박으로 문의주셔서 " . $discount_rt . "%의 할인을 해드리는 조건으로 예약이 가능합니다. \n\n"
                           . "세부 사항으로는 \n"
                           . " - 할인 조건시에는 세탁기로 직접 수건을 세탁해 쓰시고,\n\n"
                           . " - 음식물/재활용/일반 쓰레기는 체크아웃하시는 날까지 차로 5분 거리의 클린하우스에 직접 배출하셔야 함을 양지해 주세요. (클린하우스 위치는 별도 안내드리겠습니다) \n"
                           . "   꼭 마지막 날 쓰레기도 모두 비워서 클린하우스에 버려주세요.\n\n"
                           . " - 세제와 휴지통용 비닐, 종량제 봉투, 그리고 음식물 쓰레기 배출용 티머니 카드는 저희가 준비해 놓겠습니다.\n\n"
                           . " - 계시는 동안 외출하실 때 보일러/에어컨 전원과 조명 소등도 확인해 주시면 정말 감사하겠습니다. \n\n"
                           . " - 이불/베게 등 침구류의 오염, 기타 물품 파손, 쓰레기 없음, 보일러/에어컨 전원과 조명 소등 여부를 마지막 날 체크아웃 전에 꼭 확인해 주시기 바랍니다 \n\n"
                           . " - 보증금은 체크아웃 후 오염 및 파손, 쓰레기 없음을 확인하고 예약자분 계좌로 돌려드립니다. \n\n"
                           . "이 조건의 예약일 경우, \n"
                           . $bef_total_prc . "만원에서 " . $discount_rt . "% 할인된 금액인 " . $total_prc . $deposit_msg
                           ;

                $info_msg2 = $info_msg2 . $acnt_info_msg;
            }
            else
            {
                $info_msg = $info_msg . $acnt_info_msg;
            }

            //$info_msg = nl2br($info_msg);

            info_log("rsvt/prc", "info_msg        = [" . $info_msg  . "]");
            info_log("rsvt/prc", "info_msg2       = [" . $info_msg2 . "]");

            // 가격조회 이력 입력 시작
            $this->db->trans_begin();

            $qry_dt = date("Ymd");

            $i_data = array('qry_dt'      => $qry_dt
                           ,'hsrm_cls'    => $hsrm_cls
                           ,'srt_dt'      => $srt_dt
                           ,'end_dt'      => $end_dt
                           ,'gst_num'     => $gst_num
                           ,'discount_rt' => $discount_rt
                           ,'total_prc'   => $total_prc
                           );

            $result = $this->stay_m->insert_tba009l00($i_data);

            if (!$result)
            {
                info_log("rsvt/ins/insert_tba009l00", "last_query  = [" . $this->db->last_query() . "]");
                $this->db->trans_rollback();
                alert_log("rsvt/ins/insert_tba009l00", "[SQL ERR] 가격 조회 이력 입력 처리 오류!");
            }

            $this->db->trans_commit();
            // 가격조회 이력 입력 종료

            info_log("rsvt/prc", "가격 조회 종료!");
            info_log("rsvt/prc", "================================================================================");

            $this->rsvt_prc_v('r', $hsrm_cls, $srt_dt, $end_dt, $gst_num, $revisit_yn, $discount_rt, $deposit, $extend_yn, $dtl_prc, $dtl_desc_prc, $info_msg, $info_msg2);

        }
        else
        {
            info_log("rsvt/prc", "화면 로딩!");
            $this->rsvt_prc_v();
        }
    }


    public function cnfm_msg()
    {
        info_log("rsvt/cnfm_msg/", "================================================================================");
        info_log("rsvt/cnfm_msg/", "예약 확정 메시지 조회 시작!");

        // 검색 변수 초기화
        $rsv_srno = 0;
        $log_lvl = "r";

        $rsv_srno = $this->uri->segment(3);

        info_log("rsvt/cnfm_msg/", "rsv_srno = [" . $rsv_srno  . "]");

        // 예약 연장 여부 조회
        $continue_rsvt = $this->stay_m->get_continue_rsvt_yn($rsv_srno);

        if (isset($continue_rsvt))
        {
            $rsv_term_msg = rsv_term_msg($continue_rsvt->hsrm_cls, $continue_rsvt->srt_dt, $continue_rsvt->end_dt, $continue_rsvt->stnd_srt_dt, $continue_rsvt->stnd_g_end_dt);

            info_log("rsvt/cnfm_msg/", "rsv_term_msg      = [" . $rsv_term_msg . "]");

            $cnfm_msg = $continue_rsvt->stay_cnt . "박 " . $continue_rsvt->amt . "만원 입금(" . $continue_rsvt->stnd_cnfm_dt . ") 확인되어,\n"
                      . "예약일정이 " . $rsv_term_msg . "으로 변경되었습니다.\n\n"
                      . "감사합니다. :-)";

        }
        else
        {
            // 예약정보 조회
            $rsvt_info = $this->stay_m->get_rsvt_info($rsv_srno);

            // 메시지 생성
            info_log("rsvt/cnfm_msg/", "gst_nm      = [" . $rsvt_info->gst_nm . "]"     , $log_lvl);

            //if (strcmp($rsvt_info->hsrm_cls, "01") == 0)
            //{
            //    $hsrm_cls_size = "19평";
            //}
            //else if (strcmp($rsvt_info->hsrm_cls, "02") == 0)
            //{
            //    $hsrm_cls_size = "21평";
            //}

            info_log("rsvt/cnfm_msg/", "hsrm_cls  = [" . $rsvt_info->hsrm_cls . "]"    , $log_lvl);
            info_log("rsvt/cnfm_msg/", "srt_dt    = [" . $rsvt_info->srt_dt . "]" , $log_lvl);
            info_log("rsvt/cnfm_msg/", "end_dt    = [" . $rsvt_info->end_dt . "]" , $log_lvl);
    
            // get_rsv_term($hsrm_cls, $srt_dt, $end_dt, $extend_yn)
            //$term_info = $this->stay_m->get_rsv_term($rsvt_info->hsrm_cls, $rsvt_info->srt_dt, $rsvt_info->end_dt, 'Y');
            $rsv_term_msg = rsv_term_msg($rsvt_info->hsrm_cls, $rsvt_info->srt_dt, $rsvt_info->end_dt, $rsvt_info->stnd_srt_dt, $rsvt_info->stnd_g_end_dt);
       
            /* 2020.10.13. 성인/자녀수 구분 등록 처리 기능 추가로 인한 메시지 변경
            $cnfm_msg = "안녕하세요, " . $rsvt_info->gst_nm . "님!\n\n"
                      . $rsv_term_msg . "\n"
                      . "머무른채 " . $rsvt_info->hsrm_cls_nm . " " . $hsrm_cls_size . "에\n"
                      . $rsvt_info->gst_desc . "분, 입금 확인되어 예약 확정되었습니다.\n\n"
                      . "입실은 오후 5시 이후이며\n"
                      . "퇴실은 오전 11시까지입니다.\n\n"
                      . "오시는 날,\n"
                      . "주소 안내 문자를 보내드리겠습니다.\n\n"
                      . "설레임 가득한 여행 준비하시고,\n"
                      . "그 날 인사드리겠습니다.\n\n"
                      . "감사합니다. :)\n\n\n"
                      . "*혹 예약자분과 입금자분 성함이 다를 경우, 예약자분 성함도 알려주시면 감사하겠습니다.\n\n"
                      . "*자녀와 함께 오시는 경우 성별과 나이를 알려주시면 세팅에 참고가 될 거 같습니다. ^^\n\n"
                      ;
            */
    
            $gst_cnt_msg = "성인 " . $rsvt_info->adlt_cnt . "분";
    
            if ($rsvt_info->chld_cnt > 0)
            {
                $gst_cnt_msg = $gst_cnt_msg . ", 자녀 " . $rsvt_info->chld_cnt . "분";
            }
    
            //2022.11.28 숙소 평수 안내 삭제
            //$cnfm_msg = "안녕하세요, " . $rsvt_info->gst_nm . "님!\n\n"
            //          . $rsv_term_msg . "\n"
            //          . "머무른채 " . $rsvt_info->hsrm_cls_nm . " " . $hsrm_cls_size . "에\n"
            //          . $gst_cnt_msg . " 입금 확인되어 예약 확정되었습니다.\n\n"
            //          . "입실은 오후 5시 이후이며\n"
            //          . "퇴실은 오전 11시까지입니다.\n\n"
            //          . "오시는 날,\n"
            //          . "주소 안내 문자를 보내드리겠습니다.\n\n"
            //          . "건강히 설레임 가득한 여행 준비하시고,\n"
            //          . "그 날 인사드리겠습니다.\n\n"
            //          . "저희는 머무르시는 동안 아늑하고 편안하게 지내실 수 있도록 정갈하게 준비할게요!\n\n"
            //          . "감사합니다. :)\n\n\n"
            //          . "*예약자와 입금자가 다를 경우에는 예약자 성함, \n"
            //          . " 예약자가 숙박하지 않고 대신 예약해주는 경우에는 숙박자 대표의\n"
            //          . " 성함과 연락처도 알려주시기 바랍니다.\n\n"
            //          . "*자녀와 함께 오시는 경우 성별과 나이를 알려주시면 세팅에 참고가 될 거 같습니다. ^^\n\n"
            //          ;
    
            $cnfm_msg = "안녕하세요, " . $rsvt_info->gst_nm . "님!\n\n"
                      . $rsv_term_msg . "\n"
                      . "머무른채 " . $rsvt_info->hsrm_cls_nm . "에\n"
                      . $gst_cnt_msg . " 입금 확인되어 예약 확정되었습니다.\n\n"
                      . "입실은 오후 5시 이후이며\n"
                      . "퇴실은 오전 11시까지입니다.\n\n"
                      . "오시는 날,\n"
                      . "주소 안내 문자를 보내드리겠습니다.\n\n"
                      . "건강히 설레임 가득한 여행 준비하시고,\n"
                      . "그 날 인사드리겠습니다.\n\n"
                      . "저희는 머무르시는 동안 아늑하고 편안하게 지내실 수 있도록 정갈하게 준비할게요!\n\n"
                      . "감사합니다. :)\n\n\n"
                      . "*예약자와 입금자가 다를 경우에는 예약자 성함, \n"
                      . " 예약자가 숙박하지 않고 대신 예약해주는 경우에는 숙박자 대표의\n"
                      . " 성함과 연락처도 알려주시기 바랍니다.\n\n"
                      . "*자녀와 함께 오시는 경우 성별과 나이를 알려주시면 세팅에 참고가 될 거 같습니다. ^^\n\n"
                      ;

            info_log("rsvt/cnfm_msg/", "cnfm_msg      = [" . $cnfm_msg . "]"   , $log_lvl);
        }

        $data['cnfm_msg'] = $cnfm_msg;

        info_log("rsvt/cnfm_msg/", "예약 확정 메시지 조회 완료!");
        info_log("rsvt/cnfm_msg/", "================================================================================");

        // 뷰 호출
        $this->load->view('rsvt_msg_v', $data);
    }


    public function rsvt_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $data['rsv_srno'] = $this->uri->segment(3);
            $data['view'] = $this->stay_m->get_rsvt_info($this->uri->segment(3));
            $data['tr_list'] = $this->stay_m->get_tr_list($this->uri->segment(3));
        }

        $data['hsrm_cls_list']     = $this->stay_m->get_list('HSRM_CLS');
        $data['rsv_chnl_cls_list'] = $this->stay_m->get_list('RSV_CHNL_CLS', 'Y');
        $data['gst_cls_list']      = $this->stay_m->get_list('GST_CLS', 'Y');

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        $this->load->view('rsvt_reg_v', $data);
    }


    public function rsvt_cncl_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        $data['rsv_srno'] = $this->uri->segment(3);

        $view = $this->stay_m->get_rsvt_info($this->uri->segment(3));
        $dt_info = $this->stay_m->get_pay_rcv_dt($view->srt_dt);

        info_log("rsvt/rsvt_cncl_v", "rcv_dt      = [" . $dt_info->rcv_dt . "]");

        $data['view'] = $view;
        //$data['dt_info'] = $dt_info;

        $data['stnd_rcv_dt'] = substr($dt_info->rcv_dt, 0, 4) . "-" . substr($dt_info->rcv_dt, 4, 2) . "-" . substr($dt_info->rcv_dt, 6, 2);

        $data['hsrm_cls_list']     = $this->stay_m->get_list('HSRM_CLS');
        $data['rsv_chnl_cls_list'] = $this->stay_m->get_list('RSV_CHNL_CLS', 'Y');

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $data['tr_srno']  = $this->uri->segment(4);
            $data['etc_incm_view'] = $this->stay_m->get_etc_incm_info($this->uri->segment(4));
        }

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        $this->load->view('rsvt_cncl_v', $data);
    }


    public function rsvt_prc_v($prcs_cls='', $hsrm_cls='', $srt_dt='', $end_dt='', $gst_num='', $revisit_yn='', $discount_rt='', $deposit='', $extend_yn='', $dtl_prc='', $dtl_desc_prc='', $info_msg='', $info_msg2='')
    {
        $data['hsrm_cls_list'] = $this->stay_m->get_list('HSRM_CLS');
        $data['hsrm_cls']  = $hsrm_cls;

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        //info_log("rsvt/rsvt_prc_v", "stnd_dt        = [" . $stnd_dt . "]");
        //info_log("rsvt/rsvt_prc_v", "revisit_yn        = [" . $revisit_yn . "]");

        $data['prcs_cls'] = $prcs_cls;

        //$data['srt_dt']   = substr($srt_dt, 0, 4) . "-" . substr($srt_dt, 4, 2) . "-" . substr($srt_dt, 6, 2);
        //$data['end_dt']   = substr($end_dt, 0, 4) . "-" . substr($end_dt, 4, 2) . "-" . substr($end_dt, 6, 2);

        $data['srt_dt']       = $srt_dt;
        $data['end_dt']       = $end_dt;
        $data['gst_num']      = $gst_num;
        $data['revisit_yn']   = $revisit_yn;
        $data['discount_rt']  = $discount_rt;
        $data['deposit']      = $deposit;
        $data['extend_yn']    = $extend_yn;
        $data['dtl_prc']      = $dtl_prc;
        $data['dtl_desc_prc'] = $dtl_desc_prc;
        $data['info_msg']     = $info_msg;
        $data['info_msg2']     = $info_msg2;

        //info_log("rsvt/rsvt_prc_v", "data['srt_dt']        = [" . $data['srt_dt'] . "]");
        //info_log("rsvt/rsvt_prc_v", "num_prsn        = [" . $gst_num . "]");

        $this->load->view('rsvt_prc_v', $data);
    }


}
