<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

<script type="text/javascript">
    $(document).ready(function() {


        var table = $('#CuData').DataTable({

            'ajax' : { url: '<?=site_url('customers/get_customers');?>', type: 'POST', "data": function ( d ) {
                d.<?=$this->security->get_csrf_token_name();?> = "<?=$this->security->get_csrf_hash()?>";
            }},
            "buttons": [
            { extend: 'copyHtml5', 'footer': false, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6 ] } },
            { extend: 'excelHtml5', 'footer': false, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6 ] } },
            { extend: 'csvHtml5', 'footer': false, exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6 ] } },
            { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', 'footer': false,
            exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6 ] } },
            { extend: 'colvis', text: 'Columns'},
            ],
            "columns": [
            { "data": "id", "visible": false },
            { "data": "customerno" },
            { "data": "name" },
            { "data": "phone" },
            { "data": "email" },
            { "data": "address" },
            { "data": "walletbalance",  "render": currencyFormat },
            { "data": "Actions", "searchable": false, "orderable": false }
            ]

        });

        $('#search_table').on( 'keyup change', function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (((code == 13 && table.search() !== this.value) || (table.search() !== '' && this.value === ''))) {
                table.search( this.value ).draw();
            }
        });

    });
</script>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <div>
                        <a href="<?= site_url('sales/customerdeposit/') ?>" data-toggle="ajax" class="btn btn-success btn-lg">Customer Deposit</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="CuData" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th style="max-width:30px;"><?= lang("id"); ?></th>
                                    <th>Cust No</th>
                                    <th><?= lang("name"); ?></th>
                                    <th><?= lang("phone"); ?></th>
                                    <th><?= lang("email_address"); ?></th>
                                    <th>Address</th>
                                    <th> Balance</th>
                                    <th style="width:65px;"><?= lang("actions"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" class="p0"><input type="text" class="form-control b0" name="search_table" id="search_table" placeholder="<?= lang('type_hit_enter'); ?>" style="width:100%;"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>
