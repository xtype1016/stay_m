<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    예약관리 등록 컨트롤러
*/
class Fix_expns extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        $this->load->model('stay_m');
        $this->load->library('form_validation');
        //$this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('cookie');
        //$this->load->helper('My_alert_log');
        $this->load->library('user_agent');

        $usr_no = get_cookie('usr_no');
        $idntfr = get_cookie('idntfr');

        if (!isset($_SESSION['usr_no']) && strlen($usr_no) > 0 && strlen($idntfr) > 0)
        {
            info_log("expns", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->expns_smmry();
        redirect('fix_expns/list','refresh');
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

        info_log("fix_expns/list/", "================================================================================");
        info_log("fix_expns/list/", "고정지출 조회 시작!");

        // 검색 변수 초기화
        $uri_segment = 5;          // 페이지 번호가 위치한 세그먼트

        $view_cls  = $this->uri->segment(3);

        if (strlen($view_cls) == 0)
        {
            $view_cls = '1';
        }

        $list_url = uri_string();
        info_log("expns/dtl/", "list_url        = [" . $list_url . "]");
        $this->session->set_userdata('list_url', $list_url);
        info_log("expns/dtl/", "_SESSION['list_url'] = [" . $_SESSION['list_url'] . "]");

        //info_log("fix_expns/list/", "view_cls = [" . $view_cls . "]");

        // Pagination 용 주소
        $page_url = '/' . $view_cls;

        // 페이지네이션 설정
        $config['base_url']         = '/fix_expns/list/' . $page_url . '/page/';

        $config['total_rows']       = $this->stay_m->get_fix_expns_list($view_cls, 'rowcnt');         // 표시할 게시물 총 수
        $config['per_page']         = 50;            // 한 페이지에 표시할 게시물 수
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
        $page = $this->uri->segment($uri_segment, 1);
        $start = ($page - 1) * $config['per_page'];
        $limit = $config['per_page'];

        if ($config['total_rows'] > 0)
        {
            $data['fix_expns_list'] = $this->stay_m->get_fix_expns_list($view_cls, 'data', $start, $limit);
        }

        $data['view_cls'] = $view_cls;

        info_log("fix_expns/list/", "고정지출 조회 완료!");
        info_log("fix_expns/list/", "================================================================================");

        $this->load->view('fix_expns_list_v', $data);

    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("fix_expns/ins/", "================================================================================");
            info_log("fix_expns/ins/", "고정지출 입력 시작!");

            $this->form_validation->set_rules('expns_nm'  , '지출명'  , 'required');
            $this->form_validation->set_rules('expns_day' , '지출일', 'required');
            //$this->form_validation->set_rules('memo'      , '메모'  , 'required');
            //$this->form_validation->set_rules('whr_to_buy', '구입처', 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                //if ($amt <= 0)
                //{
                //    alert_log("rsvt/cncl", "고정지출금액이 0보다 작거나 같습니다!");
                //}

                $io_tr_cls = $this->input->post('io_tr_cls' , 'TRUE');
                $expns_chnl_cls = $this->input->post('expns_chnl_cls' , 'TRUE');
                $expns_cls = $this->input->post('expns_cls'      , 'TRUE');

                if (strlen($this->input->post('whr_to_buy', 'TRUE')) > 0)
                {
                    $whr_to_buy = trim($this->input->post('whr_to_buy', 'TRUE'));
                }
                else
                {
                    $whr_to_buy = NULL;
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = trim($this->input->post('memo', 'TRUE'));
                }
                else
                {
                    $memo = NULL;
                }

                if (strlen($this->input->post('sttlmt_yn', 'TRUE')) > 0)
                {
                    $sttlmt_yn = $this->input->post('sttlmt_yn', 'TRUE');
                }
                else
                {
                    $sttlmt_yn = 'N';
                }

                //결제의 경우 동월에 동일한 분류는 1건만 존재하는지 여부 체크
                if (strcmp($sttlmt_yn, "Y") == 0)
                {
                    $dup_chk = $this->stay_m->get_fix_expns_withdraw_dup_chk($io_tr_cls);

                    info_log("fix_expns/ins/", "dup_chk cnt = [" . $dup_chk->cnt . "]");

                    if ($dup_chk->cnt > 0)
                    {
                        alert_log("fix_expns/ins/", "동일 결제거래 존재! 추가 입력 불가! [" . $this->input->post('expns_chnl_cls', 'TRUE') . "]");
                    }
                }

                //지출 채널, 출금여부 정합성 체크 Begin
                if (strcmp($expns_chnl_cls, "01") == 0)
                {
                    $sttlmt_yn = 'N';
                }

                if (strcmp($expns_chnl_cls, "01") != 0 && strcmp($sttlmt_yn, "Y") == 0)
                {
                    $expns_cls  = NULL;
                    $whr_to_buy = NULL;
                }
                //지출 채널, 출금여부 정합성 체크 End


                if (strlen($this->input->post('trnsfr_day', 'TRUE')) > 0)
                {
                    $trnsfr_day = trim($this->input->post('trnsfr_day', 'TRUE'));
                }
                else
                {
                    $trnsfr_day = NULL;
                }

                if (strlen($this->input->post('bank', 'TRUE')) > 0)
                {
                    $bank = trim($this->input->post('bank', 'TRUE'));
                }
                else
                {
                    $bank = NULL;
                }

                if (strlen($this->input->post('ac_no', 'TRUE')) > 0)
                {
                    $ac_no = trim($this->input->post('ac_no', 'TRUE'));
                }
                else
                {
                    $ac_no = NULL;
                }

                if (strlen($this->input->post('rel_ac_no', 'TRUE')) > 0)
                {
                    $rel_ac_no = trim($this->input->post('rel_ac_no', 'TRUE'));
                }
                else
                {
                    $rel_ac_no = NULL;
                }

                $this->db->trans_begin();

                $fix_expns_srno = $this->stay_m->get_clm_sr_val('FIX_EXPNS_SRNO');

                //2022.02.23. group_no 삭제, trnsfr_day, rel_ac_no 추가
                //$i_data = array('fix_expns_srno' => $fix_expns_srno
                //               ,'expns_nm'       => $this->input->post('expns_nm'       , 'TRUE')
                //               ,'expns_group_no' => $this->input->post('expns_group_no' , 'TRUE')
                //               ,'expns_day'      => $this->input->post('expns_day'      , 'TRUE')
                //               ,'expns_chnl_cls' => $this->input->post('expns_chnl_cls' , 'TRUE')
                //               ,'sttlmt_yn'      => $sttlmt_yn
                //               ,'expns_cls'      => $expns_cls
                //               ,'whr_to_buy'     => $whr_to_buy
                //               ,'amt'            => $amt
                //               ,'memo'           => $memo
                //               ,'bank'           => $this->input->post('bank' , 'TRUE')
                //               ,'ac_no'          => $this->input->post('ac_no' , 'TRUE')
                //               );

                $i_data = array('fix_expns_srno' => $fix_expns_srno
                               ,'expns_nm'       => $this->input->post('expns_nm'       , 'TRUE')
                               ,'trnsfr_day'     => $trnsfr_day
                               ,'expns_day'      => $this->input->post('expns_day'      , 'TRUE')
                               ,'expns_chnl_cls' => $this->input->post('expns_chnl_cls' , 'TRUE')
                               ,'sttlmt_yn'      => $sttlmt_yn
                               ,'expns_cls'      => $expns_cls
                               ,'whr_to_buy'     => $whr_to_buy
                               ,'amt'            => $amt
                               ,'memo'           => $memo
                               ,'bank'           => $bank
                               ,'ac_no'          => $ac_no
                               ,'rel_ac_no'      => $rel_ac_no
                               );

                $result = $this->stay_m->insert_tbb005l00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();

                    info_log("fix_expns/ins/", "고정지출 입력 완료!");
                    info_log("fix_expns/ins/", "================================================================================");

                    $ins_cls = $this->uri->segment(3);

                    if (strncmp($ins_cls, "r", 1) == 0)
                    {
                        redirect(base_url("fix_expns/ins"));
                    }
                    else
                    {
                        $temp_segment = explode('/', $_SESSION['list_url']);

                        //info_log("expns/upd/", "temp_segment[2]  = [" . $temp_segment[2] . "]");
                        //info_log("expns/upd/", "temp_segment[4]  = [" . $temp_segment[4] . "]");

                        $redirect_url = "fix_expns/list/" . $temp_segment[2];
                        info_log("fix_expns/upd/", "redirect_url  = [" . $redirect_url . "]");

                        // list_url 세션데이터 초기화
                        unset($_SESSION['list_url']);

                        redirect(base_url($redirect_url));
                    }
                }
                else
                {
                    info_log("fix_expns/ins/insert_tbb005l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("fix_expns/ins/insert_tbb005l00", "[SQL ERR] 고정지출 입력 오류!");
                }
            }
            else
            {
                $this->fix_expns_reg_v('i');
            }
        }
        else
        {
            $this->fix_expns_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("fix_expns/upd/", "================================================================================");
            info_log("fix_expns/upd/", "고정지출 수정 시작!");

            $this->form_validation->set_rules('expns_nm'  , '지출명'  , 'required');
            $this->form_validation->set_rules('expns_day' , '지출일', 'required');

            //$this->form_validation->set_rules('memo'      , '메모'  , 'required');
            //$this->form_validation->set_rules('whr_to_buy', '구입처', 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                //if ($amt <= 0)
                //{
                //    alert_log("rsvt/cncl", "고정지출금액이 0보다 작거나 같습니다!");
                //}

                $expns_chnl_cls = $this->input->post('expns_chnl_cls' , 'TRUE');
                $expns_cls = $this->input->post('expns_cls'      , 'TRUE');

                if (strcmp($expns_cls, 'NULL') == 0)
                {
                    $expns_cls = NULL;
                }

                if (strlen($this->input->post('whr_to_buy', 'TRUE')) > 0)
                {
                    $whr_to_buy = $this->input->post('whr_to_buy', 'TRUE');
                }
                else
                {
                    $whr_to_buy = NULL;
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                $sttlmt_yn = $this->input->post('sttlmt_yn', 'TRUE');
                info_log("fix_expns/upd/", "sttlmt_yn = [" . $sttlmt_yn . "]");

                if (strlen($sttlmt_yn) == 0)
                {
                    $sttlmt_yn = 'N';
                }

                info_log("fix_expns/upd/", "final sttlmt_yn = [" . $sttlmt_yn . "]");

                //2022.01.14 이미 존재하는 고정지출 건의 수정처리이므로 당연히 기존 데이터가 존재함
                //처리 필요가 없어 주석처리함. 왜 만들었지???
                //출금의 경우 동월에 동일한 분류는 1건만 존재하는지 여부 체크
                //if (strcmp($sttlmt_yn, "Y") == 0)
                //{
                //    $dup_chk = $this->stay_m->get_fix_expns_withdraw_dup_chk($this->input->post('io_tr_cls' , 'TRUE'));
                //
                //    info_log("fix_expns/upd/", "dup_chk cnt = [" . $dup_chk->cnt . "]");
                //
                //    if ($dup_chk->cnt != 1)
                //    {
                //        alert_log("fix_expns/upd/", "동일 출금거래 존재! 추가 수정 불가! [" . $this->input->post('io_tr_cls', 'TRUE') . "]");
                //    }
                //}

                // 2022.01.14 왜 있는지 모르겠다....
                //지출 채널, 출금여부 정합성 체크 Begin
                //if (strcmp($expns_chnl_cls, "01") == 0)
                //{
                //    $sttlmt_yn = 'N';
                //}

                if (strcmp($expns_chnl_cls, "01") != 0 && strcmp($sttlmt_yn, "Y") == 0)
                {
                    $expns_cls  = NULL;
                    $whr_to_buy = NULL;
                }
                //지출 채널, 출금여부 정합성 체크 End

                if (strlen($this->input->post('trnsfr_day', 'TRUE')) > 0)
                {
                    $trnsfr_day = trim($this->input->post('trnsfr_day', 'TRUE'));
                }
                else
                {
                    $trnsfr_day = NULL;
                }

                $this->db->trans_begin();

                $fix_expns_srno = $this->uri->segment(3);

                //2022.02.23. gruop_no 삭제, trnsfr_day, rel_ac_no 추가
                //$u_data = array('fix_expns_srno' => $fix_expns_srno
                //               ,'expns_nm'       => $this->input->post('expns_nm'       , 'TRUE')
                //               ,'expns_group_no' => $this->input->post('expns_group_no' , 'TRUE')
                //               ,'expns_day'      => $this->input->post('expns_day'      , 'TRUE')
                //               ,'expns_chnl_cls' => $expns_chnl_cls
                //               ,'sttlmt_yn'    => $sttlmt_yn
                //               ,'expns_cls'      => $expns_cls
                //               ,'whr_to_buy'     => $whr_to_buy
                //               ,'amt'            => $amt
                //               ,'memo'           => $memo
                //               ,'bank'           => $this->input->post('bank' , 'TRUE')
                //               ,'ac_no'          => $this->input->post('ac_no' , 'TRUE')
                //               );

                $u_data = array('fix_expns_srno' => $fix_expns_srno
                               ,'expns_nm'       => $this->input->post('expns_nm'       , 'TRUE')
                               ,'trnsfr_day'     => $trnsfr_day
                               ,'expns_day'      => $this->input->post('expns_day'      , 'TRUE')
                               ,'expns_chnl_cls' => $expns_chnl_cls
                               ,'sttlmt_yn'    => $sttlmt_yn
                               ,'expns_cls'      => $expns_cls
                               ,'whr_to_buy'     => $whr_to_buy
                               ,'amt'            => $amt
                               ,'memo'           => $memo
                               ,'bank'           => $this->input->post('bank' , 'TRUE')
                               ,'ac_no'          => $this->input->post('ac_no' , 'TRUE')
                               ,'rel_ac_no'      => $this->input->post('rel_ac_no' , 'TRUE')
                               );

                $result = $this->stay_m->update_tbb005l00_1($u_data);

                if (!$result)
                {
                    info_log("fix_expns/upd/update_tbb005l00_1/", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("fix_expns/upd/update_tbb005l00_1/", "[SQL ERR] 고정지출 수정 오류!");
                }

                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $this->db->affected_rows();
                if ($prcs_cnt != 1)
                {
                    info_log("fix_expns/upd/update_tbb005l00_1/", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("fix_expns/upd/update_tbb005l00_1/", "[SQL ERR] 고정지출 수정 처리 오류![" . $prcs_cnt . "]!");
                }
                else
                {
                    $this->db->trans_commit();

                    $temp_segment = explode('/', $_SESSION['list_url']);

                    //info_log("expns/upd/", "temp_segment[2]  = [" . $temp_segment[2] . "]");
                    //info_log("expns/upd/", "temp_segment[4]  = [" . $temp_segment[4] . "]");

                    $redirect_url = "fix_expns/list/" . $temp_segment[2];
                    info_log("fix_expns/upd/", "redirect_url  = [" . $redirect_url . "]");

                    // list_url 세션데이터 초기화
                    unset($_SESSION['list_url']);

                    info_log("fix_expns/upd/", "고정지출 수정 완료!");
                    info_log("fix_expns/upd/", "================================================================================");

                    redirect(base_url($redirect_url));
                }
            }
            else
            {
                $this->fix_expns_reg_v('u');
            }
        }
        else
        {
            $this->fix_expns_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("fix_expns/del", "================================================================================");
            info_log("fix_expns/del", "고정지출 삭제 시작!");

            $this->db->trans_begin();

            $fix_expns_srno = $this->uri->segment(3);
            info_log("fix_expns/del", "fix_expns_srno = [" . $fix_expns_srno . "]");

            $u_data = array('fix_expns_srno'     => $fix_expns_srno
                           );

            $result = $this->stay_m->update_tbb005l00_2($u_data);

            if ($result)
            {
                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                $prcs_cnt = $this->db->affected_rows();
                if ($prcs_cnt != 1)
                {
                    info_log("fix_expns/del/update_tbb005l00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("fix_expns/del/update_tbb005l00_2", "[SQL ERR] 고정지출 삭제 건수 오류![" . $prcs_cnt . "]!");
                }
                else
                {
                    $this->db->trans_commit();

                    info_log("fix_expns/del", "고정지출 삭제 완료!");
                    info_log("fix_expns/del", "================================================================================");

                    //redirect(base_url($t_url));
                    redirect(base_url("fix_expns/list"));
                }
            }
            else
            {
                info_log("fix_expns/del/update_tbb005l00_2", "last_query  = [" . $this->db->last_query() . "]");
                $this->db->trans_rollback();
                alert_log("fix_expns/del/update_tbb005l00_2", "[SQL ERR] 고정지출 삭제 오류!");
            }
        }
    }


    public function fix_expns_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;
        $data['fix_expns_srno'] = $this->uri->segment(3);

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        //$data['expns_cls_list']       = $this->stay_m->get_list('EXPNS_CLS');
        $expns_cls_result = $this->stay_m->get_list('EXPNS_CLS');

        $i = -1;
        
        $expns_cls['선택없음']['NULL'] = '선택없음';

        foreach($expns_cls_result as $e_cls_list)
        {
            if (!isset($e_cls_list->othr_info))
            {
                $i = $i + 1;
                $expns_large_cls[$i] = $e_cls_list->clm_val_nm;
            }
            else
            {
                $expns_cls[$expns_large_cls[$i]][$e_cls_list->clm_val] = $e_cls_list->clm_val_nm;
            }
        }
        //print_r($expns_cls);
        $data['expns_cls_list'] = $expns_cls;


        $data['io_tr_cls_list'] = $this->stay_m->get_list('IO_TR_CLS', 'Y');

        $expns_chnl_cls_list  = $this->stay_m->get_list('EXPNS_CHNL_CLS', 'Y');
        $data['expns_chnl_cls_list'] = $expns_chnl_cls_list;

        $data['cost_cls_list']  = $this->stay_m->get_list('COST_CLS', 'Y');

        $data['bank_list']  = $this->stay_m->get_list('BANK', 'Y');

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            //info_log("expns/fix_expns_reg_v", "this->uri->segment(4) = [" . $this->uri->segment(4) . "]");
            $data['view'] = $this->stay_m->get_fix_expns_info($this->uri->segment(3));
        }

        $this->load->view('fix_expns_reg_v', $data);
    }


}
