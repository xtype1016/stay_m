    <script>
        $(document).ready(function()
        {
            $("#search_btn").click(function()
            {
                if ($("#stnd_yymm").val() == '')
                {
                    alert('기준년월을 입력하세요!');
                    return false;
                }
                else
                {
                    var t_stnd_yymm = $("#stnd_yymm").val();
                    var stnd_yymm = t_stnd_yymm.replace("-", "");
                    var act = '/etc_incm/list/' + stnd_yymm + '/page/1';
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
                        <label class="sr-only" for="stnd_yymm"></label>
                        <input type="month" class="form-control" id="stnd_yymm" name="stnd_yymm" value="<?php echo $stnd_yymm;?>">
                        <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                    </div>

                </form>
            </div>
        </div>

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th style="text-align: center"><h5>거래일</h5></th>
                    <th style="text-align: center"><h5>고객</h5></th>
                    <th style="text-align: center"><h5>거래구분</h5></th>
                    <th style="text-align: center"><h5>금액</h5></th>
                </tr>
            </thead>

            <tbody>
                <?php if (isset($etc_incm_list)) {
                    foreach ($etc_incm_list as $e_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php echo $e_list->tr_dt;?></h6></td>
                    <td align="center"><a href="/etc_incm/upd/<?php echo $e_list->rsv_srno . '/' . $e_list->tr_srno; ?>"><h6><?php echo $e_list->gst_nm;?></h6></a></td>
                    <td align="center"><h6><?php echo $e_list->tr_cls_nm;?></h6></td>
                    <td align="right"><h6><?php echo number_format($e_list->amt);?></h6></td>
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
                </tr>
            </tfoot>
        </table>
    </div>
