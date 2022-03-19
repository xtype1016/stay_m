<script>

//    $(document).ready(function()
//    {
//        $("#search_btn").click(function()
//        {
//            if ($("#stnd_dt").val() == '')
//            {
//                alert('일자 입력하세요!');
//                return false;
//            }
//            else
//            {
//                var stnd_dt = $("#stnd_dt").val();
//                //var stnd_yymm = t_stnd_yymm.replace("-", "");
//                var act = '/holyday_mng/dtl/' + stnd_dt + '/page/1';
//                $("#srch_form").attr('action', act).submit();
//            }
//        });
//    });


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
        <!--
                <?php echo form_open('', 'method="get" class="form-inline" id="srch_form"'); ?>
                    <div class="form-group form-group-sm row">
                        <input type="date" class="form-control" id="stnd_dt" name="stnd_dt" value="<?php echo $stnd_dt;?>">

                        <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                    </div>
                </form>
        -->
            <?php echo form_open('', 'class="form-horizontal" id="holyday_reg_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="dt"><h6><strong>시작일/종료일</h6></strong></label>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="srt_dt" name="srt_dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('srt_dt', $stnd_dt); } else if (isset($view) && strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('srt_dt', $view->stnd_srt_dt); } ?>" >
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="end_dt" name="end_dt" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('end_dt', $stnd_dt); } else if (isset($view) && strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('end_dt', $view->stnd_end_dt); } ?>" >
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="season_cls"><h6><strong>구분</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="season_cls" name="season_cls">
                            <?php foreach($season_cls_list as $s_list) { if (isset($view) && strcmp($view->season_cls, $s_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $s_list->clm_val; ?>" <?php echo set_select('season_cls', $s_list->clm_val, $selected)?>><?php echo $s_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/season_mng/ins">입력</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins_r" formaction="/season_mng/ins/r">계속 입력</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/season_mng/upd/' . $this->uri->segment(3); ?>">수정</button>
                            <button type="submit" class="btn btn-warning btn-sm" id="del" formaction="<?php echo '/season_mng/del/' . $this->uri->segment(3); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>
