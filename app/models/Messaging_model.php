<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Messaging_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getAllMessages() {
        $q = $this->db->get('messaging');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function messages_count($id = NULL) {
    
            return $this->db->count_all("messaging");
    }

   
    public function logMessage($data){
        if ($this->db->insert('messaging', $data)) {
            return true;
        }
        return false;
    }
    public function getTemplateByID($id) {
        $q = $this->db->get_where('sms_template', array('id' => $id), 1);
        if( $q->num_rows() > 0 ) {
            return $q->row();
        }
        return FALSE;
    }
    public function updateTemplate($id, $data = array()) {
        if($this->db->update('sms_template', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }
    public function SaveMessageTemplate($data){

        if ($this->db->insert('sms_template', $data)) {
            return true;
        }
        return false;
    }


   public function getManualSMSTemplateListSelect2($searchTerm, $type) {
        if (!empty($searchTerm)) {
            $this->db->select('*');
            $this->db->where("name like '%" . $searchTerm . "%' ");
            $this->db->where('type', $type);
            $fetched_records = $this->db->get('sms_template');
            $users = $fetched_records->result_array();
        } else {
            $this->db->select('*');
            $this->db->limit(2);
            $fetched_records = $this->db->get('sms_template');
            $users = $fetched_records->result_array();
        }
        // Initialize Array with fetched data
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['name']);
        }
        return $data;
    }

     public function getManualSMSTemplateById($id, $type) {
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $query = $this->db->get('sms_template');
        return $query->row();
    }

    public function delete($id) {
        if ($this->db->delete('messaging', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    public function deletetemplate($id) {
        if ($this->db->delete('sms_template', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

}
