    <script>
        $(document).ready(function()
        {
            //$("#search_btn").click(function()
            //{
            //    if ($("#stnd_yymm").val() == '')
            //    {
            //        alert('기준년월을 입력하세요!');
            //        return false;
            //    }
            //    else
            //    {
            //        var t_srt_dt = $("#srt_dt").val();
            //        var srt_dt = t_srt_dt.replace("-", "");
            //        var srt_dt = srt_dt.replace("-", "");
            //        var t_end_dt = $("#end_dt").val();
            //        var end_dt = t_end_dt.replace("-", "");
            //        var end_dt = end_dt.replace("-", "");
            //        //var act = '/rsvt/list/' + $("#stnd_yymm").val() + '/' + $("#hsrm_cls").val() + '/page/1';
            //        var act = '/rsvt/prc/' + srt_dt + '/' + end_dt + '/page/1';
            //        $("#srch_form").attr('action', act).submit();
            //    }
            //});
        });

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

        $(document).on("touchstart", function(){ });
    </script>

    <div class="container">
        <!--
        <?php echo validation_errors(); ?>
        -->
        <!--
        <?php
        if(validation_errors() !== "")
        {
            ?>
            <script language="JavaScript" type="text/javascript">
            <!--
              alert("<?=str_replace("\n", "\\n", strip_tags(validation_errors()))?>");
            -->
            </script>
            <?php
        }
        ?>
        -->

        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo form_open('', 'method="post" class="form-horizontal" id="srch_form"'); ?>
                    <div class="form-group form-group-sm row">
                        <label class="col-xs-4 control-label" for="hsrm_cls"><h6><strong>숙소</h6></strong></label>
                        <div class="col-xs-8">
                            <select class="form-control" id="hsrm_cls" name="hsrm_cls">
                                <?php foreach($hsrm_cls_list as $hsrm_l) { if (isset($hsrm_cls_val) && strcmp($hsrm_cls_val, $hsrm_l->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                    <option value="<?php echo $hsrm_l->clm_val; ?>" <?php echo set_select('hsrm_cls', $hsrm_l->clm_val, $selected)?>><?php echo $hsrm_l->clm_val_nm; ?></option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group form-group-sm row">
                        <label class="col-xs-4 control-label" for="srt_dt"><h6><strong>시작일자 / 종료일자</h6></strong></label>
                        <div class="col-xs-4">
                            <input type="text" class="form-control" id="srt_dt" name="srt_dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'r', 1) == 0) { echo set_value('srt_dt', $srt_dt); } else { echo set_value('srt_dt', $stnd_dt); } ?>">
                        </div>
                        <div class="col-xs-4">
                            <input type="text" class="form-control" id="end_dt" name="end_dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'r', 1) == 0) { echo set_value('end_dt', $end_dt); } else { echo set_value('srt_dt', $stnd_dt); } ?>">
                        </div>
                    </div>

                    <div class="form-group form-group-sm row">
                        <label class="col-xs-4 control-label" for="gst_num"><h6><strong>인원수(성인 / 자녀)</h6></strong></label>
                        <div class="col-xs-4">
                            <select class="form-control" id="gst_num1" name="gst_num1">
                                <?php for($i = 1; $i <= 7; $i++) { if (isset($gst_num1) && (int)$i == (int)$gst_num1) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                    <option value="<?php echo $i ?>" <?php echo set_select('gst_num1', $i, $selected)?>><?php echo $i ?></option>
                                <?php }; ?>
                            </select>
                        </div>
                        <div class="col-xs-4">
                            <select class="form-control" id="gst_num2" name="gst_num2">
                                <?php for($i = 0; $i <= 4; $i++) { if (isset($gst_num2) && (int)$i == (int)$gst_num2) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                    <option value="<?php echo $i ?>" <?php echo set_select('gst_num2', $i, $selected)?>><?php echo $i ?></option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group form-group-sm row">
                        <label class="col-xs-4 control-label" for="revisit_yn"><h6><strong>재방문 여부</h6></strong></label>
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="revisit_yn" name="revisit_yn" value="Y" "<?php if (strncmp($prcs_cls, 'r', 1) != 0) { echo set_checkbox('revisit_yn'); } else if (strncmp($prcs_cls, 'r', 1) == 0) { if (strncmp($revisit_yn, 'Y', 1) == 0) { $selected = TRUE; } else { $selected = FALSE; } echo set_checkbox('revisit_yn', $revisit_yn, $selected); } ?>" >
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group form-group-sm row">
                        <label class="col-xs-4 control-label" for="discount_rt"><h6><strong>할인율(%) / 보증금</h6></strong></label>
                        <div class="col-xs-4">
                            <input type="text" class="form-control" id="discount_rt" name="discount_rt" maxlength="2" value="<?php if (strncmp($prcs_cls, 'r', 1) == 0) { echo set_value('discount_rt', $discount_rt); } else { echo set_value('discount_rt', $discount_rt); } ?>" onkeypress="Numberchk()" onkeyup="vComma(this)">
                        </div>
                        <div class="col-xs-4">
                            <select class="form-control" id="deposit" name="deposit">
                                <?php for($i = 0; $i <= 3; $i++) { if (($i * 10) == (int)$deposit) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                    <option value="<?php echo $i * 10 ?>" <?php echo set_select('deposit', ($i * 10), $selected)?>><?php echo $i * 10 ?></option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group form-group-sm row">
                        <label class="col-xs-4 control-label" for="extend_yn"><h6><strong>예약 후 기간변경 여부</h6></strong></label>
                        <div class="col-xs-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="extend_yn" name="extend_yn" value="Y" "<?php if (strncmp($prcs_cls, 'r', 1) != 0) { echo set_checkbox('extend_yn'); } else if (strncmp($prcs_cls, 'r', 1) == 0) { if (strncmp($extend_yn, 'Y', 1) == 0) { $selected = TRUE; } else { $selected = FALSE; } echo set_checkbox('extend_yn', $extend_yn, $selected); } ?>" >
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group form-group-sm row">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                        </div>
                    </div>

                <?php echo form_close(); ?>
            </div>
        </div>

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                </tr>
            </thead>

            <tbody>
                <!--
                <tr>
                    <td><?php echo $dtl_prc;?></td>
                </tr>
                -->
                <tr>
                    <td><?php echo $dtl_desc_prc;?></td>
                </tr>
                <tr>
                    <td><textarea class="form-control" id="info_msg" name="info_msg" rows="15"><?php echo set_value('info_msg', $info_msg);?></textarea></td>
                </tr>
                <tr>
                    <td><textarea class="form-control" id="info_msg2" name="info_msg2" rows="15"><?php echo set_value('info_msg2', $info_msg2);?></textarea></td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan= "3" style="vertical-align: middle; text-align: left">
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
