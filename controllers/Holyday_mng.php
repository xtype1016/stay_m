<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    예약관리 등록 컨트롤러
*/
class Holyday_mng extends CI_Controller
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
            info_log("holyday_mng/list", "autologin start!");
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


    public function dtl()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        // 검색 변수 초기화
        $stnd_dt = $page_url = '';

        $stnd_dt    = str_replace('-', '', $this->uri->segment(3));

        $uri_segment = 6;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_dt))
        {
            //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
            $stnd_dt = date("Ymd");
            //echo "NOT POST AF stnd_dt = " . $stnd_dt . "<BR>";
            info_log("holyday_mng/list", "stnd_dt = " . $stnd_dt);
            //echo "Check Point02!!!<br>";
        }

        // Pagination 용 주소
        $page_url = '/' . $stnd_dt;

        // 페이지네이션 설정
        $config['base_url']         = '/holyday_mng/list/' . $page_url . '/page/';
        $config['total_rows']       = 1;  //$this->stay_m->get_dt_dti($stnd_dt, 'rowcnt');         // 표시할 게시물 총 수
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
            $data['dt_list'] = $this->stay_m->get_dt_dtl($stnd_dt);
        }

        $data['stnd_dt'] = substr($stnd_dt, 0, 4) . "-" . substr($stnd_dt, 4, 2) . "-" . substr($stnd_dt, 6, 2);
        
        $data['dt_cls_list']     = $this->stay_m->get_list('DT_CLS', 'Y');

        $this->load->view('holyday_dtl_v', $data);
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("holyday_mng/upd", "================================================================================");
            info_log("holyday_mng/upd", "휴일 수정 처리 시작!");

            $this->form_validation->set_rules('dt'     , '일자', 'required');
            $this->form_validation->set_rules('dt_cls' , '구분', 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $dt = str_replace('-', '', $this->input->post('dt', 'TRUE'));

                $this->db->trans_begin();

                info_log("holyday_mng/upd", "dt = [" . $dt . "]");

                $u_data = array('dt'         => $dt
                               ,'dt_cls'     => $this->input->post('dt_cls'                , 'TRUE')
                               );

                $result = $this->stay_m->update_tba004l00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("holyday_mng/upd/update_tba004l00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("holyday_mng/upd/update_tba004l00_1", "[SQL ERR] 휴일 수정 건수 오류![" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("holyday_mng/upd", "휴일 수정 완료!");
                        info_log("holyday_mng/upd", "================================================================================");

                        redirect(base_url("holyday_mng/dtl/$dt"));
                    }
                }
                else
                {
                    info_log("holyday_mng/upd/update_tba005l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("holyday_mng/upd/update_tba005l00_1", "[SQL ERR] 휴일 수정 오류!");
                }
            }
            else
            {
                info_log("holyday_mng/upd/update_tba005l00_1", "Check Point 01!");
            }
        }
        else
        {
            info_log("holyday_mng/upd/update_tba005l00_1", "Check Point 02!");
        }
    }


    public function rsvt_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $data['rsv_srno'] = $this->uri->segment(3);
            $data['view'] = $this->stay_m->get_rsvt_info($this->uri->segment(3));
        }

        $data['hsrm_cls_list']     = $this->stay_m->get_list('HSRM_CLS');
        $data['rsv_chnl_cls_list'] = $this->stay_m->get_list('RSV_CHNL_CLS', 'Y');
        $data['gst_cls_list']      = $this->stay_m->get_list('GST_CLS', 'Y');

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        $this->load->view('rsvt_reg_v', $data);
    }


}