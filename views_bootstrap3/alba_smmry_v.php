    <script>
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
                    <th style="text-align: center"><h5>직원</h5></th>
                    <th style="text-align: center"><h5>입금</h5></th>
                    <th style="text-align: center"><h5>출금</h5></th>
                    <th style="text-align: center"><h5>잔액</h5></th>
                </tr>
            </thead>
    
            <tbody>
                <?php if (isset($alba_io_smmry_list)) {
                    foreach ($alba_io_smmry_list as $e_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php echo $e_list->emp_no_nm;?></h6></td>
                    <td align="right"><h6><?php echo number_format($e_list->incm_amt);?></h6></td>
                    <td align="right"><h6><?php echo number_format($e_list->expn_amt);?></h6></td>
                    <td align="right"><h6><?php echo number_format($e_list->blnc_amt);?></h6></td>
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
