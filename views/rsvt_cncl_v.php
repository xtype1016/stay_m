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
            $('#cncl_dt').datepicker({
              format : "yyyy-mm-dd",
              language : "kr",
              disableTouchKeyboard : true,
              autoclose : true
              //todayHighlight : true
            }).on('hide', function(e) {
              e.stopPropagation(); // 모달 팝업도 같이 닫히는걸 막아준다.
            });

            $('#rcv_dt').datepicker({
              format : "yyyy-mm-dd",
              language : "kr",
              disableTouchKeyboard : true,
              autoclose : true
              //todayHighlight : true
            }).on('hide', function(e) {
              e.stopPropagation(); // 모달 팝업도 같이 닫히는걸 막아준다.
            });
        })
    }


    $(window).ready(function() {
        var rsv_chnl_cls = '';
        //rsv_chnl_cls = $("#b_rsv_chnl_cls").val();
        rsv_chnl_cls = "<?php echo $view->rsv_chnl_cls; ?>";

        //alert(rsv_chnl_cls);

        if  (rsv_chnl_cls == "2")
        {
            document.getElementById("othr_withdraw_yn").disabled = true;
        }
    });
</script>

<div class="container">
    <header>
        <!--
        <h3><p class="text-justify">예약 등록</p></h3>
        -->
    </header>

    <!--
    <?php echo validation_errors(); ?>
    -->
    <?php
    if (validation_errors() !== "")
    {
        ?>
        <script language="JavaScript" type="text/javascript">
            alert("<?=str_replace("\n", "\\n", strip_tags(validation_errors()))?>");
        </script>
        <?php
    }
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo form_open('', "class='form-horizontal' id='rsvt_info_form'"); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="hsrm_cls"><h6><strong>숙소</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control col-xs-4" id="hsrm_cls_nm" name="hsrm_cls_nm" value="<?php echo $view->hsrm_cls_nm; ?>" disabled>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="srt_dt"><h6><strong>시작일자 / 종료일자</h6></strong></label>
                    <div class="col-xs-4">
                        <input type="text" class="form-control col-xs-4" id="srt_dt" name="srt_dt" autocomplete="off" value="<?php echo set_value('srt_dt', $view->stnd_srt_dt); ?>" disabled>
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control col-xs-4" id="end_dt" name="end_dt" autocomplete="off" value="<?php echo set_value('end_dt', $view->stnd_end_dt); ?>" disabled>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="gst_nm"><h6><strong>고객명</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="gst_nm" name="gst_nm" value="<?php echo set_value('gst_nm', $view->gst_nm); ?>" disabled>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="rsv_amt"><h6><strong>금액 / 보증금</h6></strong></label>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="rsv_amt" name="rsv_amt" value="<?php echo number_format(set_value('amt', $view->amt)); ?>" onkeyup="inputNumberFormat(this)" disabled>
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="deposit_v" name="deposit_v" value="<?php echo number_format(set_value('deposit', $view->deposit)); ?>" onkeyup="inputNumberFormat(this)" disabled>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="rsv_chnl_cls"><h6><strong>예약채널</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control col-xs-4" id="rsv_chnl_cls_nm" name="rsv_chnl_cls_nm" value="<?php echo $view->rsv_chnl_cls_nm; ?>" disabled>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>

        <div class="panel-body">
            <?php echo form_open('', 'class="form-horizontal" id="rsvt_cncl_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="cncl_dt"><h6><strong><?php if (strncmp($view->rsv_chnl_cls, '1', 1) == 0) { echo '취소일자'; } else if (strncmp($view->rsv_chnl_cls, '2', 1) == 0) { echo '취소일자 / 입금일자'; }  ?></h6></strong></label>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="cncl_dt" name="cncl_dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'u', 1) == 0) { echo $etc_incm_view->stnd_cncl_dt; } else { echo set_value('cncl_dt', $stnd_dt); } ?>">
                    </div>
                    <div class="col-xs-4">
                        <?php if (strncmp($view->rsv_chnl_cls, '2', 1) == 0) { ?>
                        <input type="text" class="form-control" id="rcv_dt" name="rcv_dt" autocomplete="off" <?php if (strncmp($prcs_cls, 'u', 1) == 0) { echo "value=" . $etc_incm_view->stnd_tr_dt; } else { echo "value=" . $stnd_rcv_dt; echo ' disabled'; } ?> >
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="amt"><h6><strong><?php if (strncmp($view->rsv_chnl_cls, '1', 1) == 0) { echo '환불금액'; } else if (strncmp($view->rsv_chnl_cls, '2', 1) == 0) { echo '취소후 입금금액'; }  ?></h6></strong></label>
                    <div class="col-xs-8">
                        <?php if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <input type="text" class="form-control" id="amt" name="amt" value="<?php if (isset($etc_incm_view)) { echo number_format($etc_incm_view->amt); } ?>" onkeyup="inputNumberFormat(this)">
                        <?php } else { ?>
                            <input type="text" class="form-control" id="amt" name="amt" value="<?php echo set_value('amt', 0); ?>" onkeyup="inputNumberFormat(this)">
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="memo"><h6><strong>메모</h6></strong></label>
                    <div class="col-xs-8">
                        <textarea class="form-control" id="memo" name="memo" rows="1"><?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('memo', ''); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('memo', $etc_incm_view->memo); } ?></textarea>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="othr_withdraw_yn"><h6><strong>타계좌출금여부</h6></strong></label>
                    <div class="col-xs-8">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="othr_withdraw_yn" name="othr_withdraw_yn" value="Y" "<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_checkbox('othr_withdraw_yn'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { if (strncmp($etc_incm_view->othr_withdraw_yn, 'Y', 1) == 0) { $selected = TRUE; } else { $selected = FALSE; } echo set_checkbox('othr_withdraw_yn', $etc_incm_view->othr_withdraw_yn, $selected); } ?>" >
                            </label>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="evnt_id" value="<?php if (strncmp($prcs_cls, 'i', 1) != 0) { echo $view->evnt_id; } else { echo ''; } ?>" >
                <input type="hidden" name="rsv_srno" value="<?php echo $rsv_srno; ?>" >
                <input type="hidden" name="b_rsv_chnl_cls" value="<?php echo $view->rsv_chnl_cls; ?>" >
                <input type="hidden" name="b_rcv_dt" value="<?php echo $stnd_rcv_dt; ?>" >
                <input type="hidden" name="tr_srno" value="<?php if (isset($tr_srno)) {echo $tr_srno; } ?>" >
                <input type="hidden" name="tr_cls" value="<?php if (strncmp($view->rsv_chnl_cls, '1', 1) == 0) { echo '03'; } else if (strncmp($view->rsv_chnl_cls, '2', 1) == 0) { echo '04'; }  ?>" >
                <input type="hidden" name="deposit" value="<?php if (isset($view->deposit)) {echo $view->deposit; } ?>" >
                <input type="hidden" name="gst_nm" value="<?php if (isset($view->gst_nm)) {echo $view->gst_nm; } ?>" >

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'c', 1) == 0) { ?>
                            <button type="submit" class="btn btn-warning btn-sm" id="cncl" formaction="<?php echo '/rsvt/cncl/' . $this->uri->segment(3); ?>">예약취소</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-warning btn-sm" id="cncl" formaction="<?php echo '/rsvt/cncl_upd/'; ?>">예약취소 수정</button>
                        <?php } ?>
                    </div>
                </div>

            <?php echo form_close(); ?>
        </div>

    </div>
</div>
