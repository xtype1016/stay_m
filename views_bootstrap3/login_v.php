<div class="container">
    <header>
        <!--
        <h3><p class="text-justify">[Staying M] 로그인</p></h3>
        -->
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
            <?php echo form_open('', 'class="form-horizontal" id="login_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="usr_id"><h6><strong>아이디</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="usr_id" name="usr_id" value="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="password"><h6><strong>비밀번호</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="password" class="form-control" id="password" name="password" value="">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <div class="col-xs-offset-9">
                        <div class="checkbox">
                            <label class="col-xs-13 control-label" for="at">
                                <input type="checkbox" id="at" name="atln" value="1" <?php echo set_checkbox('atln', '1'); ?> />자동로그인
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary btn-sm" id="login">로그인</button>
                        <!--
                        <a href="/schedule_upd/upd/" class="btn btn-default">아이디/비밀번호 찾기</a>
                        -->
                        <a href="/join/" class="btn btn-default btn-sm">회원가입</a>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
