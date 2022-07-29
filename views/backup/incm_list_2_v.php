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
                    var act = '/incm/incm_list/' + stnd_yymm + '/page/1';
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
                    <th style="text-align: center"><h5>내역</h5></th>
                    <th style="text-align: center"><h5>금액</h5></th>
                </tr>
            </thead>
    
            <tbody>
                <?php if (isset($incm_list)) {
                    foreach ($incm_list as $e_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php echo $e_list->stnd_dt;?></h6></td>
                    <td align="center"><h6><?php echo $e_list->incm_cls_nm;?></h6></td>
                    <td align="center"><a href="/incm/upd/<?php echo $e_list->incm_srno; ?>"><h6><?php echo $e_list->memo;?></h6></a></td>
                    <td align="right"><h6><?php echo number_format($e_list->amt);?></h6></td>
                </tr>
                <?php
                    } }
                ?>
            </tbody>
    
            <tfoot>
                <tr>
                    <th colspan= "3" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    <th style="vertical-align: middle; text-align: right">
                        <a href="/incm/ins" class="btn btn-primary btn-sm">입력</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
