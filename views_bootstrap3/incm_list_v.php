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
                var act = '/incm/list/' + stnd_yymm + '/' + $("#view_cls").val() + '/page/1';
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
            <form class="form-inline" method="post" action="" id="srch_form">
                <div class="form-group form-group-sm row">
                    <label class="sr-only" for="stnd_yymm"></label>
                    <input type="month" class="form-control" id="stnd_yymm" name="stnd_yymm" value="<?php echo $stnd_yymm;?>">

                    <select class="form-control" id="view_cls" name="view_cls">
                        <option value="0" <?php if (strncmp($view_cls, "0", 1) == 0) { echo "selected"; } ?>>전체</option>
                        <option value="1" <?php if (strncmp($view_cls, "1", 1) == 0) { echo "selected"; } ?>>블로그&기타</option>
                        <option value="2" <?php if (strncmp($view_cls, "2", 1) == 0) { echo "selected"; } ?>>AirBnb</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                </div>

                <div class="form-group">
                    <label class="sr-only" for="csrf_name">csrf_name</label>
                    <input type="hidden" class="form-control" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>


    <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
        <thead>
            <tr>
                <th style="text-align: center"><h5>거래일자</h5></th>
                <th style="text-align: center"><h5>고객명</h5></th>
                <th style="text-align: center"><h5>거래구분</h5></th>
                <th style="text-align: center"><h5>금액</h5></th>
            </tr>
        </thead>

        <tbody>
            <?php
                if (isset($incm_list))
                {
                    $acnt_bal = 0;
                    foreach ($incm_list as $i_list)
                    {
            ?>
            <tr>
                <?php if (strlen($i_list->tr_dt) == 0) { ?>
                <td colspan="3" align="center"><h6><?php echo "합  계"; ?></h6></td>
                <td align="right"><h6><?php echo number_format($i_list->amt); ?></h6></td>

                <tr>
                <td colspan="3" align="center"><h6><?php echo "계좌 잔고"; ?></h6></td>
                <td align="right"><h6><?php echo number_format($i_list->acnt_bal); ?></h6></td>
                </tr>

                <?php } else { ?>
                <td align="center"><h6><?php echo $i_list->stnd_tr_dt; ?></h6></td>
                <td align="center"><h6><?php echo $i_list->gst_nm; ?></h6></td>
                <td align="center"><h6><?php echo $i_list->tr_cls_nm; ?></h6></td>
                <td align="right"><h6><?php echo number_format($i_list->amt); ?></h6></td>
            </tr>
            <?php
                        }
                    } ?>
                <?php
                }
            ?>
        </tbody>

        <tfoot>
            <th colspan="4" style="vertical-align: middle; text-align: left">
                <?php echo $pagination; ?>
            </th>
        </tfoot>
    </table>
</div>