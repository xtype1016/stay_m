<script>
    function inputNumberFormat(obj) {
    obj.value = comma(uncomma(obj.value));
    }

    function comma(str) {
        str = String(str);
        return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
    }

    function uncomma(str) {
        str = String(str);
        return str.replace(/[^\d]+/g, '');
    }
</script>

<div class="container">
    <header>
        <!--
        <h3><p class="text-justify">예약 등록</p></h3>
        -->
    </header>

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
            <form class="form-horizontal" method="post" action="" id="setting_reg_form">
                <div class="form-group form-group-sm row">
                    <label class="col-xs-4 control-label" for="shr_usr_id"><h6><strong>DB 공유사용자ID</h6></strong></label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="shr_usr_id" name="shr_usr_id" value="<?php if (strncmp($prcs_cls, 'i', 1) == 0) { echo set_value('shr_usr_id'); } else if (strncmp($prcs_cls, 'u', 1) == 0) { echo set_value('shr_usr_id', $view->shr_usr_id); } ?>" placeholder="DB를 공유할 ID(E-mail)을 등록해 주세요!">
                    </div>
                </div>

                <input type="hidden" name="evnt_id" value="<?php if (strncmp($prcs_cls, 'i', 1) != 0) { echo $view->evnt_id; } else { echo ''; } ?>" >
                <input type="hidden" name="rsv_srno" value="<?php if (strncmp($prcs_cls, 'u', 1) == 0) { echo $rsv_srno; } else { echo ''; } ?>" >
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

                <div class="form-group form-group-sm row">
                    <div class="pull-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            기초자료 등록<span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                            <li><a href="/hsrm/list">[숙소] 숙소 관리</a></li>
                            <li><a href="/etc_incm_itm/list">[숙소] 기타거래항목 관리</a></li>
                            <li class="divider"></li>
                            <li><a href="/expns_cls/list">[지출] 분류 관리</a></li>
                            <li><a href="/expns_chnl/list">[지출] 매체 관리</a></li>
                          </ul>
                        </div>
                        <?php if (strncmp($prcs_cls, 'u', 1) == 0) { ?>
                            <button type="submit" class="btn btn-primary btn-sm" id="upd" formaction="/setting/upd/">설정 저장</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
