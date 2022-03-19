<script>
    function changeclss(){
        var clssSelect = document.getElementById("clss");

        // select element에서 선택된 option의 value가 저장된다.
        var clssValue = clssSelect.options[clssSelect.selectedIndex].value;

        // select element에서 선택된 option의 text가 저장된다.
        var clssText = clssSelect.options[clssSelect.selectedIndex].text;

        if (clssValue == "1")
        {
            //alert(clssValue);
            //$("#uppr_clss option:eq(0)").attr("selected", "selected");
            //alert($("#uppr_clss option").index($("#uppr_clss option:selected")));
            $("#uppr_clss option:eq(0)").prop("selected", true);
            $("#uppr_clss").attr('disabled', 'true');
            $("input[type=text][name=clss_val]").val("1");
        }
        else if (clssValue == '2')
        {
            //alert(clssValue);
            //$("#uppr_clss option:eq(1)").attr("selected", "selected");
            //alert($("#uppr_clss option").index($("#uppr_clss option:selected")));
            $("#uppr_clss option:eq(1)").prop("selected", true);
            $("#uppr_clss option:eq(0)").prop("disabled", true);
            $("#uppr_clss").removeAttr('disabled');
        }
    }

    $(document).ready(function()
    {
        $("#ins_btn").click(function()
        {
            if ($("#clss").val() == '2')
            {
                if ($("#uppr_clss").val() == null)
                {
                    alert('상위분류가 없습니다!(소분류는 상위분류 필수!)');
                    return false;
                }
            }
        });
        
        $("#upd_btn").click(function()
        {
            if ($("#clss").val() == '2')
            {
                if ($("#uppr_clss").val() == null)
                {
                    alert('상위분류가 없습니다!(소분류는 상위분류 필수!)');
                    return false;
                }
            }

            $("#clss").removeAttr('disabled');
            $("#uppr_clss").removeAttr('disabled');
        });
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
            <?php echo form_open('', 'class="form-horizontal" id="tr_cls_nm_reg_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="clss"><h6><strong>분류구분</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="clss" name="clss" onchange="changeclss()" <?php if (strncmp($prcs_cls, 'u', 1) == 0) {echo "disabled";} ?> >
                        <?php foreach($clss_list as $clss_list) { if (strncmp($prcs_cls, 'u', 1) == 0 && $clss == $clss_list->clm_val) { ?>
                        <option value="<?php echo $clss_list->clm_val; ?>" selected>
                        <?php } else { ?>
                        <option value="<?php echo $clss_list->clm_val; ?>"
                        <?php echo set_select('clss', $clss_list->clm_val, False); ?> >
                        <?php } ?>
                        <?php echo $clss_list->clm_val_nm; ?>
                        </option>
                        <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="upper_clss"><h6><strong>상위분류</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="uppr_clss" name="uppr_clss" <?php if (strncmp($clss, '1', 1) == 0) {echo "disabled";} ?> >
                        <?php foreach($uppr_clss_list as $u_clss_list) { if (strncmp($prcs_cls, 'u', 1) == 0 && $view->othr_info == $u_clss_list->clm_val) { ?>
                        <option value="<?php echo $u_clss_list->clm_val; ?>" selected>
                        <?php } else { ?>
                        <option value="<?php echo $u_clss_list->clm_val; ?>"
                        <?php echo set_select('uppr_clss', $u_clss_list->clm_val, False); ?> >
                        <?php } ?>
                        <?php echo $u_clss_list->clm_val_nm; ?>
                        </option>
                        <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="clm_val_nm"><h6><strong>분류명</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="clm_val_nm" name="clm_val_nm" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('clm_val_nm'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('clm_val_nm', $view->clm_val_nm); } ?>">
                    </div>
                </div>

                <input type="hidden" name="clm_val" value="<?php echo $this->uri->segment(3); ?>" >

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <?php if (strncmp($prcs_cls, 'i', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="ins_btn" formaction="/expns_cls/ins">분류 등록</button>
                        <?php } else if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd_btn" formaction="<?php echo '/expns_cls/upd/' . $this->uri->segment(3); ?>">수정</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="del_btn" formaction="<?php echo '/expns_cls/del/' . $this->uri->segment(3); ?>">삭제</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
