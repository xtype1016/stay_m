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
                var act = '/incm/smmry/' + stnd_yymm + '/' + $("#view_cls").val() + '/page/1';
                $("#srch_form").attr('action', act).submit();
            }
        });
    });

</script>

<div class="container">
    <!--
    <header>
        <h4><p class="text-warning"><strong>숙소 리스트</strong></p></h4>
    </header>
    -->

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo form_open('', 'method="post" class="form-inline" id="srch_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="sr-only" for="stnd_yymm"></label>
                    <input type="month" class="form-control" id="stnd_yymm" name="stnd_yymm" value="<?php echo $stnd_yymm;?>">

                    <select class="form-control" id="view_cls" name="view_cls">
                        <option value="1" <?php if (strncmp($view_cls, "1", 1) == 0) { echo "selected"; } ?>>숙소</option>
                        <option value="2" <?php if (strncmp($view_cls, "2", 1) == 0) { echo "selected"; } ?>>예약채널</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                </div>

            <?php echo form_close(); ?>
        </div>
    </div>

    <div class="table-responsive">
    <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                    <?php if (strncmp($view_cls, "1", 1) == 0)
                        {
                    ?>
                    <th style="text-align: center"><h5>숙소</h5></th>
                    <?php } else if (strncmp($view_cls, "2", 1) == 0) { ?>
                    <th style="text-align: center"><h5>채널</h5></th>
                    <?php } ?>
                    <th style="text-align: center"><h5>예약율</h5></th>
                    <th style="text-align: center"><h5>당월 매출</h5></th>
                    <th style="text-align: center"><h5>전년동월매출</h5></th>
                    <th style="text-align: center"><h5>당월 수익</h5></th>
                    <th style="text-align: center"><h5>전년동월수익</h5></th>
                    <th style="text-align: center"><h5>년수익</h5></th>
                    <th style="text-align: center"><h5>전년수익</h5></th>
                </tr>
            </thead>

            <tbody>
                <?php
                    if (isset($incm_smmry))
                    {
                        foreach ($incm_smmry as $i_list)
                        {
                ?>
                <tr>
                    <?php if (strlen($i_list->clm_val_nm) == 0) { ?>
                    <td colspan="2" align="center"><h6><?php echo "합  계"; ?></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->this_year_mon_sell_amt); ?></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->last_year_mon_sell_amt); ?></h6></td>
                    <td align="right"><h6><a href="/incm/list/<?php echo $stnd_yymm; ?>/0"><?php echo number_format($i_list->this_year_mon_amt); ?></h6></a></td>
                    <td align="right"><h6><?php echo number_format($i_list->last_year_mon_amt); ?></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->this_year_amt); ?></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->last_year_amt); ?></h6></td>
                    <?php } else { ?>
                    <td align="center"><h6><?php echo $i_list->clm_val_nm; ?></h6></td>
                    <td align="right"><h6><?php echo $i_list->rsv_rt; ?></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->this_year_mon_sell_amt); ?></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->last_year_mon_sell_amt); ?></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->this_year_mon_amt); ?></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->last_year_mon_amt); ?></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->this_year_amt); ?></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->last_year_amt); ?></h6></td>
                </tr>
                <?php
                            }
                        }
                    }
                ?>
            </tbody>

            <tfoot>
            </tfoot>
        </table>
        </div>
    <br>

</div>