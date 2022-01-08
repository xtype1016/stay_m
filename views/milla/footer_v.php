    <footer id="footer">
        <nav class="navbar navbar-fixed-bottom">
            <div class="container">
                <div class="btn-group btn-group-sm dropup" role="group">
                    <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="glyphicon glyphicon-list" aria-hidden="true"></span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu scrollable-menu" role="menu">
                        <li><a href="/milla/itm/list">상품 관리</a></li>
                        <li><a href="/milla/itm/apprvl">--쿠팡 승인처리</a></li>
                        <li><a href="/milla/itm/shppng">--배송대행 정보관리</a></li>
                        <li><a href="/milla/coupang_list/">--입고 처리</a></li>
                        <li><a href="/milla/coupang_list/">--출고 처리</a></li>
                    </ul>
                </div>

                <div class="btn-group btn-group-sm dropup" role="group">
                    <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        설정
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu scrollable-menu" role="menu">
                        <li><a href="/milla/ctgr/list/">상품 분류 관리</a></li>
                        <!--
                        <li><a href="/setting/usr_pswd_upd">비밀번호 변경</a></li>
                        -->
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
