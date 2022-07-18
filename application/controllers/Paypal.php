<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH.'controllers/Checkout.php';

require_once(APPPATH . 'libraries/paypal-php-sdk/autoload.php');

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

use PayPal\Api\PayerInfo;

class Paypal extends Checkout
{
    private $api_context;

    public function  __construct()
    {
        parent::__construct();

        ini_set('MAX_EXECUTION_TIME', '-1');
        $this->config->load('paypal');

        $this->api_context = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $this->settings->paypal_client_id,
                $this->settings->paypal_secret_key
            )
        );
    }

    public function pay()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');   
        }
        else{

            $products_arr = array();
            $data_email = array();

            $buy_now=$this->input->post('buy_now');

            $address_id=$this->input->post('address_id');

            $row_address=$this->General_model->selectByid($address_id, 'tbl_addresses');

            if(!empty($row_address)){

                $coupon_id=$this->input->post('coupon_id');
                $cart_type=($buy_now=='true') ? 'temp_cart' : 'main_cart';

                $total_cart_amt=$delivery_charge=$you_save=0;

                $cart_ids=implode(',', $this->input->post('cart_ids'));
                
                $get_cart_details=$this->get_cart_details($cart_type, $cart_ids);

                if($get_cart_details['status']==1){

                    $total_cart_amt=$get_cart_details['total_cart_amt'];
                    $delivery_charge=$get_cart_details['delivery_charge'];
                    $you_save=$get_cart_details['you_save'];

                    $where=array('user_id' => $this->user_id, 'cart_type' => $cart_type);

                    $coupon_id=$this->input->post('coupon_id');

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

                    $order_unique_id = 'ORD' . $this->get_order_unique_id() . rand(0, 1000);
                    $this->session->set_userdata('order_unique_id', $order_unique_id);

                    $this->api_context->setConfig($this->config->item('settings'));

                    $itemList = new ItemList();

                    $items = array();

                    if($cart_type=='main_cart'){
                        $my_cart=$this->Api_model->get_cart($this->user_id);
                    }
                    else{
                        $my_cart=$this->Api_model->get_cart($this->user_id,$cart_ids);
                    }

                    foreach ($my_cart as $value) {

                        $cart_id = $value->id;

                        $total_price = ($value->product_qty * $value->selling_price);

                        $product_mrp = $value->selling_price;

                        $delivery_charge = $value->delivery_charge;

                        $item["name"] = $value->product_title;
                        $item["sku"] = $value->product_id;

                        $description = '';

                        if (strlen($this->General_model->selectByidsParam(array('id' => $value->product_id), 'tbl_product', 'product_desc')) > 30) {
                            $description = substr(stripslashes($this->General_model->selectByidsParam(array('id' => $value->product_id), 'tbl_product', 'product_desc')), 0, 30) . '...';
                        } else {
                            $description = $this->General_model->selectByidsParam(array('id' => $value->product_id), 'tbl_product', 'product_desc');
                        }

                        $item["description"] = strip_tags($description);
                        $item["currency"] = APP_CURRENCY;
                        $item["quantity"] = $value->product_qty;

                        $product_actual_amt = 0;

                        if ($coupon_id != 0) {

                            $product_per = sprintf("%01.2f", (float)(($product_mrp / $total_cart_amt) * 100));
                            $product_amt_per = sprintf("%01.2f", (float)(($product_per / 100) * $discount_amt));
                            $product_actual_amt = $product_mrp - $product_amt_per;
                        } else {
                            $product_actual_amt = sprintf("%01.2f", (float)$product_mrp);
                        }

                        $product_actual_amt += sprintf("%01.2f", (float)$delivery_charge);

                        $item["price"] = sprintf("%01.2f", (float)$product_actual_amt);

                        array_push($items, $item);
                    }

                    $itemList->setItems($items);

                    $payer['payment_method'] = 'paypal';

                    $details['tax'] = '';
                    $details['subtotal'] = sprintf("%01.2f", (float)$payable_amt );

                    $amount['currency'] = APP_CURRENCY;
                    $amount['total'] = sprintf("%01.2f", (float)$payable_amt);

                    $transaction['description'] = 'Order Payment description';
                    $transaction['amount'] = $amount;
                    $transaction['invoice_number'] = uniqid();
                    $transaction['item_list'] = $itemList;

                    $baseUrl = base_url();
                    $redirectUrls = new RedirectUrls();
                    $redirectUrls->setReturnUrl($baseUrl . "paypal/getPaymentStatus")
                        ->setCancelUrl($baseUrl . "paypal/getPaymentStatus");

                    $payment = new Payment();
                    $payment->setIntent("sale")
                        ->setPayer($payer)
                        ->setRedirectUrls($redirectUrls)
                        ->setTransactions(array($transaction));

                    try {
                        $payment->create($this->api_context);

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

                        $this->session->set_userdata("order_id", $order_id);

                        $products_arr=$this->save_order_items($order_id, $delivery_charge, $my_cart);

                        $data_arr = array(
                            'user_id' => $this->user_id,
                            'email' => $this->session->userdata('user_email'),
                            'order_id' => $order_id,
                            'order_unique_id' => $order_unique_id,
                            'gateway' => 'paypal',
                            'date' => strtotime(date('d-m-Y h:i:s A', now())),
                        );

                        $data_usr = $this->security->xss_clean($data_arr);

                        $this->General_model->insert($data_usr, 'tbl_transaction');

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

                        foreach ($row_items as $key2 => $value2) 
                        {
                            $data_arr = array(
                                'order_id' => $order_id,
                                'user_id' => $value2->user_id,
                                'product_id' => $value2->product_id,
                                'status_title' => 1,
                                'status_desc' => $this->lang->line('0'),
                                'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                            );

                            $data_usr = $this->security->xss_clean($data_arr);

                            $this->General_model->insert($data_usr, 'tbl_order_status');

                            $data_pro = array('pro_order_status' => '1');

                            $data_pro = $this->security->xss_clean($data_pro);

                            $this->General_model->updateByids($data_pro, array('order_id' => $order_id, 'product_id' => $value2->product_id), 'tbl_order_items');
                        }

                        $data_email['payment_mode'] = strtoupper('paypal');

                        $delivery_address=$row_address->building_name.', '.$row_address->road_area_colony.',<br/>'.$row_address->pincode.'<br/>'.$row_address->city.', '.$row_address->state.', '.$row_address->country;

                        $data_email['users_name']=$row_address->name;
                        $data_email['users_email']=$row_address->email;
                        $data_email['users_mobile']=$row_address->mobile_no;

                        $admin_name=$this->General_model->selectByidsParam(array('id' => 1),'tbl_admin','username');

                        $data_email['admin_name']=ucfirst($admin_name);

                        $data_email['order_unique_id']=$order_unique_id;
                        $data_email['order_date']=date('d M, Y');
                        $data_email['delivery_address']=$delivery_address;
                        $data_email['discount_amt'] = $discount_amt;
                        $data_email['total_amt'] = $total_cart_amt;
                        $data_email['delivery_charge'] = $delivery_charge;
                        $data_email['payable_amt'] = $payable_amt;

                        $data_email['products']=$products_arr;

                        $this->session->set_userdata("data_email", $data_email);

                    } catch (Exception $ex) {
                        ResultPrinter::printError("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $ex);
                        exit(1);
                    }

                    foreach ($payment->getLinks() as $link) {
                        if ($link->getRel() == 'approval_url') {
                            $redirect_url = $link->getHref();
                            break;
                        }
                    }

                    if (isset($redirect_url)) {
                        redirect($redirect_url);
                    }

                    $message = array('message' => $this->lang->line('something_went_wrong_err'), 'class' => 'error');
                    $this->session->set_flashdata('response_msg', $message);
                    
                }
                else{
                    $message = array('message' => $get_cart_details['msg'], 'class' => 'error');
                    $this->session->set_flashdata('response_msg', $message);
                }
            }
            else{
                $message = array('message' => $this->lang->line('no_address_found'), 'class' => 'error');
                $this->session->set_flashdata('response_msg', $message);
            }
        }

        redirect($this->input->post('current_page'), 'refresh');
    }

    public function getPaymentStatus()
    {
        $order_id = $this->session->userdata('order_id');
        $order_unique_id = $this->session->userdata('order_unique_id');

        $data_email = $this->session->userdata('data_email');

        $payment_id = $this->input->get("paymentId");
        $PayerID = $this->input->get("PayerID");
        $token = $this->input->get("token");
        
        if (empty($PayerID) || empty($token)) {

            $this->General_model->deleteByids(array('id' => $order_id, 'user_id' => $this->user_id), 'tbl_order_details');

            $this->General_model->deleteByids(array('order_id' => $order_id, 'user_id' => $this->user_id), 'tbl_order_items');

            $this->General_model->deleteByids(array('order_id' => $order_id, 'user_id' => $this->user_id), 'tbl_order_status');

            $this->General_model->deleteByids(array('order_id' => $order_id, 'user_id' => $this->user_id), 'tbl_transaction');

            $array_items = array('order_id', 'order_unique_id', 'data_email');

            $this->session->unset_userdata($array_items);

            $message = array('message' => 'Error in Payment!', 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect($this->input->post('current_page'), 'refresh');
        }

        $payment = Payment::get($payment_id, $this->api_context);

        $execution = new PaymentExecution();
        $execution->setPayerId($this->input->get('PayerID'));

        $result = $payment->execute($execution, $this->api_context);

        if ($result->getState() == 'approved') {
            $trans = $result->getTransactions();

            $relatedResources = $trans[0]->getRelatedResources();
            $sale = $relatedResources[0]->getSale();

            $saleId = $sale->getId();
            $CreateTime = $sale->getCreateTime();
            $UpdateTime = $sale->getUpdateTime();
            $State = $sale->getState();
            $Total = $sale->getAmount()->getTotal();

            $data_arr = array(
                'payment_amt' => $Total,
                'payment_id' => $payment_id,
                'status' => '1'
            );

            $data_usr = $this->security->xss_clean($data_arr);

            $where = array('order_id' => $order_id);

            $this->General_model->updateByids($data_usr, $where, 'tbl_transaction');

            $data_update = array(
                'order_status'  =>  '1',
            );

            $this->General_model->update($data_update, $order_id, 'tbl_order_details');

            if (!$this->input->post('buy_now')){

                $this->General_model->deleteByids(array('user_id' => $this->user_id, 'cart_type' => 'main_cart'),'tbl_applied_coupon');

                $this->General_model->deleteByids(array('user_id' => $this->user_id), 'tbl_cart');
            }
            else{

                $this->General_model->deleteByids(array('user_id' => $this->user_id, 'cart_type' => 'temp_cart'),'tbl_applied_coupon');

                $row_items = $this->General_model->selectByids(array('order_id' => $order_id), 'tbl_order_items');

                foreach ($row_items as $key => $value) {
                    
                    $this->General_model->deleteByids(array('user_id' => $this->user_id, 'product_id' => $value->product_id), 'tbl_cart_tmp');

                    $this->General_model->deleteByids(array('user_id' => $this->user_id, 'product_id'  =>  $value->product_id),'tbl_cart');
                }
            }

            $data_email['payment_id']=$payment_id;

            $this->send_order_email($data_email);

            $array_items = array('order_id', 'order_unique_id', 'data_email');
            $this->session->unset_userdata($array_items);
            
            redirect('order-confirm?order='.$order_unique_id, 'refresh');
        }

        redirect('paypal/cancel');
    }

    public function cancel()
    {
        $order_id = $this->session->userdata('order_id');

        $this->General_model->deleteByids(array('id' => $order_id, 'user_id' => $this->user_id), 'tbl_order_details');

        $this->General_model->deleteByids(array('order_id' => $order_id, 'user_id' => $this->user_id), 'tbl_order_items');

        $this->General_model->deleteByids(array('order_id' => $order_id, 'user_id' => $this->user_id), 'tbl_order_status');

        $this->General_model->deleteByids(array('order_id' => $order_id, 'user_id' => $this->user_id), 'tbl_transaction');

        $array_items = array('order_id', 'order_unique_id', 'data_email');

        $this->session->unset_userdata($array_items);

        $message = array('message' => 'Payment failed', 'class' => 'error');
        $this->session->set_flashdata('response_msg', $message);
        redirect($this->input->post('current_page'));
    }
}
