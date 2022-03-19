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
                var gst_nm = $("#gst_nm").val();
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

    <h5><strong><kbd><?php echo $gst_info->gst_nm . '(' . $gst_info->phone_num_fm . ')'; ?></kbd></strong><h5>
    <br>

    <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
        <thead>
            <tr>
                <th style="text-align: center"><h5>숙소</h5></th>
                <th style="text-align: center"><h5>시작일</h5></th>
                <th style="text-align: center"><h5>종료일</h5></th>
                <th style="text-align: center"><h5>취소</h5></th>
                <th style="text-align: center"><h5>고객구성</h5></th>
            </tr>
        </thead>

        <tbody>
            <?php if (isset($gst_dtl_list)) {
                foreach ($gst_dtl_list as $g_list)
                {
            ?>
            <tr>
                <td align="center"><a href="/rsvt/upd/<?php echo $g_list->rsv_srno; ?>"><h6><?php echo $g_list->hsrm_cls_nm;?></h6></a></td>
                <td align="center"><h6><?php echo $g_list->srt_dt;?></h6></td>
                <td align="center"><h6><?php echo $g_list->end_dt;?></h6></td>
                <td align="center"><h6><?php echo $g_list->cncl_yn;?></h6></td>
                <td align="center"><h6><?php echo $g_list->gst_desc;?></h6></td>
            </tr>
            <?php
                } }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan= "4" style="vertical-align: middle; text-align: left">
                    <?php echo $pagination; ?>
                </th>
                <th style="vertical-align: middle; text-align: right">
                    <a href="/gst/upd/<?php echo $gst_no; ?>" class="btn btn-primary btn-sm">고객정보변경</a>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
