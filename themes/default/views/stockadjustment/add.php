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
                        <?php echo form_open_multipart("stockadjustment/add"); ?>
                        <div class="row">
                            <div class="col-md-12 well">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= lang('date', 'date'); ?>
                                            <?= form_input('date', set_value('date', date('Y-m-d H:i')), 'class="form-control tip" id="date"  required="required" readonly'); ?>
                                        </div>
                                    </div>
                            </div>
                            <div class="col-md-12">
                               <!--    <div class="form-group">
                                    <label>Search item below</label>
                                        <input type="text" placeholder="<?= lang('search_product_by_name_code'); ?>" id="add_item" class="form-control">
                                    </div> -->

                                    <div class="row">
                                        <div class="col-md-12">
                                                 <div id="repeater" class="form-group col-md-12 well">
                                                    <br>
                                                     <div class="form-group col-md-12 repeater-heading">
                                                            <button class="btn btn-success repeater-add-btn"><i class="fa fa-plus"></i> Add Item</button>
                                                    </div>
                                                    <div class="items">
                                                        <div class="item-content">
                                                            <div class="form-group col-md-4">
                                                                <select class="form-control select2" name="product_id[]" data-name="product_id[]" id="product_id" required>
                                                                    <option value="">Select Item</option>
                                                                    <?php foreach($products as $item):?>
                                                                    <option value="<?php echo $item->id;?>"><?php echo $item->name.'***['.$this->tec->formatQuantity($item->quantity).']';?></option>
                                                                    <?php endforeach;?>
                                                                </select>
                                                            </div>
                                                   
                                                             <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <select data-skip-name="true" data-name="adjustmenttype[]" class="form-control" id="adjustmenttype">
                                                                        <option value="subtraction">Subtraction</option>
                                                                        <option value="addition">Addition</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                             <div class="form-group col-md-1">
                                                                <input data-skip-name="true" name="quantity[]" data-name="quantity[]" type="text" id="quantity" placeholder="Qty" value="" class="pa form-control quantity"
                                                                required="required"/>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                     <input data-skip-name="true" data-name="reason[]" type="text" id="reason" value="" placeholder="reason" class="pa form-control reason"/>  
                                                                </div>
                                                        
                                                            </div>
                                                             <div class="col-md-1" style="" align="center">
                                                                    <button id="remove-btn" onclick="$(this).parents('.items').remove()" class="btn btn-danger">X</button>
                                                             </div>  
                                                        </div>

                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="col-md-12 well">
                                <div class="form-group">
                                    <?= lang("note", 'note'); ?>
                                    <?= form_textarea('note', set_value('note'), 'class="form-control redactor" id="note"'); ?>
                                </div>
                            </div>
                        </div>
                      
                        <div class="form-group">
                            <?= form_submit('add_storeissue', 'Save', 'class="btn btn-lg btn-success"'); ?>
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

 <script>
    $(document).ready(function(){

        $('#repeater').createRepeater();

    });
        
    </script>
