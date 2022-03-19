<script>
    $(document).ready(function()
    {
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

    <br>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo form_open('', 'class="form-inline" id="srch_form"'); ?>
                <div class="form-group form-group-sm row">
                    <label class="sr-only" for="stnd_dt"></label>
                    <input type="date" class="form-control" id="stnd_dt" name="stnd_dt" value="<?php echo $stnd_dt;?>">

                    <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                </div>

            </form>
        </div>
    </div>

    <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
        <thead>
            <tr>
                <th style="text-align: center"><h5>숙소</h5></th>
                <th style="text-align: center"><h5>고객명</h5></th>
                <th style="text-align: center"><h5>전화번호</h5></th>
                <th style="text-align: center"><h5>고객구성</h5></th>
                <th style="text-align: center"><h5>숙박일수</h5></th>
            </tr>
        </thead>

        <tbody>
            <?php if (isset($chckin_list)) {
                foreach ($chckin_list as $c_list)
                {
            ?>
            <tr>
                <td align="center"><h6><?php echo $c_list->hsrm_nm;?></h6></td>
                <td align="center"><h6><?php echo $c_list->gst_nm;?></h6></td>
                <td align="center"><h6><?php echo $c_list->phone_num;?></h6></td>
                <td align="center"><h6><?php echo $c_list->gst_desc;?></h6></td>
                <td align="center"><h6><?php echo $c_list->stay_days;?></h6></td>
            </tr>
            <?php
                } }
            ?>
        </tbody>

        <tfoot>
            <tr>
            </tr>
        </tfoot>
    </table>
</div>
