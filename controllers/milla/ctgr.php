<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Ctgr extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $this->load->model('milla_m');
        $this->load->library('form_validation');
        //$this->load->library('session');
        $this->load->helper('url');
        //$this->load->helper('cookie');
        //$this->load->helper('My_alert_log');

        $usr_no = get_cookie('usr_no');
        $idntfr = get_cookie('idntfr');

        if (!isset($_SESSION['usr_no']) && strlen($usr_no) > 0 && strlen($idntfr) > 0)
        {
            info_log("milla/ctgr", "autologin start!");
            milla_auto_login();
        }

        milla_login_chk();
    }

    public function index()
    {
        //$this->smmry();
        redirect('milla/ctgr/list','refresh');
    }

    /*
        사이트 헤더, 푸터 추가
    */
    public function _remap($method, $params = array())
    {
        // Header
        $this->load->view('milla/header_v');

        if (method_exists($this, $method))
        {
            //$this->{"{$method}"}();
            call_user_func_array(array($this, $method), $params);
        }

        // footer
        $this->load->view('milla/footer_v');
    }


    public function list()
    {
        //$this->output->enable_profiler(TRUE);
        $this->load->library('pagination');

        info_log("milla/ctgr/list", "================================================================================");
        info_log("milla/ctgr/list", "분류 리스트 조회 시작!");

        // 검색 변수 초기화
        $page_url = '';

        $uri_segment = 7;          // 페이지 번호가 위치한 세그먼트

        $cmpny_cls  = $this->uri->segment(4);
        $lctgr_cls  = $this->uri->segment(5);

        info_log("milla/ctgr/list", "cmpny_cls    = [" . $cmpny_cls . "]");
        info_log("milla/ctgr/list", "lctgr_cls    = [" . $lctgr_cls . "]");

        // Pagination 용 주소
        $page_url = '/' . $cmpny_cls . '/' . $lctgr_cls;

        // 페이지네이션 설정
        $config['base_url']         = '/milla/ctgr/list/' . $page_url . '/page/';

        $ctgr_nm = 'CTGR_CLS_' . $cmpny_cls;

        if (empty($cmpny_cls)) {
            $config['total_rows']       = 0;
            $cmpny_cls = '01';
        }
        else {
            $config['total_rows']       = $this->milla_m->get_ctgr_list($cmpny_cls, $ctgr_nm, $lctgr_cls, 'rowcnt');         // 표시할 게시물 총 수
        }
        $config['per_page']         = 10;             // 한 페이지에 표시할 게시물 수
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
            $data['ctgr_cls_list'] = $this->milla_m->get_ctgr_list($cmpny_cls, $ctgr_nm, $lctgr_cls, 'data', $start, $limit);
        }

        $data['cmpny_cls']  = $cmpny_cls;
        $data['lctgr_cls']  = $lctgr_cls;

        $data['cmpny_select_list'] = $this->milla_m->get_meta_list('CMPNY_CLS', '', 'data');

        $lctgr_nm = 'LCTGR_CLS_' . $cmpny_cls;
        $data['lctgr_select_list'] = $this->milla_m->get_meta_list($lctgr_nm, '', 'data');


        info_log("milla/ctgr/list", "분류 리스트 조회 완료!");
        info_log("milla/ctgr/list", "================================================================================");

        $this->load->view('milla/ctgr_list_v', $data);

    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {

            info_log("milla/ctgr/ins/", "================================================================================");
            info_log("milla/ctgr/ins/", "분류 입력 시작!");

            $this->form_validation->set_rules('mt_kor_nm'  , '분류명'  , 'required');
            $this->form_validation->set_rules('addtn_info' , '분류코드'    , 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {

                // 분류코드는 4자리
                if (strlen($this->input->post('addtn_info', 'TRUE')) != 4)
                {
                    alert_log("milla/ctgr/ins", "[ERR] 분류코드 길이 오류! [" . $this->input->post('addtn_info', 'TRUE') . "]");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = trim($this->input->post('memo', 'TRUE'));
                }
                else
                {
                    $memo = NULL;
                }

                //==============================================================
                // 동일한 분류코드 존재 여부 체크 BEGIN
                $addtn_info = $this->input->post('addtn_info', 'TRUE');
                $exist_chk = $this->milla_m->get_ctgr_exist_chk($addtn_info);

                info_log("milla/ctgr/ins", "exist_chk = [" . $exist_chk->cnt . "]");

                if ($exist_chk->cnt > 0)
                {
                    //echo "<script>alert('회원 가입 오류!(동일 아이디 존재)');</script>";
                    alert_log("milla/ctgr/ins", "이미 존재하는 분류코드입니다. 입력 불가![" . $addtn_info . "]");
                    exit;
                }
                // 동일한 분류코드 존재 여부 체크 END
                //==============================================================

                $this->db->trans_begin();

                $mt_nm = 'CTGR_CLS_' . $this->input->post('cmpny_cls', 'TRUE');

                $mt_val = $this->milla_m->get_clm_sr_val($mt_nm);

                $i_data = array('mt_nm'      => $mt_nm
                               ,'mt_val'     => $mt_val
                               ,'mt_kor_nm'  => trim($this->input->post('mt_kor_nm', 'TRUE'))
                               ,'addtn_info' => $this->input->post('addtn_info', 'TRUE')
                               ,'othr_info'  => $this->input->post('lctgr_cls', 'TRUE')
                               ,'order_info' => NULL
                               ,'memo'       => $memo
                               );

                $result = $this->milla_m->insert_milla003i00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();

                    info_log("milla/ctgr/ins/", "분류 입력 완료!");
                    info_log("milla/ctgr/ins/", "================================================================================");

                    $ins_cls = $this->uri->segment(4);
                    //info_log("milla/ctgr/ins", "ins_cls = [" . $ins_cls . "]");

                    if (strncmp($ins_cls, "r", 1) == 0)
                    {
                        redirect(base_url("milla/ctgr/ins"));
                    }
                    else
                    {
                        redirect(base_url("milla/ctgr/list"));
                    }
                }
                else
                {
                    info_log("milla/ctgr/ins/insert_milla003i00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("milla/ctgr/ins/insert_milla003i00", "[SQL ERR] 분류 입력 오류!");
                }
            }
            else
            {
                $this->ctgr_reg_v('i');
            }
        }
        else
        {
            $this->ctgr_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {

            info_log("milla/ctgr/upd/", "================================================================================");
            info_log("milla/ctgr/upd/", "분류 수정 시작!");

            $this->form_validation->set_rules('mt_kor_nm'  , '분류명'  , 'required');
            $this->form_validation->set_rules('addtn_info' , '분류코드'    , 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                // 분류코드는 4자리
                if (strlen($this->input->post('addtn_info', 'TRUE')) != 4)
                {
                    alert_log("milla/ctgr/upd", "[ERR] 분류코드 길이 오류! [" . $this->input->post('addtn_info', 'TRUE') . "]");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = trim($this->input->post('memo', 'TRUE'));
                }
                else
                {
                    $memo = NULL;
                }

                $ori_mt_nm      = $this->input->post('ori_mt_nm' , 'TRUE');
                $ori_mt_val     = $this->input->post('ori_mt_val', 'TRUE');
                $ori_addtn_info = $this->input->post('ori_addtn_info', 'TRUE');
                $addtn_info     = $this->input->post('addtn_info', 'TRUE');
                
                //==============================================================
                // 동일한 분류코드 존재 여부 체크 BEGIN
                $addtn_info = $this->input->post('addtn_info', 'TRUE');
                $exist_chk = $this->milla_m->get_ctgr_exist_chk($addtn_info);

                info_log("milla/ctgr/upd", "exist_chk = [" . $exist_chk->cnt . "]");

                if ($exist_chk->cnt > 0)
                {
                    //echo "<script>alert('회원 가입 오류!(동일 아이디 존재)');</script>";
                    alert_log("milla/ctgr/upd", "이미 존재하는 분류코드입니다. 수정/삭제 불가![" . $addtn_info . "]");
                    exit;
                }
                // 동일한 분류코드 존재 여부 체크 END
                //==============================================================
                
                
                //=================================================
                //상품코드에 사용된 분류코드는 수정/삭제 불가 BEGIN
                //분류코드가 변경된 경우에만 체크!
                if (strcmp($ori_addtn_info, $addtn_info) != 0)
                {
                    $use_chk = $this->milla_m->get_ctgr_use_chk($ori_addtn_info);

                    info_log("milla/ctgr/upd", "use_chk = [" . $use_chk->cnt . "]");

                    if ($use_chk->cnt > 0)
                    {
                        //echo "<script>alert('회원 가입 오류!(동일 아이디 존재)');</script>";
                        alert_log("milla/ctgr/upd", "상품코드에 사용된 분류코드입니다. 수정/삭제 불가![" . $ori_addtn_info . "]");
                        exit;
                    }
                }
                //상품코드에 사용된 분류코드는 수정/삭제 불가 END
                //=================================================


                $this->db->trans_begin();

                $u_data = array('mt_nm'      => $ori_mt_nm
                               ,'mt_val'     => $ori_mt_val
                               ,'mt_kor_nm'  => trim($this->input->post('mt_kor_nm', 'TRUE'))
                               ,'addtn_info' => $this->input->post('addtn_info', 'TRUE')
                               ,'othr_info'  => $this->input->post('lctgr_cls', 'TRUE')
                               ,'order_info' => NULL
                               ,'memo'       => $memo
                               );

                $result = $this->milla_m->update_milla003i00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("milla/ctgr/upd/update_milla003i00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("milla/ctgr/upd/update_milla003i00_1", "[SQL ERR] 분류 수정 처리 오류![" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("milla/ctgr/upd/", "분류 수정 완료!");
                        info_log("milla/ctgr/upd/", "================================================================================");

                        redirect(base_url("milla/ctgr/list"));
                    }
                }
                else
                {
                    info_log("milla/ctgr/upd/update_milla003i00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("milla/ctgr/upd/update_milla003i00_1", "[SQL ERR] 분류 수정 오류!");
                }
            }
            else
            {
                $this->ctgr_reg_v('u');
            }
        }
        else
        {
            $this->ctgr_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("milla/ctgr/del/", "================================================================================");
            info_log("milla/ctgr/del/", "분류 삭제 시작!");

            $this->form_validation->set_rules('mt_kor_nm'  , '분류명'  , 'required');
            $this->form_validation->set_rules('addtn_info' , '분류코드'    , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $ori_mt_nm      = $this->input->post('ori_mt_nm' , 'TRUE');
                $ori_mt_val     = $this->input->post('ori_mt_val', 'TRUE');
                $ori_addtn_info = $this->input->post('ori_addtn_info', 'TRUE');
                $addtn_info     = $this->input->post('addtn_info', 'TRUE');
                //=================================================
                //상품코드에 사용된 분류코드는 수정/삭제 불가 BEGIN
                $use_chk = $this->milla_m->get_ctgr_use_chk($ori_addtn_info);

                info_log("milla/ctgr/del", "use_chk = [" . $use_chk->cnt . "]");

                if ($use_chk->cnt > 0)
                {
                    //echo "<script>alert('회원 가입 오류!(동일 아이디 존재)');</script>";
                    alert_log("milla/ctgr/del", "상품코드에 사용된 분류코드입니다. 수정/삭제 불가![" . $ori_addtn_info . "]");
                    exit;
                }
                //상품코드에 사용된 분류코드는 수정/삭제 불가 END
                //=================================================

                $this->db->trans_begin();

                $u_data = array('ori_mt_nm'  => $ori_mt_nm
                               ,'ori_mt_val' => $ori_mt_val
                               );

                $result = $this->milla_m->update_milla003i00_2($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("milla/ctgr/del/update_milla003i00_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("milla/ctgr/del/update_milla003i00_2", "[SQL ERR] 분류 삭제 처리 오류![" . $prcs_cnt . "]!");
                        info_log("milla/ctgr/del/update_milla003i00_2", "================================================================================");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("milla/ctgr/del/", "분류 삭제 완료!");
                        info_log("milla/ctgr/del/", "================================================================================");

                        redirect(base_url("milla/ctgr/list"));
                    }
                }
                else
                {
                    info_log("milla/ctgr/del/update_milla003i00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("milla/ctgr/del/update_milla003i00_2", "[SQL ERR] 분류 삭제 오류!");
                    info_log("milla/ctgr/del/update_milla003i00_2", "================================================================================");
                }
            }
            else
            {
                $this->ctgr_reg_v('u');
            }
        }
        else
        {
            $this->ctgr_reg_v('u');
        }
    }


    public function ctgr_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        $data['cmpny_select_list'] = $this->milla_m->get_meta_list('CMPNY_CLS', '', 'data');
        $data['lctgr_select_list'] = $this->milla_m->get_meta_list('LCTGR_CLS_01', '', 'data');
        //$data['lctgr02_select_list'] = $this->milla_m->get_meta_list('LCTGR_CLS_02', '', 'data');

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            //info_log("milla/ctgr/ctgr_reg_v", "this->uri->segment(4) = [" . $this->uri->segment(4) . "]");
            $cmpny_cls = $this->uri->segment(4);
            $lctgr_nm = 'LCTGR_CLS_' . $cmpny_cls;
            $ctgr_nm = 'CTGR_CLS_' . $cmpny_cls;

            $data['lctgr_select_list'] = $this->milla_m->get_meta_list($lctgr_nm, '', 'data');
            $data['view'] = $this->milla_m->get_meta_info($cmpny_cls, $ctgr_nm, $this->uri->segment(5));
        }

        $this->load->view('milla/ctgr_reg_v', $data);
    }


}
