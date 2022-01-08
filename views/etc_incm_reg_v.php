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
            $('#tr_dt').datepicker({
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
</script>

<div class="container">
    <header>
        <!--
        <h3><p class="text-justify">기타 수입 등록</p></h3>
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

    <?php
        $addtnl_info = $this->uri->segment(5);
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo form_open('', 'class="form-horizontal" id="rsvt_info_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="hsrm_cls"><h6><strong>숙소</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control col-xs-4" id="hsrm_cls_nm" name="hsrm_cls_nm" value="<?php echo $info->hsrm_cls_nm; ?>" disabled>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="srt_dt"><h6><strong>시작일자/종료일자</h6></strong></label>
                    <div class="col-xs-4">
                        <input type="text" class="form-control col-xs-4" id="srt_dt" name="srt_dt" value="<?php echo set_value('srt_dt', $info->stnd_srt_dt); ?>" disabled>
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control col-xs-4" id="end_dt" name="end_dt" value="<?php echo set_value('end_dt', $info->stnd_end_dt); ?>" disabled>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="gst_nm"><h6><strong>고객명</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="gst_nm" name="gst_nm" value="<?php echo set_value('gst_nm', $info->gst_nm); ?>" disabled>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="rsv_amt"><h6><strong>금액</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="rsv_amt" name="rsv_amt" value="<?php echo number_format(set_value('amt', $info->amt)); ?>" onkeyup="inputNumberFormat(this)" disabled>
                    </div>
                </div>

                <!--
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="rsv_chnl_cls"><h6><strong>예약채널</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="rsv_chnl_cls" name="rsv_chnl_cls" disabled>
                            <?php foreach($rsv_chnl_cls_list as $rsv_chnl_l) { if (strcmp($info->rsv_chnl_cls, $rsv_chnl_l->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $rsv_chnl_l->clm_val; ?>" <?php echo set_select('rsv_chnl_cls', $rsv_chnl_l->clm_val, $selected)?>><?php echo $rsv_chnl_l->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>
                -->
            </form>
        </div>

        <div class="panel-body">
            <?php echo form_open('', 'class="form-horizontal" id="etc_incm_reg_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="tr_dt"><h6><strong>거래일자</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="tr_dt" name="tr_dt" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('tr_dt', $stnd_dt); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('tr_dt', $view->stnd_tr_dt); } ?>">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="tr_cls"><h6><strong>거래구분</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="tr_cls" name="tr_cls" <?php if (strcmp($addtnl_info, "2") == 0) { echo 'readonly style="background-color:eeeeee" onFocus="this.initialSelect = this.selectedIndex;" onChange="this.selectedIndex = this.initialSelect;"'; } ?> >
                            <?php foreach($tr_cls_list as $tr_l) { if (isset($view) && strcmp($view->tr_cls, $tr_l->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $tr_l->clm_val; ?>" <?php echo set_select('tr_cls', $tr_l->clm_val, $selected)?>><?php echo $tr_l->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="tr_chnl_cls"><h6><strong>거래매체구분</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="tr_chnl_cls" name="tr_chnl_cls" <?php if (strcmp($addtnl_info, "2") == 0) { echo 'readonly style="background-color:eeeeee" onFocus="this.initialSelect = this.selectedIndex;" onChange="this.selectedIndex = this.initialSelect;"'; } ?> >
                            <?php foreach($tr_chnl_cls_list as $tr_l) { if (isset($view) && strcmp($view->tr_chnl_cls, $tr_l->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $tr_l->clm_val; ?>" <?php echo set_select('tr_chnl_cls', $tr_l->clm_val, $selected)?>><?php echo $tr_l->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="amt"><h6><strong>금액</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="amt" name="amt" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('amt'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('amt', number_format($view->amt)); } ?>" onkeyup="inputNumberFormat(this)" <?php if (strcmp($addtnl_info, "2") == 0) { echo 'readonly'; } ?> >
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="memo"><h6><strong>메모</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="memo" name="memo" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('memo'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('memo', $view->memo); } ?>" placeholder="" <?php if (strcmp($addtnl_info, "2") == 0) { echo 'readonly'; } ?> >
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="othr_withdraw_yn"><h6><strong>타계좌출금여부</h6></strong></label>
                    <div class="col-xs-8">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="othr_withdraw_yn" name="othr_withdraw_yn" value="Y" "<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_checkbox('othr_withdraw_yn'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { if (strncmp($view->othr_withdraw_yn, 'Y', 1) == 0) { $selected = TRUE; } else { $selected = FALSE; } echo set_checkbox('othr_withdraw_yn', $view->othr_withdraw_yn, $selected); } ?>" <?php if (strcmp($addtnl_info, "2") == 0) { echo 'style="background-color:eeeeee" onclick="return false;"'; } ?> >
                            </label>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="rsv_srno" value="<?php echo $this->uri->segment(3); ?>" >
                <input type="hidden" name="tr_srno" value="<?php if (strncmp($prcs_cls, 'u', 1) == 0) { echo $this->uri->segment(4); } else { echo ''; } ?>" >
                <input type="hidden" name="gst_nm" value="<?php echo $info->gst_nm; ?>" >

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/etc_incm/ins">거래 등록</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/etc_incm/upd/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>">수정</button>
                            <button type="submit" class="btn btn-warning btn-sm" id="del" formaction="<?php echo '/etc_incm/del/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
