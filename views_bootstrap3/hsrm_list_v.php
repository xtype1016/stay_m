
<div class="container">
    <!--
    <header>
        <h4><p class="text-warning"><strong>숙소 리스트</strong></p></h4>
    </header>
    -->

    <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
        <thead>
            <tr>
                    <th style="text-align: center"><h5>숙소</h5></th>
                    <th style="text-align: center"><h5>캘린더ID</h5></th>
            </tr>
        </thead>

        <tbody>
            <?php if (isset($hsrm_list)) {
                foreach ($hsrm_list as $h_list)
                {
            ?>
            <tr>
                <td align="center"><a href="/hsrm/upd/<?php echo $h_list->clm_val; ?>"><h6><?php echo $h_list->clm_val_nm;?></h6></a></td>
                <td align="center"><h6><?php echo substr($h_list->othr_info, 0, strpos($h_list->othr_info, '@'));?></h6></a></td>
            </tr>
            <?php
                } }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <th style="vertical-align: middle; text-align: left">
                    <?php if (!isset($pagination)) { $pagination = ''; } echo $pagination; ?>
                </th>
                <th style="vertical-align: middle; text-align: right">
                    <a href="/hsrm/ins" class="btn btn-primary btn-sm">숙소 등록</a>
                </th>
            </tr>
        </tfoot>
    </table>



    <br>
    <br>
    <br>

    <div class="alert alert-danger" role="alert">
        <h6>! 캘린더ID의 @group.calendar.google.com 은 생략하고 보여집니다</h6>
    </div>

</div>