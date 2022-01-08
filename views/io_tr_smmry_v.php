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
                var act = '/io_tr/smmry/' + stnd_yymm + '/page/1';
                $("#srch_form").attr('action', act).submit();
            }
        });

        $("#viewhidden1").click(function ()
        {
            $(".hidden_obj1").toggleClass("hidden");
        });

        $("#viewhidden2").click(function ()
        {
            $(".hidden_obj2").toggleClass("hidden");
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
            <?php echo form_open('', 'method="get" class="form-inline" id="srch_form"'); ?>
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
                <th style="text-align: center"><h5>구분</h5></th>
                <th style="text-align: center"><h5></h5></th>
                <th style="text-align: center"><h5></h5></th>
                <th style="text-align: center"><h5>금액</h5></th>
            </tr>
        </thead>

        <tbody>
            <?php
                if (isset($io_tr_smmry))
                {
                    foreach ($io_tr_smmry as $i_list)
                    {
            ?>

            <?php if (strncmp($i_list->groupby_key, "1", 1) == 0 && strlen($i_list->io_tr_nm) == 0)
                  { ?>
                    <tr>
                    <td align="center"><a href="#" id="viewhidden1" onclick="return false;"><h6><?php echo "입금 합계"; ?></h6></a></td>
                    <td align="center"><h6></h6></td>
                    <td align="center"><h6></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->amt); ?></h6></td>
            <?php }
                  else if (strncmp($i_list->groupby_key, "2", 1) == 0 && strlen($i_list->io_tr_nm) == 0)
                  { ?>
                    <tr>
                    <td align="center"><a href="#" id="viewhidden2" onclick="return false;"><h6><?php echo "출금 합계"; ?></h6></a></td>
                    <td align="center"><h6></h6></td>
                    <td align="center"><h6></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->amt); ?></h6></td>
            <?php }
                  else if (strncmp($i_list->groupby_key, "0", 1) == 0 || strncmp($i_list->groupby_key, "9", 1) == 0)
                  { ?>
                    <tr>
                    <td align="center"><h6><?php echo $i_list->io_tr_nm; ?></h6></td>
                    <td align="center"><h6></h6></td>
                    <td align="center"><h6></h6></td>
                    <td align="right"><h6><?php echo number_format($i_list->amt); ?></h6></td>
            <?php }
                  else
                  {
                      if (strncmp($i_list->groupby_key, "1", 1) == 0 && strlen($i_list->io_tr_nm) != 0)
                      { ?>
                        <tr class="hidden_obj1 hidden">
                <?php }
                      else if (strncmp($i_list->groupby_key, "2", 1) == 0 && strlen($i_list->io_tr_nm) != 0)
                      { ?>
                        <tr class="hidden_obj2 hidden">
                <?php }
                      else
                      { ?>
                        </tr>
                <?php } ?>
                    <td align="center"><h6></h6></td>
                    <td align="left"><?php if (strcmp($i_list->io_tr_cls, "201") == 0) { echo '<a href=/expns/dtl/2/' . str_replace('-', '', $stnd_yymm) . '/01/page/>'; } else {echo '<a href=/io_tr/upd/' . $i_list->io_tr_srno . '>';} ?>
                                           <h6><?php echo $i_list->io_tr_nm ; ?></h6></a></td>
                    <td align="right"><h6><?php echo number_format($i_list->amt); ?></h6></td>
                    <td align="center"><h6></h6></td>
            <?php } ?>
            </tr>
            <?php
                    }
                }
            ?>
        </tbody>

        <tfoot>
                <th colspan="4" style="vertical-align: middle; text-align: right">
                    <a href="/io_tr/ins" class="btn btn-primary btn-sm">입출금 입력</a>
                </th>
        </tfoot>
    </table>
    <br>

</div>
