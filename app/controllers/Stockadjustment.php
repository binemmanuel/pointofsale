<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stockadjustment extends MY_Controller
{

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }
        if ( ! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        $this->load->library('form_validation');
        $this->load->model('stockadjustment_model');
        $this->load->helper('string');
        $this->allowed_types = 'gif|jpg|png|jpeg|pdf|doc|docx|xls|xlsx|zip';
    }

    function index() {
        if ( ! $this->Admin ) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = 'Stock Adjustment';
        $bc = array(array('link' => '#', 'page' => 'Stock Adjustment'));
        $meta = array('page_title' => 'Stock Adjustment', 'bc' => $bc);
        $this->page_construct('stockadjustment/index', $this->data, $meta);

    }

    function get_stockadjustment() {
        if (! $this->Admin ) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->library('datatables');
        $this->datatables->select("id, date, reference, note, created_by");
        $this->datatables->from('stockadjustment');
        // ->join('users', 'users.id=products.id', 'left');
        if (!$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }
        $this->datatables->where('store_id', $this->session->userdata('store_id'));
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'><a href='".site_url('stockadjustment/view/$1')."' title='".'Click to see items'."' class='tip btn btn-warning btn-xs' data-toggle='ajax-modal'><i class='fa fa-eye'></i></a></div></div>", "id");

        $this->datatables->unset_column('id');
        echo $this->datatables->generate();

    }

    function view($id = NULL) {
        if ( ! $this->Admin ) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->data['stockadjustment'] = $this->stockadjustment_model->getStockadjustmentByID($id);
        $this->data['stockadjustment_items'] = $this->stockadjustment_model->getAllstockadjustmentItems($id);
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = 'View';
        $this->load->view($this->theme.'stockadjustment/view', $this->data);

    }

    function add(){

        if (! $this->session->userdata('store_id')) {
            $this->session->set_flashdata('warning', lang("please_select_store"));
            redirect('stores');
        }
        if ( ! $this->Admin ) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        $this->form_validation->set_rules('date', lang('date'), 'required');
        if ($this->form_validation->run() == true) {
            $total = 0;
            $quantity = 0;
            $product_id = "product_id";
            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
           
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_qty = $_POST['quantity'][$r];
                $reason = $_POST['reason'][$r];
                $adjustmenttype = $_POST['adjustmenttype'][$r];
                if( $item_id && $item_qty ) {

                    if(!$this->site->getProductByID($item_id)) {
                        $this->session->set_flashdata('error', $this->lang->line("product_not_found")." ( ".$item_id." ).");
                        redirect('stockadjustment/add');
                    }
                $itemobj = $this->site->getProductByID($item_id);
                    $products[] = array(
                        'product_id' => $item_id,
                        'quantity' => $item_qty,
                        'adjustmenttype' => $adjustmenttype,
                        'reason' => $reason
                        );
                   
                }
            }
            if (!isset($products) || empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            $data = array(
                        'date' => date('Y-m-d H:i:s'),
                        'reference' => 'ADJ#'.$this->stockadjustment_model->generateIssueRef(),
                        'note' => $this->input->post('note', TRUE),
                        'created_by' => $this->tec->getUserName($this->session->userdata('user_id')),
                        'store_id' => $this->session->userdata('store_id'),
                    );

            // $this->tec->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->stockadjustment_model->addstockadjustment($data, $products)) {

            $this->session->set_userdata('remove_spo', 1);
            $this->session->set_flashdata('message', 'Stock Adjustment Successfully Done');
            redirect("stockadjustment");

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['stores'] = $this->site->getAllStores();
            $this->data['products'] = $this->site->getAllProducts2();
            $this->data['page_title'] = 'Stock Adjustment';
            $bc = array(array('link' => site_url('stockadjustment'), 'page' => 'Stock Adjustment'), array('link' => '#', 'page' => 'Stock Adjustment'));
            $meta = array('page_title' => 'Stock Adjustment', 'bc' => $bc);
            $this->page_construct('stockadjustment/add', $this->data, $meta);

        }
    }

    function edit($id = NULL) {
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('date', lang('date'), 'required');

        if ($this->form_validation->run() == true) {
            $total = 0;
            $quantity = "quantity";
            $product_id = "product_id";
            $unit_cost = "cost";
            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $item_id = $_POST['product_id'][$r];
                $item_qty = $_POST['quantity'][$r];
                $item_cost = $_POST['cost'][$r];
                if( $item_id && $item_qty && $unit_cost ) {

                    if(!$this->site->getProductByID($item_id)) {
                        $this->session->set_flashdata('error', $this->lang->line("product_not_found")." ( ".$item_id." ).");
                        redirect('stockadjustment/edit/'.$id);
                    }

                    $products[] = array(
                        'product_id' => $item_id,
                        'cost' => $item_cost,
                        'quantity' => $item_qty,
                        'subtotal' => ($item_cost*$item_qty)
                        );

                    $total += ($item_cost * $item_qty);

                }
            }

            if (!isset($products) || empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            $data = array(
                        'date' => $this->input->post('date'),
                        'reference' => $this->input->post('reference'),
                        'note' => $this->input->post('note', TRUE),
                        'supplier_id' => $this->input->post('supplier'),
                        'received' => $this->input->post('received'),
                        'total' => $total
                    );

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');
                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = $this->allowed_types;
                $config['max_size'] = '2000';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->upload->set_flashdata('error', $error);
                    redirect("stockadjustment/add");
                }

                $data['attachment'] = $this->upload->file_name;

            }
            // $this->tec->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->stockadjustment_model->updatestockadjustment($id, $data, $products)) {

            $this->session->set_userdata('remove_spo', 1);
            $this->session->set_flashdata('message', 'Updated');
            redirect("stockadjustment");

        } else {

            $this->data['stockadjustment'] = $this->stockadjustment_model->getstockadjustmentByID($id);
            $inv_items = $this->stockadjustment_model->getAllstockadjustmentItems($id);
            $c = rand(100000, 9999999);
            foreach ($inv_items as $item) {
                $row = $this->site->getProductByID($item->product_id);
                $row->qty = $item->quantity;
                $row->cost = $item->cost;
                $ri = $this->Settings->item_addition ? $row->id : $c;
                $pr[$ri] = array('id' => $ri, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
                $c++;
            }

            $this->data['items'] = json_encode($pr);
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['suppliers'] = $this->site->getAllSuppliers();
            $this->data['page_title'] = 'Edit';
            $bc = array(array('link' => site_url('stockadjustment'), 'page' => 'Stock Adjustment'), array('link' => '#', 'page' => 'Edit'));
            $meta = array('page_title' => lang('edit_stockadjustment'), 'bc' => $bc);
            $this->page_construct('stockadjustment/edit', $this->data, $meta);

        }
    }

    function delete($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if ( ! $this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->stockadjustment_model->deletestockadjustment($id)) {
            $this->session->set_flashdata('message', 'Deleted');
            redirect('stockadjustment');
        }
    }

    function suggestions($id = NULL) {
        if($id) {
            $row = $this->site->getProductByID($id);
            $row->qty = 1;
            $pr = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
            echo json_encode($pr);
            die();
        }
        $term = $this->input->get('term', TRUE);
        $rows = $this->stockadjustment_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $row->qty = 1;
                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

     /* ----------------------------------------------------------------- */

}
