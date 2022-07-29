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
            <?php echo form_open('', 'class="form-horizontal" id="expns_chnl_cls_nm_reg_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="expns_chnl_cls_nm"><h6><strong>지출 매체</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="expns_chnl_cls_nm" name="expns_chnl_cls_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('expns_chnl_cls_nm'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('expns_chnl_cls_nm', $view->clm_val_nm); } ?>" placeholder="카드명을 입력하세요">
                    </div>
                </div>

                <input type="hidden" name="expns_chnl_cls" value="<?php echo $this->uri->segment(3); ?>" >

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins_btn" formaction="/expns_chnl/ins">항목 등록</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd_btn" formaction="<?php echo '/expns_chnl/upd/' . $this->uri->segment(3); ?>">수정</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="del_btn" formaction="<?php echo '/expns_chnl/del/' . $this->uri->segment(3); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
