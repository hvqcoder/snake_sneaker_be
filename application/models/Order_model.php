<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model
{

    public function order_list(){

      $where=array("tbl_order_details.order_status != " => '-1');

      $this->db->select('tbl_order_details.*');
      $this->db->from('tbl_order_details');
      $this->db->where($where); 
      $this->db->order_by('tbl_order_details.id', 'DESC');
      return $this->db->get()->result();
    }

    public function single_order($id){

      $this->db->select('*');
      $this->db->from('tbl_order_details');
      $this->db->where('id', $id); 
      $this->db->limit(1);
      $query = $this->db->get();
      if($query -> num_rows() == 1){                 
          return $query->result();
      }
      else{
          return false;
      }
    }

    public function get_order($order_no){

      $this->db->select('*');
      $this->db->from('tbl_order_details');
      $this->db->where('order_unique_id', $order_no); 
      $this->db->limit(1);
      $query = $this->db->get();
      if($query -> num_rows() == 1){                 
          return $query->result();
      }
      else{
          return false;
      }
    }

    public function get_product_status($order_id,$product_id=0,$user_id=0){

      if($product_id!=0){
        $where=array('order_id' => $order_id, 'product_id' => $product_id);
      }
      else if($product_id!=0 && $user_id!=0){
        $where=array('order_id' => $order_id, 'product_id' => $product_id, 'user_id' => $user_id);
      }
      else{
        $where=array('order_id' => $order_id, 'product_id' => '0');
      }

      $this->db->select('*');
      $this->db->from('tbl_order_status');
      $this->db->where($where); 
      if($product_id==0){
        $this->db->group_by("status_title");
      }
      
      $this->db->order_by('id','ASC');
      $query = $this->db->get();

      // echo $this->db->last_query();

      if($query){                 
          return $query->result();
      }
      else{
          return false;
      }
    }

    public function get_titles($default=false,$sort='ASC'){

      if($default){

        $where=array('status' => '1');

        $this->db->select('*');
        $this->db->from('tbl_status_title');
        $this->db->where($where); 
        $this->db->order_by('id',$sort);
        $query = $this->db->get();

      }
      else{
        $this->db->select('*');
        $this->db->from('tbl_status_title');
        $this->db->order_by('id',$sort);
        $query = $this->db->get();
      }

      if($query){                 
          return $query->result();
      }
      else{
          return false;
      }
    }

    public function get_cancel_titles(){

      $where=array('id' => '5');

      $this->db->select('*');
      $this->db->from('tbl_status_title');
      $this->db->where($where); 
      $query = $this->db->get();

      return $query->result();
    }

    public function delete($id){

      $this->db->delete('tbl_order_details', array('id' => $id));

      $where = array('order_id' => $id);

      $this->db->select('*');
      $this->db->from('tbl_order_items');
      $this->db->where($where); 
      $query = $this->db->get();
      $row=$query->result();

      foreach ($row as $key => $value) {

        $this->db->select('*');
        $this->db->from('tbl_rating');
        $this->db->where(array('product_id' => $value->product_id, 'user_id' => $value->user_id)); 
        $query_rate = $this->db->get();
        $row_rate=$query_rate->result();

        foreach ($row_rate as $key1 => $value1) {

          $where2 = array('parent_id' => $value1->id , 'type' => 'review');

          $this->db->select('*');
          $this->db->from('tbl_product_images');
          $this->db->where($where2); 
          $query = $this->db->get();
          $row2=$query->result();

          foreach ($row2 as $key => $value2) {

            if(file_exists('assets/images/review_images/'.$value2->image_file)){
              unlink('assets/images/review_images/'.$value2->image_file);

              $mask = $value2->id.'*_*';
              array_map('unlink', glob('assets/images/review_images/thumbs/'.$mask));

              $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value2->image_file);
              $mask = $thumb_img_nm.'*_*';
              array_map('unlink', glob('assets/images/review_images/thumbs/'.$mask));              
            }

          }

          $this->db->where($where2);
          $this->db->delete('tbl_product_images');
        }

        $this->db->delete('tbl_rating', array('product_id' => $value->product_id, 'user_id' => $value->user_id));

      }

      $this->db->delete('tbl_order_items', array('order_id' => $id));
      $this->db->delete('tbl_order_status', array('order_id' => $id)); 
      $this->db->delete('tbl_transaction', array('order_id' => $id));
      $this->db->delete('tbl_refund', array('order_id' => $id));
      return true;
    }

    public function delete_transaction($id){
      $this->db->select('*');
      $this->db->from('tbl_transaction');
      $this->db->where('id', $id); 
      $this->db->limit(1);
      $query = $this->db->get();
      if($query -> num_rows() == 1){                 
          if($this->db->delete('tbl_transaction', array('id' => $id))){
            return 'success';  
          }
          else{
            return 'failed';
          }
      }
      else{
          return 'failed';
      }
    }


    public function delete_ord_status($order_id, $status_id){

      $where=array('order_id' => $order_id, 'status_title' => $status_id);

      $this->db->delete('tbl_order_status', $where); 

      $this->db->select('*');
      $this->db->from('tbl_order_status');
      $this->db->where(array('order_id' => $order_id)); 
      $this->db->order_by('id','DESC');
      $query = $this->db->get();
      $row=$query->result();

      $status_title=$row[0]->status_title;

      $this->db->where('id',$order_id);
      $this->db->update('tbl_order_details',array('order_status' => $status_title));

      foreach ($row as $key => $value) {
        $this->db->where(array('order_id' => $order_id, 'product_id' => $value->product_id, 'pro_order_status <>' => 5));
        $this->db->update('tbl_order_items',array('pro_order_status' => $status_title));
      }
      return true;
    }

    public function get_order_details($where)
    {
      $this->db->select('order.*');
      $this->db->select('transaction.`gateway`, transaction.`gateway`, transaction.`payment_id`, transaction.`razorpay_order_id`');
      $this->db->from('tbl_order_details order');
      $this->db->join('tbl_transaction transaction', 'order.id = transaction.order_id', 'LEFT');

      $this->db->where($where);
      return $this->db->get()->row();
    }

    public function get_order_transaction($order_id)
    {
      $this->db->select('*');
      $this->db->from('tbl_transaction');
      $this->db->where('order_id', $order_id); 
      $this->db->limit(1);
      $query = $this->db->get();
      if($query -> num_rows() == 1){
        return $query->row();
      }
      else{
          return false;
      }
    }

    public function get_order_items($order_id, $extraWhere=null)
    {
      $this->db->select('items.*');
      $this->db->select('product.`product_title` AS `title`, product.`product_slug`, product.`featured_image`, product.`color`, product.`status`');
      $this->db->from('tbl_order_items items');
      $this->db->join('tbl_product product', 'items.product_id = product.id', 'LEFT');

      $where = array('items.`order_id`' => $order_id);

      if(!is_null($extraWhere)){
        $where=array_merge($where, $extraWhere);
      }

      $this->db->where($where);
      $this->db->order_by('items.`id`','DESC');
      return $this->db->get()->result();
    }

    public function get_transactions($extraWhere=null)
    {
      $this->db->select('transaction.*');
      $this->db->select('user.`user_name`');
      $this->db->from('tbl_transaction transaction');
      $this->db->join('tbl_users user', 'transaction.user_id = user.id', 'LEFT');

      if(!is_null($extraWhere)){
        $this->db->where($extraWhere);
      }
      
      $this->db->order_by('transaction.`id`','DESC');
      return $this->db->get()->result();
    }

    public function get_refunds($extraWhere=null)
    {
      $this->db->select('refund.*');
      $this->db->select('product.`product_name`');
      $this->db->from('tbl_refund refund');
      $this->db->join('tbl_product product', 'refund.product_id = product.id', 'LEFT');

      if(!is_null($extraWhere)){
        $this->db->where($extraWhere);
      }
      
      $this->db->order_by('refund.`id`','DESC');
      return $this->db->get()->result();
    }

}