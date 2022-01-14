
    <section class="h-100 py-2">
        <div class="container h-100">
            <div class="row justify-content-sm-center h-100">
                <div class="col">
                    <div class="card shadow-lg">
                        <div class="card-body p-2">
                            <h1 class="fs-5 card-title fw-bold mb-4 border-bottom">매출 및 수입</h1>
                            <?php echo form_open('', 'class="needs-validation" novalidate" id="search_form"'); ?>
                                <div class="row mb-2 ms-auto py-1">
                                    <input type="month" class="form-control-sm col-auto" id="stnd_yymm" name="stnd_yymm" value="<?php echo $stnd_yymm;?>">
                                    <select class="form-control-sm col-auto" id="view_cls" name="view_cls">
                                        <option value="1" <?php if (strncmp($view_cls, "1", 1) == 0) { echo "selected"; } ?>>숙소</option>
                                        <option value="2" <?php if (strncmp($view_cls, "2", 1) == 0) { echo "selected"; } ?>>예약채널</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary active col-auto">
                                        조회
                                    </button>
                                </div>
                            </form>

                            <div class="table-responsive-md">
                                <table class="table table-sm table-striped header-fixed">
                                        <thead class="table-dark fs-6">
                                            <tr>
                                                <?php if (strncmp($view_cls, "1", 1) == 0)
                                                    {
                                                ?>
                                                <th scope="col" align="center">숙소</th>
                                                <?php } else if (strncmp($view_cls, "2", 1) == 0) { ?>
                                                <th scope="col" align="center">채널</th>
                                                <?php } ?>
                                                <th scope="col" align="center">예약율</th>
                                                <th scope="col" align="center">당월 매출</th>
                                                <th scope="col" align="center">전년동월매출</th>
                                                <th scope="col" align="center">당월 수익</th>
                                                <th scope="col" align="center">전년동월수익</th>
                                                <th scope="col" align="center">년수익</th>
                                                <th scope="col" align="center">전년수익</th>
                                            </tr>
                                        </thead>

                                        <tbody class="fs-6">
                                            <?php
                                                if (isset($incm_smmry))
                                                {
                                                    foreach ($incm_smmry as $i_list)
                                                    {
                                            ?>
                                            <tr>
                                                <?php if (strlen($i_list->clm_val_nm) == 0) { ?>
                                                <td scope="row" colspan="2" align="center"><?php echo "합  계"; ?></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->this_year_mon_sell_amt); ?></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->last_year_mon_sell_amt); ?></td>
                                                <td scope="row" align="right"><a href="/incm/list/<?php echo $stnd_yymm; ?>/0"><?php echo number_format($i_list->this_year_mon_amt); ?></a></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->last_year_mon_amt); ?></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->this_year_amt); ?></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->last_year_amt); ?></td>
                                                <?php } else { ?>
                                                <td scope="row" align="center"><?php echo $i_list->clm_val_nm; ?></td>
                                                <td scope="row" align="right"><?php echo $i_list->rsv_rt; ?></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->this_year_mon_sell_amt); ?></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->last_year_mon_sell_amt); ?></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->this_year_mon_amt); ?></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->last_year_mon_amt); ?></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->this_year_amt); ?></td>
                                                <td scope="row" align="right"><?php echo number_format($i_list->last_year_amt); ?></td>
                                            </tr>
                                            <?php
                                                        }
                                                    }
                                                }
                                            ?>
                                        </tbody>

                                        <tfoot>
                                        </tfoot>
                                    </table>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

