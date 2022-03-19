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
                    //var act = '/rsvt/list/' + stnd_yymm + '/' + $("#hsrm_cls").val() + '/page/1';
                    var act = '/rsvt/list/' + stnd_yymm + '/' + $("#hsrm_cls").val() + '/' + $("#rsv_chnl_cls").val() + '/' + $("#view_cls").val() + '/page/1';
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
                        <input type="month" class="form-control" id="stnd_yymm" name="stnd_yymm" value="<?php echo $stnd_yymm;?>">

                        <select class="form-control" id="hsrm_cls" name="hsrm_cls">
                            <?php foreach($hsrm_cls_list as $hsrm_l) { if (strcmp($hsrm_cls, $hsrm_l->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $hsrm_l->clm_val; ?>" <?php echo set_select('hsrm_cls', $hsrm_l->clm_val, $selected)?>><?php echo $hsrm_l->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>

                        <select class="form-control" id="rsv_chnl_cls" name="rsv_chnl_cls">
                            <?php foreach($rsv_chnl_cls_list as $rsv_chnl_l) { if (strcmp($rsv_chnl_cls, $rsv_chnl_l->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $rsv_chnl_l->clm_val; ?>" <?php echo set_select('rsv_chnl_cls', $rsv_chnl_l->clm_val, $selected)?>><?php echo $rsv_chnl_l->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>
                        
                        <select class="form-control" id="view_cls" name="view_cls">
                            <option value="1" <?php if (strncmp($view_cls, "1", 1) == 0) { echo "selected"; } ?>>예약일자</option>
                            <option value="2" <?php if (strncmp($view_cls, "2", 1) == 0) { echo "selected"; } ?>>확정일자</option>
                        </select>

                        <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                    </div>
                </form>
            </div>
        </div>

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th style="text-align: center"><h5>숙소</h5></th>
                    
                    <th style="text-align: center"><h5><?php if (strcmp($view_cls, '1') == 0) { echo '시작일'; } else if (strcmp($view_cls, '2') == 0) { echo '확정일'; } ?></h5></th>
                    <th style="text-align: center"><h5>고객</h5></th>
                    <th style="text-align: center"><h5>예약채널</h5></th>
                </tr>
            </thead>

            <tbody>
                <?php if (isset($rsvt_list)) {
                    foreach ($rsvt_list as $r_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php echo $r_list->hsrm_cls_nm;?></h6></td>
                    <td align="center"><h6><?php if (strcmp($view_cls, '1') == 0) { echo $r_list->srt_dt; } else if (strcmp($view_cls, '2') == 0) { echo $r_list->cnfm_dt; } ?></h6></td>
                    <td align="center"><a href="/rsvt/upd/<?php echo $r_list->rsv_srno; ?>"><h6><?php echo $r_list->gst_nm;?></h6></a></td>
                    <td align="center"><h6><?php echo $r_list->rsv_chnl_cls_nm;?></h6></td>
                </tr>
                <?php
                    } }
                ?>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan= "3" style="vertical-align: middle; text-align: left;">
                        <?php echo $pagination; ?>
                    </th>
                    <th style="vertical-align: middle; text-align: right;">
                        <a href="/rsvt/ins" class="btn btn-primary btn-sm">예약 등록</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
