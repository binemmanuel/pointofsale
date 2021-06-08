<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<section class="content">
    <div class="row">
        <div class="col-xs-8">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('enter_info'); ?></h3>
                </div>
                <div class="box-body">

                        <?php echo form_open_multipart("messaging/edittemplate/".$template->id, 'class="validation" name="myform"'); ?>
                        <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Message Type</label>
                                     <?php
                                        $bs = array('sms' => 'SMS',);
                                        echo form_dropdown('type', $bs, set_value('type', $template->type ), 'class="form-control select2" id="type" required="required" style="width:100%;"');
                                      ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label" for="name">Name</label>
                                    <?= form_input('name', set_value('name', $template->name), 'class="form-control input-sm" id="name"');?>
                                </div>
                                
                                <div class="form-group col-md-12">
                                    <label class="control-label" for="message">Message</label>
                                          <?php
                                            $count = 0;
                                            foreach ($shortcodes as $shortcode) {
                                                ?>
                                                <input type="button" name="myBtn" value="<?php echo $shortcode->name; ?>" onClick="addtext1(this);">
                                                <?php
                                                $count+=1;
                                                if ($count === 3) {
                                                    ?>
                                                    <br>
                                                    <?php
                                                }
                                            }
                                            ?>
                                    <?= form_textarea('message', set_value('message',$template->message), 'class="form-control tip " id="message"'); ?>
                                </div>
                        </div>

                        <div class="form-group">
                            <?= form_submit('send_message', 'Edit', 'class="btn btn-primary"'); ?>
                        </div>

                        <?php echo form_close();?>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function addtext1(ele) {
        var fired_button = ele.value;
        document.myform.message.value += fired_button;
    }
</script>