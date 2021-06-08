<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('enter_info'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="col-lg-12">
                        <?php echo form_open_multipart("storeissues/add", 'class="validation"'); ?>
                        <div class="row">
                            <div class="col-md-6 well">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?= lang('date', 'date'); ?>
                                            <?= form_input('date', set_value('date', date('Y-m-d H:i')), 'class="form-control tip" id="date"  required="required" readonly'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?= lang('reference', 'reference'); ?>
                                            <input type="text" name="reference" class="form-control tip" value="<?php echo $this->tec->randomref('ISH',7); ?>" readonly>
                                          
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Staff</label>
                                            <?php
                                            $stf[''] = lang("select")." ".'Staff';
                                            foreach($staffs as $staff) {
                                                $stf[$staff->id] = $staff->first_name.' '.$staff->last_name;
                                            }
                                            ?>
                                            <?= form_dropdown('staff', $stf, set_value('staff'), 'class="form-control select2 tip" id="staff"  required="required" style="width:100%;"'); ?>
                                        </div>
                                    </div>

                                <div class="form-group">
                                    <?= lang("note", 'note'); ?>
                                    <?= form_textarea('note', set_value('note'), 'class="form-control redactor" id="note"'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                  <div class="form-group">
                                    <label>Search item below</label>
                                        <input type="text" placeholder="<?= lang('search_product_by_name_code'); ?>" id="add_item" class="form-control">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="poTable" class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr class="active">
                                                            <th><?= lang('product'); ?></th>
                                                            <th class="col-xs-2"><?= lang('quantity'); ?></th>
                                                            <th class="col-xs-2">Issue as</th>
                                                            <th style="width:25px;"><i class="fa fa-trash-o"></i></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="5"><?= lang('add_product_by_searching_above_field'); ?></td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="active">
                                                            <th><?= lang('total'); ?></th>
                                                            <th class="col-xs-2"></th>
                                                            <th class="col-xs-2"></th>

                                                            <th style="width:25px;"></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                      
                        <div class="form-group">
                            <?= form_submit('add_storeissue', 'Issue Items', 'class="btn btn-lg btn-success"'); ?>
                            <button type="button" id="reset" class="btn btn-lg btn-danger"><?= lang('reset'); ?></button>
                        </div>

                        <?php echo form_close();?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="<?= $assets ?>dist/js/repeater.js" type="text/javascript"></script>

<script type="text/javascript">
    var spoitems = {};
    if (localStorage.getItem('remove_spo')) {
        if (localStorage.getItem('spoitems')) {
            localStorage.removeItem('spoitems');
        }
        localStorage.removeItem('remove_spo');
    }
</script>
<script src="<?= $assets ?>dist/js/storeissues.min.js" type="text/javascript"></script>
