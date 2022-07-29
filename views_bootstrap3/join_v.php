<div class="container">
    <header>
        <h3><p class="text-justify">[Staying M] 회원가입</p></h3>
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
            <label class="col-xs-4 control-label"><h5><strong>약관</h5></strong></label>
            <div class="col-xs-8">
                <textarea class="form-control" rows="5" style="overflow-y:scroll" id="clas" name="clas">
제1조

이 약관은 StayingM에서 제공하는 온라인 서비스(이하 "서비스"라 한다)의 이용조건 및 절차에 관한 사항을 규정함을 목적으로 합니다.

제2조

최소한의 개인정보만을 가지고 있을 생각입니다.(이메일과 비밀번호)
비밀번호는 Hash(bcrypt 알고리즘) 암호화되어 보관됩니다.

제3조
아직은 개발중인 서비스이므로 혹 오동작하더라도 너그럽게 이해 부탁드립니다.
오류 역시 가능한 빨리 수정해 보겠습니다. 감사합니다.
                </textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-3">
                <div class="checkbox">
                    <label class="col-xs-12 control-label" for="clas_agr">
                        <input type="checkbox" id="clas_agr" name="clas_agr" value="1" <?php echo set_checkbox('clas_agr', '1'); ?> />회원가입 약관을 읽었으며 내용에 동의합니다
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-4 control-label" for="password"><h5><strong>아이디(이메일)</h5></strong></label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="usr_id" name="usr_id" placeholder="이메일을 입력하세요" value="<?php echo set_value('usr_id'); ?>" />
            </div>
        </div>

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
                <button type="submit" class="btn btn-primary" id="join_btn">가입</button>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>
