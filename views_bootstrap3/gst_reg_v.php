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
            <?php echo form_open('', 'class="form-horizontal" id="etc_incm_reg_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="gst_nm"><h6><strong>고객명</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="gst_nm" name="gst_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('gst_nm'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('gst_nm', $view->gst_nm); } ?>">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="phone_num"><h6><strong>전화번호</h6></strong></label>
                    <div class="col-xs-3">
                        <input type="text" class="form-control" id="phone_prefix" name="phone_prefix" value="010" style = "text-align:center;" readonly="readonly">
                    </div>                    
                    <div class="col-xs-5">
                        <input type="text" class="form-control" id="phone_num" name="phone_num" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('phone_num'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('phone_num', $view->phone_num); } ?>" placeholder="- 없이 숫자만 입력해 주세요">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="memo"><h6><strong>메모</h6></strong></label>
                    <div class="col-xs-8">
                        <textarea class="form-control" id="memo" name="memo" rows="5"><?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('memo'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('memo', $view->memo); } ?></textarea>
                    </div>
                </div>

                <input type="hidden" name="rsv_srno" value="<?php echo $this->uri->segment(3); ?>" >
                <input type="hidden" name="gst_nm_bef" value="<?php if (strncmp($prcs_cls, 'u', 1) == 0) { echo $view->gst_nm; } else { echo ''; } ?>" >

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                                <button type="submit" class="btn btn-primary btn-sm" id="ins_rsv" formaction="/gst/ins/rsv">고객등록/예약</button>
                                <button type="submit" class="btn btn-default btn-sm" id="ins" formaction="/gst/ins">등록</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                                <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/gst/upd/' . $this->uri->segment(3); ?>">고객수정</button>
                                <button type="submit" class="btn btn-primary btn-sm" id="del" formaction="<?php echo '/gst/del/' . $this->uri->segment(3); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
