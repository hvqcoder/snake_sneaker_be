<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH.'controllers/Checkout.php';

class Order extends Checkout 
{
    public function __construct(){
        parent::__construct();
        $this->load->model('Order_model');
    }

    public function place_new_order()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $buy_now=$this->input->post('buy_now');

            $cart_type=($buy_now=='true') ? 'temp_cart' : 'main_cart';

            $address_id=$this->input->post('address_id');

            $row_address=$this->General_model->selectByid($address_id, 'tbl_addresses');

            if(!empty($row_address)){

                $products_arr=array();
                $data_email=array();

                $total_cart_amt=$delivery_charge=$you_save=0;

                $order_unique_id='ORD'.$this->get_order_unique_id().rand(0,1000);

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
                        'email' => $this->session->userdata('user_email'),
                        'order_id' => $order_id,
                        'order_unique_id' => $order_unique_id,
                        'gateway' => $this->input->post('payment_method'),
                        'payment_amt' => $payable_amt,
                        'payment_id' => '0',
                        'date' => strtotime(date('d-m-Y h:i:s A',now())),
                        'status' => '1'
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

                    $data_email['payment_mode'] = strtoupper('cod');
                    $data_email['payment_id'] = 0;

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

                    $this->send_order_email($data_email);

                    $this->General_model->deleteByids(array('user_id' => $this->user_id, 'cart_type' => $cart_type),'tbl_applied_coupon');

                    if($buy_now=='false'){
                        $this->General_model->deleteByids(array('user_id' => $this->user_id),'tbl_cart');
                    }
                    else{
                        $this->General_model->deleteByids(array('user_id' => $this->user_id),'tbl_cart_tmp');
                        $this->General_model->deleteByids(array('user_id' => $this->user_id, 'product_id' => $this->session->userdata('product_id')),'tbl_cart');
                    }

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

        echo json_encode($response);
        exit();
    }

    public function order_confirm()
    {

        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $order_unique_id=$this->input->get("order");

        if(empty($order_unique_id)){
            $message = array('message' => $this->lang->line('no_data_found_msg'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('/', 'refresh');
        }

        $this->load->model('Api_model');

        $data = array();
        $data['page_title'] = $this->lang->line('myorders_lbl');
        $data['current_page'] = $order_unique_id;

        $data['my_order'] = $this->Api_model->get_order($order_unique_id);

        if(empty($data['my_order'])){
            if(isset($_SERVER['HTTP_REFERER']))
                redirect($_SERVER['HTTP_REFERER']);
            else
                redirect('/', 'refresh');
        }

        $this->template->load('site/template2', 'site/pages/order-confirm', $data);
    }

    public function my_order($order_unique_id=NULL){

        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $data = array();
        $data['page_title'] = $this->lang->line('myorders_lbl');
        $data['current_page'] = $this->lang->line('myorders_lbl');

        if($order_unique_id!=NULL){

            $row_order=$this->Order_model->get_order_details(array('order.order_unique_id' => $order_unique_id));

            if(empty($row_order)){
                if(isset($_SERVER['HTTP_REFERER']))
                    redirect($_SERVER['HTTP_REFERER']);
                else
                    redirect('/', 'refresh');
            }

            $data['order_details'] = $row_order;

            $data['order_items'] = $this->Order_model->get_order_items($row_order->id);

            $data['current_page'] = $order_unique_id;

            $data['order_address'] = $this->General_model->selectByid($row_order->order_address, 'tbl_addresses');

            $data['status_titles'] = $this->Order_model->get_titles(true);

            $data['order_status'] = $this->Order_model->get_product_status($row_order->id,0);

            $where = array('order_unique_id ' => $order_unique_id);

            $rowRefund = $this->General_model->selectByids($where, 'tbl_refund');

            $data['refund_data'] = $rowRefund;

            $data['bank_details'] = $this->General_model->selectByids(array('user_id' => $this->user_id), 'tbl_bank_details','is_default');

            $this->template->load('site/template2', 'site/pages/order_detail', $data);
        }
        else
        {
            $this->load->library('pagination');

            $config = array();
            $config["base_url"] = base_url('my-orders');
            $config["per_page"] = $this->page_limit;
            $config['use_page_numbers'] = TRUE;
            $config['page_query_string'] = TRUE;
            $config['query_string_segment'] = 'page';

            $page = ($this->input->get('page')) ? $this->input->get('page') : 1;
            $page = ($page - 1) * $config["per_page"];

            $row_orders=$this->Api_model->get_my_orders($this->user_id);

            $orders=$this->Api_model->get_my_orders($this->user_id, $config["per_page"], $page);

            $config["total_rows"] = count($row_orders);

            $config['num_links'] = 2;
            $config['reuse_query_string'] = TRUE;

            $config['full_tag_open'] = '<ul class="page-number">';
            $config['full_tag_close'] = '</ul>';

            $config['first_link'] = '<i class="fa fa-angle-double-left"></i>';
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';

            $config['last_link'] = '<i class="fa fa-angle-double-right"></i>';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';

            $config['next_link'] = '';
            $config['next_tag_open'] = '<span class="nextlink">';
            $config['next_tag_close'] = '</span>';

            $config['prev_link'] = '';
            $config['prev_tag_open'] = '<span class="prevlink">';
            $config['prev_tag_close'] = '</span>';

            $config['cur_tag_open'] = '<li class="active"><a href="#">';
            $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';

            $config['num_tag_open'] = '<li style="margin:3px">';
            $config['num_tag_close'] = '</li>';

            $this->pagination->initialize($config);

            $data["links"] = $this->pagination->create_links();

            $data['my_orders'] = $orders;

            $data['bank_details'] = $this->General_model->selectByids(array('user_id' => $this->user_id), 'tbl_bank_details','is_default');

            $this->template->load('site/template2', 'site/pages/my_orders', $data);
        }
    }

    public function is_order_claim($order_id)
    {
        $count_items=count($this->General_model->selectByids(array('order_id' => $order_id), 'tbl_order_items'));
        $count_refund_items=count($this->General_model->selectByids(array('order_id' => $order_id, 'request_status' => '-1'), 'tbl_refund'));

        if($count_items==$count_refund_items){
            return true;
        }
        else{
            return false;
        }
    }

    public function order_status($order_id, $product_id=NULL){
        $where=array('order_id' => $order_id);
        return $this->General_model->selectWhere('tbl_order_status',$where,'DESC');
    }

    public function get_status_title($id){
        return $this->General_model->selectByidParam($id,'tbl_status_title','title');
    }

    public function cancel_product(){

        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $order_id = $this->input->post('order_id');
            $product_id = $this->input->post('product_id');

            $reason = $this->input->post('reason');
            $bank_id = $this->input->post('bank_id');

            $where=array('order_id' => $order_id);

            $row_transaction=$this->Order_model->get_order_transaction($order_id);

            $row_ord=$this->General_model->selectByid($order_id, 'tbl_order_details');

            if(!empty($row_transaction)){

                $products_arr = array();

                $actual_pay_amt = ($row_ord->payable_amt - $row_ord->delivery_charge);

                $refund_amt = $pro_refund_amt = $product_per = $refund_per = $new_payable_amt = $total_refund_amt = $total_refund_per = 0;

                $order_items=$this->Order_model->get_order_items($order_id, array('items.`pro_order_status` <>' => 5));

                $total_items=count($order_items);

                if($product_id==0){

                    foreach ($order_items as $key => $value) {

                        $product_per = $new_payable_amt = 0;

                        if ($row_ord->coupon_id != 0) {

                            $product_per = amount_format(($value->total_price / $row_ord->total_amt) * 100);

                            $refund_per = amount_format(($product_per / 100) * $row_ord->discount_amt);

                            $refund_amt = amount_format($value->total_price - $refund_per);
                        } else {
                            $refund_amt = $value->total_price;
                            $new_payable_amt = ($row_ord->payable_amt - $refund_amt);
                        }

                        if ($row_transaction->gateway == 'COD' || $row_transaction->gateway == 'cod') {
                            $bank_id = 0;
                            $status = 1;
                        } else {
                            $status = 0;
                        }

                        $total_refund_amt += $refund_amt;
                        $total_refund_per += $refund_per;

                        $data_arr = array(
                            'bank_id' => $bank_id,
                            'user_id' => $this->user_id,
                            'order_id' => $order_id,
                            'order_unique_id' => $row_ord->order_unique_id,
                            'product_id' => $value->product_id,
                            'product_title' => $value->product_title,
                            'product_amt' => $value->total_price,
                            'refund_pay_amt' => $refund_amt,
                            'refund_per' => $refund_per,
                            'gateway' => $row_transaction->gateway,
                            'refund_reason' => $reason,
                            'last_updated' => strtotime(date('d-m-Y h:i:s A', now())),
                            'request_status' => $status,
                            'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                        );

                        $data_update = $this->security->xss_clean($data_arr);

                        $this->General_model->insert($data_update, 'tbl_refund');

                        $data = array(
                            'order_id' => $order_id,
                            'user_id' => $this->user_id,
                            'product_id' => $value->product_id,
                            'status_title' => '5',
                            'status_desc' => $this->lang->line('pro_ord_cancel'),
                            'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                        );

                        $data = $this->security->xss_clean($data);

                        $this->General_model->insert($data, 'tbl_order_status');

                        $data = array('pro_order_status' => '5');

                        $data = $this->security->xss_clean($data);

                        $this->General_model->updateByids($data, array('order_id' => $order_id, 'product_id' => $value->product_id), 'tbl_order_items');
                    }

                    $data = array(
                        'order_status' => '5',
                        'new_payable_amt'  =>  '0',
                        'refund_amt'  =>  $total_refund_amt,
                        'refund_per'  =>  $total_refund_per
                    );

                    $data = $this->security->xss_clean($data);
                    $this->General_model->update($data, $order_id, 'tbl_order_details');

                    $data = array(
                        'order_id' => $order_id,
                        'user_id' => $this->user_id,
                        'product_id' => '0',
                        'status_title' => '5',
                        'status_desc' => $this->lang->line('ord_cancel'),
                        'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                    );

                    $data = $this->security->xss_clean($data);

                    $this->General_model->insert($data, 'tbl_order_status');

                }
                else{

                    $order_items=$this->Order_model->get_order_items($order_id, array('items.`product_id`' => $product_id));

                    if(!empty($order_items)){

                        foreach ($order_items as $value)
                        {
                            if ($row_ord->coupon_id != 0) {
                                $product_per = amount_format(($value->total_price / $row_ord->total_amt) * 100);

                                $refund_per = amount_format(($product_per / 100) * $row_ord->discount_amt);

                                $refund_amt = amount_format($value->total_price - $refund_per);

                                $new_payable_amt = amount_format($row_ord->new_payable_amt - $refund_amt);
                            }
                            else {
                                $refund_amt = $value->total_price;
                                $new_payable_amt = ($row_ord->new_payable_amt - $refund_amt);
                            }

                            if ($row_transaction->gateway == 'COD' || $row_transaction->gateway == 'cod') {
                                $bank_id = 0;
                                $status = 1;
                            } else {
                                $status = 0;
                            }

                            $data_arr = array(
                                'bank_id' => $bank_id,
                                'user_id' => $this->user_id,
                                'order_id' => $order_id,
                                'order_unique_id' => $row_ord->order_unique_id,
                                'product_id' => $product_id,
                                'product_title' => $value->product_title,
                                'product_amt' => $value->total_price,
                                'refund_pay_amt' => $refund_amt,
                                'refund_per' => $refund_per,
                                'gateway' => $row_transaction->gateway,
                                'refund_reason' => $reason,
                                'last_updated' => strtotime(date('d-m-Y h:i:s A', now())),
                                'request_status' => $status,
                                'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                            );

                            $data_update = $this->security->xss_clean($data_arr);

                            $this->General_model->insert($data_update, 'tbl_refund');

                            $where = array('order_id' => $order_id, 'pro_order_status <> ' => 5);

                            if (count($this->General_model->selectByids($where, 'tbl_order_items')) == 1) {

                                $pro_refund_amt = $refund_amt;

                                $refund_amt = $row_ord->refund_amt + $refund_amt;
                                $new_payable_amt = ($row_ord->payable_amt - $refund_amt);
                                $refund_per = $row_ord->refund_per + $refund_per;

                                $data_update = array(
                                    'order_status' => '5',
                                    'new_payable_amt'  =>  '0',
                                    'refund_amt'  =>  $refund_amt,
                                    'refund_per'  =>  $refund_per
                                );

                                $data = array(
                                    'order_id' => $order_id,
                                    'user_id' => $this->user_id,
                                    'product_id' => '0',
                                    'status_title' => '5',
                                    'status_desc' => $this->lang->line('ord_cancel'),
                                    'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                                );

                                $data = $this->security->xss_clean($data);

                                $this->General_model->insert($data, 'tbl_order_status');
                            } else {

                                $pro_refund_amt = $refund_amt;

                                $refund_amt = $row_ord->refund_amt + $refund_amt;
                                $new_payable_amt = ($row_ord->payable_amt - $refund_amt);
                                $refund_per = $row_ord->refund_per + $refund_per;

                                $data_update = array(
                                    'new_payable_amt'  =>  $new_payable_amt,
                                    'refund_amt'  =>  $refund_amt,
                                    'refund_per'  =>  $refund_per
                                );
                            }

                            $this->General_model->update($data_update, $order_id, 'tbl_order_details');

                            $data_pro = array(
                                'pro_order_status' => '5'
                            );

                            $data_pro = $this->security->xss_clean($data_pro);

                            $this->General_model->updateByids($data_pro, array('order_id' => $order_id, 'product_id' => $product_id), 'tbl_order_items');

                            $data = array(
                                'order_id' => $order_id,
                                'user_id' => $this->user_id,
                                'product_id' => $product_id,
                                'status_title' => '5',
                                'status_desc' => $this->lang->line('pro_ord_cancel'),
                                'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                            );

                            $data = $this->security->xss_clean($data);

                            $this->General_model->insert($data, 'tbl_order_status');

                            $this->General_model->updateByids($data_pro, array('order_id' => $order_id, 'product_id' => $value->product_id), 'tbl_order_items');
                        }
                    }
                    else{
                        $response=array('status' => 0, 'msg' => $this->lang->line('no_data_found_msg'));
                        echo json_encode($response);
                        exit();
                    }
                }

                foreach ($order_items as $value) {
                    $thumb_img = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value->featured_image);

                    $img_file=$this->_generate_thumbnail('assets/images/products/',$thumb_img,$value->featured_image,300,300);

                    $p_items['product_url']=base_url('product/'.$value->product_slug);

                    $p_items['product_title']=$value->product_title;
                    $p_items['product_img']=base_url($img_file);
                    $p_items['product_qty']=$value->product_qty;
                    $p_items['product_price']=$value->product_price;
                    $p_items['product_size']=$value->product_size;

                    $product_color=$value->color;

                    if ($product_color != '') {
                        $color_arr = explode('/', $product_color);
                        $color_name = $color_arr[0];
                        $product_color=$color_name;
                    }

                    $p_items['product_color'] = $product_color;

                    array_push($products_arr, $p_items);
                }

                $data_email = array();

                $admin_name = $this->General_model->selectByidsParam(array('id' => 1), 'tbl_admin', 'username');

                $data_email['payment_mode'] = strtoupper($row_transaction->gateway);
                $data_email['payment_id'] = $row_transaction->payment_id;

                $data_email['users_name'] = $row_ord->name;

                $data_email['cancel_heading'] = str_replace('###', $row_ord->order_unique_id, $this->lang->line('self_cancelled_lbl'));

                $data_email['admin_cancel_heading'] = '';
                $data_email['admin_name'] = '';

                $data_email['order_unique_id'] = $row_ord->order_unique_id;
                $data_email['order_date'] = date('d M, Y', $row_ord->order_date);

                $data_email['delivery_date'] = date('d M, Y', $row_ord->delivery_date);
                $data_email['refund_amt'] = ($total_refund_amt == 0) ? amount_format($pro_refund_amt) : amount_format($total_refund_amt);

                $data_email['status_desc'] = $reason;
                $data_email['order_status'] = $row_ord->order_status;

                $data_email['products'] = $products_arr;

                $subject = $this->settings->app_name . ' - ' . $this->lang->line('ord_status_update_lbl');

                $body = $this->load->view('emails/order_cancel.php', $data_email, TRUE);

                send_email($row_ord->email, $row_ord->name, $subject, $body);

                if ($this->settings->app_order_email != '') {

                    $data_email['admin_cancel_heading'] = str_replace('###', $row_ord->order_unique_id, $this->lang->line('admin_cancelled_lbl'));
                    $data_email['admin_name'] = $admin_name;

                    $subject = $this->settings->app_name . ' - ' . $this->lang->line('ord_cancel_detail_lbl');
                    $body = $this->load->view('emails/order_cancel.php', $data_email, TRUE);
                    send_email($this->settings->app_order_email, $admin_name, $subject, $body);
                }

                $message = array('message' => $this->lang->line('ord_cancelled_lbl'),'class' => 'success');
                $this->session->set_flashdata('response_msg', $message);
                $response = array('status' => 1, 'msg' => $this->lang->line('ord_cancelled_lbl'));
            }
            else{
                $response=array('status' => 0, 'msg' => $this->lang->line('no_data_found_msg'));
            }
        }

        echo json_encode($response);
        exit();
    }

    public function download_invoice()
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $data = array();

        $order_no =  $this->uri->segment(2);

        $row_order=$this->Order_model->get_order_details(array('order.order_unique_id' => $order_no));

        if (empty($row_order)) {
            redirect('page_not_found','refresh');
        }

        $data['page_title'] = 'Orders';
        $data['current_page'] = 'Order Summary';
        $data['order_details'] = $row_order;

        $data['order_items'] = $this->Order_model->get_order_items($row_order->id, array('items.`pro_order_status` <> ' => 5));

        $data['invoice_no'] = $row_order->id;

        $this->load->view('download_invoice', $data);

        $stylesheet = file_get_contents("assets/site_assets/css/bootstrap.min.css");
        $stylesheet .= file_get_contents("assets/css/invoice.css");

        $html = $this->output->get_output();
        $file_name = $this->lang->line('ord_invoice_lbl') . " - " . $order_no . ".pdf";

        require_once(APPPATH . '../vendor/autoload.php');

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_left' => 0, 'margin_right' => 0, 'margin_top' => 0, 'margin_bottom' => 0]);
        $mpdf->SetDisplayMode('fullpage');

        $mpdf->debug = true;
        $mpdf->SetFont('Poppins-SemiBold');
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html,2);

        $mpdf->Output($file_name, 'I');
    }
}