<script>

    $(document).ready(function()
    {
        $("#search_btn").click(function()
        {
            if ($("#stnd_dt").val() == '')
            {
                alert('일자 입력하세요!');
                return false;
            }
            else
            {
                var stnd_dt = $("#stnd_dt").val();
                //var stnd_yymm = t_stnd_yymm.replace("-", "");
                var act = '/holyday_mng/dtl/' + stnd_dt + '/page/1';
                $("#srch_form").attr('action', act).submit();
            }
        });
    });

    $(document).on("touchstart", function(){ });
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
                <?php echo form_open('', 'method="get" class="form-inline" id="srch_form"'); ?>
                    <div class="form-group form-group-sm row">
                        <input type="date" class="form-control" id="stnd_dt" name="stnd_dt" value="<?php echo $stnd_dt;?>">

                        <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                    </div>
                <?php echo form_close(); ?>
        
            <?php echo form_open('', 'class="form-horizontal" id="holyday_reg_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="dt"><h6><strong>일자</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="dt" name="dt" autocomplete="off" value="<?php echo $stnd_dt;?>">
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="dt_cls"><h6><strong>구분</h6></strong></label>
                    <div class="col-xs-8">
                        <select class="form-control" id="dt_cls" name="dt_cls">
                            <?php foreach($dt_cls_list as $d_list) { if (isset($dt_list) && strcmp($dt_list->dt_cls, $d_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $d_list->clm_val; ?>" <?php echo set_select('dt_cls', $d_list->clm_val, $selected)?>><?php echo $d_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="/holyday_mng/upd">수정</button>
                    </div>
                </div>

            <?php echo form_close(); ?>

        </div>
    </div>
</div>
