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
    
    function ckbx_chk(obj)
    {
        if (obj.value == "01")
            ssamzi_yn.disabled = false;
        else
            ssamzi_yn.disabled = true;
    }

    //function trim(str)
    //{
    //    return str.replace(/(^\s*)|(\s*$)/g, "");
    //}

    //function getNumber(str)
    //{
    //    str = "" + str.replace(/,/gi,''); // 콤마 제거
    //    str = str.replace(/(^\s*)|(\s*$)/g, ""); // trim
    //    return (new Number(str));
    //}

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
            $('#dt').datepicker({
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
                    <h5><strong>수입 등록/수정</h5></strong>
                </div>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="dt"><h6><strong>일자</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="dt" name="dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('dt', $stnd_dt); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('dt', $view->stnd_dt); } ?>">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="incm_cls"><h6><strong>분류</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="incm_cls" name="incm_cls">
                            <?php foreach($incm_cls_list as $dd_incm_cls_list) { if (isset($view) && strcmp($view->incm_cls, $dd_incm_cls_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $dd_incm_cls_list->clm_val; ?>" <?php echo set_select('incm_cls', $dd_incm_cls_list->clm_val, $selected)?>><?php echo $dd_incm_cls_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="memo"><h6><strong>내역</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="memo" name="memo" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('memo'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('memo', $view->memo); } ?>" placeholder="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="amt"><h6><strong>금액</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="amt" name="amt" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('amt'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('amt', number_format($view->amt)); } ?>" onkeypress="Numberchk()" onkeyup="vComma(this)">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/incm/ins">입력</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins_r" formaction="/incm/ins/r">계속 입력</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/incm/upd/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>">수정</button>
                            <button type="submit" class="btn btn-warning btn-sm" id="del" formaction="<?php echo '/incm/del/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
