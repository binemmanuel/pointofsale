<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header modal-primary">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <button type="button" class="close mr10" onclick="window.print();"><i class="fa fa-print"></i></button>
            <h4 class="modal-title" id="myModalLabel">
                <?= 'Details of Items Issued'.' # '.$stockadjustment->id; ?>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="col-xs-2"><?= lang('date'); ?></td>
                                    <td class="col-xs-10"><?= $this->tec->hrld($stockadjustment->date); ?></td>
                                </tr>
                                <tr>
                                    <td class="col-xs-2"><?= lang('reference'); ?></td>
                                    <td class="col-xs-10"><?= $stockadjustment->reference; ?></td>
                                </tr>
                                 <tr>
                                    <td class="col-xs-2">Adjusted By</td>
                                    <td class="col-xs-10"><?= $stockadjustment->created_by; ?></td>
                                </tr>
                                <?php
                                if ($stockadjustment->note) {
                                    ?>
                                    <tr>
                                        <td class="col-xs-2"><?= lang('note'); ?></td>
                                        <td class="col-xs-10"><?= $stockadjustment->note; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" style="margin-bottom:0;">
                                <thead>
                                    <tr class="active">
                                        <th><?= lang('product'); ?></th>
                                        <th class="col-xs-2"><?= lang('quantity'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($stockadjustment_items) {
                                        foreach ($stockadjustment_items as $stockadjustment_item) {
                                            echo '<tr>';
                                            echo '<td>'.$stockadjustment_item->product_name.' ('.$stockadjustment_item->product_code.')</td>';
                                    
                                            echo '<td class="text-center">'.$this->tec->formatQuantity($stockadjustment_item->quantity).'</td>';
                                          
                                            echo '</tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                                <thead>
                                    <tr class="active">
                                        <td></td>
                                        <td class="col-xs-2"></td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
