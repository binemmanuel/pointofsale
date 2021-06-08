<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stockadjustment_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getStoreProductByID($id) {
        $q = $this->db->get_where('products', array('id' => $id), 1);
        if( $q->num_rows() > 0 ) {
            return $q->row();
        }
        return FALSE;
    }

    public function getStockadjustmentByID($id) {
        $q = $this->db->get_where('stockadjustment', array('id' => $id), 1);
        if( $q->num_rows() > 0 ) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllstockadjustmentItems($stockadjustment_id) {
        $this->db->select('stockadjustment_items.*, products.code as product_code, products.name as product_name')
            ->join('products', 'products.id=stockadjustment_items.product_id', 'left')
            ->group_by('stockadjustment_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('stockadjustment_items', array('stockadjustment_id' => $stockadjustment_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


    public function generateIssueRef() {

        $this->db->order_by('id','DESC')->limit(1);

        $q = $this->db->get('stockadjustment');
         if($q->num_rows()>0) {
         
         $data = $q->row_array();
         
         $value = $data['id'];
                
         $code =  $value + 1;
         
         return $code + 1000;
         
         } 
         else {
         
            return '10000';
         
         }
    }


    public function addstockadjustment($data, $items) {

        if ($this->db->insert('stockadjustment', $data)) {

            $stockadjustment_id = $this->db->insert_id();
            foreach ($items as $item) {
                $item['stockadjustment_id'] = $stockadjustment_id;
                if ($this->db->insert('stockadjustment_items', $item)) {
                        $this->setStoreQuantity1($item['product_id'], $data['store_id'], $item['quantity'], $item['adjustmenttype']);
                }
            }
            return true;
        }
        return false;
    }

 
    public function setStoreQuantity1($product_id, $store_id, $quantity, $adjustmenttype) {
        if ($adjustmenttype == 'addition') {
              if ($store_qty = $this->getStoreQuantity1($product_id, $store_id)) {
             $this->db->update('product_store_qty', array('quantity' => ($store_qty->quantity+$quantity)), array('product_id' => $product_id, 'store_id' => $store_id));
            } 
        }
        if ($adjustmenttype =='subtraction' ) {
             if ($store_qty = $this->getStoreQuantity1($product_id, $store_id)) {
            $this->db->update('product_store_qty', array('quantity' => ($store_qty->quantity-$quantity)), array('product_id' => $product_id, 'store_id' => $store_id));
            } 
        }
       
    }

    public function getStoreQuantity1($product_id, $store_id) {
        $q = $this->db->get_where('product_store_qty', array('product_id' => $product_id, 'store_id' => $store_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    // public function setStoreQuantity1($product_id, $store_id, $quantity) {
    //     if ($store_qty = $this->getStoreQuantity1($product_id, $store_id)) {
    //     $this->db->update('storeproduct_store_qty', array('quantity' => ($store_qty->quantity+$quantity)), array('product_id' => $product_id, 'store_id' => $store_id));
    //     } else {
    //     $this->db->insert('storeproduct_store_qty', array('product_id' => $product_id, 'store_id' => $store_id, 'quantity' => $quantity));
    //     }
    // }

    // public function getStoreQuantity1($product_id, $store_id) {
    //     $q = $this->db->get_where('storeproduct_store_qty', array('product_id' => $product_id, 'store_id' => $store_id), 1);
    //     if ($q->num_rows() > 0) {
    //         return $q->row();
    //     }
    //     return FALSE;
    // }

    public function updatestockadjustment($id, $data = NULL, $items = array()) {
        $stockadjustment = $this->getstockadjustmentByID($id);
        if ($stockadjustment->received) {
            $oitems = $this->getAllstockadjustmentItems($id);
            foreach ($oitems as $oitem) {
                if ($product = $this->site->getProductByID($oitem->product_id)) {
                    $this->setStoreQuantity($oitem->product_id, $stockadjustment->store_id, (0-$oitem->quantity));
                }
            }
        }
        if ($this->db->update('stockadjustment', $data, array('id' => $id)) && $this->db->delete('stockadjustment_items', array('stockadjustment_id' => $id))) {
            foreach ($items as $item) {
                $item['stockadjustment_id'] = $id;
                if ($this->db->insert('stockadjustment_items', $item)) {
                    if ($data['received'] && $product = $this->site->getProductByID($item['product_id'])) {
                        $this->setStoreQuantity($item['product_id'], $stockadjustment->store_id, $item['quantity']);
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function deletestockadjustment($id) {
        $stockadjustment = $this->getstockadjustmentByID($id);
        if ($stockadjustment->received) {
            $oitems = $this->getAllstockadjustmentItems($id);
            foreach ($oitems as $oitem) {
                if ($product = $this->site->getProductByID($oitem->product_id)) {
                    $this->setStoreQuantity($oitem->product_id, $stockadjustment->store_id, (0-$oitem->quantity));
                }
            }
        }
        if ($this->db->delete('stockadjustment', array('id' => $id)) && $this->db->delete('stockadjustment_items', array('stockadjustment_id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getProductNames($term, $limit = 10) {
        if ($this->db->dbdriver == 'sqlite3') {
            $this->db->where("type != 'combo' AND (name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  (name || ' (' || code || ')') LIKE '%" . $term . "%')");
        } else {
            $this->db->where("type != 'combo' AND (name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        }
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }



}
