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
                var view_cls = $("#view_cls").val();
                var act = '/expns/smmry/' + view_cls + '/' + stnd_yymm + '/page/1';
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
                        <option value="1">필요</option>
                        <option value="2">매체</option>
                        <option value="3" selected>분류</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>


    <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
        <thead>
            <tr>
                <th style="text-align: center"><h5>대분류</h5></th>
                <th style="text-align: center"><h5>소분류</h5></th>
                <th style="text-align: center"><h5>금액</h5></th>
            </tr>
        </thead>

        <tbody>
            <?php
                if (isset($expns_cls_smmry))
                {
                    foreach ($expns_cls_smmry as $e_list)
                    {
                        if (isset($e_list->key_val))
                        {
                            $href = "<a href=/expns/dtl/3/" . $e_list->key_val .">";
                            $h_end = "</a>";
                        }
                        else
                        {
                            $href = "";
                            $h_end = "";
                        }
            ?>
            <tr>
                <td align="center"><h6><?php echo $e_list->upp_cls_nm;?></h6></td>
                <td align="center"><h6><?php echo $e_list->clm_val_nm;?></h6></td>
                <td align="right"><?php echo $href; ?><h6><?php echo number_format($e_list->amt);?></h6><?php echo $h_end; ?></td>
            </tr>
            <?php
                    }
                }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan= "2" style="vertical-align: middle; text-align: left">
                    <?php echo $pagination; ?>
                </th>
                <th style="vertical-align: middle; text-align: right">
                    <a href="/expns/ins/3" class="btn btn-primary btn-sm">입력</a>
                </th>
            </tr>
        </tfoot>
    </table>
</div>