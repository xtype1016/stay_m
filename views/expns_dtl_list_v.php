    <script>
        $(document).on("touchstart", function(){ });

        // 접속 기기 분리 Begin
        var filter = "win16|win32|win64|mac|macintel";
        if (filter.indexOf(navigator.platform.toLowerCase()) < 0)
        {
            //MOBILE
        }
        else
        {
            //PC
        }
        // 접속 기기 분리 End
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
                    <th style="text-align: center"><h5>지출일자</h5></th>
                    <th style="text-align: center"><h5><?php if (strncmp($view_cls, "3", 1) != 0) { echo "분류"; } else { echo "지출매체"; } ?></h5></th>
                    <th style="text-align: center"><h5>내역</h5></th>
                    <!--
                    <?php if (strncmp($view_cls, "chnl", 4) != 0) { ?>
                        <th style="text-align: center"><h5>구입처</h5></th>
                    <?php } ?>
                    -->
                    <?php if (!$this->agent->is_mobile()) { ?>
                        <th style="text-align: center"><h5>구입처</h5></th>
                        <th colspan="2" style="text-align: center"><h5>금액</h5></th>
                    <?php } else { ?>
                        <th style="text-align: center"><h5>금액</h5></th>
                    <?php } ?>
                </tr>
            </thead>

            <tbody>
                <?php if (isset($expns_dtl_list)) {
                    foreach ($expns_dtl_list as $e_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php echo $e_list->stnd_expns_dt;?></h6></td>
                    <td align="center"><h6><?php if (strncmp($view_cls, "3", 1) != 0) { echo $e_list->expns_cls_nm; } else { echo $e_list->expns_chnl_cls_nm; } ?></h6></td>
                    <td align="center"><h6><?php if ($this->agent->is_mobile()) { echo $e_list->memo; } else { echo $e_list->memo_whole; } ?></h6></td>
                    <!--
                    <?php if (strncmp($view_cls, "chnl", 4) != 0) { ?>
                        <td align="center"><h6><?php echo $e_list->whr_to_buy;?></h6></td>
                    <?php } ?>
                    -->
                    <?php if (!$this->agent->is_mobile()) { ?>
                        <td align="center"><h6><?php echo $e_list->whr_to_buy;?></h6></td>
                    <?php } ?>
                    <td align="right"><a href="/expns/upd/<?php echo $e_list->expns_srno; ?>"><h6><?php echo number_format($e_list->amt);?></h6></a></td>

                    <?php if (!$this->agent->is_mobile()) { ?>
                        <td align="right"><h6><?php if ($e_list->sum_amt <> $e_list->amt) { echo number_format($e_list->sum_amt); } ?></h6></td>
                    <?php } ?>
                </tr>
                <?php
                    } }
                ?>
            </tbody>

            <tfoot>
                <tr>
                    <!--
                    <th colspan="3" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    -->

                    <?php if (!$this->agent->is_mobile()) { ?>
                        <th colspan="5" style="vertical-align: middle; text-align: left">
                    <?php } else { ?>
                        <th colspan="3" style="vertical-align: middle; text-align: left">
                    <?php } ?>
                    <?php echo $pagination; ?>
                    </th>

                    <th style="vertical-align: middle; text-align: right">
                        <a href="<?php if (isset($_SESSION['expns_smmry_uri'])) { echo '/' . $_SESSION['expns_smmry_uri']; } else { echo '/expns/smmry/'; } ; ?>" class="btn btn-default btn-sm">목록</a>
                        <!--
                        <a href="<?php echo '/expns/smmry/' . $view_cls . '/' . $stnd_yymm . '/' . $this->uri->segment(5); ?>" class="btn btn-default btn-sm">목록</a>
                        <a hef="<?php echo '/expns/ins/d/' . $view_cls; ?>" class="btn btn-primary btn-sm">입력</a>
                        -->
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
