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
                    var io_cls = $("#io_cls").val();
                    var stnd_yymm = t_stnd_yymm.replace("-", "");
                    var act = '/asset/ac_bal/' + stnd_yymm + '/page/1';
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
                    <th style="text-align: center"><h5>은행</h5></th>
                    <th style="text-align: center"><h5>계좌번호</h5></th>
                    <th style="text-align: center"><h5>계좌주</h5></th>
                    <th style="text-align: center"><h5>계좌구분</h5></th>
                    <th style="text-align: center"><h5>잔액</h5></th>
                </tr>
            </thead>

            <tbody>
                <?php if (isset($ac_bal_list)) {
                    foreach ($ac_bal_list as $a_list)
                    {
                ?>
                <tr>

                    <?php if (strlen($a_list->bank_nm) == 0) { ?>
                    <td colspan="4" align="center"><h6><?php echo "합  계"; ?></h6></td>
                    <td align="right"><h6><?php echo number_format($a_list->balance);?></h6></td>

                    <?php } else { ?>
                    <td align="center"><h6><?php echo $a_list->bank_nm;?></h6></td>
                    <td align="center"><h6><?php echo $a_list->ac_no;?></h6></td>
                    <td align="center"><h6><?php echo $a_list->ac_owner_nm;?></h6></td>
                    <td align="center"><h6><?php echo $a_list->ac_cls_nm;?></h6></td>
                    <td align="right"><h6><?php echo number_format($a_list->balance);?></h6></td>
                    <?php } ?>
                </tr>
                <?php
                    } }
                ?>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan= "5" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    <!--
                    <th style="vertical-align: middle; text-align: right">
                        <a href="/asset/ac_ins" class="btn btn-primary btn-sm">입력</a>
                    </th>
                    -->
                </tr>
            </tfoot>
        </table>
    </div>
