    <script>
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

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
            </thead>

            <tbody>
                <tr>
                    <td align="center"><h5>구글 캘린더 인증이 완료되었습니다!</h5></td>
                </tr>
            </tbody>

            <tfoot>
            </tfoot>
        </table>
    </div>
