    <script>
        $(document).ready(function()
        {
            $("#search_btn").click(function()
            {
                var act = '/milla/itm/list/' + $("#cmpny_cls").val() + '/' + $("#lctgr_cls").val() + '/' + $("#ctgr_cls").val() + '/page/1';
                $("#srch_form").attr('action', act).submit();
            });

            $("#download_btn").click(function()
            {
                var act = '/milla/itm/spreadsheet_download/'  + $("#cmpny_cls").val() + '/' + $("#lctgr_cls").val() + '/' + $("#ctgr_cls").val();
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
                     $('#lctgr_cls').find('option').not(':first').remove();
                     //$('#lctgr_cls').find('option').remove();

                     // Add options
                     $.each(response,function(index,data){
                       $('#lctgr_cls').append('<option value="'+data['mt_val']+'">'+data['mt_kor_nm']+'</option>');
                     });
                   }
               });
            });
            //lctgr End

            //ctgr Begin
             $('#lctgr_cls').change(function(){
                 //var cmpny_cls = $(this).val();

                 //alert('<?=base_url()?>milla/fnctn/getLctgr');
                 //alert('CheckPoint01!')

                 //var base_url = <?php echo base_url(); ?>;

                 //alert(base_url);

                 // AJAX request
                 $.ajax({
                   url:'<?=base_url()?>milla/fnctn/getCtgr',
                   method: 'post',
                   data: {'cmpny_cls': encodeURIComponent($("#cmpny_cls").val()),
                          'lctgr_cls': encodeURIComponent($("#lctgr_cls").val()),
                         '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                         },
                   dataType: 'json',
                   success: function(response){

                     // Remove options
                     $('#ctgr_cls').find('option').not(':first').remove();
                     //$('#ctgr_cls').find('option').remove();

                     // Add options
                     $.each(response,function(index,data){
                       $('#ctgr_cls').append('<option value="'+data['mt_val']+'">'+data['mt_kor_nm']+'</option>');
                     });
                   }
               });
            });
            //ctgr End

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

                        <select class="form-control" id="ctgr_cls" name="ctgr_cls">
                            <?php foreach($ctgr_select_list as $ctgr_slist) { if (strcmp($ctgr_cls, $ctgr_slist->mt_val) == 0) { $selected = TRUE; } else { $selected = FALSE; } ?>
                                <option value="<?php echo $ctgr_slist->mt_val; ?>" <?php echo set_select('ctgr_cls', $ctgr_slist->mt_val, $selected)?>><?php echo $ctgr_slist->mt_kor_nm; ?></option>
                            <?php }; ?>
                        </select>

                        <button type="submit" class="btn btn-primary btn-sm" id="search_btn">조회</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="download_btn">Excel Download</button>
                        <!--
                        <a href="/milla/itm/spreadsheet_download/" target="_blank" class="btn btn-primary btn-sm">Excel Download</a>
                        -->
                    </div>
                </form>
            </div>
        </div>

        <table cellspacing="0" cellpadding="0" class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th style="text-align: center"><h5>상품코드</h5></th>
                    <th style="text-align: center"><h5>이전 상품코드</h5></th>
                    <th style="text-align: center"><h5>상품명</h5></th>
                    <th style="text-align: center"><h5>색상</h5></th>
                    <th style="text-align: center"><h5>소재</h5></th>
                    <th style="text-align: center"><h5>사이즈</h5></th>
                    <th style="text-align: center"><h5>무게</h5></th>
                    <th style="text-align: center"><h5>입고가격</h5></th>
                    <th style="text-align: center"><h5>출고가격</h5></th>
                    <th style="text-align: center"><h5>SKU ID</h5></th>
                    <th style="text-align: center"><h5>바코드</h5></th>
                    <!--
                    <th style="text-align: center"><h5>메모</h5></th>
                    -->
                </tr>
            </thead>

            <tbody>
                <?php if (isset($itm_rlist)) {
                    foreach ($itm_rlist as $itm_list)
                    {
                ?>
                <tr>
                    <td align="center"><a href="/milla/itm/upd/<?php echo $itm_list->itm_cd; ?>"><h6><?php echo $itm_list->itm_cd;?></h6></a></td>
                    <td align="center"><h6><?php echo $itm_list->old_itm_cd;?></h6></td>
                    <td align="left"><h6><?php echo $itm_list->itm_nm;?></h6></td>
                    <td align="center"><h6><?php echo $itm_list->color_nm;?></h6></td>
                    <td align="center"><h6><?php echo $itm_list->material;?></h6></td>
                    <td align="center"><h6><?php echo $itm_list->size;?></h6></td>
                    <td align="center"><h6><?php echo number_format($itm_list->wt);?></h6></td>
                    <td align="center"><h6><?php echo number_format($itm_list->in_prc);?></h6></td>
                    <td align="center"><h6><?php echo number_format($itm_list->ot_prc);?></h6></td>
                    <td align="center"><h6><?php echo $itm_list->sku_id;?></h6></td>
                    <td align="center"><h6><?php echo $itm_list->bar_cd;?></h6></td>
                    <!--
                    <td align="center"><h6><?php echo $itm_list->memo;?></h6></td>
                    -->
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
                    <th colspan= "10" style="vertical-align: middle; text-align: left">
                        <?php echo $pagination; ?>
                    </th>
                    <th style="vertical-align: middle; text-align: right">
                        <a href="/milla/itm/ins" class="btn btn-primary btn-sm">입력</a>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
