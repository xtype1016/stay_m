<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    예약관리 등록 컨트롤러
*/
class Season_mng extends CI_Controller
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
            info_log("season_mng/list", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        $this->ins();
        //redirect('season_mng/ins','refresh');
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

        // 검색 변수 초기화
        $stnd_yy = $page_url = '';

        $stnd_yy    = str_replace('-', '', $this->uri->segment(3));

        $uri_segment = 5;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_yy))
        {
            //date Y: 4자리 연도, m: 0을 포함한 월(01, 02, ...), d: 0을 포함한 일(01, 02, ...31)
            $stnd_yy = date("Y");
            info_log("season_mng/list", "stnd_yy = " . $stnd_yy);
        }

        // Pagination 용 주소
        $page_url = '/' . $stnd_yy;

        // 페이지네이션 설정
        $config['base_url']         = '/season_mng/list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_season_list($stnd_yy, 'rowcnt');         // 표시할 게시물 총 수
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
            $data['season_list'] = $this->stay_m->get_season_list($stnd_yy, 'data', $start, $limit);
        }

        $data['stnd_yy'] = $stnd_yy;
        
        $data['season_cls_list']     = $this->stay_m->get_list('SEASON_CLS');

        $this->load->view('season_list_v', $data);
    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("season_mng/ins", "================================================================================");
            info_log("season_mng/ins", "시즌 입력 처리 시작!");

            $this->form_validation->set_rules('srt_dt'      , '시작일자'    , 'required');
            $this->form_validation->set_rules('end_dt'      , '종료일자'    , 'required');
            $this->form_validation->set_rules('season_cls'  , '시즌구분'    , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $srt_dt  = str_replace('-', '', $this->input->post('srt_dt' , 'TRUE'));
                $end_dt  = str_replace('-', '', $this->input->post('end_dt' , 'TRUE'));

                // 시작일이 종료일보다 작으면 오류
                if (strncmp($srt_dt, $end_dt, 8) > 0)
                {
                    alert_log("season_mng/ins", "시작일자가 종료일보다 늦습니다!");
                }

                $this->db->trans_begin();

                $season_srno = $this->stay_m->get_clm_sr_val('SEASON_SRNO');

                info_log("get_clm_sr_val", "season_srno  = [" . $season_srno . "]");

                $i_data = array('season_srno'  => $season_srno
                               ,'srt_dt'       => $srt_dt
                               ,'end_dt'       => $end_dt
                               ,'season_cls'   => $this->input->post('season_cls'                  , 'TRUE')
                               );

                $result = $this->stay_m->insert_tba008l00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();

                    info_log("season_mng/ins", "시즌 입력 처리 종료!");
                    info_log("season_mng/ins", "================================================================================");

                    $ins_cls = $this->uri->segment(3);

                    if (strncmp($ins_cls, "r", 1) == 0)
                    {
                        redirect(base_url("season_mng/ins"));
                    }
                    else
                    {
                        redirect(base_url("season_mng/list"));
                    }

                }
                else
                {
                    info_log("season_mng/ins/insert_tba008l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("season_mng/ins/insert_tba008l00", "[SQL ERR] 시즌 입력 처리 오류!");
                }
            }
            else
            {
                $this->season_reg_v('i');
            }
        }
        else
        {
            $this->season_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("season_mng/upd", "================================================================================");
            info_log("season_mng/upd", "시즌 수정 처리 시작!");

            $this->form_validation->set_rules('srt_dt'      , '시작일자'    , 'required');
            $this->form_validation->set_rules('end_dt'      , '종료일자'    , 'required');
            $this->form_validation->set_rules('season_cls'  , '시즌구분'    , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $srt_dt  = str_replace('-', '', $this->input->post('srt_dt' , 'TRUE'));
                $end_dt  = str_replace('-', '', $this->input->post('end_dt' , 'TRUE'));

                // 시작일이 종료일보다 작으면 오류
                if (strncmp($srt_dt, $end_dt, 8) > 0)
                {
                    alert_log("season_mng/upd", "시작일자가 종료일보다 늦습니다!");
                }

                $this->db->trans_begin();

                $season_srno = $this->uri->segment(3);

                info_log("season_mng/upd", "season_srno  = [" . $season_srno . "]");

                $u_data = array('season_srno'  => $season_srno
                               ,'srt_dt'       => $srt_dt
                               ,'end_dt'       => $end_dt
                               ,'season_cls'   => $this->input->post('season_cls', 'TRUE')
                               );

                $result = $this->stay_m->update_tba008l00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("season_mng/upd/update_tba008l00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("season_mng/upd/update_tba008l00_1", "[SQL ERR] 시즌 수정 처리 오류![" . $prcs_cnt . "]!");
                        info_log("season_mng/upd/update_tba008l00_1", "================================================================================");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("season_mng/upd/update_tba008l00_1", "시즌 수정 완료!");
                        info_log("season_mng/upd/update_tba008l00_1", "================================================================================");

                        redirect(base_url("season_mng/list"));
                    }
                }
                else
                {
                    info_log("season_mng/upd/update_tba008l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("season_mng/upd/update_tba008l00_1", "[SQL ERR] 시즌 수정 오류!");
                    info_log("season_mng/upd/update_tba008l00_1", "================================================================================");
                }
            }
            else
            {
                $this->season_reg_v('u');
            }
        }
        else
        {
            $this->season_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("season_mng/upd", "================================================================================");
            info_log("season_mng/upd", "시즌 삭제 처리 시작!");

            $this->form_validation->set_rules('srt_dt'      , '시작일자'    , 'required');
            $this->form_validation->set_rules('end_dt'      , '종료일자'    , 'required');
            $this->form_validation->set_rules('season_cls'  , '시즌구분'    , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $srt_dt  = str_replace('-', '', $this->input->post('srt_dt' , 'TRUE'));
                $end_dt  = str_replace('-', '', $this->input->post('end_dt' , 'TRUE'));

                // 시작일이 종료일보다 작으면 오류
                if (strncmp($srt_dt, $end_dt, 8) > 0)
                {
                    alert_log("season_mng/upd", "시작일자가 종료일보다 늦습니다!");
                }

                $this->db->trans_begin();

                $season_srno = $this->uri->segment(3);

                info_log("season_mng/upd", "season_srno  = [" . $season_srno . "]");

                $u_data = array('season_srno'  => $season_srno
                               );

                $result = $this->stay_m->update_tba008l00_2($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("season_mng/del/update_tba008l00_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("season_mng/del/update_tba008l00_2", "[SQL ERR] 시즌 삭제 처리 오류![" . $prcs_cnt . "]!");
                        info_log("season_mng/del/update_tba008l00_2", "================================================================================");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("season_mng/del/update_tba008l00_2", "시즌 삭제 완료!");
                        info_log("season_mng/del/update_tba008l00_2", "================================================================================");

                        redirect(base_url("season_mng/list"));
                    }
                }
                else
                {
                    info_log("season_mng/del/update_tba008l00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("season_mng/del/update_tba008l00_2", "[SQL ERR] 시즌 삭제 오류!");
                    info_log("season_mng/del/update_tba008l00_2", "================================================================================");
                }
            }
            else
            {
                $this->season_reg_v('u');
            }
        }
        else
        {
            $this->season_reg_v('u');
        }
    }


    public function season_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $data['season_srno'] = $this->uri->segment(3);
            $data['view'] = $this->stay_m->get_season_info($this->uri->segment(3));
        }

        $data['season_cls_list']     = $this->stay_m->get_list('SEASON_CLS');

        $this->load->view('season_reg_v', $data);
    }


}