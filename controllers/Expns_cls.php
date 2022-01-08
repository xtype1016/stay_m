<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    예약관리 등록 컨트롤러
*/
class Expns_cls extends CI_Controller
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
            info_log("expns_cls", "autologin start!");
            auto_login();
        }

        login_chk();
    }


    public function index()
    {
        //$this->list();
        redirect('expns_cls/list','refresh');
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
        $page_url = '';
        // Pagination 용 주소
        $page_url = '/';

        $uri_segment = 4;          // 페이지 번호가 위치한 세그먼트

        // 페이지네이션 설정
        $config['base_url']         = '/expns_cls/list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_expns_cls_list('rowcnt');         // 표시할 게시물 총 수
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
            $data['expns_cls_list'] = $this->stay_m->get_expns_cls_list('data', $start, $limit);
            //print_r($data['expns_cls_list']);
        }

        $this->load->view('expns_cls_list_v', $data);
    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->form_validation->set_rules('clss'      , '분류구분', 'required');
            $this->form_validation->set_rules('clm_val_nm', '분류명'  , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $uppr_clss = $this->input->post('uppr_clss', 'TRUE');

                $clss = $this->input->post('clss', 'TRUE');

                info_log("expns_cls/ins", "clss = [" . $clss . "]");
                info_log("expns_cls/ins", "uppr_clss = [" . $uppr_clss . "]");

                if (strncmp($clss, "1", 1) == 0)
                {
                    $uppr_clss = NULL;
                    $mid_cls = "00";
                }
                else if (strncmp($clss, "2", 1) == 0)
                {
                    if (strncmp($uppr_clss, "NA", 2) == 0 || strlen($uppr_clss) == 0)
                    {
                        alert_log("expns_cls/ins", "상위분류가 없습니다!(소분류는 상위분류 필수!)");
                    }
                    $mid_cls = substr($uppr_clss, -2, 2);
                }

                $t_expns_cls_srno = $this->stay_m->get_clm_sr_val('EXPNS_CLS_' . $clss . $mid_cls);
                $expns_cls = $clss . $mid_cls . $t_expns_cls_srno;

                $i_data = array('clm_nm'      => 'EXPNS_CLS'
                               ,'clm_val'     => $expns_cls
                               ,'clm_val_nm'  => trim($this->input->post('clm_val_nm'  , 'TRUE'))
                               ,'othr_info'   => $uppr_clss
                               );

                $result = $this->stay_m->insert_tba003i00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();

                    info_log("expns_cls/ins/insert_tba003i00", "지출구분 입력 완료!");
                    info_log("expns_cls/ins/insert_tba003i00", "================================================================================");

                    redirect(base_url("/expns_cls/list"));
                }
                else
                {
                    info_log("expns_cls/ins/insert_tba003i00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("expns_cls/ins/insert_tba003i00", "[SQL ERR] 지출구분 입력 오류!");
                }
            }
            else
            {
                $this->expns_cls_reg_v('i');
            }
        }
        else
        {
            $this->expns_cls_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->form_validation->set_rules('clss'      , '분류구분', 'required');
            $this->form_validation->set_rules('clm_val_nm', '분류명'  , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $uppr_clss = $this->input->post('uppr_clss', 'TRUE');

                $clss = $this->input->post('clss', 'TRUE');

                //info_log("expns_cls/ins", "clss = [" . $clss . "]");
                //info_log("expns_cls/ins", "uppr_clss = [" . $uppr_clss . "]");

                if (strncmp($clss, "1", 1) == 0)
                {
                    $uppr_clss = NULL;
                }
                else if (strncmp($clss, "2", 1) == 0)
                {
                    if (strncmp($uppr_clss, "NA", 2) == 0 || strlen($uppr_clss) == 0)
                    {
                        alert_log("expns_cls/upd", "상위분류가 없습니다!(소분류는 상위분류 필수!)");
                    }
                }

                $u_data = array('clm_nm'      => 'EXPNS_CLS'
                               ,'clm_val'     => $this->uri->segment(3)
                               ,'clm_val_nm'  => trim($this->input->post('clm_val_nm'  , 'TRUE'))
                               ,'othr_info'   => $uppr_clss
                               );

                $result = $this->stay_m->update_tba003i00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("expns_cls/upd/update_tba003i00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("expns_cls/upd/update_tba003i00_1", "[SQL ERR] 지출구분 수정 건수 오류![" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("rsvt/cncl_upd/update_tba003i00_1", "지출구분 수정 완료!");
                        info_log("rsvt/cncl_upd/update_tba003i00_1", "================================================================================");

                        redirect(base_url("/expns_cls/list"));
                    }
                }
                else
                {
                    info_log("expns_cls/upd/update_tba003i00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("expns_cls/upd/update_tba003i00_1", "[SQL ERR] 지출구분 수정 오류!");
                }
            }
            else
            {
                $this->expns_cls_reg_v('u');
            }
        }
        else
        {
            $this->expns_cls_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            $this->db->trans_begin();

            $u_data = array('clm_nm'    => 'EXPNS_CLS'
                           ,'clm_val'   => $this->uri->segment(3)
                           );

            $result = $this->stay_m->update_tba003i00_2($u_data);

            if ($result)
            {
                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $this->db->affected_rows();
                if ($prcs_cnt != 1)
                {
                    info_log("expns_cls/upd/update_tba003i00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("expns_cls/upd/update_tba003i00_2", "[SQL ERR] 지출구분 삭제 건수 오류![" . $prcs_cnt . "]!");
                }
                else
                {
                    $this->db->trans_commit();

                    info_log("rsvt/cncl_upd/update_tba003i00_2", "지출구분 삭제 완료!");
                    info_log("rsvt/cncl_upd/update_tba003i00_2", "================================================================================");

                    redirect(base_url("/expns_cls/list"));
                }
            }
            else
            {
                info_log("expns_cls/upd/update_tba003i00_2", "last_query  = [" . $this->db->last_query() . "]");
                $this->db->trans_rollback();
                alert_log("expns_cls/upd/update_tba003i00_2", "[SQL ERR] 지출구분 삭제 오류!");
            }
        }
    }


    public function expns_cls_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        $data['clss_list']      = $this->stay_m->get_list('CLSS', 'Y');

        $t_uppr_clss_list1 = new stdClass();
        $t_uppr_clss_list1->clm_val    = 'NA';
        $t_uppr_clss_list1->clm_val_nm = 'N/A';
        $t_uppr_clss_list1->othr_info  = '';

        $t_uppr_clss_list2 = $this->stay_m->get_item_list('EXPNS_CLS', '1', 'data');
        if (count($t_uppr_clss_list2) > 1)
        {
            array_unshift($t_uppr_clss_list2, $t_uppr_clss_list1);
            $uppr_clss_list = $t_uppr_clss_list2;
        }
        else if (count($t_uppr_clss_list2) == 1)
        {
            $uppr_clss_list = array($t_uppr_clss_list1);
            array_push($uppr_clss_list, $t_uppr_clss_list2);
        }
        else
        {
            $uppr_clss_list = array($t_uppr_clss_list1);
        }

        //print_r($uppr_clss_list);
        $data['uppr_clss_list'] = $uppr_clss_list;
        //var_dump($data['uppr_clss_list']);
        //echo "cnt = [" . count($data['uppr_clss_list']) . "]";

        if (strncmp($prcs_cls, "i", 1) == 0)
        {
            $clss = $this->input->post('clss', 'TRUE');
            if (empty($clss))
            {
                $clss = '1';
            }
        }
        else if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $expns_cls = $this->uri->segment(3);
            $clss = substr($expns_cls, 0, 1);
            $data['view'] = $this->stay_m->get_item_list('EXPNS_CLS', $expns_cls, 'single');
        }

        $data['clss'] = $clss;

        //print_r($data['uppr_clss_list']);
        //var_dump($data['uppr_clss_list']);
        //var_export($data['uppr_clss_list']);
        $this->load->view('expns_cls_reg_v', $data);
    }


}