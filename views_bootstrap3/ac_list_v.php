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
                    var act = '/alba/list/' + stnd_yymm + '/' + io_cls + '/page/1';
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

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th style="text-align: center"><h5>계좌주</h5></th>
                    <th style="text-align: center"><h5>계좌구분</h5></th>
                    <th style="text-align: center"><h5>은행명</h5></th>
                    <th style="text-align: center"><h5>계좌번호</h5></th>
                </tr>
            </thead>

            <tbody>
                <?php if (isset($ac_list)) {
                    foreach ($ac_list as $a_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php echo $a_list->ac_owner;?></h6></td>
                    <td align="center"><h6><?php echo $a_list->ac_cls_nm;?></h6></td>
                    <td align="center"><h6><?php echo $a_list->bank_nm;?></h6></td>
                    <td align="center"><a href="/asset/ac_upd/<?php echo $a_list->ac_srno; ?>"><h6><?php echo $a_list->ac_no;?></h6></a></td>
                </tr>
                <?php
                    } }
                ?>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan= "3" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    <th style="vertical-align: middle; text-align: right">
                        <a href="/asset/ac_ins" class="btn btn-primary btn-sm">입력</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
