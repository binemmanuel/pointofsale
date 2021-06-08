<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getQtyAlerts() {
        if (!$this->session->userdata('store_id')) {
            return 0;
        }
        $this->db->join("( SELECT (CASE WHEN quantity IS NULL THEN 0 ELSE quantity END) as quantity, product_id from {$this->db->dbprefix('product_store_qty')} WHERE store_id = {$this->session->userdata('store_id')} ) psq", 'products.id=psq.product_id', 'left')
        ->where("psq.quantity < {$this->db->dbprefix('products')}.alert_quantity", NULL, FALSE)
        ->where('products.alert_quantity >', 0);
        return $this->db->count_all_results('products');
    }

    public function getProductByID($id, $store_id = NULL) {
        if (!$store_id) {
            $store_id = $this->session->userdata('store_id');
        }
        $jpsq = "( SELECT product_id, quantity, price from {$this->db->dbprefix('product_store_qty')} WHERE store_id = ".($store_id ? $store_id : "''")." ) AS PSQ";
        $this->db->select("{$this->db->dbprefix('products')}.*, COALESCE(PSQ.quantity, 0) as quantity, COALESCE(PSQ.price, {$this->db->dbprefix('products')}.price) as store_price", FALSE)
        ->join($jpsq, 'PSQ.product_id=products.id', 'left');
        $q = $this->db->get_where('products', array('products.id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function sendSMS($receiverMobile, $message, $sender )
    {
        $username = 'kenvy2k@gmail.com';
        $pass = 'mother@1989';
        $message = urlencode($message);
        $url = sprintf("https://account.kudisms.net/api/?username=%s&password=%s&message=%s&sender=%s&mobile=%s",
            $username, $mobile, $pass, $message,$sender,$receiverMobile);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output = json_decode($output, true);
    }

     public function getSuspendIdFromSales($id) {
        $q = $this->db->get_where('sales', array('suspend_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

   public function getTotalCustomerSales($customer_id) {
        $this->db->select('COUNT(id) as id, sum(pos_balance) as balance, sum(pos_paid) as paid');
        // if ($start_date && $end_date) {
        //     $this->db->where('date >=','1900-01-01 00:00:00');
        //     $this->db->where('date <=', date('Y-m-d H:i:s'));
        // }
        $q = $this->db->get_where('payments', array('customer_id' => $customer_id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

  public function getStoreProductByID($id, $store_id = NULL) {
        if (!$store_id) {
            $store_id = $this->session->userdata('store_id');
        }
        $jpsq = "( SELECT product_id, quantity from {$this->db->dbprefix('storeproduct_store_qty')} WHERE store_id = ".($store_id ? $store_id : "''")." ) AS PSQ";
        $this->db->select("{$this->db->dbprefix('storeproducts')}.*, COALESCE(PSQ.quantity, 0) as quantity", FALSE)
        ->join($jpsq, 'PSQ.product_id=storeproducts.id', 'left');
        $q = $this->db->get_where('storeproducts', array('storeproducts.id' => $id), 1);
        if ($q && $q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductStoreIdById($id) {
       $q = $this->db->get_where('product_store_qty', array('product_id' => $id), 1);
          
        if($q->num_rows()>0) {
         
         $data = $q->row_array();
         
         $value = $data['store_id'];
         
         return $value;
         
         } 
         else {
         
            return FALSE;
         
         }
    }

    public function getCustomerWalletBalance($id) {
       $q = $this->db->get_where('customers', array('id' => $id), 1);
          
        if($q->num_rows()>0) {
         
         $data = $q->row_array();
         
         $value = $data['walletbalance'];
         
         return  empty($value) ? 0 : $value;
         
         } 
         else {
         
            return FALSE;
         
         }
    }

    public function getSettings() {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllCustomers() {
        $q = $this->db->get('customers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function updateWallet($cid, $amount = 0) {
        if ($this->db->update('customers', array('walletbalance' => $amount), array('id' => $cid))) {
            return true;
        }
        return false;
    }

    public function baladdtowallet($sid) {
        if ($this->db->update('sales', array('baladdtowallet' => 1))) {
            return true;
        }
        return false;
    }


    public function getAllTemplates() {
        $q = $this->db->get('sms_template');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function messagingtemplatesbtn(){
        
        $templates = $this->getAllTemplates();
        foreach ($templates as $template) {
            $url = 'messaging/customer?hold=$1?tm='.$template->id;
            $data[] =  "<ul class='dropdown-menu' role='menu'>
                    <li><a href='".site_url($url)."'>".$template->name."</a></li>
                   
                  </ul>";
        }
        return $data;
        
    }

    public function getAllSuppliers() {
        $q = $this->db->get('suppliers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllUsers() {
        $this->db->select("{$this->db->dbprefix('users')}.id as id, first_name, last_name, {$this->db->dbprefix('users')}.email, company, {$this->db->dbprefix('groups')}.name as group, active, {$this->db->dbprefix('stores')}.name as store")
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->join('stores', 'users.store_id=stores.id', 'left')
            ->group_by('users.id');
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
  public function getAllProducts2() {
        $this->db->select("{$this->db->dbprefix('products')}.name as name,{$this->db->dbprefix('products')}.id as id, {$this->db->dbprefix('product_store_qty')}.store_id as store_id, {$this->db->dbprefix('product_store_qty')}.quantity as quantity")
            ->join('product_store_qty', 'products.id=product_store_qty.product_id', 'left');
            // ->group_by('users.id');
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getUsers() {
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUserName($id = NULL) {
            if (!$id) {
                $id = $this->session->userdata('user_id');
            }
            $q = $this->db->get_where('users', array('id' => $id), 1);
            if ($q->num_rows() > 0) {
                 $data = $q->row_array();
                 $value =  $data['first_name'].'  '.$data['last_name'];
                 return $value;
            }
            return FALSE;
        }

    public function getUser($id = NULL) {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllCategories() {
        $this->db->order_by('code');
        $q = $this->db->get('categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCategoryByID($id) {
        $q = $this->db->get_where('categories', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCategoryByCode($code) {
        $q = $this->db->get_where('categories', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGiftCard($no) {
        $q = $this->db->get_where('gift_cards', array('card_no' => $no), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getUpcomingEvents() {
        $dt = date('Y-m-d');
        $this->db->where('date >=', $dt)->order_by('date')->limit(5);
        if ($this->Settings->restrict_calendar) {
            $q = $this->db->get_where('calendar', array('user_id' => $this->session->userdata('iser_id')));
        } else {
            $q = $this->db->get('calendar');
        }
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUserGroup($user_id = NULL) {
        if ($group_id = $this->getUserGroupID($user_id)) {
            $q = $this->db->get_where('groups', array('id' => $group_id), 1);
            if ($q->num_rows() > 0) {
                return $q->row();
            }
        }
        return FALSE;
    }

    public function getUserGroupID($user_id = NULL) {
        if ($user = $this->getUser($user_id)) {
            return $user->group_id;
        }
        return FALSE;
    }

    public function getUserSuspenedSales() {
        $user_id = $this->session->userdata('user_id');
        $this->db->select('id, date, customer_name, hold_ref')
        ->order_by('id desc');
        //->limit(10);
        $this->db->where('store_id', $this->session->userdata('store_id'));
        $q = $this->db->get_where('suspended_sales', array('created_by' => $user_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStoreByID($id = NULL) {
        if ( ! $id) {
            return FALSE;
        }
        $q = $this->db->get_where('stores', array('id' => $id), 1);
        if ( $q->num_rows() > 0 ) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllStores() {
        $q = $this->db->get('stores');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function registerData($user_id)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('registers', array('user_id' => $user_id, 'status' => 'open'), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllPrinters() {
        $this->db->order_by('title');
        $q = $this->db->get('printers');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSmsTemplates() {
        $this->db->order_by('id');
        $q = $this->db->get('sms_template');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getPrinterByID($id) {
        $q = $this->db->get_where('printers', array('id' => $id), 1);
        if ( $q->num_rows() > 0 ) {
            return $q->row();
        }
        return FALSE;
    }

  public function getMessageLog(){
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getCustomerByID($id) {
        $q = $this->db->get_where('customers', array('id' => $id), 1);
        if ( $q->num_rows() > 0 ) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTemplate($type) {

        $q = $this->db->get_where('sms_template', array('type' => $type), 1);
        if ( $q->num_rows() > 0 ) {
            return $q->row();
        }
        return FALSE;
    }
    public function getSmsSettings() {
        $q = $this->db->get_where('sms_setting', array('id' => 1), 1);
        if ( $q->num_rows() > 0 ) {
            return $q->row();
        }
        return FALSE;
    }
    public function getSmsShortcodeTag() {
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('smsshortcode');
        return $query->result();
    }

    public function getStoreNameByID($id) {
        $q = $this->db->get_where('stores', array('id' => $id), 1);
        
        if($q->num_rows()>0) {
         
         $data = $q->row_array();
         
         $value = $data['name'];
         
         return $value;
         
         } 
         else {
         
            return 'No Store';
         
         }
    }
    
    public function getGiftCardByID($id) {
        $q = $this->db->get_where('gift_cards', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getSaleByID($id) {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

}
