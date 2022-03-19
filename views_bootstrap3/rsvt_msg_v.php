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

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td><textarea class="form-control" id="cnfm_msg" name="cnfm_msg" rows="15"><?php echo set_value('cnfm_msg', $cnfm_msg);?></textarea></td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <th style="vertical-align: middle; text-align: right">
                    <a href="<?php echo '/rsvt/list/'; ?>" class="btn btn-primary btn-sm">예약목록</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
