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


    $(document).ready(function()
    {
        $("#search_btn").click(function()
        {
            if ($("#memo").val() == '')
            {
                alert('내역을 입력하세요!!');
                return false;
            }
            else
            {
                var view_cls = $("#view_cls").val();
                //var memo = encodeURIComponent($("#memo").val());
                //var stnd_yymm = t_stnd_yymm.replace("-", "");
                var act = '/fix_expns/list/' + view_cls + '/page/';
                $("#srch_form").attr('action', act).submit();
            }
        });
    });

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

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo form_open('', 'method="post" class="form-inline" id="srch_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="sr-only" for="view_cls"></label>
                    <select class="form-control" id="view_cls" name="view_cls">
                        <option value="1" <?php if (strncmp($view_cls, "1", 1) == 0) { echo "selected"; } ?>>현금 & 카드지출</option>
                        <option value="2" <?php if (strncmp($view_cls, "2", 1) == 0) { echo "selected"; } ?>>현금 & 카드결제</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                </div>

            </form>
        </div>
    </div>

    <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
        <thead>
            <tr>
                <th style="text-align: center"><h5>지출명</h5></th>
                <th style="text-align: center"><h5>그룹</h5></th>
                <th style="text-align: center"><h5>지출일</h5></th>
                <th style="text-align: center"><h5>매체</h5></th>
                <th style="text-align: center"><h5>분류</h5></th>
                <?php if (!$this->agent->is_mobile()) { ?> <th style="text-align: center"><h5>구입처</h5></th> <?php } ?>
                <?php if (!$this->agent->is_mobile()) { ?> <th style="text-align: center"><h5>메모</h5></th> <?php } ?>
                <?php if (!$this->agent->is_mobile()) { ?> <th style="text-align: center"><h5>연결은행</h5></th> <?php } ?>
                <?php if (!$this->agent->is_mobile()) { ?> <th style="text-align: center"><h5>연결계좌</h5></th> <?php } ?>
                <th style="text-align: center"><h5>금액</h5></th>
                <?php
                if (strncmp($view_cls, "2", 1) == 0)
                { ?>
                    <th style="text-align: center"><h5>합계</h5></th>
                <?php
                } ?>
            </tr>
        </thead>

        <tbody>
            <?php if (isset($fix_expns_list)) {
                $total_sum = 0;
                foreach ($fix_expns_list as $e_list)
                {
            ?>
            <tr>
                <td align="center"><a href="/fix_expns/upd/<?php echo $e_list->fix_expns_srno; ?>"><h6><?php echo $e_list->expns_nm;?></h6></a></td>
                <td align="center"><h6><?php echo $e_list->expns_group_no;?></h6></td>
                <td align="center"><h6><?php echo $e_list->expns_day;?></h6></td>
                <td align="center"><h6><?php echo $e_list->expns_chnl_cls_nm;?></h6></td>
                <td align="center"><h6><?php echo $e_list->expns_cls_nm;?></h6></td>
                <?php if (!$this->agent->is_mobile()) { ?><td align="center"><h6><?php echo $e_list->whr_to_buy;?></h6></td> <?php } ?>
                <?php if (!$this->agent->is_mobile()) { ?><td align="left"><h6><?php echo $e_list->memo;?></h6></td> <?php } ?>
                <?php if (!$this->agent->is_mobile()) { ?><td align="left"><h6><?php echo $e_list->bank_nm;?></h6></td> <?php } ?>
                <?php if (!$this->agent->is_mobile()) { ?><td align="left"><h6><?php echo $e_list->ac_no;?></h6></td> <?php } ?>
                <td align="right"><h6><?php echo number_format($e_list->amt);?></h6></td>
                <?php
                    if (strncmp($view_cls, "2", 1) == 0 && $e_list->rownum == 1)
                    {
                        $sum_amt = number_format($e_list->sum_amt);
                        echo "<td rowspan = $e_list->rowspan_val align='right'><h6>$sum_amt</h6></td>";
                    }
                    $total_sum = $total_sum + $e_list->amt;
                ?>
            </tr>
            <?php
                }
                 ?>
                    <tr>
                    <td align="right" colspan=<?php if ($this->agent->is_mobile()) { if (strncmp($view_cls, "1", 1) == 0) { echo '5'; } else { echo '6'; } } else { if (strncmp($view_cls, "1", 1) == 0) { echo '9'; } else if (strncmp($view_cls, "2", 1) == 0) { echo '10'; } } ?>><h5>합 계</h5></td>
                    <td align="right"><h5><?php echo number_format($total_sum);?></h5></td>
                    </tr>
                <?php

            }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <!--
                <th colspan=<?php if ($this->agent->is_mobile()) { if (strncmp($view_cls, "1", 1) == 0) { echo '5'; } else { echo '6'; } } else if (strncmp($view_cls, "1", 1) == 0) { echo '9'; } else if (strncmp($view_cls, "2", 1) == 0) { echo '10'; }?>  style="vertical-align: middle; text-align: left">
                    <?php echo $pagination; ?>
                </th>
                -->
                <th style="vertical-align: middle; text-align: right" colspan=<?php if ($this->agent->is_mobile()) { if (strncmp($view_cls, "1", 1) == 0) { echo '6'; } else { echo '7'; } } else if (strncmp($view_cls, "1", 1) == 0) { echo '10'; } else if (strncmp($view_cls, "2", 1) == 0) { echo '11'; }?>  style="vertical-align: middle; text-align: left">
                    <a href="/fix_expns/ins" class="btn btn-primary btn-sm">입력</a>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
