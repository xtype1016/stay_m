<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    수입(income) 조회
*/

Class Alba extends CI_Controller
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
            info_log("alba", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->smmry();
        redirect('alba/list','refresh');
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

        info_log("alba/smmry", "================================================================================");
        info_log("alba/smmry", "알바 입출금 요약(잔액) 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = $page_url = '';

        $stnd_yymm = str_replace('-', '', $this->uri->segment(3));

        //info_log("alba/list", "stnd_yymm = [" . $stnd_yymm . "]");

        $uri_segment = 5;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_yymm))
        {
            //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
            $stnd_yymm = date("Ym");
        }

        $stnd_dt = date("Ymd");

        //info_log("alba/list", "rsv_chnl_cls = [" . $rsv_chnl_cls . "]");

        // Pagination 용 주소
        $page_url = '/' . $stnd_yymm;

        // 페이지네이션 설정
        $config['base_url']         = '/alba/smmry/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_alba_io_smmry('rowcnt');         // 표시할 게시물 총 수
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
            $data['alba_io_smmry_list'] = $this->stay_m->get_alba_io_smmry('data', $start, $limit);
        }

        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);

        //$data['view_cls'] = $view_cls;

        info_log("alba/smmry", "알바 입출금 요약(잔액) 조회 완료!");
        info_log("alba/smmry", "================================================================================");

        $this->load->view('alba_smmry_v', $data);
    }



    public function list()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("alba/list", "================================================================================");
        info_log("alba/list", "알바 입출금 리스트 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = $page_url = '';

        $stnd_yymm = str_replace('-', '', $this->uri->segment(3));
        $io_cls = $this->uri->segment(4);

        //info_log("alba/list", "stnd_yymm = [" . $stnd_yymm . "]");

        $uri_segment = 6;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_yymm))
        {
            //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
            $stnd_yymm = date("Ym");
        }

        if (empty($io_cls))
        {
            $io_cls = "all";
        }

        $stnd_dt = date("Ymd");

        //info_log("alba/list", "io_cls = [" . $io_cls . "]");

        // Pagination 용 주소
        $page_url = '/' . $stnd_yymm . '/' . $io_cls;

        $data['io_cls'] = $io_cls;

        if (strcmp($io_cls, "all") == 0)
        {
            $io_cls = "";
        }

        // 페이지네이션 설정
        $config['base_url']         = '/alba/list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_alba_io_list($stnd_yymm, $io_cls, 'rowcnt');         // 표시할 게시물 총 수
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
            $data['alba_io_list'] = $this->stay_m->get_alba_io_list($stnd_yymm, $io_cls, 'data', $start, $limit);
        }

        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);

        $t_io_cls_list = new stdClass();
        $t_io_cls_list->clm_nm     = 'IO_CLS';
        $t_io_cls_list->clm_val    = 'all';
        $t_io_cls_list->clm_val_nm = '전체';
        $t_io_cls_list->othr_info  = '';

        $io_cls_list = $this->stay_m->get_list('IO_CLS');

        array_unshift($io_cls_list, $t_io_cls_list);

        $data['io_cls_list'] = $io_cls_list;

        info_log("alba/list", "알바 입출금 리스트 조회 완료!");
        info_log("alba/list", "================================================================================");

        $this->load->view('alba_list_v', $data);

    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {

            info_log("alba/ins/", "================================================================================");
            info_log("alba/ins/", "알바 입출금 입력 시작!");

            $this->form_validation->set_rules('dt'      , '일자'    , 'required');
            $this->form_validation->set_rules('io_cls'  , '입출구분', 'required');
            $this->form_validation->set_rules('memo'    , '메모'    , 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                if ($amt <= 0)
                {
                    alert_log("alba/ins", "금액이 0보다 작거나 같습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                //info_log("alba/ins", "ssamzi_yn = [" . $this->input->post('ssamzi_yn', 'TRUE') . "]");

                $this->db->trans_begin();

                $i_data = array('dt'      => str_replace('-', '', $this->input->post('dt', 'TRUE'))
                               ,'io_cls'  => $this->input->post('io_cls', 'TRUE')
                               ,'emp_no'  => $this->input->post('emp_no', 'TRUE')
                               ,'memo'    => trim($memo)
                               ,'amt'     => $amt
                               );

                $result = $this->stay_m->insert_tbc001l00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();

                    info_log("alba/ins/", "알바 입출금 입력 완료!");
                    info_log("alba/ins/", "================================================================================");

                    $ins_cls = $this->uri->segment(3);
                    //info_log("alba/ins", "ins_cls = [" . $ins_cls . "]");

                    if (strncmp($ins_cls, "r", 1) == 0)
                    {
                        redirect(base_url("alba/ins"));
                    }
                    else
                    {
                        redirect(base_url("alba/list"));
                    }
                }
                else
                {
                    info_log("alba/ins/insert_tbc001l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("alba/ins/insert_tbc001l00", "[SQL ERR] 알바 입출금 입력 오류!");
                }
            }
            else
            {
                $this->alba_reg_v('i');
            }
        }
        else
        {
            $this->alba_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("alba/upd/", "================================================================================");
            info_log("alba/upd/", "알바 입출금 수정 시작!");

            $this->form_validation->set_rules('dt'      , '일자'    , 'required');
            $this->form_validation->set_rules('io_cls'  , '입출구분', 'required');
            $this->form_validation->set_rules('memo'    , '메모'    , 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                if ($amt <= 0)
                {
                    alert_log("alba/ins", "금액이 0보다 작거나 같습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                $this->db->trans_begin();

                $srno = $this->uri->segment(3);

                $u_data = array('srno'    => $srno
                               ,'dt'      => str_replace('-', '', $this->input->post('dt', 'TRUE'))
                               ,'io_cls'  => $this->input->post('io_cls', 'TRUE')
                               ,'emp_no'  => $this->input->post('emp_no', 'TRUE')
                               ,'memo'    => trim($memo)
                               ,'amt'     => $amt
                               );

                $result = $this->stay_m->update_tbc001l00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("alba/upd/update_tbc001l00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("alba/upd/update_tbc001l00_1", "[SQL ERR] 알바 입출금 수정 처리 오류![" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("alba/upd/", "알바 입출금 수정 완료!");
                        info_log("alba/upd/", "================================================================================");

                        redirect(base_url("alba/list"));
                    }
                }
                else
                {
                    info_log("alba/upd/update_tbc001l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("alba/upd/update_tbc001l00_1", "[SQL ERR] 알바 입출금 수정 오류!");
                }
            }
            else
            {
                $this->alba_reg_v('u');
            }
        }
        else
        {
            $this->alba_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("alba/upd/", "================================================================================");
            info_log("alba/upd/", "알바 입출금 삭제 시작!");

            $this->form_validation->set_rules('dt'      , '일자'    , 'required');
            $this->form_validation->set_rules('io_cls'  , '입출구분', 'required');
            $this->form_validation->set_rules('memo'    , '메모'    , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $srno = $this->uri->segment(3);

                $u_data = array('srno'     => $srno
                           );

                $result = $this->stay_m->update_tbc001l00_2($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("alba/upd/update_tbc001l00_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("alba/upd/update_tbc001l00_2", "[SQL ERR] 알바 입출금 삭제 처리 오류![" . $prcs_cnt . "]!");
                        info_log("alba/upd/update_tbc001l00_2", "================================================================================");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("alba/upd/", "알바 입출금 삭제 완료!");
                        info_log("alba/upd/", "================================================================================");

                        redirect(base_url("alba/list"));
                    }
                }
                else
                {
                    info_log("alba/upd/update_tbc001l00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("alba/upd/update_tbc001l00_2", "[SQL ERR] 알바 입출금 삭제 오류!");
                    info_log("alba/upd/update_tbc001l00_2", "================================================================================");
                }
            }
            else
            {
                $this->alba_reg_v('u');
            }
        }
        else
        {
            $this->alba_reg_v('u');
        }
    }


    public function alba_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        ////$data['alba_cls_list']       = $this->stay_m->get_list('alba_CLS');
        //$alba_cls_result = $this->stay_m->get_list('alba_CLS');
        //
        //$i = -1;
        //foreach($alba_cls_result as $e_cls_list)
        //{
        //    if (!isset($e_cls_list->othr_info))
        //    {
        //        $i = $i + 1;
        //        $alba_large_cls[$i] = $e_cls_list->clm_val_nm;
        //    }
        //    else
        //    {
        //        $alba_cls[$alba_large_cls[$i]][$e_cls_list->clm_val] = $e_cls_list->clm_val_nm;
        //    }
        //}
        ////print_r($alba_cls);
        //$data['alba_cls_list'] = $alba_cls;
        //
        //$data['alba_chnl_cls_list']  = $this->stay_m->get_list('alba_CHNL_CLS', 'Y');
        //
        //$data['cost_cls_list']  = $this->stay_m->get_list('COST_CLS', 'Y');

        //print_r($data['alba_chnl_cls_list']);
        //print_r($data['cost_cls_list']);

        $data['io_cls_list']  = $this->stay_m->get_list('IO_CLS', 'N');
        $data['emp_list']     = $this->stay_m->get_list('USR', 'Y');

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            //info_log("alba/alba_reg_v", "this->uri->segment(4) = [" . $this->uri->segment(4) . "]");
            $data['view'] = $this->stay_m->get_alba_io_info($this->uri->segment(3));
        }

        $this->load->view('alba_reg_v', $data);
    }


}
