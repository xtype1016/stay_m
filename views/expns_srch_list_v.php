<script>
    $(document).ready(function()
    {
        $("#search_btn").click(function()
        {
            if ($("#memo").val() == '')
            {
                alert('내역을 입력하세요!!');
                return false;
            }
            else
            {
                var memo = $("#memo").val();
                var view_cls = $("#view_cls").val();
                //var memo = encodeURIComponent($("#memo").val());
                //var stnd_yymm = t_stnd_yymm.replace("-", "");
                var act = '/expns/srch/' + memo + '/' + view_cls + '/page/';
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
                    <label class="sr-only" for="memo"></label>
                    <input type="text" class="form-control" id="memo" name="memo" value="<?php if (!isset($memo)) { $memo = ''; } echo set_value('memo', $memo);?>">
                    <select class="form-control" id="view_cls" name="view_cls">
                        <option value="2" <?php if (isset($view_cls) && strncmp($view_cls, "2", 1) == 0) { echo "selected"; } ?>>지출</option>
                        <option value="1" <?php if (isset($view_cls) && strncmp($view_cls, "1", 1) == 0) { echo "selected"; } ?>>수입</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                </div>

            </form>
        </div>
    </div>

    <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
        <thead>
            <tr>            
                <th style="text-align: center"><h5>일자</h5></th>
                <th style="text-align: center"><h5>구분</h5></th>
                <th style="text-align: center"><h5>내역</h5></th>
                <!--
                <th style="text-align: center"><h5>구입처</h5></th>
                -->
                <th style="text-align: center"><h5>금액</h5></th>
            </tr>
        </thead>

        <tbody>
            <?php if (isset($expns_srch_list)) {
                foreach ($expns_srch_list as $e_list)
                {
            ?>
            <tr>
                <td align="center"><h6><?php echo $e_list->stnd_dt;?></h6></td>
                <td align="center"><h6><?php echo $e_list->io_cls_nm;?></h6></td>
                <td align="center"><h6><?php echo $e_list->memo_whole;?></h6></td>
                <td align="right"><?php if (strncmp($e_list->io_cls, '1', 1) == 0) { echo '<a href=/io_tr/upd/' . $e_list->key_val . '>'; } else { echo '<a href=/expns/upd/' . $e_list->key_val . '>'; } ?> <h6><?php echo number_format($e_list->amt);?></h6></a></td>
                

            </tr>
            <?php
                } } else if ($total_rows == 0) { alert_log("expns_srch_list_v", "일치하는 지출이 없습니다!"); }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="4" style="vertical-align: middle; text-align: left">
                    <?php if (isset($expns_srch_list)) { echo $pagination; } ?>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
