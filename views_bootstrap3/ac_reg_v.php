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
        })
    }

    function leftPad(value)
    {
        if (value >= 10)
        {
            return value;
        }
        return `0${value}`;
    }

    //function changecls(){
    //    var cls_Select = document.getElementById("ac_cls");
//
    //    // select element에서 선택된 option의 value가 저장된다.
    //    var cls_Value = cls_Select.options[cls_Select.selectedIndex].value;
//
    //    // select element에서 선택된 option의 text가 저장된다.
    //    var cls_Text = cls_Select.options[cls_Select.selectedIndex].text;
//
    //    if (cls_Value == "1")
    //    {
    //        var now = new Date();
    //        var yr = now.getFullYear()
    //        var mon = leftPad(now.getMonth() + 1);
    //        var dt = leftPad(now.getDate());
//
    //        var stnd_dt = yr + '-' + mon + '-' + dt;
//
    //        $("input[type=text][name=srt_dt]").val(stnd_dt);
    //        $("input[type=text][name=end_dt]").val("9999-12-31");
    //        //$("#srt_dt").attr('disabled', 'true');
    //        $("#end_dt").attr('disabled', 'true');
    //    }
    //    else
    //    {
    //        $("#srt_dt").removeAttr('disabled');
    //        $("#end_dt").removeAttr('disabled');
//
    //        var now = new Date();
    //        var yr = now.getFullYear()
    //        var mon = leftPad(now.getMonth() + 1);
    //        var dt = leftPad(now.getDate());
//
    //        var stnd_dt = yr + '-' + mon + '-' + dt;
//
    //        $("input[type=text][name=srt_dt]").val(stnd_dt);
    //        $("input[type=text][name=end_dt]").val(stnd_dt);
    //    }
    //}


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
            <?php echo form_open('', 'class="form-horizontal" id="expns_reg_form"'); ?>
                <div>
                    <h5><strong>계좌 등록/수정</h5></strong>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="ac_no"><h6><strong>계좌번호</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="ac_no" name="ac_no" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('ac_no'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('ac_no', $view->ac_no); } ?>" placeholder="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="bank_nm"><h6><strong>은행</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="bank" name="bank">
                            <?php foreach($bank_list as $dd_bank_list) { if (isset($view) && strcmp($view->bank, $dd_bank_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $dd_bank_list->clm_val; ?>" <?php echo set_select('bank', $dd_bank_list->clm_val, $selected)?>><?php echo $dd_bank_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="ac_owner"><h6><strong>계좌주</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="ac_owner" name="ac_owner">
                            <?php foreach($usr_list as $dd_usr_list) { if (isset($view) && strcmp($view->ac_owner, $dd_usr_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $dd_usr_list->clm_val; ?>" <?php echo set_select('ac_owner', $dd_usr_list->clm_val, $selected)?>><?php echo $dd_usr_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="ac_cls"><h6><strong>계좌구분</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="ac_cls" name="ac_cls" onchange="changecls()">
                            <?php foreach($ac_cls_list as $dd_ac_cls_list) { if (isset($view) && strcmp($view->ac_cls, $dd_ac_cls_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $dd_ac_cls_list->clm_val; ?>" <?php echo set_select('ac_cls', $dd_ac_cls_list->clm_val, $selected)?>><?php echo $dd_ac_cls_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="primary_yn"><h6><strong>주거래계좌 여부</h6></strong></label>
                    <div class="col-xs-8">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="primary_yn" name="primary_yn" value="Y" "<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_checkbox('primary_yn'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { if (strncmp($view->primary_yn, 'Y', 1) == 0) { $selected = TRUE; } else { $selected = FALSE; } echo set_checkbox('primary_yn', $view->primary_yn, $selected); } ?>" >
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="srt_dt"><h6><strong>시작일자/종료일자</h6></strong></label>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="srt_dt" name="srt_dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { if (strcmp($ac_cls, '1') == 0) { echo $stnd_dt; } else { echo set_value('srt_dt', $stnd_dt); } } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('srt_dt', $view->srt_dt); } ?>" >
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="end_dt" name="end_dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { if (strcmp($ac_cls, '1') == 0) { echo '9999-12-31'; } else { echo set_value('srt_dt', $stnd_dt); } } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('end_dt', $view->end_dt); } ?>" >
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="amt"><h6><strong>금액</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="amt" name="amt" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('amt'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('amt', number_format($view->amt)); } ?>" onkeyup="inputNumberFormat(this)">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="memo"><h6><strong>메모</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="memo" name="memo" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('memo'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('memo', $view->memo); } ?>" placeholder="">
                    </div>
                </div>

                <input type="hidden" name="ac_srno" value="<?php if (isset($view) && strncmp($prcs_cls, 'u', 1) == 0) { echo $view->ac_srno; } else { echo ''; } ?>" >
                <input type="hidden" name="ori_primary_yn" value="<?php if (isset($view) && strncmp($prcs_cls, 'u', 1) == 0) { echo $view->primary_yn; } else { echo ''; } ?>" >

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/asset/ac_ins">입력</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins_r" formaction="/asset/ac_ins/r">계속 입력</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/asset/ac_upd/' . $this->uri->segment(3); ?>">수정</button>
                            <button type="submit" class="btn btn-warning btn-sm" id="del" formaction="<?php echo '/asset/ac_del/' . $this->uri->segment(3); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
