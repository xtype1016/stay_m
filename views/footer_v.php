    <footer id="footer">
        <nav class="navbar navbar-fixed-bottom">
            <div class="container">
                <div class="btn-group btn-group-sm dropup" role="group">
                    <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="glyphicon glyphicon-list" aria-hidden="true"></span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu scrollable-menu" role="menu">
                        <?php if (isset($_SESSION['usr_no'])) { if (strcmp($_SESSION['usr_no'], "0000000006") != 0) { ?>
                            <li><a href="/incm/smmry/">수익 조회</a></li>
                            <?php if (isset($_SESSION['usr_no'])) { if (strcmp($_SESSION['db_no'], "0000000002") == 0) { ?>
                            <li><a href="/rsvt/prc/">가격 조회</a></li>
                            <?php }  } ?>
                            <?php if (isset($_SESSION['usr_no'])) { if (strcmp($_SESSION['usr_no'], "0000000005") != 0) { ?>
                            <li><a href="/gst/list/">고객 관리</a></li>
                            <?php }  } ?>
                            <li><a href="/rsvt/list/">예약 관리</a></li>
                            <li><a href="/rsvt/cncl_list/">예약취소 조회</a></li>
                            <li><a href="/etc_incm/list/">기타 거래 조회</a></li>
                            <li><a href="/chckin/list/">체크인 고객 정보</a></li>
                            <?php if (isset($_SESSION['usr_no'])) { if (strcmp($_SESSION['db_no'], "0000000002") == 0) { ?>
                            <li class="divider"></li>
                            <li><a href="/expns/smmry/">지출 내역</a></li>
                            <!--
                            <li><a href="/incm/incm_list">수입 내역</a></li>
                            -->
                            <li><a href="/expns/srch/">수입/지출 검색</a></li>
                            <li><a href="/fix_expns/list/1">고정지출 조회</a></li>
                            <li><a href="/io_tr/smmry/">입출금거래 조회</a></li>
                            <li><a href="/asset/ac_bal/">계좌잔고 조회</a></li>
                            <li class="divider"></li>
                            <li><a href="/alba/list/">아이들 용돈 조회</a></li>
                            <li><a href="/time_mng/list/">아이들 시간 관리</a></li>
                            <?php }  } ?>
                        <?php }  } ?>
                        <?php if (isset($_SESSION['usr_no'])) { if (strcmp($_SESSION['usr_no'], "0000000006") == 0) { ?>
                            <li><a href="/time_mng/list/">아이들 시간 관리</a></li>
                        <?php }  } ?>
                    </ul>
                </div>

                <div class="btn-group btn-group-sm dropup" role="group">
                    <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        설정
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu scrollable-menu" role="menu">
                        <?php if (isset($_SESSION['usr_no'])) { if (strcmp($_SESSION['usr_no'], "0000000006") != 0) { ?>
                            <li><a href="/hsrm/list">숙소 관리</a></li>
                            <li><a href="/etc_incm_itm/list">기타거래항목 관리</a></li>
                            <?php if (isset($_SESSION['usr_no'])) { if (strcmp($_SESSION['usr_no'], "0000000001") == 0) { ?>
                            <li><a href="/holyday_mng/dtl">휴일 관리</a></li>
                            <?php }  } ?>
                            <li><a href="/season_mng/list">시즌 관리</a></li>
                            <?php if (isset($_SESSION['usr_no'])) { if (strcmp($_SESSION['db_no'], "0000000002") == 0) { ?>
                            <li class="divider"></li>
                            <li><a href="/expns_chnl/list">지출매체 관리</a></li>
                            <li><a href="/expns_cls/list">지출분류 관리</a></li>
                            <li><a href="/io_tr/cls_list">입출금거래 분류 관리</a></li>
                            <li><a href="/asset/ac_list">계좌 관리</a></li>
                            <?php }  } ?>
                            <li class="divider"></li>
                            <li><a href="/setup/g_acnt">구글계정 등록</a></li>
                            <!--
                            <li><a href="/setting/usr_pswd_upd">비밀번호 변경</a></li>
                            <li><a href="/setting/shr_usr_reg">공유 사용자 관리</a></li>
                            -->
                        <?php }  } ?>
                    </ul>
                </div>

                <div class="pull-right">
                    <?php $this->benchmark->mark('code_end'); ?>
                    수행시간: <?php echo $this->benchmark->elapsed_time('code_start', 'code_end'); ?>
                </div>
            </div>
        </nav>
    </footer>


<!-- Modal -->
<div class="modal fade" id="gst_srch" tabindex="-1" role="dialog" aria-labelledby="gst_SrchLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <!-- Content will be loaded here from "remote.php" file -->
    </div>
  </div>
</div>

</body>



</html>
