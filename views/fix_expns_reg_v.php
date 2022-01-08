<script>
    function Numberchk()
    {
        if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;
    }

    function vComma(obj)
    {
        var str = "" + obj.value.replace(/,/gi,''); // 콤마 제거
        var regx = new RegExp(/(-?\d+)(\d{3})/);
        var bExists = str.indexOf(".",0);
        var strArr = str.split('.');

        while(regx.test(strArr[0]))
        {
            strArr[0] = strArr[0].replace(regx,"$1,$2");
        }

        if (bExists > -1)
            obj.value = strArr[0] + "." + strArr[1];
        else
            obj.value = strArr[0];
    }

    //function chnl_chk(obj)
    //{
    //    if (obj.value == "01")
    //    {
    //        document.getElementById("sttlmt_yn").disabled = true;
    //        document.getElementById("sttlmt_yn").checked = false;
    //        $("#expns_cls").removeAttr('disabled');
    //        $("#whr_to_buy").removeAttr('disabled');
    //    }
    //    else
    //    {
    //        /*
    //        $("#sttlmt_yn").removeAttr('disabled');
    //        */
    //        document.getElementById("sttlmt_yn").disabled = false;
    //    }
    //}
    
    function sttlmt_chk(obj)
    {
        if (document.getElementById('sttlmt_yn').checked) {
            $("#expns_chnl_cls").attr('disabled', 'true');
            $("#expns_cls").attr('disabled', 'true');
            $("#whr_to_buy").attr('disabled', 'true');
        } else {
            $("#expns_chnl_cls").removeAttr('disabled');
            $("#expns_cls").removeAttr('disabled');
            $("#whr_to_buy").removeAttr('disabled');
        }
    }
    //
    //$(window).ready(function() {
    //    alert("TEST!");
    //    //alert($("#expns_chnl_cls").value);
    //    //alert(document.getElementById('expns_chnl_cls').value);
    //    //alert(document.getElementById('sttlmt_yn').value);
    //    alert($("#expns_chnl_cls option:selected").val() );
    //
    //});

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
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="expns_nm"><h6><strong>지출명</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="expns_nm" name="expns_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('expns_nm'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('expns_nm', $view->expns_nm); } ?>">
                    </div>
                </div>


                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="expns_group_no"><h6><strong>지출그룹번호</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="expns_group_no" name="expns_group_no">
                            <?php for($i = 1; $i <= 10; $i++) { if (isset($view) && $i == $view->expns_group_no) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $i ?>" <?php echo set_select('expns_group_no', $i, $selected)?>><?php echo $i ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="sttlmt_yn"><h6><strong>결제여부</h6></strong></label>
                    <div class="col-xs-8">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="sttlmt_yn" name="sttlmt_yn" value="Y" onclick="sttlmt_chk()" <?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_checkbox('sttlmt_yn');} else if (strncmp($prcs_cls, 'u', 1) == 0) { if (strncmp($view->sttlmt_yn, 'Y', 1) == 0) { $selected = TRUE; } else { $selected = FALSE; } echo set_checkbox('sttlmt_yn', $view->sttlmt_yn, $selected); } ?>  >
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="io_tr_cls"><h6><strong>거래구분</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="io_tr_cls" name="io_tr_cls">
                            <?php foreach($io_tr_cls_list as $io_tr_slist) { if (isset($view) && strcmp($view->io_tr_cls, $io_tr_slist->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $io_tr_slist->clm_val; ?>" <?php echo set_select('io_tr_cls', $io_tr_slist->clm_val, $selected)?>><?php echo $io_tr_slist->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="expns_day"><h6><strong>지출일/결제일</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="expns_day" name="expns_day" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('expns_day', substr($stnd_dt, 8, 2)); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('expns_day', $view->expns_day); } ?>">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="expns_chnl_cls"><h6><strong>지출매체</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="expns_chnl_cls" name="expns_chnl_cls" onChange="chnl_chk(this)">
                            <?php foreach($expns_chnl_cls_list as $e_chnl_list) { if (isset($view) && strcmp($view->expns_chnl_cls, $e_chnl_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $e_chnl_list->clm_val; ?>" <?php echo set_select('expns_chnl_cls', $e_chnl_list->clm_val, $selected)?>><?php echo $e_chnl_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="expns_cls"><h6><strong>지출분류</h6></strong></label>
                    <div class="col-xs-8">
                        <?php echo form_dropdown('expns_cls', $expns_cls_list, '', 'class="form-control" id="expns_cls"'); ?>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="whr_to_buy"><h6><strong>구입처</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="whr_to_buy" name="whr_to_buy" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('whr_to_buy'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('whr_to_buy', $view->whr_to_buy); } ?>" placeholder="" >
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="amt"><h6><strong>금액</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="amt" name="amt" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('amt'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('amt', number_format($view->amt)); } ?>" onkeypress="Numberchk()" onkeyup="vComma(this)">
                    </div>
                </div>


                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="memo"><h6><strong>메모</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="memo" name="memo" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('memo'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('memo', $view->memo); } ?>" placeholder="">
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
                    <label class="col-xs-4 control-label" for="ac_no"><h6><strong>계좌번호</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="ac_no" name="ac_no" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('ac_no'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('ac_no', $view->ac_no); } ?>" placeholder="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/fix_expns/ins">입력</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins_r" formaction="/fix_expns/ins/r">계속 입력</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/fix_expns/upd/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>">수정</button>
                            <button type="submit" class="btn btn-warning btn-sm" id="del" formaction="<?php echo '/fix_expns/del/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
