<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title"><?= lang('update_info'); ?></h3>
        </div>
        <div class="box-body">
          <?php echo form_open("customers/edit/".$customer->id);?>

          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="code"><?= $this->lang->line("name"); ?></label>
              <?= form_input('name', set_value('name', $customer->name), 'class="form-control input-sm" id="name"'); ?>
            </div>

            <div class="form-group">
              <label class="control-label" for="email_address"><?= $this->lang->line("email_address"); ?></label>
              <?= form_input('email', set_value('email', $customer->email), 'class="form-control input-sm" id="email_address"'); ?>
            </div>

            <div class="form-group">
              <label class="control-label" for="phone"><?= $this->lang->line("phone"); ?></label>
              <?= form_input('phone', set_value('phone', $customer->phone), 'class="form-control input-sm" id="phone"');?>
            </div>

            <div class="form-group">
              <label class="control-label" for="address">Address</label>
              <?= form_input('address', set_value('address', $customer->address), 'class="form-control input-sm" id="address"'); ?>
            </div>

            <div class="form-group">
              <label class="control-label" for="customerno">Customer No</label>
              <?= form_input('customerno', set_value('customerno', $customer->customerno), 'class="form-control input-sm" id="customerno"');?>
            </div>


            <div class="form-group">
              <?php echo form_submit('edit_customer', $this->lang->line("edit_customer"), 'class="btn btn-primary"');?>
            </div>
          </div>
          <?php echo form_close();?>
        </div>
      </div>
    </div>
  </div>
</section>
