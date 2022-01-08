    <script>
        $(document).ready(function()
        {
            $("#search_btn").click(function()
            {
                if ($("#stnd_yy").val() == '')
                {
                    alert('기준년월을 입력하세요!');
                    return false;
                }
                else
                {
                    var t_stnd_yy = $("#stnd_yy").val();
                    //var io_cls = $("#io_cls").val();
                    var stnd_yy = t_stnd_yy.replace("-", "");
                    //var act = '/season_mng/list/' + stnd_yy + '/' + io_cls + '/page/1';
                    var act = '/season_mng/list/' + stnd_yy + '/page/1';
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
                        <label class="sr-only" for="stnd_yy"></label>
                        <input type="yy" class="form-control" id="stnd_yy" name="stnd_yy" value="<?php echo $stnd_yy;?>">

                        <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                    </div>

                </form>
            </div>
        </div>

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th style="text-align: center"><h5>시작일자</h5></th>
                    <th style="text-align: center"><h5>종료일자</h5></th>
                    <th style="text-align: center"><h5>분류</h5></th>
                </tr>
            </thead>

            <tbody>
                <?php if (isset($season_list)) {
                    foreach ($season_list as $e_list)
                    {
                ?>
                <tr>
                    <td align="center"><h6><?php echo $e_list->srt_dt;?></h6></td>
                    <td align="center"><h6><?php echo $e_list->end_dt;?></h6></td>
                    <td align="center"><a href="/season_mng/upd/<?php echo $e_list->season_srno; ?>"><h6><?php echo $e_list->season_cls_nm;?></h6></a></td>
                </tr>
                <?php
                    } }
                ?>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan= "2" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    <th style="vertical-align: middle; text-align: right">
                        <a href="/season_mng/ins" class="btn btn-primary btn-sm">입력</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
