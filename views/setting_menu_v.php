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
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    기초자료/설정 등록
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu scrollable-menu" role="menu">
                    <li><a href="/hsrm/list">[숙소] 숙소 관리</a></li>
                    <li><a href="/etc_incm_itm/list">[숙소] 기타거래항목 관리</a></li>
                    <li class="divider"></li>
                    <li><a href="/expns_chnl/list">[지출] 매체 관리</a></li>
                    <li><a href="/expns_cls/list">[지출] 분류 관리</a></li>
                    <li class="divider"></li>
                    <li><a href="/setting/usr_pswd_upd">[사용자] 비밀번호 변경</a></li>
                    <li><a href="/setting/shr_usr_reg">[사용자] 공유 사용자 등록</a></li>
                    <li><a href="/setting/shr_usr_list">[사용자] 공유 사용자 조회</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
