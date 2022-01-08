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
                var act = '/incm/smmry/' + stnd_yymm + '/page/1';
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
            <?php echo form_open('', 'class="form-inline" id="srch_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="sr-only" for="stnd_yymm"></label>
                    <input type="month" class="form-control" id="stnd_yymm" name="stnd_yymm" value="<?php echo $stnd_yymm;?>">

                    <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                </div>

            </form>
        </div>
    </div>


    <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
        <thead>
            <tr>
                <th style="text-align: center"><h5>숙소</h5></th>
                <th style="text-align: center"><h5>예약율</h5></th>
                <th style="text-align: center"><h5>매출액</h5></th>
                <th style="text-align: center"><h5>현재수입</h5></th>
                <th style="text-align: center"><h5>월말수입</h5></th>
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
                <?php if (strlen($i_list->hsrm_cls) == 0) { ?>
                <td colspan="2" align="center"><h6><?php echo "합  계"; ?></h6></a></td>
                <td align="right"><h6><?php echo number_format($i_list->sell_amt); ?></h6></a></td>
                <td align="right"><h6><?php echo number_format($i_list->amt); ?></h6></a></td>
                <td align="right"><h6><?php echo number_format($i_list->expt_amt); ?></h6></a></td>
                <?php } else { ?>
                <td align="center"><h6><?php echo $i_list->hsrm_cls_nm; ?></h6></a></td>
                <td align="right"><h6><?php echo $i_list->rsv_rt; ?></h6></a></td>
                <td align="right"><h6><?php echo number_format($i_list->sell_amt); ?></h6></a></td>
                <td align="right"><h6><?php echo number_format($i_list->amt); ?></h6></a></td>
                <td align="right"><h6><?php echo number_format($i_list->expt_amt); ?></h6></a></td>
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

    <br>

</div>