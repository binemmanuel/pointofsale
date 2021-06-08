<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i>
            </button>
            <h4 class="modal-title" id="myModalLabel">Send Message to </h4>
        </div>
        <?= form_open_multipart("messaging/customer/"); ?>
        <div class="modal-body">
                     <?php echo form_open_multipart("messaging/new", 'class="validation" name="myform" id="myform"'); ?>
                        <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Message Type</label>
                                     <?php
                                        $bs = array('sms' => 'SMS');
                                        echo form_dropdown('messagetype', $bs, set_value('messagetype', 'SMS'), 'class="form-control select2" id="messagetype" required="required" style="width:100%;"');
                                      ?>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Recipient</label>
                                    <?php foreach($customers as $customer){ $cus[$customer->id] = $customer->name; } ?>
                                    <?= form_dropdown('customerid', $cus, set_value('customerid', $customer->id), 'id="customer_id" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control select2" style="width:100%;position:absolute;"'); ?>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Template</label>
                                    <?php foreach($templates as $template){ $tem[$template->id] = $template->name; } ?>
                                    <?= form_dropdown('template', $tem, set_value('template', $template->name), 'id="template" data-placeholder="' . 'Select Template'. '" required="required" class="form-control select2" style="width:100%;position:absolute;"'); ?>
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
                                    <?= form_textarea('message', set_value('message'), 'class="form-control tip" id="message"'); ?>
                                </div>
                                
                        </div>

                        <div class="form-group">
                            <?= form_submit('send_message', 'Send', 'class="btn btn-primary"'); ?>
                        </div>

                        <?php echo form_close();?>
                    <div class="clearfix"></div>
        </div>

    </div>

</div>
<script>
    function addtext1(ele) {
        var fired_button = ele.value;
        modal.myform.message.value += fired_button;
    }
</script>
<script>
    $(document).ready(function () {
        $("#template").select2({
            placeholder: '<?php echo lang('select_template'); ?>',
            allowClear: true,
            ajax: {
                url: 'messaging/getManualSMSTemplateinfo',
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term // search term                   
                    };

                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('#template').on('change', function () {
            var iid = $(this).val();
            var type = 'sms';

            $.ajax({
                url: 'messaging/getManualSMSTemplateMessageboxText?id=' + iid + '&type=' + type,
                method: 'GET',
                data: '',
                dataType: 'json',
            }).success(function (response) {
                   $('#myform').find('[name="message"]').val(response.user.message).end();

            //    CKEDITOR.instances['editor1'].setData(response.user.message)
                //  $('#myform').find('[name="message"]').val(response.user.message).end();
            })
        });
    });
</script>

