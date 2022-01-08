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

        //ctgr Begin
         $('#lctgr_cls').change(function(){

             // AJAX request
             $.ajax({
               url:'<?=base_url()?>milla/fnctn/getCtgr',
               method: 'post',
               data: {'cmpny_cls': encodeURIComponent($("#cmpny_cls").val()),
                      'lctgr_cls': encodeURIComponent($("#lctgr_cls").val()),
                     '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                     },
               dataType: 'json',
               success: function(response){

                 // Remove options
                 //$('#lctgr_cls').find('option').not(':first').remove();
                 $('#ctgr_cls').find('option').remove();

                 // Add options
                 $.each(response,function(index,data){
                   $('#ctgr_cls').append('<option value="'+data['mt_val']+'">'+data['mt_kor_nm']+'</option>');
                 });
               }
           });
        });
        //ctgr End

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
                    <h5><strong>상품 등록</h5></strong>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="cmpny_cls"><h6><strong>회사</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="cmpny_cls" name="cmpny_cls" <?php if (strcmp($prcs_cls, 'u') == 0) { echo 'disabled'; } ?> >
                            <?php foreach($cmpny_select_list as $cmpny_slist) { if (isset($view) && strcmp($view->cmpny_cls, $cmpny_slist->mt_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $cmpny_slist->mt_val; ?>" <?php echo set_select('cmpny_cls', $cmpny_slist->mt_val, $selected)?>><?php echo $cmpny_slist->mt_kor_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="ctgr_cls"><h6><strong>상품분류</h6></strong></label>
                    <div class="col-xs-4">
                        <select class="form-control" id="lctgr_cls" name="lctgr_cls" <?php if (strcmp($prcs_cls, 'u') == 0) { echo 'disabled'; } ?> >
                            <?php foreach($lctgr_select_list as $lctgr_slist) { if (isset($view) && strcmp($view->lctgr_cls, $lctgr_slist->mt_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $lctgr_slist->mt_val; ?>" <?php echo set_select('lctgr_cls', $lctgr_slist->mt_val, $selected)?>><?php echo $lctgr_slist->mt_kor_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                    <div class="col-xs-4">
                        <select class="form-control" id="ctgr_cls" name="ctgr_cls" <?php if (strcmp($prcs_cls, 'u') == 0) { echo 'disabled'; } ?> >
                            <?php foreach($ctgr_select_list as $ctgr_slist) { if (isset($view) && strcmp($view->ctgr_cls, $ctgr_slist->mt_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $ctgr_slist->mt_val; ?>" <?php echo set_select('ctgr_cls', $ctgr_slist->mt_val, $selected)?>><?php echo $ctgr_slist->mt_kor_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="itm_cd"><h6><strong>상품코드</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="itm_cd" name="itm_cd" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('itm_cd'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('itm_cd', $view->itm_cd); } ?>" placeholder="" disabled>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="itm_nm"><h6><strong>상품명</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="itm_nm" name="itm_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('itm_nm'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('itm_nm', $view->itm_nm); } ?>" placeholder="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="color_nm"><h6><strong>색상</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="color_nm" name="color_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('color_nm'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('color_nm', $view->color_nm); } ?>" placeholder="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="material"><h6><strong>소재</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="material" name="material" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('material'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('material', $view->material); } ?>" placeholder="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="len"><h6><strong>사이즈(mm) / 무게(g)</h6></strong></label>
                    <div class="col-xs-6">
                        <input type="text" class="form-control" id="size" name="size" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('size'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('size', $view->size); } ?>"">
                    </div>
                    <div class="col-xs-2">
                        <input type="text" class="form-control" id="wt" name="wt" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('wt'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('wt', number_format($view->wt)); } ?>" onkeyup="inputNumberFormat(this)">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="supply_prc"><h6><strong>입고가 / 출고가</h6></strong></label>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="in_prc" name="in_prc" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('in_prc'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('in_prc', number_format($view->in_prc)); } ?>" onkeyup="inputNumberFormat(this)">
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control" id="ot_prc" name="ot_prc" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('ot_prc'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('ot_prc', number_format($view->ot_prc)); } ?>" onkeyup="inputNumberFormat(this)">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="memo"><h6><strong>메모</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="memo" name="memo" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('memo'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('memo', $view->memo); } ?>" placeholder="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="sku_id"><h6><strong>SKU_ID</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="sku_id" name="sku_id" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('sku_id'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('sku_id', $view->sku_id); } ?>" placeholder="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="bar_cd"><h6><strong>바코드</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="bar_cd" name="bar_cd" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('bar_cd'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('bar_cd', $view->bar_cd); } ?>" placeholder="">
                    </div>
                </div>

                <input type="hidden" name="pk_itm_cd"    value="<?php if (isset($view) && strncmp($prcs_cls, 'u', 1) == 0) { echo $view->itm_cd; } ?>" >

                <div class="form-group form-group-sm row">
                    <div class="col-xs-12 text-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/milla/itm/ins">입력</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins_r" formaction="/milla/itm/ins/r">계속 입력</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/milla/itm/upd/' . $this->uri->segment(4) . '/' . $this->uri->segment(5); ?>">수정</button>
                            <button type="submit" class="btn btn-warning btn-sm" id="del" formaction="<?php echo '/milla/itm/del/' . $this->uri->segment(4) . '/' . $this->uri->segment(5); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
