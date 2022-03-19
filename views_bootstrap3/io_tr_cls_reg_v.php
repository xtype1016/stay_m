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
                    <label class="col-xs-4 control-label" for="clss"><h6><strong>거래구분</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="tr_cls" name="tr_cls" <?php if (strncmp($prcs_cls, 'u', 1) == 0) {echo "disabled";} ?>>
                            <option value="1" <?php if (strncmp($prcs_cls, 'u', 1) == 0 && strncmp(substr($view->clm_val, 1, 1), "1", 1) == 0) { echo "selected"; } ?>>수입</option>
                            <option value="2" <?php if (strncmp($prcs_cls, 'u', 1) == 0 && strncmp(substr($view->clm_val, 1, 1), "2", 1) == 0) { echo "selected"; } ?>>지출</option>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="clm_val_nm"><h6><strong>입출금거래명</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="clm_val_nm" name="clm_val_nm" autocomplete="off" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('clm_val_nm'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('clm_val_nm', $view->clm_val_nm); } ?>">
                    </div>
                </div>

                <input type="hidden" name="clm_val" value="<?php echo $this->uri->segment(3); ?>" >

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins_btn" formaction="/io_tr/cls_ins">분류 등록</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd_btn" formaction="<?php echo '/io_tr/cls_upd/' . $this->uri->segment(3); ?>">수정</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="del_btn" formaction="<?php echo '/io_tr/cls_del/' . $this->uri->segment(3); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
