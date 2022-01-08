<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    예약관리 등록 컨트롤러
*/
class Etc_incm_itm extends CI_Controller
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
            info_log("etc_incm_itm", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->list();
        redirect('etc_incm_itm/list','refresh');
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

        info_log("etc_incm_itm/list/", "================================================================================");
        info_log("etc_incm_itm/list/", "기타수익항목 리스트 조회 시작!");

        // 검색 변수 초기화
        $page_url = '';
        // Pagination 용 주소
        $page_url = '/';

        $uri_segment = 4;          // 페이지 번호가 위치한 세그먼트

        // 페이지네이션 설정
        $config['base_url']         = '/etc_incm_itm/list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_item_list('TR_CLS', '', 'rowcnt');         // 표시할 게시물 총 수
        //info_log("etc_incm_itm/list/get_item_list", "RowCnt last_query  = [" . $this->db->last_query() . "]");
        //info_log("etc_incm_itm/list/get_item_list", "config['total_rows']  = [" . $config['total_rows'] . "]");
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
            $data['etc_incm_itm_list'] = $this->stay_m->get_item_list('TR_CLS', '', 'data', $start, $limit);
            //info_log("etc_incm_itm/list/get_item_list", "last_query  = [" . $this->db->last_query() . "]");
        }

        //print_r($data['etc_incm_itm_list']);
        //exit;

        info_log("etc_incm_itm/list/", "기타수익항목 리스트 조회 완료!");
        info_log("etc_incm_itm/list/", "================================================================================");

        $this->load->view('etc_incm_itm_list_v', $data);
    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("etc_incm_itm/ins/", "================================================================================");
            info_log("etc_incm_itm/ins/", "기타수익항목 입력 시작!");

            $this->form_validation->set_rules('tr_cls_nm'     , '기타거래 항목'  , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $tr_cls = $this->stay_m->get_clm_sr_val('TR_CLS');

                $i_data = array('clm_nm'      => 'TR_CLS'
                               ,'clm_val'     => $tr_cls
                               ,'clm_val_nm'  => trim($this->input->post('tr_cls_nm'  , 'TRUE'))
                               ,'othr_info'   => $this->input->post('sign_cls'  , 'TRUE')
                               );

                $result = $this->stay_m->insert_tba003i00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();

                    info_log("etc_incm_itm/ins/", "기타수익항목 입력 완료!");
                    info_log("etc_incm_itm/ins/", "================================================================================");

                    redirect(base_url("/etc_incm_itm/list"));
                }
                else
                {
                    info_log("etc_incm_itm/ins/insert_tba003i00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("etc_incm_itm/ins", "[SQL ERR] 기타수익항목 입력 오류!");
                }
            }
            else
            {
                $this->etc_incm_itm_reg_v('i');
            }
        }
        else
        {
            $this->etc_incm_itm_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("etc_incm_itm/upd/", "================================================================================");
            info_log("etc_incm_itm/upd/", "기타수익 항목 수정 시작!");


            $this->form_validation->set_rules('tr_cls_nm', '기타거래 분류'     , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $tr_cls = $this->uri->segment(3);

                $u_data = array('clm_nm'      => 'TR_CLS'
                               ,'clm_val'     => $tr_cls
                               ,'clm_val_nm'  => trim($this->input->post('tr_cls_nm'  , 'TRUE'))
                               ,'othr_info'   => $this->input->post('sign_cls'  , 'TRUE')
                               );

                $result = $this->stay_m->update_tba003i00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("etc_incm_itm/upd/update_tba003i00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("etc_incm_itm/upd/update_tba003i00_1", "[SQL ERR] 기타수익 항목 수정 건수 오류[" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("etc_incm_itm/upd/", "기타수익 항목 수정 완료!");
                        info_log("etc_incm_itm/upd/", "================================================================================");

                        redirect(base_url("/etc_incm_itm/list"));
                    }
                }
                else
                {
                    info_log("etc_incm_itm/upd/update_tba003i00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("etc_incm_itm/upd/update_tba003i00_1", "[SQL ERR] 기타수익 항목 수정 오류!");
                }
            }
            else
            {
                $this->etc_incm_itm_reg_v('u');
            }
        }
        else
        {
            $this->etc_incm_itm_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("etc_incm_itm/del/", "================================================================================");
            info_log("etc_incm_itm/del/", "기타수익 항목 삭제 시작!");

            $this->db->trans_begin();

            $u_data = array('clm_nm'    => 'TR_CLS'
                           ,'clm_val'   => $this->input->post('tr_cls'  , 'TRUE')
                           );

            $result = $this->stay_m->update_tba003i00_2($u_data);

            if ($result)
            {
                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $this->db->affected_rows();
                if ($prcs_cnt != 1)
                {
                    info_log("etc_incm_itm/del/update_tba003i00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("etc_incm_itm/del/update_tba003i00_2", "[SQL ERR] 기타수익 항목 삭제 건수 오류[" . $prcs_cnt . "]!");
                }
                else
                {
                    $this->db->trans_commit();

                    info_log("etc_incm_itm/del/", "기타수익 항목 삭제 완료!");
                    info_log("etc_incm_itm/del/", "================================================================================");

                    redirect(base_url("/etc_incm_itm/list"));
                }
            }
            else
            {
                info_log("etc_incm_itm/del/update_tba003i00_2", "last_query  = [" . $this->db->last_query() . "]");
                $this->db->trans_rollback();
                alert_log("etc_incm_itm/del/update_tba003i00_2", "[SQL ERR] 기타수익 항목 삭제 오류!");
            }
        }
    }


    public function etc_incm_itm_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;
        $data['tr_cls'] = $this->uri->segment(3);

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $data['view'] = $this->stay_m->get_item_list('TR_CLS', $this->uri->segment(3), 'single');
        }

        $this->load->view('etc_incm_itm_reg_v', $data);
    }


}