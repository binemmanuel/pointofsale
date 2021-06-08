<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Messaging extends MY_Controller
{

    function __construct() {
        parent::__construct();


        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('messaging_model');
    }

    function index() {

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['Message'] = $this->site->getAllMessage();
        $this->data['page_title'] = 'Message ';
        $bc = array(array('link' => '#', 'page' => 'Message '));
        $meta = array('page_title' => 'Message Categories', 'bc' => $bc);
        $this->page_construct('messaging/index', $this->data, $meta);

    }
  
    function log(){

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['messagelog'] = $this->site->getMessageLog();
        $this->data['page_title'] = 'Messaging Log ';
        $bc = array(array('link' => '#', 'page' => 'Message Log'));
        $meta = array('page_title' => 'Message Log', 'bc' => $bc);
        $this->page_construct('messaging/log', $this->data, $meta);

    }
    function templates(){

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['page_title'] = 'Templates ';
        $bc = array(array('link' => '#', 'page' => 'Templates'));
        $meta = array('page_title' => 'Templates', 'bc' => $bc);
        $this->page_construct('messaging/templates', $this->data, $meta);

    }

    function get_Message() {

        $this->load->library('datatables');
        $this->datatables->select("id, code, name");
        $this->datatables->from('Message');
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'> <a href='" . site_url('Message/edit/$1') . "' title='" . lang("edit_") . "' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('Message/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_') . "')\" title='" . lang("delete_") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id");
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();

    }

    function get_templates() {

        $this->load->library('datatables');
        $this->datatables->select("id, type, name, message");
        $this->datatables->from('sms_template');
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group'> <a href='" . site_url('messaging/edittemplate/$1') . "' title='" . lang("edit_") . "' class='tip btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='" . site_url('messaging/deletetemplate/$1') . "' onClick=\"return confirm('" . lang('alert_x_') . "')\" title='" . lang("delete_") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id")
        ->unset_column('id');
        echo $this->datatables->generate();

    }
 
    function get_log() {

        $this->load->library('datatables');
        $this->datatables->select("*");
        $this->datatables->from('messaging');
        $this->datatables->unset_column('id');
        echo $this->datatables->generate();

    }
    function newmessage() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->post('customerid')) {
             $customerobj = $this->site->getCustomerByID($this->input->post('customerid'));
             $type = $this->input->post('messagetype');
             $template = $this->input->post('template');
        }
       

        $this->form_validation->set_rules('customerid', 'Customer', 'required');

        if ($this->form_validation->run() == true) {

            $messageobj = $this->site->getTemplate($type);
            $message = $this->input->post('message');

            $data1 = array(
                    'name' => $customerobj->name,
                    'phone' => $customerobj->phone,
                    'email' => $customerobj->email
                );
            $messageprint = $this->parser->parse_string($message, $data1);
            $sendto = $customerobj->phone;
            if ($type = 'sms') {
                 $response = json_encode($this->tec->sendsms($messageprint, $sendto));
            }
            $data = array('messagetype' => $this->input->post('messagetype'),'template' => $messageobj->id, 'sender' => $this->Settings->sms_senderid ,'receiver' => $customerobj->name, 'phone' => $customerobj->phone, 'body' => $message, 'date' => date('Y-m-d H:i:s'), 'delivery' => !($response == '"sent"') ? 'failed' : 'sent', 'createdby' => $this->session->userdata('user_id'), 'deliveryreport' => $response);
            }
            if ($this->form_validation->run() == true && $this->messaging_model->logMessage($data)) {

                $this->session->set_flashdata('message', 'Message  Sent');
                redirect("messaging/log");

            }else{

                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['page_title'] = 'Send New Message ';
                $this->data['customers'] = $this->site->getAllCustomers();
                $this->data['templates'] = $this->site->getSmsTemplates();
                $this->data['shortcodes'] = $this->site->getSmsShortcodeTag();
                $bc = array(array('link' => site_url('messaging/newmessage'), 'page' => 'new'), array('link' => '#', 'page' => 'Add New Message'));
                $meta = array('page_title' => 'Send New Message', 'bc' => $bc);
                $this->page_construct('messaging/newmessage', $this->data, $meta);
            }
    }
    function newtemplate() {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        $this->form_validation->set_rules('name', 'name', 'required');

        if ($this->form_validation->run() == true) {
            $data = array('type' => $this->input->post('type'),'name' => $this->input->post('name'), 'message' =>  $this->input->post('message'));
            }
            if ($this->form_validation->run() == true && $this->messaging_model->SaveMessageTemplate($data)) {

                $this->session->set_flashdata('message', 'Template Saved');
                redirect("messaging/templates");

            }else{

                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['page_title'] = 'New Message Template';
                $this->data['shortcodes'] = $this->site->getSmsShortcodeTag();
                $bc = array(array('link' => site_url('messaging/newtemplate'), 'page' => 'newtemplate'), array('link' => '#', 'page' => 'New Message Template'));
                $meta = array('page_title' => 'New Message Template', 'bc' => $bc);
                $this->page_construct('messaging/newtemplate', $this->data, $meta);
            }
    }
    function edittemplate($id = NULL){
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if($this->input->get('id')) { $id = $this->input->get('id', TRUE); }

        $this->form_validation->set_rules('name', 'name', 'required');

        if ($this->form_validation->run() == true) {
            $data = array('type' => $this->input->post('type'),'name' => $this->input->post('name'), 'message' =>  $this->input->post('message'));
            }
            if ($this->form_validation->run() == true && $this->messaging_model->updateTemplate($id, $data)) {

                $this->session->set_flashdata('message', 'Template Edited');
                redirect("messaging/templates");

            }else{

                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['page_title'] = 'New Message Template';
                $this->data['template'] = $this->messaging_model->getTemplateByID($id);
                $this->data['shortcodes'] = $this->site->getSmsShortcodeTag();
                $bc = array(array('link' => site_url('messaging/edittemplate'), 'page' => 'edittemplate'), array('link' => '#', 'page' => 'Edit Message Template'));
                $meta = array('page_title' => 'Edit Message Template', 'bc' => $bc);
                $this->page_construct('messaging/edittemplate', $this->data, $meta);
            }
    }

    public function getManualSMSTemplateinfo() {
        // Search term
        $searchTerm = $this->input->post('searchTerm');
        $type = 'sms';
        // Get users
        $response = $this->sms_model->getManualSMSTemplateListSelect2($searchTerm, $type);

        echo json_encode($response);
    }

    public function getManualSMSTemplateMessageboxText() {
        $id = $this->input->get('id');
        $type = $this->input->get('type');
        $data['user'] = $this->messaging_model->getManualSMSTemplateById($id, $type);
        echo json_encode($data);
    }

    public function customer($id = NULL) {
          if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        
       
        if ($this->input->get('s')) {
             $sobj = $this->site->getSaleByID($this->input->get('s'));
             $customerobj = $this->site->getCustomerByID($sobj->customer_id);
             $type = $this->input->post('messagetype');
             $template = $this->input->post('template');
        }

        if ($this->input->post('customerid')) {
             $sobj = $this->site->getSaleByID($this->input->get('s'));
             $customerobj = $this->site->getCustomerByID($this->input->post('customerid'));
             $type = $this->input->post('messagetype');
             $template = $this->input->post('template');
        }

        $this->form_validation->set_rules('message', 'message', 'required');

        if ($this->form_validation->run() == true) {

            //$messageobj = $this->site->getTemplate($type);
            $message = $this->input->post('message');

            $data1 = array(
                    'name' => $customerobj->name,
                    'phone' => $customerobj->phone,
                    'email' => $customerobj->email
                );
          
            $messageprint = $this->parser->parse_string($message, $data1);
            $sendto = $customerobj->phone;
            if ($type = 'sms') {
                 $response = json_encode($this->tec->sendsms($messageprint, $sendto));
            }
            $data = array('messagetype' => $this->input->post('messagetype'),'template' => $messageobj->id, 'sender' => $this->Settings->sms_senderid ,'receiver' => $customerobj->name, 'phone' => $customerobj->phone, 'body' => $message, 'date' => date('Y-m-d H:i:s'), 'delivery' => !($response == '"sent"') ? 'failed' : 'sent', 'createdby' => $this->session->userdata('user_id'), 'deliveryreport' => $response);
            }

        if ($this->form_validation->run() == true && $this->messaging_model->logMessage($data)) {

            $this->session->set_flashdata('message', 'Message  Sent');
            redirect("sales");

        }else{
            $msg = "Send SMS to ".$customerobj->name;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['page_title'] = 'Send Message ';
            $this->data['customerid'] = $customerobj->id;
            $this->data['customers'] = $this->site->getAllCustomers();
            $this->data['templates'] = $this->site->getSmsTemplates();
            $this->data['shortcodes'] = $this->site->getSmsShortcodeTag();
            $bc = array(array('link' => site_url('messaging/customer'), 'page' => 'new'), array('link' => '#', 'page' => 'Send Message'));
            $meta = array('page_title' => $msg, 'bc' => $bc);
            $this->page_construct('messaging/customer', $this->data, $meta);
        }
    }

    function delete($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->messaging_model->delete($id)) {
            $this->session->set_flashdata('message', lang("_deleted"));
            redirect('message');
        }
    }
    function deletetemplate($id = NULL) {
        if(DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->messaging_model->deletetemplate($id)) {
            $this->session->set_flashdata('message', lang("_deleted"));
            redirect('messaging/templates');
        }
    }



    
    
}
