<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    예약관리 등록 컨트롤러
*/
class Expns_chnl extends CI_Controller
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
            info_log("expns_chnl", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->list();
        redirect('expns_chnl/list','refresh');
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

        info_log("expns_chnl/list/", "================================================================================");
        info_log("expns_chnl/list/", "지출 매체 조회 시작!");

        $uri_segment = 4;          // 페이지 번호가 위치한 세그먼트
        
        // 검색 변수 초기화
        // Pagination 용 주소
        $page_url = '';

        // 페이지네이션 설정
        //$config['base_url']         = '/expns_chnl/list/' . $page_url . '/page/';
        $config['base_url']         = '/expns_chnl/list/page/';
        $config['total_rows']       = $this->stay_m->get_item_list('EXPNS_CHNL_CLS', '', 'rowcnt');         // 표시할 게시물 총 수
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
            $data['expns_chnl_list'] = $this->stay_m->get_item_list('EXPNS_CHNL_CLS', '', 'data', $start, $limit);
        }

        
        info_log("expns_chnl/list/", "config['total_rows'] = [" . $config['total_rows']  . "]");

        info_log("expns_chnl/list/", "지출 매체 조회 완료!");
        info_log("expns_chnl/list/", "================================================================================");

        $this->load->view('expns_chnl_list_v', $data);
    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->form_validation->set_rules('expns_chnl_cls_nm', '지출매체'  , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $expns_chnl_cls = $this->stay_m->get_clm_sr_val('EXPNS_CHNL_CLS');

                $i_data = array('clm_nm'      => 'EXPNS_CHNL_CLS'
                               ,'clm_val'     => $expns_chnl_cls
                               ,'clm_val_nm'  => trim($this->input->post('expns_chnl_cls_nm'  , 'TRUE'))
                               ,'othr_info'   => NULL
                               );

                $result = $this->stay_m->insert_tba003i00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();
                    
                    info_log("expns_chnl/ins/insert_tba003i00", "지출매체 입력 완료!");
                    info_log("expns_chnl/ins/insert_tba003i00", "================================================================================");
                    
                    redirect(base_url("/expns_chnl/list"));
                }
                else
                {
                    info_log("expns_chnl/ins/insert_tba003i00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("expns_chnl/ins/insert_tba003i00", "[SQL ERR] 지출매체 입력 오류!");
                }
            }
            else
            {
                $this->expns_chnl_reg_v('i');
            }
        }
        else
        {
            $this->expns_chnl_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->form_validation->set_rules('expns_chnl_cls_nm', '지출매체'     , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $u_data = array('clm_nm'      => 'EXPNS_CHNL_CLS'
                               ,'clm_val'     => $this->uri->segment(3)
                               ,'clm_val_nm'  => trim($this->input->post('expns_chnl_cls_nm'  , 'TRUE'))
                               ,'othr_info'   => NULL
                               );

                $result = $this->stay_m->update_tba003i00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("expns_chnl/upd/update_tba003i00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("expns_chnl/upd/update_tba003i00_1", "[SQL ERR] 지출매체 수정 건수 오류![" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("expns_chnl/upd/update_tba003i00_1", "지출매체 수정 완료!");
                        info_log("expns_chnl/upd/update_tba003i00_1", "================================================================================");

                        redirect(base_url("/expns_chnl/list"));
                    }
                }
                else
                {
                    info_log("expns_chnl/upd/update_tba003i00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("expns_chnl/upd/update_tba003i00_1", "[SQL ERR] 지출매체 수정 오류!");
                }
            }
            else
            {
                $this->expns_chnl_reg_v('u');
            }
        }
        else
        {
            $this->expns_chnl_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->db->trans_begin();

            $u_data = array('clm_nm'    => 'EXPNS_CHNL_CLS'
                           ,'clm_val'   => $this->uri->segment(3)
                           );

            $result = $this->stay_m->update_tba003i00_2($u_data);

            if ($result)
            {
                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $this->db->affected_rows();
                if ($prcs_cnt != 1)
                {
                    info_log("expns_chnl/del/update_tba003i00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("expns_chnl/del/update_tba003i00_2", "[SQL ERR] 지출매체 삭제 건수 오류![" . $prcs_cnt . "]!");
                }
                else
                {
                    $this->db->trans_commit();

                    info_log("expns_chnl/del/update_tba003i00_2", "지출매체 삭제 수정 완료!");
                    info_log("expns_chnl/del/update_tba003i00_2", "================================================================================");

                    redirect(base_url("/expns_chnl/list"));
                }
            }
            else
            {
                info_log("expns_chnl/del/update_tba003i00_2", "last_query  = [" . $this->db->last_query() . "]");
                $this->db->trans_rollback();
                alert_log("expns_chnl/del/update_tba003i00_2", "[SQL ERR] 지출매체 삭제 오류!");
            }
        }
    }


    public function expns_chnl_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;
        $data['EXPNS_CHNL_CLS'] = $this->uri->segment(3);

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $data['view'] = $this->stay_m->get_item_list('EXPNS_CHNL_CLS', $this->uri->segment(3), 'single');
        }

        $this->load->view('expns_chnl_reg_v', $data);
    }


}