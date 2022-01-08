<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH.'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

Class Itm extends CI_Controller
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
            info_log("milla/itm", "autologin start!");
            milla_auto_login();
        }

        milla_login_chk();
    }

    public function index()
    {
        //$this->smmry();
        redirect('milla/itm/list','refresh');
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

        info_log("milla/itm/list", "================================================================================");
        info_log("milla/itm/list", "상품 리스트 조회 시작!");

        // 검색 변수 초기화
        $page_url = '';

        $uri_segment = 8;          // 페이지 번호가 위치한 세그먼트

        $cmpny_cls  = $this->uri->segment(4);
        $lctgr_cls  = $this->uri->segment(5);
        $ctgr_cls   = $this->uri->segment(6);

        info_log("milla/itm/list", "cmpny_cls = [" . $cmpny_cls . "]");
        info_log("milla/itm/list", "lctgr_cls = [" . $lctgr_cls . "]");
        info_log("milla/itm/list", "ctgr_cls  = [" . $ctgr_cls . "]");

        // Pagination 용 주소
        $page_url = '/' . $cmpny_cls . '/' . $lctgr_cls . '/' . $ctgr_cls;

        // 페이지네이션 설정
        $config['base_url']         = '/milla/itm/list/' . $page_url . '/page/';

        if (empty($cmpny_cls) || empty($lctgr_cls)) {
            $config['total_rows']       = 0;
            $cmpny_cls = '01';
            //$lctgr_cls = '001';
        }
        else {
            $config['total_rows']       = $this->milla_m->get_itm_list($cmpny_cls, $lctgr_cls, $ctgr_cls, 'rowcnt');         // 표시할 게시물 총 수
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
            $data['itm_rlist'] = $this->milla_m->get_itm_list($cmpny_cls, $lctgr_cls, $ctgr_cls, 'data', $start, $limit);
        }

        $data['cmpny_cls']  = $cmpny_cls;
        $data['lctgr_cls']  = $lctgr_cls;
        $data['ctgr_cls']   = $ctgr_cls;

        $lctgr_nm = 'LCTGR_CLS_' . $cmpny_cls;
        $ctgr_nm = 'CTGR_CLS_' . $cmpny_cls;

        $t_lctgr_select_list = new stdClass();
        $t_lctgr_select_list->mt_nm       = $lctgr_nm;
        $t_lctgr_select_list->mt_val      = 'all';
        $t_lctgr_select_list->mt_kor_nm   = '전체';
        $t_lctgr_select_list->addtn_info  = '';
        $t_lctgr_select_list->othr_info   = '';
        $t_lctgr_select_list->order_info  = '';
        $t_lctgr_select_list->memo        = '';

        $t_ctgr_select_list = new stdClass();
        $t_ctgr_select_list->mt_nm       = $ctgr_nm;
        $t_ctgr_select_list->mt_val      = 'all';
        $t_ctgr_select_list->mt_kor_nm   = '전체';
        $t_ctgr_select_list->addtn_info  = '';
        $t_ctgr_select_list->othr_info   = '';
        $t_ctgr_select_list->order_info  = '';
        $t_ctgr_select_list->memo        = '';

        $data['cmpny_select_list'] = $this->milla_m->get_meta_list('CMPNY_CLS', '', 'data');

        $lctgr_select_list = $this->milla_m->get_meta_list($lctgr_nm, '', 'data');
        array_unshift($lctgr_select_list, $t_lctgr_select_list);
        $data['lctgr_select_list'] = $lctgr_select_list;

        $ctgr_select_list = $this->milla_m->get_meta_list($ctgr_nm, '', 'data');
        array_unshift($ctgr_select_list, $t_ctgr_select_list);
        $data['ctgr_select_list']  = $ctgr_select_list;

        info_log("milla/itm/list", "상품 리스트 조회 완료!");
        info_log("milla/itm/list", "================================================================================");

        $this->load->view('milla/itm_list_v', $data);

    }


    public function ins()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {

            info_log("milla/itm/ins/", "================================================================================");
            info_log("milla/itm/ins/", "상품 입력 시작!");

            $this->form_validation->set_rules('cmpny_cls', '회사구분', 'required');
            $this->form_validation->set_rules('lctgr_cls', '대분류'  , 'required');
            $this->form_validation->set_rules('ctgr_cls' , '소분류'  , 'required');
            $this->form_validation->set_rules('itm_nm'   , '상품명'  , 'required');
            $this->form_validation->set_rules('color_nm' , '색상'    , 'required');
            $this->form_validation->set_rules('material' , '소재'    , 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $cmpny_cls = $this->input->post('cmpny_cls', 'TRUE');
                $lctgr_cls = $this->input->post('lctgr_cls', 'TRUE');
                $ctgr_cls  = $this->input->post('ctgr_cls' , 'TRUE');

                // 숫자 항목
                $wt     = str_replace(',', '', $this->input->post('wt'     , 'TRUE'));
                $in_prc = str_replace(',', '', $this->input->post('in_prc' , 'TRUE'));
                $ot_prc = str_replace(',', '', $this->input->post('ot_prc' , 'TRUE'));

                //info_log("milla/itm/ins", "ht = [" . $ht . "]");
                //info_log("milla/itm/ins", "wt = [" . $wt . "]");

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = trim($this->input->post('memo', 'TRUE'));
                }
                else
                {
                    $memo = NULL;
                }

                $this->db->trans_begin();

                //======================================================================================
                //상품코드 생성 BEGIN
                $ctgr_nm = 'CTGR_CLS_' . $cmpny_cls;

                $ctgr_info = $this->milla_m->get_meta_list($ctgr_nm, $ctgr_cls, 'data');

                info_log("milla/itm/ins", "ctgr_info[0]->addtn_info = [" . $ctgr_info[0]->addtn_info . "]");
                info_log("milla/itm/ins", "cmpny_cls = [" . $cmpny_cls . "]");

                //상품코드일련번호 생성기준 = 분류코드 + 회사코드
                $tmp_itm_stnd = $ctgr_info[0]->addtn_info . '_' . $cmpny_cls;

                info_log("milla/itm/ins", "tmp_itm_stnd = [" . $tmp_itm_stnd . "]");

                $itm_srno = $this->milla_m->get_clm_sr_val($tmp_itm_stnd);
                $itm_srno_fm = sprintf("%03d", $itm_srno);
                $itm_cd = $ctgr_info[0]->addtn_info . $itm_srno_fm;

                info_log("milla/itm/ins", "itm_srno    = [" . $itm_srno . "]");
                info_log("milla/itm/ins", "itm_srno_fm = [" . $itm_srno_fm . "]");
                info_log("milla/itm/ins", "itm_cd      = [" . $itm_cd . "]");

                //상품코드 생성 END
                //======================================================================================

                $i_data = array('cmpny_cls' => $cmpny_cls
                               ,'itm_cd'    => $itm_cd
                               ,'ctgr_cls'  => $ctgr_cls
                               ,'itm_nm'    => trim($this->input->post('itm_nm', 'TRUE'))
                               ,'color_nm'  => trim($this->input->post('color_nm', 'TRUE'))
                               ,'material'  => trim($this->input->post('material', 'TRUE'))
                               ,'size'      => trim($this->input->post('size', 'TRUE'))
                               ,'wt'        => $wt
                               ,'in_prc'    => $in_prc
                               ,'ot_prc'    => $ot_prc
                               ,'memo'      => trim($memo)
                               ,'sku_id'    => trim($this->input->post('sku_id', 'TRUE'))
                               ,'bar_cd'    => trim($this->input->post('bar_cd', 'TRUE'))
                               );

                $result = $this->milla_m->insert_milla004l00($i_data);

                if ($result)
                {
                    $this->db->trans_commit();

                    info_log("milla/itm/ins/", "상품 입력 완료!");
                    info_log("milla/itm/ins/", "================================================================================");

                    $ins_cls = $this->uri->segment(4);
                    //info_log("milla/itm/ins", "ins_cls = [" . $ins_cls . "]");

                    if (strncmp($ins_cls, "r", 1) == 0)
                    {
                        redirect(base_url("milla/itm/ins"));
                    }
                    else
                    {
                        redirect(base_url("milla/itm/list"));
                    }
                }
                else
                {
                    info_log("milla/itm/ins/insert_milla004l00", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("milla/itm/ins/insert_milla004l00", "[SQL ERR] 상품 입력 오류!");
                }
            }
            else
            {
                $this->itm_reg_v('i');
            }
        }
        else
        {
            $this->itm_reg_v('i');
        }
    }


    public function upd()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {

            info_log("milla/itm/upd/", "================================================================================");
            info_log("milla/itm/upd/", "상품 수정 시작!");

            $this->form_validation->set_rules('pk_cmpny_cls', '회사'    , 'required');
            $this->form_validation->set_rules('pk_itm_cd'   , '상품코드', 'required');
            $this->form_validation->set_rules('itm_nm'      , '상품명'  , 'required');
            $this->form_validation->set_rules('color_nm'    , '색상'    , 'required');
            $this->form_validation->set_rules('material'    , '소재'    , 'required');

            //$this->form_validation->set_rules('amt'         , '금액'        , 'integer');

            if ($this->form_validation->run() == TRUE)
            {
                $itm_cd    = $this->input->post('pk_itm_cd', 'TRUE');
                $ctgr_cls  = $this->input->post('ctgr_cls' , 'TRUE');

                // 숫자 항목
                $wt     = str_replace(',', '', $this->input->post('wt'     , 'TRUE'));
                $in_prc = str_replace(',', '', $this->input->post('in_prc' , 'TRUE'));
                $ot_prc = str_replace(',', '', $this->input->post('ot_prc' , 'TRUE'));

                //info_log("milla/itm/ins", "ht = [" . $ht . "]");
                //info_log("milla/itm/ins", "wt = [" . $wt . "]");

                if (strlen($this->input->post('memo', 'TRUE')) > 0)
                {
                    $memo = $this->input->post('memo', 'TRUE');
                }
                else
                {
                    $memo = NULL;
                }

                $this->db->trans_begin();

                $u_data = array('itm_cd'    => $itm_cd
                               ,'itm_nm'    => trim($this->input->post('itm_nm', 'TRUE'))
                               ,'color_nm'  => trim($this->input->post('color_nm', 'TRUE'))
                               ,'material'  => trim($this->input->post('material', 'TRUE'))
                               ,'size'      => trim($this->input->post('size', 'TRUE'))
                               ,'wt'        => $wt
                               ,'in_prc'    => $in_prc
                               ,'ot_prc'    => $ot_prc
                               ,'memo'      => trim($memo)
                               ,'sku_id'    => trim($this->input->post('sku_id', 'TRUE'))
                               ,'bar_cd'    => trim($this->input->post('bar_cd', 'TRUE'))
                               );

                $result = $this->milla_m->update_milla004l00_1($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("milla/itm/upd/update_milla004l00_1", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("milla/itm/upd/update_milla004l00_1", "[SQL ERR] 상품 수정 처리 오류![" . $prcs_cnt . "]!");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("milla/itm/upd/", "상품 수정 완료!");
                        info_log("milla/itm/upd/", "================================================================================");

                        redirect(base_url("milla/itm/list"));
                    }
                }
                else
                {
                    info_log("milla/itm/upd/update_milla004l00_1", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("milla/itm/upd/update_milla004l00_1", "[SQL ERR] 상품 수정 오류!");
                }
            }
            else
            {
                $this->itm_reg_v('u');
            }
        }
        else
        {
            $this->itm_reg_v('u');
        }
    }


    public function del()
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

        if ($_POST)
        {
            info_log("milla/itm/del/", "================================================================================");
            info_log("milla/itm/del/", "상품 삭제 시작!");

            $this->form_validation->set_rules('pk_itm_cd'   , '상품코드', 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $itm_cd    = $this->input->post('pk_itm_cd', 'TRUE');

                $this->db->trans_begin();

                $u_data = array('itm_cd'    => $itm_cd
                           );

                $result = $this->milla_m->update_milla004l00_2($u_data);

                if ($result)
                {
                    // No data found/변경내용이 없을 경우, 다건 처리시 정상으로 리턴되어 추가로 확인 처리함
                    $prcs_cnt = $this->db->affected_rows();
                    if ($prcs_cnt != 1)
                    {
                        info_log("milla/itm/del/update_milla004l00_2", "last_query  = [" . $this->db->last_query() . "]");
                        $this->db->trans_rollback();
                        alert_log("milla/itm/del/update_milla004l00_2", "[SQL ERR] 상품 삭제 처리 오류![" . $prcs_cnt . "]!");
                        info_log("milla/itm/del/update_milla004l00_2", "================================================================================");
                    }
                    else
                    {
                        $this->db->trans_commit();

                        info_log("milla/itm/del/", "상품 삭제 완료!");
                        info_log("milla/itm/del/", "================================================================================");

                        redirect(base_url("milla/itm/list"));
                    }
                }
                else
                {
                    info_log("milla/itm/del/update_milla004l00_2", "last_query  = [" . $this->db->last_query() . "]");
                    $this->db->trans_rollback();
                    alert_log("milla/itm/del/update_milla004l00_2", "[SQL ERR] 상품 삭제 오류!");
                    info_log("milla/itm/del/update_milla004l00_2", "================================================================================");
                }
            }
            else
            {
                $this->itm_reg_v('u');
            }
        }
        else
        {
            $this->itm_reg_v('u');
        }
    }

    public function spreadsheet_download()
    {
        info_log("milla/itm/spreadsheet_download/", "================================================================================");
        info_log("milla/itm/spreadsheet_download/", "상품 엑셀 다운로드 시작!");

        $spreadsheet = new Spreadsheet();
        //$sheet = $spreadsheet->getActiveSheet();
        //$sheet->setCellValue('A1', 'Hello World !');

        // 엑셀 필드명을 지정한다.
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A1", "상품코드")
        ->setCellValue("B1", "이전 상품코드")
        ->setCellValue("C1", "상품명")
        ->setCellValue("D1", "색상")
        ->setCellValue("E1", "재질")
        ->setCellValue("F1", "사이즈(mm)")
        ->setCellValue("G1", "무게(g)")
        ->setCellValue("H1", "입고가격")
        ->setCellValue("I1", "출고가격")
        ->setCellValue("J1", "SKU ID")
        ->setCellValue("K1", "BAR CD")
        ->setCellValue("L1", "메모");

        $cmpny_cls  = $this->uri->segment(4);
        $lctgr_cls  = $this->uri->segment(5);
        $ctgr_cls   = $this->uri->segment(6);

        info_log("milla/itm/spreadsheet_download", "cmpny_cls = [" . $cmpny_cls . "]");
        info_log("milla/itm/spreadsheet_download", "lctgr_cls = [" . $lctgr_cls . "]");
        info_log("milla/itm/spreadsheet_download", "ctgr_cls  = [" . $ctgr_cls . "]");

        $itm_rlist = $this->milla_m->get_itm_list($cmpny_cls, $lctgr_cls, $ctgr_cls, 'data');

        // 본문 데이터 저장
        $row_no = 2;

        foreach ($itm_rlist as $itm_list)
        {
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A$row_no", "$itm_list->itm_cd")
            ->setCellValue("B$row_no", "$itm_list->old_itm_cd")
            ->setCellValue("C$row_no", "$itm_list->itm_nm")
            ->setCellValue("D$row_no", "$itm_list->color_nm")
            ->setCellValue("E$row_no", "$itm_list->material")
            ->setCellValue("F$row_no", "$itm_list->size")
            ->setCellValue("G$row_no", "$itm_list->wt")
            ->setCellValue("H$row_no", "$itm_list->in_prc")
            ->setCellValue("I$row_no", "$itm_list->ot_prc")
            ->setCellValue("J$row_no", "$itm_list->sku_id")
            ->setCellValue("K$row_no", "$itm_list->bar_cd")
            ->setCellValue("L$row_no", "$itm_list->memo");
            $row_no += 1;
        }

        // 저장 형식을 지정한다.
        //get_meta_info
        $cmpny_info = $this->milla_m->get_meta_info($cmpny_cls, 'CMPNY_CLS', $cmpny_cls);

        if (strcmp($lctgr_cls, 'all') == 0)
        {
            $lctgr_kor_nm = '전체';
        }
        else
        {
            $lctgr_nm = 'LCTGR_CLS_' . $cmpny_cls;
            $lctgr_info  = $this->milla_m->get_meta_info($cmpny_cls, $lctgr_nm, $lctgr_cls );
            $lctgr_kor_nm = $lctgr_info->mt_kor_nm;
        }

        if (strcmp($ctgr_cls, 'all') == 0)
        {
            $ctgr_kor_nm = '전체';
        }
        else
        {
            $ctgr_nm = 'CTGR_CLS_' . $cmpny_cls;
            $ctgr_info  = $this->milla_m->get_meta_info($cmpny_cls, $ctgr_nm, $ctgr_cls );
            $ctgr_kor_nm = $ctgr_info->mt_kor_nm;
        }

        $file_name = $cmpny_info->mt_kor_nm . '_' . $lctgr_kor_nm . '_' . $ctgr_kor_nm . '_list_' . date("Ymd_His") . ".xlsx";
        $writer = new Xlsx($spreadsheet);

        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename='.$file_name);
        header("Cache-Control: max-age=0");
        $writer->save('php://output');

        info_log("milla/itm/spreadsheet_download/", "상품 엑셀 다운로드 종료!");
        info_log("milla/itm/spreadsheet_download/", "================================================================================");

        exit();

    }

    public function itm_reg_v($prcs_cls)
    {
        $data['prcs_cls'] = $prcs_cls;

        $data['cmpny_select_list'] = $this->milla_m->get_meta_list('CMPNY_CLS', '', 'data');

        if (strncmp($prcs_cls, "u", 1) == 0)
        {
            //info_log("milla/itm/itm_reg_v", "this->uri->segment(4) = [" . $this->uri->segment(4) . "]");
            $data['view'] = $this->milla_m->get_itm_info($this->uri->segment(4));

            info_log("milla/itm/itm_reg_v", "cmpny_cls = [" . $data['view']->cmpny_cls . "]");

            $cmpny_cls = $data['view']->cmpny_cls;
            $lctgr_nm = 'LCTGR_CLS_' . $cmpny_cls;
            $ctgr_nm  = 'CTGR_CLS_' . $cmpny_cls;

            $data['lctgr_select_list'] = $this->milla_m->get_meta_list($lctgr_nm, '', 'data');
            $data['ctgr_select_list']  = $this->milla_m->get_meta_list($ctgr_nm, '', 'data');
        }
        else
        {
            $data['lctgr_select_list'] = $this->milla_m->get_meta_list('LCTGR_CLS_01', '', 'data');
            $data['ctgr_select_list'] = $this->milla_m->get_meta_list('CTGR_CLS_01', '', 'data');
        }

        $this->load->view('milla/itm_reg_v', $data);
    }


}
