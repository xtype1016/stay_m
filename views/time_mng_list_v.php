    <script>
        $(document).ready(function()
        {
            $("#search_btn").click(function()
            {
                if ($("#dt_fr").val() == '')
                {
                    alert('기준년월을 입력하세요!');
                    return false;
                }
                else
                {
                    var t_stnd_dt = $("#stnd_dt").val();
                    var stnd_dt = t_stnd_dt.replace(/-/g, '');
                    var usr = $("#usr").val();
                    var time_cls = $("#time_cls").val();
                    var view_cls = $("#view_cls").val();
                    var act = '/time_mng/list/' + stnd_dt + '/' + usr + '/' + time_cls + '/' + view_cls + '/page/1';
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
                        <label class="sr-only" for="stnd_dt"></label>
                        <input type="date" class="form-control" id="stnd_dt" name="stnd_dt" value="<?php echo $stnd_dt;?>">

                        <select class="form-control" id="usr" name="usr">
                            <?php foreach($usr_list as $dd_list) { if (strcmp($usr, $dd_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $dd_list->clm_val; ?>" <?php echo set_select('usr', $dd_list->clm_val, $selected)?>><?php echo $dd_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>

                        <select class="form-control" id="time_cls" name="time_cls">
                            <?php foreach($time_cls_list as $t_list) { if (strcmp($time_cls, $t_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $t_list->clm_val; ?>" <?php echo set_select('time_cls', $t_list->clm_val, $selected)?>><?php echo $t_list->clm_val_nm; ?></option>
                            <?php }; ?>
                        </select>

                        <select class="form-control" id="view_cls" name="view_cls">
                            <option value="1" <?php if (strncmp($view_cls, "1", 1) == 0) { echo "selected"; } ?>>일별</option>
                            <option value="2" <?php if (strncmp($view_cls, "2", 1) == 0) { echo "selected"; } ?>>주별</option>
                        </select>

                        <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                    </div>

                <?php echo form_close(); ?>
            </div>
        </div>

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th style="text-align: center"><h5>일자</h5></th>
                    <th style="text-align: center"><h5>사용자</h5></th>
                    <th style="text-align: center"><h5>시간구분</h5></th>
                    <th style="text-align: center"><h5>시간</h5></th>
                    <th style="text-align: center"><h5>메모</h5></th>
                </tr>
            </thead>

            <tbody>
                <?php if (isset($time_mng_list)) {
                    foreach ($time_mng_list as $main_list)
                    {
                ?>
                <tr>
                    <?php if (strcmp($main_list->stnd_dt, "9999-12-31") == 0) { ?>
                    <td colspan="3" align="right"><h6><?php echo $main_list->time_cls_nm . " 합계";?></h6></td>
                    <td align="right"><h6><?php echo number_format($main_list->time);?></h6></td>
                    <td align="center"><h6><?php echo $main_list->memo;?></h6></td>
                    <?php } else { ?>
                    <td align="center"><h6><?php echo $main_list->stnd_dt; ?></h6></td>
                    <td align="center"><h6><?php echo $main_list->usr_nm;?></h6></td>
                    <td align="center"><a href="/time_mng/upd/<?php echo $main_list->srno; ?>"><h6><?php echo $main_list->time_cls_nm;?></h6></a></td>
                    <td align="right"><h6><?php echo number_format($main_list->time);?></h6></td>
                    <td align="center"><h6><?php echo $main_list->memo;?></h6></td>
                </tr>
                <?php
                    } } }
                ?>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan= "4" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    <th style="vertical-align: middle; text-align: right">
                        <a href="/time_mng/ins" class="btn btn-primary btn-sm">입력</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
