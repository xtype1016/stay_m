<script>
    function inputNumberFormat(obj) {
    obj.value = comma(uncomma(obj.value));
    }

    function comma(str) {
        str = String(str);
        return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
    }

    function uncomma(str) {
        str = String(str);
        return str.replace(/[^\d]+/g, '');
    }

    $(document).ready(function()
    {
        $("#search_btn").click(function()
        {
            if ($("#gst_nm").val() == '')
            {
                alert('고객명을 입력하세요!');
                return false;
            }
            else
            {
                var gst_nm = $("#gst_nm").val();
                //var stnd_yymm = t_stnd_yymm.replace("-", "");
                var act = '/gst/list/' + gst_nm + '/page/1';
                $("#srch_form").attr('action', act).submit();
            }
        });

        $("#search_btn").click(function()
        {
            if ($("#gst_nm").val() == '')
            {
                alert('고객명을 입력하세요!');
                return false;
            }
            else
            {
                var gst_nm = $("#gst_nm").val();
                //var stnd_yymm = t_stnd_yymm.replace("-", "");
                var act = '/gst/list/' + gst_nm + '/page/1';
                $("#srch_form").attr('action', act).submit();
            }
        });
    });

    function closeWin(gst_no, gst_nm) {
        //alert(gst_no);
        //alert(gst_nm);
        document.getElementById("gst_no").value=gst_no;
        document.getElementById("gst_nm").value=gst_nm;
        $('#modalclose').trigger('click');
    }

    window.onload=function()
    {
        !function(a) {
          a.fn.datepicker.dates.kr = {
            days : [ "일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일" ],
            daysShort : [ "일", "월", "화", "수", "목", "금", "토" ],
            daysMin : [ "일", "월", "화", "수", "목", "금", "토" ],
            months : [ "1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월" ],
            monthsShort : [ "1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월" ],
            titleFormat : "yyyy년 MM", /* Leverages same syntax as 'format' */
          }
        }(jQuery);

        $(document).ready(function() {
            $('#cnfm_dt').datepicker({
              format : "yyyy-mm-dd",
              language : "kr",
              disableTouchKeyboard : true,
              autoclose : true
              //todayHighlight : true
            }).on('hide', function(e) {
              e.stopPropagation(); // 모달 팝업도 같이 닫히는걸 막아준다.
            });

            $('#srt_dt').datepicker({
              format : "yyyy-mm-dd",
              language : "kr",
              disableTouchKeyboard : true,
              autoclose : true
              //todayHighlight : true
            }).on('hide', function(e) {
              e.stopPropagation(); // 모달 팝업도 같이 닫히는걸 막아준다.
            });

            $('#end_dt').datepicker({
              format : "yyyy-mm-dd",
              language : "kr",
              disableTouchKeyboard : true,
              autoclose : true
              //todayHighlight : true
            }).on('hide', function(e) {
              e.stopPropagation(); // 모달 팝업도 같이 닫히는걸 막아준다.
            });

            //var sdate1 = new Date();
            //sdate1.setDate(sdate1.getDate() - 7);
            //$("#sdate1").datepicker('setDate', sdate1);
            //$("#edate1").datepicker('setDate', new Date());
            //$('#datepicker1').datepicker('updateDates');
        })
    }

    $(document).on("touchstart", function(){ });
</script>



<div class="container">
    <header>
        <!--
        <h3><p class="text-justify">예약 등록</p></h3>
        -->
        <style>
            .control-label {
                text-align: right;
            }
        </style>
    </header>

    <!--
    <?php echo validation_errors(); ?>
    -->
    <?php
    if(validation_errors() !== "")
    {
        ?>
        <script language="JavaScript" type="text/javascript">
        //<!--
            alert("<?=str_replace("\n", "\\n", strip_tags(validation_errors()))?>");
        //-->
        </script>
        <?php
    }
    ?>



    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo form_open('', 'class="form-horizontal" id="rsvt_reg_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="cnfm_dt"><h6><strong>예약확정일</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="cnfm_dt" name="cnfm_dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('cnfm_dt', $stnd_dt); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('cnfm_dt', $view->stnd_cnfm_dt); } ?>">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="hsrm_cls"><h6><strong>숙소</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="hsrm_cls" name="hsrm_cls">
                            <?php foreach($hsrm_cls_list as $hsrm_l) { if (isset($view) && strcmp($view->hsrm_cls, $hsrm_l->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $hsrm_l->clm_val; ?>" <?php echo set_select('hsrm_cls', $hsrm_l->clm_val, $selected)?>><?php echo $hsrm_l->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="srt_dt"><h6><strong>시작일자 / 종료일자</h6></strong></label>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="srt_dt" name="srt_dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('srt_dt', $stnd_dt); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('srt_dt', $view->stnd_srt_dt); } ?>">
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="end_dt" name="end_dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('end_dt', $stnd_dt); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('end_dt', $view->stnd_end_dt); } ?>">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="gst_nm"><h6><strong>고객</h6></strong></label>

                    <?php if (strncmp($_SESSION['usr_no'], '0000000005', 10) != 0) { ?>
                    <div class="col-xs-2">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" href="/gst/srch"  data-target="#gst_srch">
                        검색
                        </button>
                    </div>

                    <div class="col-xs-1">
                        <input type="text" class="form-control sr-only" id="gst_no" name="gst_no" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('gst_no', $this->uri->segment(3)); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('gst_no', $view->gst_no); } ?>" readonly>
                    </div>
                    <div class="col-xs-6">
                        <input type="text" class="form-control" id="gst_nm" name="gst_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('gst_nm', urldecode($this->uri->segment(4))); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('gst_nm', $view->gst_nm); } ?>" readonly>
                    </div>
                    <?php } else { ?>
                    <div class="col-xs-8">
                        <input type="hidden" class="form-control sr-only" id="gst_no" name="gst_no" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('gst_no', $this->uri->segment(3)); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('gst_no', $view->gst_no); } ?>" readonly>
                        <input type="text" class="form-control" id="gst_nm" name="gst_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('gst_nm', urldecode($this->uri->segment(4))); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('gst_nm', $view->gst_nm); } ?>">
                    </div>
                    <?php } ?>

                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="amt"><h6><strong>금액 / 보증금</h6></strong></label>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="amt" name="amt" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('amt'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('amt', number_format($view->amt)); } ?>" onkeyup="inputNumberFormat(this)">
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="deposit" name="deposit" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('deposit'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('deposit', number_format($view->deposit)); } ?>" onkeyup="inputNumberFormat(this)">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="rsv_chnl_cls"><h6><strong>예약채널</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="rsv_chnl_cls" name="rsv_chnl_cls">
                            <?php foreach($rsv_chnl_cls_list as $rsv_chnl_l) { if (isset($view) && strcmp($view->rsv_chnl_cls, $rsv_chnl_l->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $rsv_chnl_l->clm_val; ?>" <?php echo set_select('rsv_chnl_cls', $rsv_chnl_l->clm_val, $selected)?>><?php echo $rsv_chnl_l->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="gst_num"><h6><strong>인원수(성인 / 자녀)</h6></strong></label>
                    <div class="col-xs-4">
                        <select class="form-control" id="adlt_cnt" name="adlt_cnt">
                            <?php for($i = 1; $i <= 5; $i++) { if (isset($view) && (int)$i == (int)$view->adlt_cnt) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $i ?>" <?php echo set_select('adlt_cnt', $i, $selected)?>><?php echo $i ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                    <div class="col-xs-4">
                        <select class="form-control" id="chld_cnt" name="chld_cnt">
                            <?php for($i = 0; $i <= 4; $i++) { if (isset($view) && (int)$i == (int)$view->chld_cnt) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $i ?>" <?php echo set_select('chld_cnt', $i, $selected)?>><?php echo $i ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <?php if (strncmp($prcs_cls, 'u', 1) == 0 && $view->adlt_cnt == 0 && $view->chld_cnt == 0)
                {
                    echo '<div class="form-group form-group-sm row">';
                    echo     '<label class="col-xs-4 control-label" for="gst_desc"><h6><strong>고객구성</h6></strong></label>';
                    echo     '<div class="col-xs-8">';
                    echo         '<input type="text" class="form-control" id="gst_desc" name="gst_desc" value=';
                                      if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('gst_desc'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('gst_desc', $view->gst_desc); } ;
                    echo                 ' placeholder="예: 성인 / 청소년 / 소아 / 유아" readonly>';
                    echo     '</div>';
                    echo '</div>';
                } ?>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="cal_id"><h6><strong>고객구분</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="gst_cls" name="gst_cls">
                            <?php foreach($gst_cls_list as $gst_l) { if (isset($view) && strcmp($view->gst_cls, $gst_l->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $gst_l->clm_val; ?>" <?php echo set_select('gst_cls', $gst_l->clm_val, $selected)?>><?php echo $gst_l->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="memo"><h6><strong>메모</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="memo" name="memo" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('memo'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('memo', $view->memo); } ?>" placeholder="">
                    </div>
                </div>

                <input type="hidden" name="evnt_id" value="<?php if (strncmp($prcs_cls, 'i', 1) != 0) { echo $view->evnt_id; } else { echo ''; } ?>" >
                <input type="hidden" name="cncl_yn" value="<?php if (isset($view->cncl_yn) && strncmp($prcs_cls, 'u', 1) == 0) { echo $view->cncl_yn; } else { echo ''; } ?>" >
                <input type="hidden" name="rsv_srno" value="<?php if (isset($rsv_srno) && strncmp($prcs_cls, 'u', 1) == 0) { echo $rsv_srno; } else { echo ''; } ?>" >

                <div class="form-group form-group-sm row">
                    <?php if (isset($view) && strncmp($view->cncl_yn, 'Y', 1) == 0) { ?>
                    <label class="col-xs-12 label label-danger"><h4><strong>예약 취소건입니다!</h4></strong></label>
                    <?php } ?>
                </div>

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/rsvt/ins">예약등록</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0 && strncmp($view->cncl_yn, 'N', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/rsvt/upd/' . $this->uri->segment(3); ?>">예약수정</button>
                            <a href="<?php echo '/etc_incm/ins/' . $this->uri->segment(3); ?>" class="btn btn-default btn-sm">기타거래</a>
                            <a href="<?php echo '/rsvt/cncl/' . $this->uri->segment(3); ?>" class="btn btn-warning btn-sm">예약취소</a>
                            <a href="<?php echo '/rsvt/cnfm_msg/' . $this->uri->segment(3); ?>" class="btn btn-default btn-sm">예약확인</a>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0 && strncmp($view->cncl_yn, 'Y', 1) == 0) { ?>
                            <!--
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/rsvt/upd/' . $this->uri->segment(3); ?>">예약수정</button>
                            <a href="<?php echo '/etc_incm/ins/' . $this->uri->segment(3); ?>" class="btn btn-default btn-sm">기타거래</a>
                            -->
                        <?php } ?>
                    </div>
                </div>

            <?php echo form_close(); ?>

        </div>

        <br>
        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th style="text-align: center"><h5>거래일</h5></th>
                    <th style="text-align: center"><h5>거래구분</h5></th>
                    <th style="text-align: center"><h5>금액</h5></th>
                </tr>
            </thead>
    
            <tbody>
                <?php if (isset($tr_list) && !empty($tr_list)) {
                    foreach ($tr_list as $e_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php echo $e_list->dtfm_tr_dt;?></h6></td>
                    <td align="center"><h6><?php echo $e_list->clm_val_nm;?></h6></td>
                    <td align="right"><h6><?php echo number_format($e_list->amt);?></h6></td>
                </tr>
                <?php
                    } }
                ?>
            </tbody>
    
            <tfoot>
            </tfoot>
        </table>

    </div>
</div>
