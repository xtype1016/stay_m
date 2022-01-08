    <script>
        $(document).ready(function()
        {
            $("#search_btn").click(function()
            {
                var act = '/milla/ctgr/list/' + $("#cmpny_cls").val() + '/' + $("#lctgr_cls").val() + '/page/1';
                $("#srch_form").attr('action', act).submit();
            });

            //lctgr Begin
             $('#cmpny_cls').change(function(){
                 //var cmpny_cls = $(this).val();

                 //alert('<?=base_url()?>milla/fnctn/getLctgr');
                 //alert('CheckPoint01!')

                 //var base_url = <?php echo base_url(); ?>;

                 //alert(base_url);

                 // AJAX request
                 $.ajax({
                   url:'<?=base_url()?>milla/fnctn/getLctgr',
                   method: 'post',
                   data: {'cmpny_cls': encodeURIComponent($("#cmpny_cls").val()),
                         '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                         },
                   dataType: 'json',
                   success: function(response){

                     // Remove options
                     //$('#lctgr_cls').find('option').not(':first').remove();
                     $('#lctgr_cls').find('option').remove();

                     // Add options
                     $.each(response,function(index,data){
                       $('#lctgr_cls').append('<option value="'+data['mt_val']+'">'+data['mt_kor_nm']+'</option>');
                     });
                   }
               });
            });
            //lctgr End

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

                        <select class="form-control" id="cmpny_cls" name="cmpny_cls">
                            <?php foreach($cmpny_select_list as $cmpny_slist) { if (strcmp($cmpny_cls, $cmpny_slist->mt_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $cmpny_slist->mt_val; ?>" <?php echo set_select('cmpny_cls', $cmpny_slist->mt_val, $selected)?>><?php echo $cmpny_slist->mt_kor_nm; ?></option>
                            <?php }; ?>
                        </select>

                        <select class="form-control" id="lctgr_cls" name="lctgr_cls">
                            <?php foreach($lctgr_select_list as $lctgr_slist) { if (strcmp($lctgr_cls, $lctgr_slist->mt_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $lctgr_slist->mt_val; ?>" <?php echo set_select('lctgr_cls', $lctgr_slist->mt_val, $selected)?>><?php echo $lctgr_slist->mt_kor_nm; ?></option>
                            <?php }; ?>
                        </select>

                        <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                    </div>
                </form>
            </div>
        </div>

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th style="text-align: center"><h5>소분류</h5></th>
                    <th style="text-align: center"><h5>분류코드</h5></th>
                    <th style="text-align: center"><h5>메모</h5></th>
                </tr>
            </thead>

            <tbody>
                <?php if (isset($ctgr_cls_list)) {
                    foreach ($ctgr_cls_list as $ctgr_list)
                    {
                ?>
                <tr>
                    <td align="center"><a href="/milla/ctgr/upd/<?php echo $ctgr_list->cmpny_cls . '/' . $ctgr_list->mt_val; ?>"><h6><?php echo $ctgr_list->mt_kor_nm;?></h6></a></td>
                    <td align="center"><h6><?php echo $ctgr_list->addtn_info;?></h6></td>
                    <td align="center"><h6><?php echo $ctgr_list->memo;?></h6></td>
                </tr>
                <?php
                    }
                ?>
                <?php
                    }
                ?>
            </tbody>

            <tfoot>
                <tr>
                    <th colspan= "2" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    <th style="vertical-align: middle; text-align: right">
                        <a href="/milla/ctgr/ins" class="btn btn-primary btn-sm">입력</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
