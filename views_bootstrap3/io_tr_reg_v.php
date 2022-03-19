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

    function clssChange()
    {
        //alert('111');
        var io_cls_select = document.getElementById("io_cls");

        // select element에서 선택된 option의 value가 저장된다.
        var clssValue = io_cls_select.options[io_cls_select.selectedIndex].value;


        //Create and append select list
        //var selectList = document.createElement("select");
        //selectList.id = "mySelect";
        //myDiv.appendChild(selectList);

        var arr_itm1    = <?php echo json_encode($io_tr_cls_list_1); ?>;
        var arr_itm2    = <?php echo json_encode($io_tr_cls_list_2); ?>;

        //alert(arr_itm1[0]['clm_val']);
        //alert(arr_itm1[0]['clm_val_nm']);

        if (clssValue == "1")
        {
            arr_itm = arr_itm1;
            //$("#memo").removeAttr('disabled');
        }
        else if (clssValue == "2")
        {
            arr_itm = arr_itm2;
            //$("#memo").attr('disabled', 'true');
        }

        //alert(arr_itm[0]['clm_val_nm']);

        $('#io_tr_cls').empty();

        //alert(arr_itm[0]['clm_val']);
        //alert(arr_itm["length"]);
        for(var cnt = 0; cnt < arr_itm["length"]; cnt++){
            var option = $("<option value='"+ arr_itm[cnt]['clm_val'] + "'>" + arr_itm[cnt]['clm_val_nm'] + "</option>");
            $('#io_tr_cls').append(option);
        }
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
                    <h5><strong>입출금 등록/수정</h5></strong>
                </div>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="dt"><h6><strong>일자</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="dt" name="dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('dt', $stnd_dt); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('dt', $view->stnd_dt); } ?>">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="io_cls"><h6><strong>입출금거래분류</h6></strong></label>
                    <div class="col-xs-4">
                        <select class="form-control" id="io_cls" name="io_cls" onchange="clssChange()" <?php if (strncmp($prcs_cls, 'u', 1) == 0) {echo "disabled";} ?> >
                            <?php foreach($io_cls_list as $dd_io_cls_list) { if (isset($view) && strcmp($view->io_cls, $dd_io_cls_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $dd_io_cls_list->clm_val; ?>" <?php echo set_select('io_cls', $dd_io_cls_list->clm_val, $selected)?>><?php echo $dd_io_cls_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                    <div class="col-xs-4">
                        <select class="form-control" id="io_tr_cls" name="io_tr_cls" <?php if (strncmp($prcs_cls, 'u', 1) == 0) {echo "disabled";} ?> >
                            <?php if (isset($view) && strcmp(substr($view->io_tr_cls, 0, 1), "1") == 0) { $io_tr_cls_list = $io_tr_cls_list_1; } else if (isset($view) && strcmp(substr($view->io_tr_cls, 0, 1), "2") == 0) { $io_tr_cls_list = $io_tr_cls_list_2; } else { $io_tr_cls_list = $io_tr_cls_list_1; }
                                foreach($io_tr_cls_list as $f_list) { if (isset($view) && strcmp($view->io_tr_cls, $f_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $f_list->clm_val; ?>" <?php echo set_select('io_tr_cls', $f_list->clm_val, $selected)?>><?php echo $f_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                        <input type="hidden" class="form-control" id="io_tr_cls_hid" name="io_tr_cls_hid" value="<?php if (isset($view)) { echo $view->io_tr_cls; } ?>">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="memo"><h6><strong>메모</h6></strong></label>
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
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/io_tr/ins">입력</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins_r" formaction="/io_tr/ins/r">계속 입력</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/io_tr/upd/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>">수정</button>
                            <button type="submit" class="btn btn-warning btn-sm" id="del" formaction="<?php echo '/io_tr/del/' . $this->uri->segment(3) . '/' . $this->uri->segment(4); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
