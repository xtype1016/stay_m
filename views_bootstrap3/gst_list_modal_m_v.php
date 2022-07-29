        <script>
            function getCookie(name) {
                var nameOfCookie = name + "=";
                var x = 0;

                while ( x <= document.cookie.length) {
                    var y = (x + nameOfCookie.length);

                    if (document.cookie.substring(x, y) == nameOfCookie) {
                        if (( endOfCookie = document.cookie.indexOf(";", y)) == -1)
                            endOfCookie = document.cookie.length;

                        return unescape(document.cookie.substring(y, endOfCookie));
                    }

                    x = document.cookie.indexOf(" ", x) + 1;

                    if ( x == 0)

                    break;
                }
            }

            $(function() {
                $("#search_btn").click(function () {
                    //alert('Click Test!');
                    $.ajax({
                        url: "/gst/srch",
                        type: "POST",
                        data: {
                            "srch_gst_nm": encodeURIComponent($("#srch_gst_nm").val()),
                            "csrf_tk_nm": getCookie('csrf_ck_nm')
                        },
                        dataType: "html",
                        complete: function(xhr, textStatus) {
                            if (textStatus == 'success') 
                            {
                                $("#list_table").html(xhr.responseText);
                            }
                        }
                    });
                });
            });


        </script>

        <div class="modal-header">
        </div>
        <div class="modal-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php echo form_open('', 'class="form-inline" id="srch_form"'); ?>
                        <div class="form-group form-group-sm row">
                            <label class="sr-only" for="gst_nm"></label>
                            <input type="text" class="form-control" id="srch_gst_nm" name="srch_gst_nm" value="">

                            <input class="btn btn-primary" type="button" id="search_btn" value="조회" />
                        </div>

                    <?php echo form_close(); ?>
                </div>
            </div>

            <div id="list_table">
                <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th style="text-align: center"><h5>고객명</h5></th>
                            <th style="text-align: center"><h5>전화번호</h5></th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="2" style="vertical-align: middle; text-align: left">
                                <?php if (!isset($pagination)) { $pagination = ''; } echo $pagination; ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>  <!-- list_table -->
            
        </div>  <!-- modal-body -->

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal" id="modalclose">닫기</button>
        </div>
