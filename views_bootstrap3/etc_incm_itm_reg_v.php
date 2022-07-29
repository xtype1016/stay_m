<script>
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
            <?php echo form_open('', 'class="form-horizontal" id="tr_cls_nm_reg_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="tr_cls_nm"><h6><strong>기타거래 항목</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="tr_cls_nm" name="tr_cls_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('tr_cls_nm'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('tr_cls_nm', $view->clm_val_nm); } ?>" placeholder="기타거래 항목을 입력하세요">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="sign_cls"><h6><strong>부호</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="sign_cls" name="sign_cls">
                            <option value=1 <?php if ($prcs_cls == 'u' && 1 == $othr_info) {echo "selected";} ?>>증가</option>
                            <option value=-1 <?php if ($prcs_cls == 'u' && -1 == $othr_info) {echo "selected";} ?>>감소</option>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="tr_cls" value="<?php echo $this->uri->segment(3); ?>" >

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/etc_incm_itm/ins">항목 등록</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/etc_incm_itm/upd/' . $this->uri->segment(3); ?>">수정</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="del" formaction="<?php echo '/etc_incm_itm/del/' . $this->uri->segment(3); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
