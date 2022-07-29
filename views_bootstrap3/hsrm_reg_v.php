<div class="container">
    <header>
        <!--
        <h3><p class="text-justify">숙소 등록</p></h3>
        -->
        <style>
            .control-label {
                text-align: right;
            }
        </style>
    </header>
    <br>

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

            <?php echo form_open('', 'class="form-horizontal" id="hsrm_reg_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="hsrm_nm"><h6><strong>숙소명</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="hsrm_nm" name="hsrm_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('hsrm_nm'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('hsrm_nm', $view->clm_val_nm); } ?>" placeholder="숙소명 또는 객실명을 입력하세요">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <!--
                    <label class="col-xs-4 control-label" for="cal_id"><h6><strong>google 캘린더ID</h6></strong></label>
                    -->
                    <div class="col-xs-8">
                        <input type="hidden" class="form-control" id="cal_id" name="cal_id" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('cal_id'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo $view->othr_info; } ?>" placeholder="캘린더ID 전체를 입력하세요">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <input type="hidden" name="hsrm_cls" value="<?php if (strncmp($prcs_cls, 'u', 1) == 0) { echo $view->clm_val; } else { echo ''; } ?>" >
                </div>

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins" formaction="/hsrm/ins">숙소등록</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="<?php echo '/hsrm/upd/' . $this->uri->segment(3); ?>">숙소수정</button>
                            <button type="submit" class="btn btn-warning btn-sm" id="del" formaction="/hsrm/del">숙소삭제</button>
                        <?php } ?>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
