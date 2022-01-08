<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
    예약관리 등록 컨트롤러
*/
class Etc_incm extends CI_Controller
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
            info_log("etc_incm", "autologin start!");
            auto_login();
        }

        login_chk();
    }

    public function index()
    {
        //$this->list();
        redirect('etc_incm/list','refresh');
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

        info_log("etc_incm/list/", "================================================================================");
        info_log("etc_incm/list/", "기타 수익 리스트 조회 시작!");

        // 검색 변수 초기화
        $stnd_yymm = $page_url = '';

        $stnd_yymm = str_replace('-', '', $this->uri->segment(3));

        $uri_segment = 5;          // 페이지 번호가 위치한 세그먼트

        if (empty($stnd_yymm))
        {
            $stnd_yymm = date("Ym");
        }

        // Pagination 용 주소
        $page_url = '/' . $stnd_yymm . '/';

        // 페이지네이션 설정
        $config['base_url']         = '/etc_incm/list/' . $page_url . '/page/';
        $config['total_rows']       = $this->stay_m->get_etc_incm_list($stnd_yymm, 'rowcnt');         // 표시할 게시물 총 수
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
            $data['etc_incm_list'] = $this->stay_m->get_etc_incm_list($stnd_yymm, 'data', $start, $limit);
        }

        $data['stnd_yymm'] = substr($stnd_yymm, 0, 4) . "-" . substr($stnd_yymm, 4, 2);

        $data['tr_cls'] = $this->stay_m->get_list('TR_CLS');

        info_log("etc_incm/list/", "기타 수익 리스트 조회 완료!");
        info_log("etc_incm/list/", "================================================================================");

        $this->load->view('etc_incm_list_v', $data);

    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("etc_incm/ins/", "================================================================================");
            info_log("etc_incm/ins/", "기타수익 입력 시작!");

            $this->form_validation->set_rules('tr_dt'     , '거래일'  , 'required');
            $this->form_validation->set_rules('tr_cls'    , '거래구분', 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                if ($amt <= 0)
                {
                    alert_log("rsvt/cncl", "거래금액이 0보다 작거나 같습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

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

                $this->db->trans_begin();

                $tr_srno = $this->stay_m->get_clm_sr_val('TR_SRNO');

                $i_data = array('tr_srno'          => $tr_srno
                               ,'rsv_srno'         => $this->input->post('rsv_srno', 'TRUE')
                               ,'tr_dt'            => str_replace('-', '', $this->input->post('tr_dt', 'TRUE'))
                               ,'tr_cls'           => $this->input->post('tr_cls'     , 'TRUE')
                               ,'tr_chnl_cls'      => $this->input->post('tr_chnl_cls', 'TRUE')
                               ,'amt'              => $amt
                               ,'memo'             => trim($memo)
                               ,'othr_withdraw_yn' => $othr_withdraw_yn
                               ,'expns_srno'       => $expns_srno
                               );

                $result = $this->stay_m->insert_tba006l00($i_data);

                if (!$result)
                {
                    info_log("etc_incm/ins/insert_tba006l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("etc_incm/ins/insert_tba006l00", "[SQL ERR] 기타수익 입력 오류!");
                }

                //2021.09.09. 취소 처리시 타계좌 출금의 경우 지출 입력 처리 Begin
                if (strcmp($othr_withdraw_yn, 'Y') == 0)
                {
                    $i_data = array('expns_srno'     => $expns_srno
                                   ,'expns_dt'       => str_replace('-', '', $this->input->post('tr_dt', 'TRUE'))
                                   ,'expns_chnl_cls' => '01'
                                   ,'expns_cls'      => '20213'
                                   ,'memo'           => '기타 환불(생활비 계좌 출금)'
                                   ,'whr_to_buy'     => $this->input->post('gst_nm', 'TRUE')
                                   ,'ssamzi_yn'      => 'N'
                                   ,'cost_cls'       => '1'
                                   ,'amt'            => $amt
                                   );

                    $result = $this->stay_m->insert_tbb001l00($i_data);

                    if (!$result)
                    {
                        info_log("etc_incm/ins/insert_tbb001l00", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("etc_incm/ins/insert_tbb001l00", "[SQL ERR][기타거래] 지출 입력 오류!");
                    }

                    // 입출금거래 현금지출합계금액 ins/upd
                    $stnd_yymm = substr(str_replace('-', '', $this->input->post('tr_dt', 'TRUE')), 0, 6);
                    cash_bal_ins_upd($stnd_yymm);

                }
                //2021.09.09. 취소 처리시 타계좌 출금의 경우 지출 입력 처리 End


                $this->db->trans_commit();

                info_log("etc_incm/ins/", "기타수익 입력 완료!");
                info_log("etc_incm/ins/", "================================================================================");

                redirect(base_url("/etc_incm/list"));
            }
            else
            {
                $this->etc_incm_reg_v('i');
            }
        }
        else
        {
            $this->etc_incm_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("etc_incm/upd/", "================================================================================");
            info_log("etc_incm/upd/", "기타수익 수정 시작!");

            $this->form_validation->set_rules('tr_dt'     , '거래일'  , 'required');
            $this->form_validation->set_rules('tr_cls'    , '거래구분', 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                (int)$amt = str_replace(',', '', $this->input->post('amt', 'TRUE'));

                // 금액 입력 확인
                if ($amt <= 0)
                {
                    alert_log("rsvt/cncl", "거래금액이 0보다 작거나 같습니다!");
                }

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                //2021.09.10
                //expns_srno 존재 여부 확인 BEGIN
                $tr_srno = $this->uri->segment(4);
                $expns_info = $this->stay_m->get_etc_incm_info($tr_srno);
                info_log("etc_incm/upd/", "expns_srno  = [" . $expns_info->expns_srno . "]");
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

                $this->db->trans_begin();

                $u_data = array('tr_srno'          => $tr_srno
                               ,'rsv_srno'         => $this->input->post('rsv_srno', 'TRUE')
                               ,'tr_dt'            => str_replace('-', '', $this->input->post('tr_dt', 'TRUE'))
                               ,'tr_cls'           => $this->input->post('tr_cls'     , 'TRUE')
                               ,'tr_chnl_cls'      => $this->input->post('tr_chnl_cls', 'TRUE')
                               ,'amt'              => $amt
                               ,'memo'             => trim($memo)
                               ,'othr_withdraw_yn' => $othr_withdraw_yn
                               ,'expns_srno'       => $expns_srno
                               );

                $result = $this->stay_m->update_tba006l00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("etc_incm/upd/update_tba006l00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("etc_incm/upd/update_tba006l00_1", "[SQL ERR] 기타수익 수정 건수 오류[" . $prcs_cnt . "]!");
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
                                               ,'expns_dt'       => str_replace('-', '', $this->input->post('tr_dt', 'TRUE'))
                                               ,'expns_chnl_cls' => '01'
                                               ,'expns_cls'      => '20213'
                                               ,'memo'           => '기타 환불(생활비 계좌 출금)'
                                               ,'whr_to_buy'     => $this->input->post('gst_nm', 'TRUE')
                                               ,'ssamzi_yn'      => 'N'
                                               ,'cost_cls'       => '1'
                                               ,'amt'            => $amt
                                               );

                                $result = $this->stay_m->update_tbb001l00_1($u_data);

                                if (!$result)
                                {
                                    info_log("etc_incm/upd/update_tbb001l00_1", "last_query  = [" . $this->db->last_query() . "]");
                                    $this->db->trans_rollback();
                                    alert_log("etc_incm/upd/update_tbb001l00_1", "[SQL ERR][기타 환불] 지출 수정 오류!");
                                }

                                // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                                $prcs_cnt = $this->db->affected_rows();
                                if ($prcs_cnt != 1)
                                {
                                    info_log("etc_incm/upd/update_tbb001l00_1", "last_query  = [" . $this->db->last_query() . "]");
                                    $this->db->trans_rollback();
                                    alert_log("etc_incm/upd/update_tbb001l00_1", "[SQL ERR][기타 환불] 지출 수정 처리 오류![" . $prcs_cnt . "]!");
                                }

                                // 입출금거래 현금지출합계금액 ins/upd
                                $stnd_yymm = substr(str_replace('-', '', $this->input->post('tr_dt', 'TRUE')), 0, 6);
                                cash_bal_ins_upd($stnd_yymm);

                            }
                            else {
                                $i_data = array('expns_srno'     => $expns_srno
                                               ,'expns_dt'       => str_replace('-', '', $this->input->post('tr_dt', 'TRUE'))
                                               ,'expns_chnl_cls' => '01'
                                               ,'expns_cls'      => '20213'
                                               ,'memo'           => '기타 환불(생활비 계좌 출금)'
                                               ,'whr_to_buy'     => $this->input->post('gst_nm', 'TRUE')
                                               ,'ssamzi_yn'      => 'N'
                                               ,'cost_cls'       => '1'
                                               ,'amt'            => $amt
                                               );

                                $result = $this->stay_m->insert_tbb001l00($i_data);

                                if (!$result)
                                {
                                    info_log("etc_incm/upd/insert_tbb001l00", "last_query  = [" . $this->db->last_query() . "]");
                                    $this->db->trans_rollback();
                                    alert_log("etc_incm/upd/insert_tbb001l00", "[SQL ERR][기타 환불] 지출 입력 오류!");
                                }
                            }
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
                                    alert_log("rsvt/cncl_upd/update_tbb001l00_2", "[SQL ERR][기타 환불] 지출 삭제 오류!");
                                }

                            }
                        }


                        $this->db->trans_commit();

                        $redirect_url = $_SESSION['bef_url'];
                        info_log("rsvt/upd/", "redirect_url  = [" . $redirect_url . "]");

                        info_log("etc_incm/upd/", "기타수익 수정 완료!");
                        info_log("etc_incm/upd/", "================================================================================");

                        if (isset($redirect_url))
                        {
                            redirect(base_url($redirect_url));
                        }
                        else
                        {
                            redirect(base_url("/etc_incm/list"));
                        }
                    }
                }
                else
                {
                    info_log("etc_incm/upd/update_tba006l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("etc_incm/upd/update_tba006l00_1", "[SQL ERR] 기타수익 수정 오류!");
                }
            }
            else
            {
                $this->etc_incm_reg_v('u');
            }
        }
        else
        {
            $this->etc_incm_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("etc_incm/del/", "================================================================================");
            info_log("etc_incm/del/", "기타수익 삭제 시작!");

            $this->form_validation->set_rules('tr_srno'    , '거래일련번호', 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $this->db->trans_begin();

                $d_data = array('tr_srno'   => $this->input->post('tr_srno'  , 'TRUE')
                               );

                $result = $this->stay_m->update_tba006l00_2($d_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("etc_incm/del/update_tba006l00_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("etc_incm/del/update_tba006l00_2", "[SQL ERR] 기타수익 삭제 건수 오류[" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("etc_incm/del/", "기타수익 삭제 완료!");
                        info_log("etc_incm/del/", "================================================================================");

                        redirect(base_url("etc_incm/list"));
                    }
                }
                else
                {
                    info_log("etc_incm/del/update_tba006l00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("etc_incm/del/update_tba006l00_2", "[SQL ERR] 기타수익 삭제 오류!");
                }
            }
            else
            {
                $this->etc_incm_reg_v('u');
            }
        }
    }


    public function etc_incm_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;
        $data['rsv_srno'] = $this->uri->segment(3);

        $stnd_dt = date("Y-m-d");
        $data['stnd_dt'] = $stnd_dt;

        $data['tr_cls_list']       = $this->stay_m->get_list('TR_CLS', 'Y');
        $data['hsrm_cls_list']     = $this->stay_m->get_list('HSRM_CLS');
        $data['rsv_chnl_cls_list'] = $this->stay_m->get_list('RSV_CHNL_CLS', 'Y');

        // 2019.07.11. tr_chnl_cls 추가
        $data['tr_chnl_cls_list'] = $this->stay_m->get_list('TR_CHNL_CLS', 'Y');

        $data['info'] = $this->stay_m->get_rsvt_info($this->uri->segment(3));

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            $data['view'] = $this->stay_m->get_etc_incm_info($this->uri->segment(4));
        }

        $this->load->view('etc_incm_reg_v', $data);
    }


}
