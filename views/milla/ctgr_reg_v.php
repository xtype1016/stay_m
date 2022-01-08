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

    $(document).ready(function(){

        $('#addtn_info').bind("keyup", function(){
            $(this).val($(this).val().toUpperCase());
        });

       //lctgr Begin
        $('#cmpny_cls').change(function(){
            //var cmpny_cls = $(this).val();

            //alert('<?=base_url()?>milla/fnctn/getLctgr');
            //alert('CheckPoint01!')

            //var base_url = <?php echo base_url(); ?>;

            //alert(base_url);

            // AJAX request
            $.ajax({
              url:'<?=base_url()?>milla/fnctn/getLctgr',
              method: 'post',
              data: {'cmpny_cls': encodeURIComponent($("#cmpny_cls").val()),
                    '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                    },
              dataType: 'json',
              success: function(response){

                // Remove options
                //$('#lctgr_cls').find('option').not(':first').remove();
                $('#lctgr_cls').find('option').remove();

                // Add options
                $.each(response,function(index,data){
                  $('#lctgr_cls').append('<option value="'+data['mt_val']+'">'+data['mt_kor_nm']+'</option>');
                });
              }
          });
       });
       //lctgr End

    });

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
                    <h5><strong>분류 등록/수정</strong></h5>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="cmpny_cls"><h6><strong>브랜드</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="cmpny_cls" name="cmpny_cls">
                            <?php foreach($cmpny_select_list as $cmpny_slist) { if (isset($view) && strcmp($view->cmpny_cls, $cmpny_slist->mt_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $cmpny_slist->mt_val; ?>" <?php echo set_select('cmpny_cls', $cmpny_slist->mt_val, $selected)?>><?php echo $cmpny_slist->mt_kor_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="lctgr_cls"><h6><strong>대분류</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="lctgr_cls" name="lctgr_cls">
                            <?php foreach($lctgr_select_list as $lctgr_slist) { if (isset($view) && strcmp($view->lctgr_cls, $lctgr_slist->mt_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $lctgr_slist->mt_val; ?>" <?php echo set_select('cmpny_cls', $lctgr_slist->mt_val, $selected)?>><?php echo $lctgr_slist->mt_kor_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="mt_kor_nm"><h6><strong>소분류</strong></h6></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="mt_kor_nm" name="mt_kor_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('mt_kor_nm'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('mt_kor_nm', $view->mt_kor_nm); } ?>" placeholder="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="addtn_info"><h6><strong>분류코드</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="addtn_info" name="addtn_info" maxlength="4" style="text-transform: uppercase"; value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('addtn_info'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('addtn_info', $view->addtn_info); } ?>" placeholder="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="memo"><h6><strong>메모</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="memo" name="memo" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('memo'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('memo', $view->memo); } ?>" placeholder="">
                    </div>
                </div>

                <input type="hidden" name="ori_mt_nm"  value="<?php if (isset($view) && strncmp($prcs_cls, 'u', 1) == 0) { echo $view->mt_nm; } ?>" >
                <input type="hidden" name="ori_mt_val" value="<?php if (isset($view) && strncmp($prcs_cls, 'u', 1) == 0) { echo $view->mt_val; } ?>" >
                <input type="hidden" name="ori_addtn_info" value="<?php if (isset($view) && strncmp($prcs_cls, 'u', 1) == 0) { echo $view->addtn_info; } ?>" >

                <input type="hidden" id="id_token" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

                <div class="form-group form-group-sm row">
                    <?php if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                        <label class="col-xs-12 label label-danger"><h6><strong>상품코드에 사용된 분류코드는 수정/삭제가 불가합니다.</h6></strong></label>
                    <?php } ?>
                </div>

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/milla/ctgr/ins">입력</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins_r" formaction="/milla/ctgr/ins/r">계속 입력</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/milla/ctgr/upd/' . $this->uri->segment(4) . '/' . $this->uri->segment(5); ?>">수정</button>
                            <button type="submit" class="btn btn-warning btn-sm" id="del" formaction="<?php echo '/milla/ctgr/del/' . $this->uri->segment(4) . '/' . $this->uri->segment(5); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
