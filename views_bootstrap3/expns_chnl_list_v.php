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
                    <th style="text-align: center"><h5>지출 매체</h5></th>
                </tr>
            </thead>
    
            <tbody>
                <?php if (isset($expns_chnl_list)) {
                    foreach ($expns_chnl_list as $e_list)
                    {
                ?>
                <tr>
                    <td colspan="2" align="center"><a href="/expns_chnl/upd/<?php echo $e_list->clm_val; ?>"><h6><?php echo $e_list->clm_val_nm;?></h6></a></td>
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
                        <a href="/expns_chnl/ins" class="btn btn-primary btn-sm">매체 등록</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
