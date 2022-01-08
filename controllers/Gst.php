<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    고객 관리 등록 컨트롤러
*/
class Gst extends CI_Controller
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
            info_log("incm", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->list();
        redirect('gst/list','refresh');
    }


    /*
        사이트 헤더, 푸터 추가
    */
    public function _remap($method, $params = array())
    {
        //info_log("gst/list", "method = [" . $method . "]");

        if (strncmp($method, "srch", 4) == 0)
        {
            if (method_exists($this, $method))
            {
                //$this->{"{$method}"}();
                call_user_func_array(array($this, $method), $params);
            }
        }
        else
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
    }


    public function list()
    {
        info_log("gst/list/", "================================================================================");
        info_log("gst/list/", "고객정보 조회 시작!");

        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        // 검색 변수 초기화
        $gst_nm = $page_url = '';

        //2019.12.30. Get > Post 방식으로 변경
        //2020.01.11 검색조건 수정
        $gst_nm = urldecode($this->uri->segment(3));
        $uri_segment = 5;          // 페이지 번호가 위치한 세그먼트

        //info_log("gst/list", "gst_nm  = [" . $gst_nm . "]");

        if (empty($gst_nm))
        {
            $data['total_rows'] = -1;
            //$this->load->view('gst_list_v');
            $this->load->view('gst_list_v', $data);
            //info_log("gst/list", "gst_nm empty!");
            return;
        }

        // Pagination 용 주소
        $page_url = '/' . $gst_nm . '/';

        // 페이지네이션 설정
        $config['base_url']         = '/gst/list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_gst_list($gst_nm, 'rowcnt');         // 표시할 게시물 총 수
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
            $data['gst_list'] = $this->stay_m->get_gst_list($gst_nm, 'data', $start, $limit);
            //info_log("gst/list/get_item_list", "last_query  = [" . $this->db->last_query() . "]");
        }

        $data['total_rows'] = $config['total_rows'];

        //print_r($data['gst_list']);
        //exit;

        info_log("gst/list/", "================================================================================");
        info_log("gst/list/", "고객정보 조회 완료!");

        $this->load->view('gst_list_v', $data);

    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("gst/ins/", "================================================================================");
            info_log("gst/ins/", "고객정보 입력 시작!");

            $this->form_validation->set_rules('gst_nm', '고객명', 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $gst_nm = $this->input->post('gst_nm'  , 'TRUE');
                $phone_num  = "010" . trim(str_replace('-', '', $this->input->post('phone_num', 'TRUE')));

                $dup_chk = $this->stay_m->get_gst_dup_chk($gst_nm, $phone_num);

                if ($dup_chk->cnt > 0)
                {
                    //echo "<script>alert('회원 가입 오류!(동일 아이디 존재)');</script>";
                    alert_log("gst/ins", "동일 고객정보 존재[" . $this->input->post('gst_nm'  , 'TRUE') . "]");
                    //alert_log("gst/ins", "동일 고객정보 존재!");
                }

                $gst_no = $this->stay_m->get_clm_sr_val('GST_NO');

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                $i_data = array('gst_no'    => $gst_no
                               ,'gst_nm'    => trim($this->input->post('gst_nm'  , 'TRUE'))
                               ,'phone_num' => $phone_num
                               ,'memo'      => trim($memo)
                               );

                $result = $this->stay_m->insert_tba007l00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();

                    info_log("gst/ins/", "고객정보 입력 완료!");
                    info_log("gst/ins/", "================================================================================");

                    $prcs_cls = $this->uri->segment(3);

                    if (strncmp($prcs_cls, "rsv", 3) == 0)
                    {
                        redirect(base_url("/rsvt/ins/" . $gst_no . "/" . $this->input->post('gst_nm'  , 'TRUE')));
                    }
                    else
                    {
                        redirect(base_url("/gst/list"));
                    }
                }
                else
                {
                    info_log("gst/ins/insert_tba007l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("gst/ins/insert_tba007l00", "[SQL ERR] 고객정보 입력 오류!");
                }
            }
            else
            {
                $this->gst_reg_v('i');
            }
        }
        else
        {
            $this->gst_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("gst/upd/", "================================================================================");
            info_log("gst/upd/", "고객정보 수정 시작!");

            $this->form_validation->set_rules('gst_nm', '고객명', 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $gst_no = $this->uri->segment(3);

                $phone_num  = "010" . trim(str_replace('-', '', $this->input->post('phone_num', 'TRUE')));

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                $u_data = array('gst_no'    => $gst_no
                               ,'gst_nm'    => trim($this->input->post('gst_nm'  , 'TRUE'))
                               ,'phone_num' => $phone_num
                               ,'memo'      => trim($memo)
                               );

                $result = $this->stay_m->update_tba007l00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("gst/upd/update_tba007l00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("gst/upd/update_tba007l00_1", "[SQL ERR] 고객정보 수정 건수 오류[" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $gst_nm_bef = trim($this->input->post('gst_nm_bef'  , 'TRUE'));
                        $gst_nm_aft = trim($this->input->post('gst_nm'  , 'TRUE'));

                        if (strcmp($gst_nm_bef, $gst_nm_aft) != 0)
                        {
                            info_log("gst/upd", "gst_nm_bef  = [" . $gst_nm_bef . "]");
                            info_log("gst/upd", "gst_nm_aft  = [" . $gst_nm_aft . "]");

                            info_log("gst/upd", "고객명 변경으로 인한 캘린더 정보 변경!");

                            //$gst_rsvt_list = $this->stay_m->get_gst_rsvt_list($gst_no);

                            //!!!TEST
                            //if (isset($gst_rsvt_list))
                            //{
                            //    foreach ($gst_rsvt_list as $g_list)
                            //    {
                            //        info_log("gst/upd", "g_list->rsv_srno  = [" . $g_list->rsv_srno . "]");
                            //        info_log("gst/upd", "g_list->hsrm_cls  = [" . $g_list->hsrm_cls . "]");
                            //        info_log("gst/upd", "g_list->evnt_id   = [" . $g_list->evnt_id . "]");
                            //        info_log("gst/upd", "============================================================");
                            //    }
                            //}

                            // 구글 캘린더 처리
                            //if (strncmp("http://www.stayingm.co.kr", base_url(), 25) == 0)
                            if (strcmp("https://xsvr.duckdns.org/", base_url()) == 0)
                            {
                                $gst_rsvt_list = $this->stay_m->get_gst_rsvt_list($gst_no);

                                if (isset($gst_rsvt_list))
                                {
                                    foreach ($gst_rsvt_list as $g_list)
                                    {
                                        g_auth('upd', $g_list->rsv_srno, $g_list->hsrm_cls, $g_list->evnt_id, '');
                                    }
                                }
                            }
                        }

                        $this->db->trans_commit();

                        info_log("gst/upd/", "고객정보 수정 완료!");
                        info_log("gst/upd/", "================================================================================");

                        redirect(base_url("/gst/list"));
                    }
                }
                else
                {
                    info_log("gst/upd/update_tba007l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("gst/upd/update_tba007l00_1", "[SQL ERR] 고객정보 수정 오류!");
                }
            }
            else
            {
                $this->gst_reg_v('u');
            }
        }
        else
        {
            $this->gst_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("gst/upd/", "================================================================================");
            info_log("gst/del/", "고객정보 삭제 시작!");

            $this->db->trans_begin();

            $gst_no = $this->uri->segment(3);

            $u_data = array('gst_no' => $gst_no
                           );

            $result = $this->stay_m->update_tba007l00_2($u_data);

            if ($result)
            {
                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $this->db->affected_rows();
                if ($prcs_cnt != 1)
                {
                    info_log("gst/del/update_tba007l00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("gst/del/update_tba007l00_2", "[SQL ERR] 고객정보 삭제 건수 오류[" . $prcs_cnt . "]!");
                }
                else
                {
                    $this->db->trans_commit();

                    info_log("gst/del/", "고객정보 삭제 완료!");
                    info_log("gst/upd/", "================================================================================");

                    redirect(base_url("/gst/list"));
                }
            }
            else
            {
                info_log("gst/del/update_tba007l00_2", "last_query  = [" . $this->db->last_query() . "]");
                $this->db->trans_rollback();
                alert_log("gst/del/update_tba007l00_2", "[SQL ERR] 고객정보 삭제 오류!");
            }
        }
    }


    public function srch()
    {
        //echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("gst/srch/", "================================================================================");
            info_log("gst/srch/", "고객정보 검색 조회 시작!");

            //$this->output->enable_profiler(TRUE);
            $this->load->library('pagination');

            // 검색 변수 초기화
            $gst_nm = $page_url = '';

            $gst_nm = $this->input->post('srch_gst_nm', TRUE);
            //info_log("gst/srch", "gst_nm = [" . $gst_nm . "]");

            $uri_segment = 5;          // 페이지 번호가 위치한 세그먼트

            if (empty($gst_nm))
            {
                $this->load->view('gst_list_modal_v', $data);
                info_log("gst/srch", "gst_nm empty!");
                return;
            }

            // 페이지네이션 설정
            $config['base_url']         = '/gst/srch/' . $page_url . '/page/';
            $config['total_rows']       = $this->stay_m->get_gst_list($gst_nm, 'rowcnt');         // 표시할 게시물 총 수
            //info_log("gst/list/get_item_list", "RowCnt last_query  = [" . $this->db->last_query() . "]");
            //info_log("gst/list/get_item_list", "config['total_rows']  = [" . $config['total_rows'] . "]");
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
                $data['gst_list'] = $this->stay_m->get_gst_list($gst_nm, 'data', $start, $limit);
                //info_log("gst/list/get_item_list", "last_query  = [" . $this->db->last_query() . "]");
            }

            info_log("gst/srch/", "고객정보 검색 조회 완료!");
            info_log("gst/srch/", "================================================================================");

            //print_r($data['gst_list']);
            //exit;
            $view_html = '';
            $view_html = $this->load->view('gst_list_modal_d1_v', $data);
            //echo $view_html;

        }
        else
        {
            $this->load->view('gst_list_modal_m_v');
        }

    }


    public function dtl()
    {
        //echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("gst/dtl/", "================================================================================");
        info_log("gst/dtl/", "고객정보 상세 조회 시작!");

        // 검색 변수 초기화
        $gst_no = $page_url = '';

        $bef_url = uri_string();
        info_log("gst/dtl/", "bef_url  = [" . $bef_url . "]");
        // bef_list 세션데이터 초기화
        unset($_SESSION['bef_url']);
        $this->session->set_userdata('bef_url', $bef_url);

        //info_log("gst/dtl/", "_SESSION['bef_url']  = [" . $_SESSION['bef_url'] . "]");

        $gst_no = $this->uri->segment(3);
        //info_log("gst/dtl", "gst_no = [" . $gst_no . "]");

        $uri_segment = 5;          // 페이지 번호가 위치한 세그먼트

        if (empty($gst_no))
        {
            $this->load->view('gst_dtl_list_v', $data);
            info_log("gst/dtl", "gst_no empty!");
            info_log("gst/dtl", "gst_no = [" . $gst_no . "]");
            return;
        }

        // Pagination 용 주소
        $page_url = '/' . $gst_no;

        // 페이지네이션 설정
        $config['base_url']         = '/gst/dtl/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_gst_dtl_list($gst_no, 'rowcnt');         // 표시할 게시물 총 수
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
            $data['gst_dtl_list'] = $this->stay_m->get_gst_dtl_list($gst_no, 'data', $start, $limit);
            //info_log("gst/list/get_item_list", "last_query  = [" . $this->db->last_query() . "]");
        }

        $data['gst_info'] = $this->stay_m->get_gst_info($gst_no);
        $data['gst_no'] = $this->uri->segment(3);

        info_log("gst/dtl/", "고객정보 상세 조회 완료!");
        info_log("gst/dtl/", "================================================================================");

        $this->load->view('gst_dtl_list_v', $data);

    }


    public function gst_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;
        $gst_no = $this->uri->segment(3);

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $data['view'] = $this->stay_m->get_gst_info($gst_no);
        }

        $this->load->view('gst_reg_v', $data);
    }


}