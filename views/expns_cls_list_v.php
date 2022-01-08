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
    
        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed table-responsive">
            <colgroup>
                <col style="width: 25%;"/>
                <col style="width: 25%;"/>
                <col style="width: 25%;"/>
                <col style="width: 25%;"/>
            </colgroup>
            <thead>
                <tr>
                    <th colspan="2" style="text-align: center"><h5>대분류</h5></th>
                    <th colspan="2" style="text-align: center"><h5>소분류</h5></th>
                </tr>
            </thead>
    
            <tbody>
                <?php if (isset($expns_cls_list)) {
                    foreach ($expns_cls_list as $e_list)
                    {
                ?>
                <tr>
                    <td colspan="2" align="center"><a href="/expns_cls/upd/<?php echo $e_list->clm_val_l; ?>"><h6><?php echo $e_list->clm_val_nm_l;?></h6></a></td>
                    <td colspan="2" align="center"><a href="/expns_cls/upd/<?php echo $e_list->clm_val_s; ?>"><h6><?php echo $e_list->clm_val_nm_s;?></h6></a></td>
                </tr>
                <?php
                    } }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    <th colspan="1" style="vertical-align: middle; text-align: right">
                        <a href="/expns_cls/ins" class="btn btn-primary btn-sm">분류 등록</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
