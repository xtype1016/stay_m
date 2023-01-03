<div class="container">
    <header>
        <h3><p class="text-justify">[Staying M] 비밀번호 변경</p></h3>
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

    <?php echo form_open('', 'class="form-horizontal" id="join_form"'); ?>

        <div class="form-group">
            <label class="col-xs-4 control-label" for="password"><h5><strong>비밀번호</h5></strong></label>
            <div class="col-xs-8">
                <input type="password" class="form-control" id="password" name="password" value="<?php echo set_value('password'); ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-4 control-label" for="password_re"><h5><strong>비밀번호 확인</h5></strong></label>
            <div class="col-xs-8">
                <input type="password" class="form-control" id="password_re" name="password_re" value="<?php echo set_value('password_re'); ?>" />
            </div>
        </div>

        <div class="form-group">
            <div class="pull-right">
                <button type="submit" class="btn btn-primary" id="join_btn" formaction="/join/upd">비밀번호 변경</button>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
