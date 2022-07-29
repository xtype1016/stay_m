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
                    var io_cls = $("#io_cls").val();
                    var stnd_yymm = t_stnd_yymm.replace("-", "");
                    var act = '/alba/list/' + stnd_yymm + '/' + io_cls + '/page/1';
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
                        
                        <select class="form-control" id="io_cls" name="io_cls">
                            <?php foreach($io_cls_list as $i_list) { if (strcmp($io_cls, $i_list->clm_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $i_list->clm_val; ?>" <?php echo set_select('io_cls', $i_list->clm_val, $selected)?>><?php echo $i_list->clm_val_nm; ?></option>
                            <?php }; ?>
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
                    <th style="text-align: center"><h5>분류</h5></th>
                    <th style="text-align: center"><h5>직원</h5></th>
                    <th style="text-align: center"><h5>내역</h5></th>
                    <th style="text-align: center"><h5>금액</h5></th>
                </tr>
            </thead>
    
            <tbody>
                <?php if (isset($alba_io_list)) {
                    foreach ($alba_io_list as $e_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php echo $e_list->stnd_dt;?></h6></td>
                    <td align="center"><h6><?php echo $e_list->io_cls_nm;?></h6></td>
                    <td align="center"><h6><?php echo $e_list->emp_no_nm;?></h6></td>
                    <td align="center"><a href="/alba/upd/<?php echo $e_list->srno; ?>"><h6><?php echo $e_list->memo;?></h6></a></td>
                    <td align="right"><h6><?php echo number_format($e_list->amt);?></h6></td>
                </tr>
                <?php
                    } }
                ?>
            </tbody>
    
            <tfoot>
                <tr>
                    <th colspan= "4" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    <th style="vertical-align: middle; text-align: right">
                        <a href="/alba/ins" class="btn btn-primary btn-sm">입력</a>
                        <a href="/alba/smmry" class="btn btn-primary btn-sm">잔액</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
