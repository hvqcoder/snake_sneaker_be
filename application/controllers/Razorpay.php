<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/razorpay-php/Razorpay.php');

include_once APPPATH.'controllers/Checkout.php';

use Razorpay\Api\Api;

use Razorpay\Api\Errors\SignatureVerificationError;

class Razorpay extends Checkout {

    private $key_id;

    private $secret_key;

    public function __construct() {
        parent::__construct();

        ini_set('MAX_EXECUTION_TIME', '-1');
        
        $this->key_id=$this->settings->razorpay_key;
        $this->secret_key=$this->settings->razorpay_secret;
    }

    public function generate_ord()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $buy_now=$this->input->post('buy_now');

            $address_id=$this->input->post('address_id');

            $row_address=$this->General_model->selectByid($address_id, 'tbl_addresses');

            if(!empty($row_address)){

                $coupon_id=$this->input->post('coupon_id');

                $cart_type=($buy_now=='true') ? 'temp_cart' : 'main_cart';

                $this->session->set_userdata("buy_now", $buy_now);
                $this->session->set_userdata("cart_type", $cart_type);
                $this->session->set_userdata("address_id", $address_id);

                $this->session->set_userdata("current_page", $this->input->post('current_page'));

                $total_cart_amt=$delivery_charge=$you_save=0;

                $cart_ids=implode(',', $this->input->post('cart_ids'));
                
                $get_cart_details=$this->get_cart_details($cart_type, $cart_ids);

                if($get_cart_details['status']==1){

                    $total_cart_amt=$get_cart_details['total_cart_amt'];
                    $delivery_charge=$get_cart_details['delivery_charge'];
                    $you_save=$get_cart_details['you_save'];

                    $where=array('user_id' => $this->user_id, 'cart_type' => $cart_type);

                    if(count($this->General_model->selectByids($where,'tbl_applied_coupon'))==0){
                        $coupon_id=0;
                    }

                    if($coupon_id==0)
                    {
                        $payable_amt=$total_cart_amt+$delivery_charge;
                    }
                    else
                    {
                        $coupon_json=json_decode($this->inner_apply_coupon($coupon_id,$this->input->post('cart_ids'),$cart_type));
                        $payable_amt=$coupon_json->payable_amt;
                    }

                    $this->session->set_userdata("coupon_id", $coupon_id);
                    $this->session->set_userdata("cart_ids", $this->input->post('cart_ids'));

                    $api = new Api($this->key_id, $this->secret_key);

                    $payable_amt=$payable_amt*100;

                    if(APP_CURRENCY == 'INR')
                    {
                        if($this->session->userdata('razorpay_order_id'))
                        {
                            $order = $api->order->fetch($this->session->userdata('razorpay_order_id'));

                            if(($order['amount'] != $payable_amt))
                            {
                                $this->session->unset_userdata(array('razorpay_order_id'));

                                $order  = $api->order->create(array('receipt' => 'user_rcptid_'.$this->user_id, 'amount' => $payable_amt, 'currency' => APP_CURRENCY,'payment_capture' =>  '1'));

                                $orderId = $order['id']; 

                                $this->session->set_userdata("razorpay_order_id", $orderId);
                            }
                            else
                            {
                                $orderId = $this->session->userdata('razorpay_order_id');
                            }
                        }
                        else
                        {
                            $order  = $api->order->create(array('receipt' => 'user_rcptid_'.$this->user_id, 'amount' => $payable_amt, 'currency' => APP_CURRENCY,'payment_capture' =>  '1'));
                            $orderId = $order['id']; 
                            $this->session->set_userdata("razorpay_order_id", $orderId);
                        }

                        $response=array('status' => 1,'razorpay_order_id' => $orderId,'key' => $this->key_id, 'amount' => $payable_amt, 'site_name' => $this->web_settings->site_name, 'description' => $this->lang->line('pay_with_razorpay_lbl'),'theme_color' => '#'.$this->settings->razorpay_theme_color,'user_name' => $this->current_user_data->user_name,'user_email' => $this->current_user_data->user_email,'user_phone' => $this->current_user_data->user_phone,'logo' => base_url('assets/images/'.$this->web_settings->web_logo_1));
                    }
                    else{
                        $response=array('success' => '0','msg' => $this->lang->line('razorpay_currency_err'));
                    }
                }
                else{
                    echo json_encode($get_cart_details);
                    exit();
                }
            }
            else{
                $response = array('status' => 0, 'msg' => $this->lang->line('no_address_found'));
            }
        }
        echo json_encode($response);
        exit();
    }

    public function pay()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $success = true;
            $error = "";

            if(empty($this->input->post('razorpay_payment_id')) === false)
            {
                $api = new Api($this->key_id, $this->secret_key);

                try{

                    $products_arr=array();
                    $data_email=array();

                    $payment_id=$this->input->post('razorpay_payment_id');
                    $razorpay_order_id=$this->session->userdata("razorpay_order_id");

                    $buy_now=$this->session->userdata('buy_now');
                    $address_id=$this->session->userdata('address_id');

                    $row_address=$this->General_model->selectByid($address_id, 'tbl_addresses');

                    if(!empty($row_address)){

                        $order_unique_id='ORD'.$this->get_order_unique_id().rand(0,1000);

                        $cart_type=$this->session->userdata('cart_type');
                        $cart_ids=implode(',', $this->session->userdata('cart_ids'));

                        $get_cart_details=$this->get_cart_details($cart_type, $cart_ids);

                        if($get_cart_details['status']==1){

                            $total_cart_amt=$get_cart_details['total_cart_amt'];
                            $delivery_charge=$get_cart_details['delivery_charge'];
                            $you_save=$get_cart_details['you_save'];

                            $coupon_id=$this->session->userdata('coupon_id');

                            $where=array('user_id' => $this->user_id, 'cart_type' => $cart_type);

                            if(count($this->General_model->selectByids($where,'tbl_applied_coupon'))==0){
                                $coupon_id=0;
                            }

                            if($coupon_id==0){
                                $discount=0;
                                $discount_amt=0;
                                $payable_amt=sprintf("%01.2f", ($total_cart_amt+$delivery_charge));
                            }
                            else{

                                $coupon_json=json_decode($this->inner_apply_coupon($coupon_id));
                                $discount=$coupon_json->discount;
                                $discount_amt=$coupon_json->discount_amt;
                                $payable_amt=$coupon_json->payable_amt;
                            }

                            $data_arr = array(
                                'user_id' => $this->user_id,
                                'coupon_id' => $this->input->post('coupon_id'),
                                'order_unique_id' => $order_unique_id,
                                'order_address' => $address_id,
                                'total_amt' => $total_cart_amt,
                                'discount' => $discount,
                                'discount_amt' => $discount_amt,
                                'payable_amt' => $payable_amt,
                                'new_payable_amt' => $payable_amt,
                                'delivery_date' => strtotime(date('d-m-Y h:i:s A', strtotime('+7 days'))),
                                'order_date' => strtotime(date('d-m-Y h:i:s A',now())),
                                'delivery_charge' => $delivery_charge,
                                'pincode' => $row_address->pincode,
                                'building_name' => $row_address->building_name,
                                'road_area_colony' => $row_address->road_area_colony,
                                'city' => $row_address->city,
                                'district' => $row_address->district,
                                'state' => $row_address->state,
                                'country' => $row_address->country,
                                'landmark' => $row_address->landmark,
                                'name' => $row_address->name,
                                'email' => $row_address->email,
                                'mobile_no' => $row_address->mobile_no,
                                'alter_mobile_no' => $row_address->alter_mobile_no,
                                'address_type' => $row_address->address_type
                            );

                            $data_ord = $this->security->xss_clean($data_arr);

                            $order_id = $this->General_model->insert($data_ord, 'tbl_order_details');

                            if($cart_type=='main_cart'){
                                $my_cart=$this->Api_model->get_cart($this->user_id);
                            }
                            else{
                                $my_cart=$this->Api_model->get_cart($this->user_id,$cart_ids);
                            }

                            $products_arr=$this->save_order_items($order_id, $delivery_charge, $my_cart);

                            $data_arr = array(
                                'user_id' => $this->user_id,
                                'email' => $this->current_user_data->user_email,
                                'order_id' => $order_id,
                                'order_unique_id' => $order_unique_id,
                                'gateway' => 'razorpay',
                                'payment_amt' => $payable_amt,
                                'payment_id' => $payment_id,
                                'razorpay_order_id' => $razorpay_order_id,
                                'date' => strtotime(date('d-m-Y h:i:s A',now())),
                                'status' => '1',
                            );

                            $data_usr = $this->security->xss_clean($data_arr);

                            $this->General_model->insert($data_usr, 'tbl_transaction');

                            $data_update = array('order_status'  =>  '1');

                            $this->General_model->update($data_update, $order_id,'tbl_order_details');

                            $data_arr = array(
                                'order_id' => $order_id,
                                'user_id' => $this->user_id,
                                'product_id' => 0,
                                'status_title' => 1,
                                'status_desc' => $this->lang->line('0'),
                                'created_at' => strtotime(date('d-m-Y h:i:s A',now()))
                            );

                            $data_usr = $this->security->xss_clean($data_arr);

                            $this->General_model->insert($data_usr, 'tbl_order_status');

                            $where = array('order_id' => $order_id);

                            $row_items=$this->General_model->selectByids($where, 'tbl_order_items');

                            foreach ($row_items as $key2 => $value2) {

                                $data_arr = array(
                                    'order_id' => $order_id,
                                    'user_id' => $value2->user_id,
                                    'product_id' => $value2->product_id,
                                    'status_title' => '1',
                                    'status_desc' => $this->lang->line('0'),
                                    'created_at' => strtotime(date('d-m-Y h:i:s A',now()))
                                );

                                $data_usr = $this->security->xss_clean($data_arr);

                                $this->General_model->insert($data_usr, 'tbl_order_status');
                            }

                            $data_email['payment_mode'] = strtoupper('razorpay');
                            $data_email['payment_id'] = $payment_id;

                            $delivery_address=$row_address->building_name.', '.$row_address->road_area_colony.',<br/>'.$row_address->pincode.'<br/>'.$row_address->city.', '.$row_address->state.', '.$row_address->country;

                            $data_email['users_name']=$row_address->name;
                            $data_email['users_email']=$row_address->email;
                            $data_email['users_mobile']=$row_address->mobile_no;

                            $admin_name=$this->General_model->selectByidsParam(array('id' => 1),'tbl_admin','username');

                            $data_email['admin_name']=ucfirst($admin_name);

                            $data_email['order_unique_id']=$order_unique_id;
                            $data_email['order_date']=date('d M, Y');
                            $data_email['delivery_address']=$delivery_address;
                            $data_email['discount_amt']=$this->session->userdata('discount_amt');
                            $data_email['total_amt']=$this->session->userdata('total_amt');
                            $data_email['delivery_charge']=$this->session->userdata('delivery_charge');
                            $data_email['payable_amt']=$this->session->userdata('payable_amt');

                            $data_email['products']=$products_arr;

                            $this->send_order_email($data_email);

                            $this->General_model->deleteByids(array('user_id' => $this->user_id, 'cart_type' => $cart_type),'tbl_applied_coupon');

                            if($buy_now=='false'){
                                $this->General_model->deleteByids(array('user_id' => $this->user_id),'tbl_cart');
                            }
                            else{
                                $this->General_model->deleteByids(array('user_id' => $this->user_id),'tbl_cart_tmp');
                                $this->General_model->deleteByids(array('user_id' => $this->user_id, 'product_id' => $this->session->userdata('product_id')),'tbl_cart');
                            }

                            $array_items = array('buy_now','coupon_id', 'address_id', 'cart_ids', 'razorpay_order_id','product_id');

                            $this->session->unset_userdata($array_items);

                            $response=array('status' => 1,'msg' => $this->lang->line('ord_summary_mail_msg'), 'order_unique_id' => $order_unique_id); 
                        }
                        else{
                            echo json_encode($get_cart_details);
                            exit();
                        }
                    }
                    else{
                        $response = array('status' => 0, 'msg' => $this->lang->line('no_address_found'));
                    }
                }
                catch(SignatureVerificationError $e){

                    $response=array('status' => 0, 'msg' => $e->getMessage());
                }
            }
        }

        echo json_encode($response);
        exit();
    }
}