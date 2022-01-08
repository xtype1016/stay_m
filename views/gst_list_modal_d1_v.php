                <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th style="text-align: center"><h5>고객명</h5></th>
                            <th style="text-align: center"><h5>전화번호</h5></th>
                        </tr>
                    </thead>
    
                    <tbody>
                        <?php if (isset($gst_list)) {
                            foreach ($gst_list as $g_list)
                            {
                        ?>
                        <tr>
                            <td align="center"><a href="javascript:closeWin( '<?php echo $g_list->gst_no; ?>', '<?php echo $g_list->gst_nm; ?>' );"><h6><?php echo $g_list->gst_nm;?></h6></a></td>
                            <td align="center"><h6><?php echo $g_list->phone_num;?></h6></td>
                        </tr>
                        <?php
                            } }
                        ?>
                    </tbody>
    
                    <tfoot>
                        <tr>
                            <th colspan="2" style="vertical-align: middle; text-align: left">
                                <?php echo $pagination; ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>