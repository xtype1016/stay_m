    <script>
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
                    <th colspan="2" style="text-align: center"><h5>기타거래 항목</h5></th>
                </tr>
            </thead>
    
            <tbody>
                <?php if (isset($etc_incm_itm_list)) {
                    foreach ($etc_incm_itm_list as $e_list)
                    {
                ?>
                <tr>
                    <td align="center"><a href="/etc_incm_itm/upd/<?php echo $e_list->clm_val; ?>"><h6><?php echo $e_list->clm_val_nm;?></h6></a></td>
                    <td align="center"><h6><?php if (1 == $e_list->othr_info) { echo "증가"; } elseif (-1 == $e_list->othr_info) { echo "감소"; } ?></h6></a></td>
                </tr>
                <?php
                    } }
                ?>
            </tbody>
    
            <tfoot>
                <tr>
                    <th style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    <th style="vertical-align: middle; text-align: right">
                        <a href="/etc_incm_itm/ins" class="btn btn-primary btn-sm">항목 등록</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
