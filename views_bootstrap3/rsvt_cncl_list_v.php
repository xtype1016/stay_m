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
                    //var act = '/rsvt/list/' + $("#stnd_yymm").val() + '/' + $("#hsrm_cls").val() + '/page/1';
                    var act = '/rsvt/cncl_list/' + stnd_yymm + '/' + $("#hsrm_cls").val() + '/page/1';
                    $("#srch_form").attr('action', act).submit();
                }
            });
        });

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

        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo form_open('', 'method="post" class="form-inline" id="srch_form"'); ?>
                    <div class="form-group form-group-sm row">
                        <label class="sr-only" for="stnd_yymm"></label>
                        <input type="month" class="form-control" id="stnd_yymm" name="stnd_yymm" value="<?php echo $stnd_yymm;?>">

                        <label class="sr-only" for="hsrm_cls"></label>
                        <select class="form-control" id="hsrm_cls" name="hsrm_cls">
                        <?php foreach($hsrm_cls_list as $hsrm_l) { if ($hsrm_cls == $hsrm_l->clm_val) { ?>
                        <option value="<?php echo $hsrm_l->clm_val; ?>" selected>
                        <?php } else { ?>
                        <option value="<?php echo $hsrm_l->clm_val; ?>"
                        <?php echo set_select('hsrm_cls', $hsrm_l->clm_val, False); ?> >
                        <?php } ?>
                        <?php echo $hsrm_l->clm_val_nm; ?>
                        </option>
                        <?php } ?>
                        </select>

                        <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th style="text-align: center"><h5>취소일</h5></th>
                    <th style="text-align: center"><h5>시작일</h5></th>
                    <th style="text-align: center"><h5>예약채널</h5></th>
                    <!--
                    <th style="text-align: center"><h5>숙소</h5></th>
                    -->
                    <th style="text-align: center"><h5>고객</h5></th>
                    <th style="text-align: center"><h5>환불금액</h5></th>

                </tr>
            </thead>

            <tbody>
                <?php if (isset($cncl_list)) {
                    foreach ($cncl_list as $c_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php echo $c_list->cncl_dt;?></h6></td>
                    <td align="center"><h6><?php echo $c_list->srt_dt;?></h6></td>
                    <td align="center"><h6><?php echo $c_list->rsv_chnl_cls_nm;?></h6></td>
                    <!--
                    <td align="center"><h6><?php echo $c_list->hsrm_cls_nm;?></h6></td>
                    -->
                    <td align="center"><a href="/rsvt/cncl_upd/<?php echo $c_list->rsv_srno . '/' . $c_list->tr_srno; ?>"><h6><?php echo $c_list->gst_nm;?></h6></a></td>
                    <td align="right"><h6><?php echo number_format($c_list->refund_amt); ?></h6></td>
                </tr>
                <?php
                    }
                ?>
                <?php 
                    }
                ?>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan= "5" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
