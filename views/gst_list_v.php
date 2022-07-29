<script>
    $(document).ready(function()
    {
        $("#search_btn").click(function()
        {
            if ($("#gst_nm").val() == '')
            {
                alert('고객명을 입력하세요!');
                return false;
            }
            else
            {
                //var gst_nm = $("#gst_nm").val();
                var gst_nm = encodeURIComponent($("#gst_nm").val());
                //var stnd_yymm = t_stnd_yymm.replace("-", "");
                var act = '/gst/list/' + gst_nm + '/page/1';
                $("#srch_form").attr('action', act).submit();
            }
        });
    });

    $(document).on("touchstart", function(){ });
</script>

<div class="container">
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
            <?php echo form_open('', 'method="post" class="form-inline" id="srch_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="sr-only" for="gst_nm"></label>
                    <input type="text" class="form-control" id="gst_nm" name="gst_nm" value="<?php if (!isset($gst_nm)) { $gst_nm = ''; } echo set_value('gst_nm', $gst_nm);?>">

                    <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                </div>

            <?php echo form_close(); ?>
        </div>
    </div>

    <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
        <thead>
            <tr>
                <th style="text-align: center"><h5>고객명</h5></th>
                <th style="text-align: center"><h5>전화번호</h5></th>
            </tr>
        </thead>

        <tbody>
            <?php
                //info_log("gst_list_v", "_SESSION['mbl_cls']  = [" . $_SESSION['mbl_cls'] . "]");
                if (strncmp($_SESSION['mbl_cls'], "iphone", 6) == 0 || strncmp($_SESSION['mbl_cls'], "ipad", 4) == 0)
                {
                    $separator = "&";
                }
                else if (strncmp($_SESSION['mbl_cls'], "android", 7) == 0)
                {
                    $separator = "?";
                }
                //info_log("gst_list_v", "separator  = [" . $separator . "]");
            ?>
            <?php if (isset($gst_list)) {
                foreach ($gst_list as $g_list)
                {
            ?>
            <tr>
                <td align="center"><a href="/gst/dtl/<?php echo $g_list->gst_no; ?>"><h6><?php echo $g_list->gst_nm;?></h6></a></td>
                <?php if (isset($separator)) { ?>
                        <td align="center"><?php echo "<a href=sms:" . $g_list->phone_num . $separator . "body=>"; ?><h6><?php echo $g_list->phone_num;?></h6></a></td>
                    <?php } else { ?>
                        <td align="center"><h6><?php echo $g_list->phone_num; ?></h6></td>
                    <?php } ?>
            </tr>
            <?php
                } } else if ($total_rows == 0) { alert_log("gst_list_v", "일치하는 고객이 없습니다!"); }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <th style="vertical-align: middle; text-align: left">
                    <?php if (!isset($pagination)) { $pagination = ''; } echo $pagination; ?>
                </th>
                <th style="vertical-align: middle; text-align: right">
                    <a href="/gst/ins" class="btn btn-primary btn-sm">고객 등록</a>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
