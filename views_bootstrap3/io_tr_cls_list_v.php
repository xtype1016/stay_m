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
                    <th style="text-align: center"><h5>입출금거래구분</h5></th>
                    <th style="text-align: center"><h5>입출금거래구분명</h5></th>
                </tr>
            </thead>
    
            <tbody>
                <?php if (isset($io_tr_list)) {
                    foreach ($io_tr_list as $i_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php if (strncmp(substr($i_list->clm_val, 0, 1), "1", 1) == 0) { echo "수입"; } else { echo "지출"; };?></h6></a></td>
                    <td align="center"><a href="/io_tr/cls_upd/<?php echo $i_list->clm_val; ?>"><h6><?php echo $i_list->clm_val_nm;?></h6></a></td>
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
                        <a href="/io_tr/cls_ins" class="btn btn-primary btn-sm">입출금거래구분 등록</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
