<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller 
{
    public function __construct(){
        parent::__construct();
        $this->load->model('Users_model');
        $this->load->model('Api_model');
    }

    public function my_account(){

        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $data = array();
        $data['page_title'] = $this->lang->line('my_profile_lbl');
        $data['current_page'] = $this->lang->line('my_profile_lbl');

        $data['user_data'] = $this->General_model->selectByid($this->user_id, 'tbl_users');

        $this->template->load('site/template2', 'site/pages/my_account', $data);

    }

    public function update_profile()
    {
        $response=array();
        $rowData=$this->General_model->selectByids(array('user_type' => $this->current_user_data->user_type,'user_email' => $this->input->post('user_email'),'id <> ' => $this->user_id), 'tbl_users');

        if(empty($rowData))
        {
            if($_FILES['file_name']['error']!=4){

                if($this->current_user_data->user_image!=''){
                    unlink('assets/images/users/'.$this->current_user_data->user_image);
                    $mask = $this->current_user_data->id.'*_*';
                    array_map('unlink', glob('assets/images/users/thumbs/'.$mask));

                    $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->current_user_data->user_image);
                    $mask = $thumb_img_nm.'*_*';
                    array_map('unlink', glob('assets/images/users/thumbs/'.$mask));
                }

                $config['upload_path'] =  'assets/images/users/';
                $config['allowed_types'] = 'jpg|png|jpeg';

                $image = date('dmYhis').'_'.rand(0,99999).".".pathinfo($_FILES['file_name']['name'], PATHINFO_EXTENSION);

                $config['file_name'] = $image;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('file_name')) {
                    $response = array('status' => 0,'msg' => $this->upload->display_errors());
                    echo base64_encode(json_encode($response));
                    return;
                }
            }
            else{
                $image=$this->current_user_data->user_image;
            }

            $data = array(
                'user_name'  => $this->input->post('user_name'),
                'user_email'  => $this->input->post('user_email'),
                'user_phone'  => $this->input->post('user_phone'),
                'user_image'  => $image
            );

            $data_update = $this->security->xss_clean($data);

            $this->General_model->update($data_update, $this->user_id,'tbl_users');

            if ($image=='' || !file_exists('assets/images/users/'.$image)) {
                $image = base_url('assets/images/photo.jpg');
            }
            else{
                $image=base_url('assets/images/users/'.$image);
            }

            $response=array('status' => 1, 'msg' => $this->lang->line('profile_update_msg'), 'image' => $image);
        }
        else{
            $response=array('status' => 0, 'msg' => $this->lang->line('email_exist_error'));   
        }

        echo json_encode($response);
    }

    public function change_password_page()
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        if (strcmp($this->session->userdata('user_type'), 'Normal') != 0) {
            $this->template->load('site/template2', 'site/pages/404');
            return;
        }

        $data = array();
        $data['page_title'] = $this->lang->line('change_password_lbl');
        $data['current_page'] = $this->lang->line('change_password_lbl');

        $this->template->load('site/template2', 'site/pages/change_password', $data);
    }

    public function change_password()
    {
        $response=array();

        extract($this->input->post());

        if($this->current_user_data->user_password==md5($old_password)){

            $data_update = array(
                'user_password'  =>  md5($new_password)
            );

            $this->General_model->update($data_update, $this->user_id,'tbl_users');

            $response=array('status' => 1, 'msg' => $this->lang->line('change_password_msg'));
        }
        else{
            $response=array('status' => 0, 'msg' => $this->lang->line('wrong_password_error'), 'class' => 'err_old_password');
        }

        echo json_encode($response);
    }

    public function remove_profile(){

        $response=array();

        if($this->current_user_data->user_image!=''){
            unlink('assets/images/users/'.$this->current_user_data->user_image);
            $mask = $this->current_user_data->id.'*_*';
            array_map('unlink', glob('assets/images/users/thumbs/'.$mask));

            $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->current_user_data->user_image);
            $mask = $thumb_img_nm.'*_*';
            array_map('unlink', glob('assets/images/users/thumbs/'.$mask));
        }

        $data = array('user_image'  => '');

        $data_update = $this->security->xss_clean($data);

        $this->General_model->update($data_update, $this->user_id,'tbl_users');

        $response=array('status' => 1, 'msg' => $this->lang->line('remove_profile_success'));

        echo json_encode($response);
    }

    public function wishlist()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $data = array();
        $data['page_title'] = $this->lang->line('wishlist_lbl');
        $data['current_page'] = $this->lang->line('wishlist_lbl');
        $data['wishlist'] = $this->Api_model->get_wishlist($this->user_id);

        $this->template->load('site/template2', 'site/pages/wishlist', $data);
    }

    public function wishlist_action(){

        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 0, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $this->load->helper("date");

            $product_id=$this->input->post('product_id');

            $where = array('user_id' => $this->user_id , 'product_id' => $product_id);

            $row=$this->General_model->selectByids($where,'tbl_wishlist');

            $response=array();

            if(!empty($row)){

                $this->General_model->deleteByids($where, 'tbl_wishlist');

                $count=count($this->General_model->selectByids($where,'tbl_wishlist'));

                $response=array('icon_lbl' => $this->lang->line('add_wishlist_lbl'),'status' => 1, 'msg' => $this->lang->line('remove_wishlist'), "is_favorite" => false);

            }
            else{
                $data_arr = array(
                    'user_id' => $this->user_id,
                    'product_id' => $product_id,
                    'created_at' => strtotime(date('d-m-Y h:i:s A',now()))
                );

                $data_usr = $this->security->xss_clean($data_arr);

                $last_id = $this->General_model->insert($data_usr, 'tbl_wishlist');

                $count=count($this->General_model->selectByids($where,'tbl_wishlist'));

                $response=array('icon_lbl' => $this->lang->line('remove_wishlist_lbl'),'status' => 1, 'msg' => $this->lang->line('add_wishlist'), "is_favorite" => true);
            }
        }
        echo json_encode($response);
    }

    public function cart_action(){

        $response=array();

        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 0, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $this->load->model("Product_model");

            $product_id=$this->input->post('product_id');    

            $where = array('id' => $product_id);

            $row=$this->Product_model->getSingle($product_id);

            if(!empty($row)){

                $title=$old_price=$price=$size_view='';

                $row_cart=$this->General_model->selectByids(array('user_id' => $this->user_id, 'product_id' => $product_id),'tbl_cart');

                if(!empty($row_cart)){
                    $row_cart=$row_cart[0];
                }

                if(strlen($row->product_title) > 40){
                    $title=substr(stripslashes($row->product_title), 0, 40).'...';  
                }else{
                    $title=$row->product_title;
                }

                if($row->you_save_amt!='0'){
                    $price='<span class="new-price">'.CURRENCY_CODE.' '.$row->selling_price.'</span> 
                    <span class="old-price">'.CURRENCY_CODE.' '.$row->product_mrp.'</span>';
                }
                else{
                    $price='<span class="new-price">'.CURRENCY_CODE.' '.$row->product_mrp.'</span>';
                }

                $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $row->featured_image);

                $img_file=$this->_generate_thumbnail('assets/images/products/',$thumb_img_nm,$row->featured_image,250,250);

                if($row->product_size !=''){

                    $selected_size=(!empty($row_cart) AND $row_cart->product_size!='') ? $row_cart->product_size : 0;
                    $size='';
                    foreach (explode(',', $row->product_size) as $key => $value) {

                        if($selected_size!=0 AND $value==$selected_size){
                            $size.='<div class="radio_btn selected" data-value="'.$value.'">'.$value.'</div>';
                        }
                        else{

                            if($key==0)
                                $size.='<div class="radio_btn selected" data-value="'.$value.'">'.$value.'</div>';
                            else
                                $size.='<div class="radio_btn" data-value="'.$value.'">'.$value.'</div>';
                        }
                    }

                    $size_chart=(file_exists('assets/images/products/'.$row->size_chart) AND $row->size_chart!='') ? base_url('assets/images/products/'.$row->size_chart) : "";

                    if($size_chart!=''){
                        $size_view.='<p style="font-weight: 600;margin:5px 0px">'.$this->lang->line('size_lbl').': </p>
                        <div class="radio-group" style="margin-bottom:10px">
                        '.$size.'
                        <input type="hidden" id="radio-value" name="product_size" value="'.$selected_size.'" />

                        </div><a href="javascript:void(0)" class="size_chart" data-img="'.$size_chart.'"><img src="'.base_url('assets/images/size_chart.png').'" style="width:20px;height:20px;margin-right:4px;"> '.$this->lang->line('size_chart_lbl').'</a><br/><br/>';
                    }
                    else{

                        $size_view.='
                        <div class="clearfix"></div>
                        <p style="font-weight: 600;margin:5px 0px">'.$this->lang->line('size_lbl').'</p>
                        <div class="radio-group">
                        '.$size.'
                        <input type="hidden" id="radio-value" name="product_size" value="'.$selected_size.'" />
                        </div><br/>';

                    }
                }

                $preview_url='';

                if(isset($_SERVER['HTTP_REFERER']))
                { 
                    $preview_url=str_replace(base_url().'site/register','',$_SERVER['HTTP_REFERER']);
                }

                $max_unit_buy=($row->max_unit_buy) ? $row->max_unit_buy: 1;

                $product_qty=(empty($row_cart)) ? '1' : $row_cart->product_qty;

                $is_avail=($row->status==0) ? 'style="display:none"' : '';

                $cart_btn=(empty($row_cart)) ? $this->lang->line('add_cart_btn') : $this->lang->line('update_cart_btn');

                $response['status']=1;

                $response['max_unit_buy']=$row->max_unit_buy;

                $response['html_code']='<div class="modal-details">
                <div class="row">
                <div class="product-info">
                <div class="col-md-3 col-sm-3 col-xs-12">
                <img src="'.base_url($img_file).'" />
                </div>
                <div class="col-md-9 col-sm-9 col-xs-12">
                <h3>'.$title.'</h3>
                <div class="product-price">'.$price.'</div>
                <hr style="margin:10px 0"/>
                <div '.$is_avail.'>
                <form class="add-quantity" action="" method="post" id="cartForm" style="margin-bottom: 0px;">
                '.$size_view.'
                <input type="hidden" name="preview_url" value="'.$preview_url.'">
                <input type="hidden" name="product_id" value="'.encrypt_url($product_id).'" />
                <div class="quantity" style="">
                <input type="hidden" name="max_unit_buy" value="'.$max_unit_buy.'" class="max_unit_buy">
                <div class="buttons_added" style="display:inline-block;margin-right: 15px;margin-top: 1px;">
                <input type="button" value="-" class="minus">
                <input class="input-text product_qty" name="product_qty" value="'.$product_qty.'" type="text" min="1" max="'.$max_unit_buy.'" onkeypress="return isNumberKey(event)" readonly="">
                <input type="button" value="+" class="plus">
                </div>
                <button type="submit" class="form-button grow-btn" data-text="'.$cart_btn.'">'.$cart_btn.'</button>
                </form>
                </div>
                </div>
                </div>
                </div>';
            }
        }
        echo json_encode($response);
    }

    public function add_to_cart(){

        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 0, 'msg' => $this->lang->line('login_required_error'),'login_require' => 1);
        }
        else{

            if($this->input->post('product_id'))
            {
                $product_id=decrypt_url($this->input->post('product_id'));

                $product_row=$this->General_model->selectByid($product_id, 'tbl_product');

                if(!empty($product_row)){

                    if($product_row->status==0){
                        $response=array('status' => '0','msg' => $this->lang->line('product_unavailable_lbl'));
                    }
                    else{

                        if($this->input->post('product_qty') <= $product_row->max_unit_buy){

                            if(!$this->check_cart($product_id, $this->user_id)) {

                                $data_arr = array(
                                    'product_id' => $product_id,
                                    'user_id' => $this->user_id,
                                    'product_qty' => $this->input->post('product_qty'),
                                    'product_size' => $this->input->post('product_size'),
                                    'created_at' => strtotime(date('d-m-Y h:i:s A',now()))
                                );

                                $data_usr = $this->security->xss_clean($data_arr);

                                $cart_id = $this->General_model->insert($data_usr, 'tbl_cart');

                                $removeCartUrl=site_url('remove-to-cart/'.$cart_id);

                                $rowCart = $this->get_cart(3);

                                $html_content='';

                                foreach ($rowCart as $value) {

                                    $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value->featured_image);

                                    $img_file = base_url($this->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $value->featured_image, 50, 50));

                                    if (strlen($value->product_title) > 20) {
                                        $product_title=substr(stripslashes($value->product_title), 0, 20) . '...';
                                    } else {
                                        $product_title=$value->product_title;
                                    }

                                    $price = amount_format($value->selling_price * $value->product_qty);
                                    if (strlen($price) > 20) {
                                        $price= CURRENCY_CODE . ' ' . substr(stripslashes($price), 0, 20) . '...';
                                    } else {
                                        $price= CURRENCY_CODE . ' ' . $price;
                                    }

                                    $html_content.='<li class="cart-item">
                                    <div class="cart-img" style="width: auto"> <a href="javascript:void(0)"><img src="'.$img_file.'" alt="" style="width: 68px;height: 68px"></a> </div>
                                    <div class="cart-content">
                                    <h4>
                                    <a href="'.base_url('product/'.$value->product_slug).'" title="'.$product_title.'">'.$product_title.'</a>
                                    </h4>
                                    <p class="cart-quantity">'.$this->lang->line('qty_lbl').': '.$value->product_qty.'</p>
                                    <p class="cart-price">'.$price.'</p>
                                    </div>
                                    <div class="cart-close"> <a href="'.site_url('remove-to-cart/'.$value->id).'" class="btn_remove_cart" title="Remove"><i class="ion-android-close"></i></a> </div>
                                    </li>';
                                }

                                $total_cart_items=count($this->get_cart());

                                if($total_cart_items > 3){
                                    $html_content.='<li class="cart-item text-center"><h4 style="font-weight: 500">'.str_replace('###', ($total_cart_items - 3), $this->lang->line('remain_cart_items_lbl')).'</h4></li>';
                                }

                                $html_content.='<li class="cart-button"> <a href="'.base_url('my-cart').'" class="button2">'.$this->lang->line('view_cart_btn').'</a> <a href="'.base_url('checkout').'" class="button2">'.$this->lang->line('checkout_btn').'</a>
                                </li>';


                                $response=array('status' => 1,'msg' => $this->lang->line('add_cart'),'btn_lbl' => $this->lang->line('remove_cart_btn'),'product_id' => $product_id,'update_lbl' => $this->lang->line('update_cart_btn'),'tooltip_lbl' => $this->lang->line('remove_cart_lbl'),'removeCartUrl' => $removeCartUrl,'cart_items' => $total_cart_items,'cart_view' => $html_content);

                            }
                            else{

                                $data_arr = array(
                                    'product_qty' => $this->input->post('product_qty'),
                                    'product_size' => $this->input->post('product_size'),
                                    'last_update' => strtotime(date('d-m-Y h:i:s A',now()))
                                );

                                $data_usr = $this->security->xss_clean($data_arr);

                                $where = array('product_id ' => $product_id, 'user_id' => $this->user_id);

                                $updated_id=$this->General_model->updateByids($data_usr, $where,'tbl_cart');

                                $removeCartUrl=site_url('remove-to-cart/'.$updated_id);

                                $rowCart = $this->get_cart(3);
                                $html_content='';

                                foreach ($rowCart as $value) {

                                    $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value->featured_image);

                                    $img_file = base_url($this->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $value->featured_image, 50, 50));

                                    if (strlen($value->product_title) > 20)
                                    {
                                        $product_title=substr(stripslashes($value->product_title), 0, 20) . '...';
                                    }
                                    else {
                                        $product_title=$value->product_title;
                                    }

                                    $price = amount_format($value->selling_price * $value->product_qty);
                                    if (strlen($price) > 20) {
                                        $price= CURRENCY_CODE . ' ' . substr(stripslashes($price), 0, 20) . '...';
                                    } else {
                                        $price= CURRENCY_CODE . ' ' . $price;
                                    }

                                    $html_content.='<li class="cart-item">
                                    <div class="cart-img" style="width: auto"> <a href="javascript:void(0)"><img src="'.$img_file.'" alt="" style="width: 68px;height: 68px"></a> </div>
                                    <div class="cart-content">
                                    <h4>
                                    <a href="'.base_url('product/'.$value->product_slug).'" title="'.$product_title.'">'.$product_title.'</a>
                                    </h4>
                                    <p class="cart-quantity">'.$this->lang->line('qty_lbl').': '.$value->product_qty.'</p>
                                    <p class="cart-price">'.$price.'</p>
                                    </div>
                                    <div class="cart-close"> <a href="'.site_url('remove-to-cart/'.$value->id).'" class="btn_remove_cart" title="Remove"><i class="ion-android-close"></i></a> </div>
                                    </li>';
                                }

                                $total_cart_items=count($this->get_cart());

                                if($total_cart_items > 3){
                                    $html_content.='<li class="cart-item text-center"><h4 style="font-weight: 500">'.str_replace('###', ($total_cart_items - 3), $this->lang->line('remain_cart_items_lbl')).'</h4></li>';
                                }

                                $html_content.='<li class="cart-button"> <a href="'.base_url('my-cart').'" class="button2">'.$this->lang->line('view_cart_btn').'</a> <a href="'.base_url('checkout').'" class="button2">'.$this->lang->line('checkout_btn').'</a>
                                </li>';

                                $response=array('status' => 1,'msg' => $this->lang->line('update_cart'),'btn_lbl' => $this->lang->line('remove_cart_btn'),'product_id' => $product_id,'update_lbl' => $this->lang->line('update_cart_btn'),'tooltip_lbl' => $this->lang->line('remove_cart_lbl'),'removeCartUrl' => $removeCartUrl,'cart_items' => $total_cart_items,'cart_view' => $html_content);
                            }
                        }
                        else{

                            $error_msg=str_replace("###",$row_product->max_unit_buy,$this->lang->line("err_cart_item_buy_lbl"));
                            $response = array('status' => 0,'msg' => $error_msg);
                        }
                    }
                }
                else{
                    $response=array('status' => 0, 'msg' => $this->lang->line('no_data_found_msg'));
                }
            }
            else{
                $response=array('status' => 0, 'msg' => $this->lang->line('something_went_wrong_err'));
            }
        }

        echo json_encode($response);
    }

    public function remove_cart($id)
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{
            $where = array('id ' => $id, 'user_id' => $this->user_id);

            if($this->General_model->deleteByids($where, 'tbl_cart')){

                $rowCart = $this->get_cart(3);
                $html_content='<li class="cart-item text-center" style="padding: 15px"><h4 style="font-weight: 500"><i class="ion-android-cart"></i> Your cart is empty!</h4></li>';

                $total_cart_items=count($this->get_cart());

                if(count($rowCart) > 0){

                    $html_content='';

                    foreach ($rowCart as $value) {

                        $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value->featured_image);

                        $img_file = base_url($this->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $value->featured_image, 50, 50));

                        if (strlen($value->product_title) > 20) {
                            $product_title=substr(stripslashes($value->product_title), 0, 20) . '...';
                        } else {
                            $product_title=$value->product_title;
                        }

                        $price = amount_format($value->selling_price * $value->product_qty);
                        if (strlen($price) > 20) {
                            $price= CURRENCY_CODE . ' ' . substr(stripslashes($price), 0, 20) . '...';
                        } else {
                            $price= CURRENCY_CODE . ' ' . $price;
                        }

                        $html_content.='<li class="cart-item">
                        <div class="cart-img" style="width: auto"> <a href="javascript:void(0)"><img src="'.$img_file.'" alt="" style="width: 68px;height: 68px"></a> </div>
                        <div class="cart-content">
                        <h4>
                        <a href="'.base_url('product/'.$value->product_slug).'" title="'.$product_title.'">'.$product_title.'</a>
                        </h4>
                        <p class="cart-quantity">'.$this->lang->line('qty_lbl').': '.$value->product_qty.'</p>
                        <p class="cart-price">'.$price.'</p>
                        </div>
                        <div class="cart-close"> <a href="'.site_url('remove-to-cart/'.$value->id).'" class="btn_remove_cart" title="Remove"><i class="ion-android-close"></i></a> </div>
                        </li>';
                    }

                    if($total_cart_items > 3){
                        $html_content.='<li class="cart-item text-center"><h4 style="font-weight: 500">'.str_replace('###', ($total_cart_items - 3), $this->lang->line('remain_cart_items_lbl')).'</h4></li>';
                    }

                    $html_content.='<li class="cart-button"> <a href="'.base_url('my-cart').'" class="button2">'.$this->lang->line('view_cart_btn').'</a> <a href="'.base_url('checkout').'" class="button2">'.$this->lang->line('checkout_btn').'</a>
                    </li>';
                }

                $response=array('status' => 1,'msg' => $this->lang->line('remove_cart'),'btn_lbl' => $this->lang->line('add_cart_btn'),'tooltip_lbl' => $this->lang->line('add_cart_lbl'),'cart_items' => $total_cart_items,'cart_view' => $html_content);
            }
            else{
                $response=array('status' => 0,'msg' => $this->lang->line('err_remove_cart'),'lbl_title' => $this->lang->line('error_lbl'));
            }
        }

        echo json_encode($response);
        exit;
    }

    public function my_addresses()
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $data = array();
        $data['page_title'] = $this->lang->line('addresses_lbl');
        $data['current_page'] = $this->lang->line('addresses_lbl');

        $data['addresses'] = $this->General_model->get_addresses($this->user_id);

        $this->template->load('site/template2', 'site/pages/addresses', $data);
    }

    public function addAddress()
    {
        $this->load->helper("date");
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $this->form_validation->set_rules('billing_name', $this->lang->line('name_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('billing_mobile_no', $this->lang->line('phone_no_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('building_name', $this->lang->line('address_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('road_area_colony', $this->lang->line('road_area_colony_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('pincode', $this->lang->line('zipcode_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('city', $this->lang->line('city_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('state', $this->lang->line('state_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('country', $this->lang->line('country_place_lbl'), 'trim|required');

            if($this->form_validation->run())  
            {
                if($_POST)
                {
                    if($row = $this->General_model->get_addresses($this->user_id)){
                        $data_arr = array(
                            'is_default' => 'false'
                        );

                        $data_usr = $this->security->xss_clean($data_arr);

                        $where = array('user_id ' => $this->user_id);

                        $updated_id = $this->General_model->updateByids($data_usr, $where,'tbl_addresses');
                    }

                    $data_arr = array(
                        'user_id' => $this->user_id,
                        'pincode' => $this->input->post('pincode'),
                        'building_name' => $this->input->post('building_name'),
                        'road_area_colony' => $this->input->post('road_area_colony'),
                        'city' => $this->input->post('city'),
                        'district' => $this->input->post('district'),
                        'state' => $this->input->post('state'),
                        'country' => $this->input->post('country'),
                        'landmark' => $this->input->post('landmark'),
                        'name' => $this->input->post('billing_name'),
                        'email' => $this->input->post('billing_email'),
                        'mobile_no' => $this->input->post('billing_mobile_no'),
                        'alter_mobile_no' => $this->input->post('alter_mobile_no'),
                        'address_type' => $this->input->post('address_type'),
                        'is_default' => 'true',
                        'created_at' => strtotime(date('d-m-Y h:i:s A',now()))
                    );

                    $data_usr = $this->security->xss_clean($data_arr);

                    $address_id = $this->General_model->insert($data_usr, 'tbl_addresses');

                    $row_addresses = $this->General_model->get_addresses($this->user_id);

                    $addresses='';

                    if(is_null($this->input->post("page"))){
                        foreach ($row_addresses as $key => $value) {

                            $is_checked='';

                            if($value->is_default=='true'){
                                $is_checked='checked="checked"';
                            }

                            $addresses.='<div class="address_details_item">
                                            <label class="container">
                                            <input type="radio" name="address_id" class="address_radio" value="'.$value->id.'" '.$is_checked.'>
                                            <span class="checkmark"></span>
                                            </label>                
                                            <div class="address_list">
                                            <span>'.$value->name.' '.$value->mobile_no.'</span>
                                            <div class="address_list_edit">
                                            <a href="javascript:void(0)" class="btn_edit_address" data-stuff="'.htmlentities(json_encode($value)).'">'.$this->lang->line('edit_btn').'</a>
                                            </div>
                                            <p class="address-field">'.$value->building_name.', '.$value->road_area_colony.', '.$value->city.', '.$value->state.', '.$value->country.' - '.$value->pincode.'</p>';

                                            if($value->is_default=='true'){
                                                $addresses.='<button class="btn-continue form-button grow-btn mt-10" data-type="address">'.$this->lang->line("delivery_here_lbl").'</button>';
                                            }

                                            $addresses.='</div></div>';
                        }
                    }
                    else{
                        foreach ($row_addresses as $key => $value) {

                            $address_type=$this->lang->line('home_address_val_lbl');

                            if($value->address_type!=1){
                                $address_type=$this->lang->line('office_address_val_lbl');
                            }

                            $addresses.='<div class="address_details_item">
                                            <div class="address_list">
                                                <div class="home_address">'.$address_type.'</div>
                                                <span>'.$value->name.' '.$value->mobile_no.'</span>
                                                <div class="address_list_edit">
                                                    <a href="javascript:void(0)" class="btn_edit_address" data-stuff="'.htmlentities(json_encode($value)).'">'.$this->lang->line('edit_btn').'</a>
                                                    <a href="javascript:void(0)" class="btn_delete_address" data-id="'.encrypt_url($value->id).'">'.$this->lang->line('delete_btn').'</a>
                                                </div>
                                                <p>'.$value->building_name.', '.$value->road_area_colony.', '.$value->city.', '.$value->state.', '.$value->country.' - '.$value->pincode.'</p>
                                            </div>            
                                        </div>';
                        }
                    }

                    $response=array('status' => 1, 'msg' => $this->lang->line('add_success'), 'addresses' => $addresses);
                }
            }
            else  
            {  
                $response=array('status' => 0, 'msg' => $this->lang->line('all_required_field_err'));
            }
        }

        echo json_encode($response);
    }

    public function delete_address()
    {
        $address_id=decrypt_url($this->input->post("address_id"));

        $this->load->helper("date");
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $row=$this->General_model->selectByids(array('user_id' => $this->user_id, 'id' => $address_id),'tbl_addresses');

            if(!empty($row))
            {
                $row=$row[0];

                if($row->is_default=='true')
                {
                    $data_arr=$this->General_model->selectByids(array('user_id'=>$this->user_id),'tbl_addresses');

                    if(count($data_arr) > 0){

                        $this->General_model->delete($address_id,'tbl_addresses');

                        $data_update = array(
                            'is_default' => 'true'
                        );

                        $data_update = $this->security->xss_clean($data_update);

                        $where=array('user_id' => $this->user_id);

                        $max_id=$this->General_model->getMaxId('tbl_addresses',$where);

                        $updated_id = $this->General_model->update($data_update, $max_id, 'tbl_addresses');
                    }
                }
                else{
                    $this->General_model->delete($address_id,'tbl_addresses');
                }

                $row_addresses = $this->General_model->get_addresses($this->user_id);

                $addresses='';

                if(!empty($row_addresses)){
                    foreach ($row_addresses as $key => $value) {

                        $address_type=$this->lang->line('home_address_val_lbl');

                        if($value->address_type!=1){
                            $address_type=$this->lang->line('office_address_val_lbl');
                        }

                        $addresses.='<div class="address_details_item">
                                        <div class="address_list">
                                            <div class="home_address">'.$address_type.'</div>
                                            <span>'.$value->name.' '.$value->mobile_no.'</span>
                                            <div class="address_list_edit">
                                                <a href="javascript:void(0)" class="btn_edit_address" data-stuff="'.htmlentities(json_encode($value)).'">'.$this->lang->line('edit_btn').'</a>
                                                <a href="javascript:void(0)" class="btn_delete_address" data-id="'.encrypt_url($value->id).'">'.$this->lang->line('delete_btn').'</a>
                                            </div>
                                            <p>'.$value->building_name.', '.$value->road_area_colony.', '.$value->city.', '.$value->state.', '.$value->country.' - '.$value->pincode.'</p>
                                        </div>            
                                    </div>';
                    }
                }
                else{
                    $addresses.='<div class="col-md-12 text-center" style="padding: 1em 0px 1em 0px">
                                <h3>'.$this->lang->line('no_address_lbl').' 
                                </div><div class="clearfix"></div>';
                }

                $response=array('status' => 1,'msg' => $this->lang->line('delete_success'), 'addresses' => $addresses);
            }
            else
            {
                $response=array('status' => 0,'msg' => $this->lang->line('no_address_found'));
            }
        }

        echo json_encode($response);
        exit;
    }

    public function set_default_address()
    {
        $address_id=$this->input->post("address_id");

        $this->load->helper("date");
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $row=$this->General_model->selectByids(array('user_id' => $this->user_id, 'id' => $address_id),'tbl_addresses');

            if(!empty($row))
            {
                $data_arr = array(
                    'is_default' => 'false'
                );

                $data_usr = $this->security->xss_clean($data_arr);

                $where = array('user_id ' => $this->user_id);

                $updated_id = $this->General_model->updateByids($data_usr, $where,'tbl_addresses');

                $data_arr = array(
                    'is_default' => 'true'
                );

                $data_usr = $this->security->xss_clean($data_arr);

                $where = array('id ' => $address_id);

                $updated_id = $this->General_model->updateByids($data_usr, $where,'tbl_addresses');

                $row_addresses = $this->General_model->get_addresses($this->user_id);

                $addresses='';

                foreach ($row_addresses as $key => $value) {

                    $is_checked='';

                    if($value->is_default=='true'){
                        $is_checked='checked="checked"';
                    }

                    $addresses.='<div class="address_details_item">
                    <label class="container">
                    <input type="radio" name="address_id" class="address_radio" value="'.$value->id.'" '.$is_checked.'>
                    <span class="checkmark"></span>
                    </label>                
                    <div class="address_list">
                    <span>'.$value->name.' '.$value->mobile_no.'</span>
                    <div class="address_list_edit">
                    <a href="javascript:void(0)" class="btn_edit_address" data-stuff="'.htmlentities(json_encode($value)).'">'.$this->lang->line('edit_btn').'</a>
                    </div>
                    <p class="address-field">'.$value->building_name.', '.$value->road_area_colony.', '.$value->city.', '.$value->state.', '.$value->country.' - '.$value->pincode.'</p>';

                    if($value->is_default=='true'){
                        $addresses.='<button class="btn-continue form-button grow-btn mt-10" data-type="address">'.$this->lang->line("delivery_here_lbl").'</button>';
                    }

                    $addresses.='</div></div>';
                }

                $response=array('status' => 1, 'msg' => '', 'addresses' => $addresses);
            }
            else
            {
                $response=array('status' => 0,'msg' => $this->lang->line('no_address_found'));
            }
        }

        echo json_encode($response);
        exit;
    }

    public function update_cart()
    {
        $product_id=decrypt_url($this->input->post("product_id"));
        $qty=$this->input->post("qty");

        $type=($this->input->post("buy_now")=='true') ? 'temp_cart' : 'main_cart';

        $coupon_id=$this->input->post("coupon_id");
        $chkout_ref=$this->input->post("chkout_ref");

        $perform=$this->input->post("perform");

        $this->load->helper("date");
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{
            $row_product=$this->General_model->selectByid($product_id, 'tbl_product');

            if(!empty($row_product)){

                if($qty > $row_product->max_unit_buy && strcmp($perform, 'plus')==0){

                    $error_msg=str_replace("###",$row_product->max_unit_buy,$this->lang->line("err_cart_item_buy_lbl"));
                    $response = array('status' => 0,'msg' => $error_msg);
                }
                else{

                    if(strcmp($type, 'main_cart')==0){

                        $data_arr = array(
                            'product_qty' => $qty,
                            'last_update' => strtotime(date('d-m-Y h:i:s A',now()))
                        );

                        $data_update = $this->security->xss_clean($data_arr);

                        $where = array('product_id' => $product_id, 'user_id' => $this->user_id);

                        $updated_id = $this->General_model->updateByids($data_update, $where,'tbl_cart');

                        $rowCart = $this->Api_model->get_cart($this->user_id);

                        $sub_total=$total=$delivery_charge=$you_save=0;

                        foreach ($rowCart as $value) {

                            $sub_total += ($value->selling_price * $value->product_qty);
                            $delivery_charge += $value->delivery_charge;
                            $you_save += ($value->you_save_amt * $value->product_qty);
                        }

                        $total=$sub_total+$delivery_charge;
                        $total = CURRENCY_CODE.' '.amount_format($total);

                        $sub_total = CURRENCY_CODE.' '.amount_format($sub_total);

                        $delivery_charge = ($delivery_charge != 0) ? '+ '.CURRENCY_CODE.' '.amount_format($delivery_charge) : $this->lang->line('free_lbl');

                        $save_msg = ($you_save > 0) ? str_replace('###', CURRENCY_CODE.' '.amount_format($you_save), $this->lang->line('coupon_save_msg_lbl')) : '';

                        if($coupon_id!=0){

                            $coupon_json=json_decode($this->inner_apply_coupon($coupon_id));

                            $save_msg = ($coupon_json->discount_amt > 0) ? str_replace('###', CURRENCY_CODE.' '.amount_format($coupon_json->discount_amt+$you_save), $this->lang->line('coupon_save_msg_lbl')) : '';

                            $total = CURRENCY_CODE.' '.$coupon_json->price;
                            $sub_total = CURRENCY_CODE.' '.$coupon_json->payable_amt;
                        }
                    }
                    else{

                        $data_arr = array(
                            'product_qty' => $qty
                        );

                        $data_update = $this->security->xss_clean($data_arr);

                        $where = array('cart_unique_id' => $chkout_ref, 'product_id' => $product_id, 'user_id' => $this->user_id);

                        $updated_id = $this->General_model->updateByids($data_update, $where,'tbl_cart_tmp');

                        $rowCart = $this->Api_model->get_cart($this->user_id, $updated_id);

                        $sub_total=$total=$delivery_charge=$you_save=0;

                        foreach ($rowCart as $value) {

                            $sub_total += ($value->selling_price * $value->product_qty);
                            $delivery_charge += $value->delivery_charge;
                            $you_save += ($value->you_save_amt * $value->product_qty);
                        }

                        $total=$sub_total+$delivery_charge;
                        $total = CURRENCY_CODE.' '.amount_format($total);

                        $sub_total = CURRENCY_CODE.' '.amount_format($sub_total);

                        $delivery_charge = ($delivery_charge != 0) ? '+ '.CURRENCY_CODE.' '.amount_format($delivery_charge) : $this->lang->line('free_lbl');

                        $save_msg = ($you_save > 0) ? str_replace('###', CURRENCY_CODE.' '.amount_format($you_save), $this->lang->line('coupon_save_msg_lbl')) : '';

                        if($coupon_id!=0){

                            $coupon_json=json_decode($this->inner_apply_coupon($coupon_id, $updated_id, $type));

                            $save_msg = ($coupon_json->discount_amt > 0) ? str_replace('###', CURRENCY_CODE.' '.amount_format($coupon_json->discount_amt+$you_save), $this->lang->line('coupon_save_msg_lbl')) : '';

                            $total = CURRENCY_CODE.' '.$coupon_json->price;
                            $sub_total = CURRENCY_CODE.' '.$coupon_json->payable_amt;
                        }
                    }

                    $product_amount='';

                    if($row_product->you_save_amt!='0'){
                        $product_amount.='<span class="new-price">'.CURRENCY_CODE.' '.amount_format(($row_product->selling_price * $qty));
                        $product_amount.='</span>';
                        $product_amount.='<span class="old-price">';
                        $product_amount.=CURRENCY_CODE.' '.amount_format(($row_product->product_mrp * $qty));
                        $product_amount.='</span>';
                    }
                    else{
                        $product_amount.='<span class="new-price">'.CURRENCY_CODE.' '.amount_format(($row_product->selling_price * $qty)).'</span>';
                    }

                    $response = array('status' => 1,'msg' => $this->lang->line('update_cart'),'total' => $total,'sub_total' => $sub_total,'delivery_charge' => $delivery_charge,'you_save' => $save_msg, 'product_amount' => $product_amount);
                }
            }
            else{
                $response = array('status' => 0,'msg' => $this->lang->line('no_data_found_msg'));
            }
        }

        echo json_encode($response);
        exit();
    }

    public function remove_to_cart()
    {
        $cart_id=decrypt_url($this->input->post("id"));

        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $where = array('user_id' => $this->user_id , 'id' => $cart_id);

            $row=$this->General_model->selectByids($where,'tbl_cart');

            if(!empty($row)){

                $this->General_model->deleteByids($where, 'tbl_cart');

                $rowCart = $this->get_cart();

                if(count($rowCart) > 0){

                    $sub_total=$total=$delivery_charge=$you_save=0;

                    $cart_ids='';

                    foreach ($rowCart as $value) {

                        $cart_ids.=$value->id.',';

                        $sub_total += ($value->selling_price * $value->product_qty);
                        $delivery_charge += $value->delivery_charge;
                        $you_save += ($value->you_save_amt * $value->product_qty);
                    }

                    $cart_ids=rtrim($cart_ids,',');

                    $total=$sub_total+$delivery_charge;
                    $total = CURRENCY_CODE.' '.amount_format($total);

                    $sub_total = CURRENCY_CODE.' '.amount_format($sub_total);

                    $delivery_charge = ($delivery_charge != 0) ? '+ '.CURRENCY_CODE.' '.amount_format($delivery_charge) : $this->lang->line('free_lbl');

                    $you_save = ($you_save > 0) ? str_replace('###', CURRENCY_CODE.' '.amount_format($you_save), $this->lang->line('coupon_save_msg_lbl')) : '';

                    $response = array('status' => 1,'msg' => $this->lang->line('update_cart'),'total' => $total,'sub_total' => $sub_total,'delivery_charge' => $delivery_charge, 'you_save' => $you_save, 'cart_ids' => $cart_ids);
                }
                else{
                    $response = array('status' => 2,'msg' => '');
                }
            }
            else{
                $response = array('status' => 0,'msg' => $this->lang->line('no_data_found_msg'));
            }
        }

        echo json_encode($response);
        exit();
    }

    public function update_product_qty()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $cart_id=decrypt_url($this->input->post("cart_id"));
            $qty=$this->input->post("qty");

            $data = array(
                'product_qty' => $qty,
                'last_update' => strtotime(date('d-m-Y h:i:s A', now()))
            );

            $data = $this->security->xss_clean($data);

            $this->General_model->updateByids($data, array('id' => $cart_id), 'tbl_cart');

            $rowCart = $this->Api_model->get_cart($this->user_id);

            $sub_total=$total=$delivery_charge=$product_amount=0;

            foreach ($rowCart as $value) {

                if($value->id==$cart_id){
                   $product_amount = ($value->selling_price * $value->product_qty);
                }

                $sub_total += ($value->selling_price * $value->product_qty);
                $delivery_charge += $value->delivery_charge;
            }

            $total = CURRENCY_CODE.' '.amount_format($sub_total+$delivery_charge);

            $sub_total = CURRENCY_CODE.' '.amount_format($sub_total); 

            $product_amount = CURRENCY_CODE.' '.amount_format($product_amount);

            $delivery_charge = ($delivery_charge != 0) ? '+ '.CURRENCY_CODE.' '.amount_format($delivery_charge) : $this->lang->line('free_lbl');

            $response = array('status' => 1,'msg' => $this->lang->line('update_cart'),'total' => $total,'sub_total' => $sub_total,'delivery_charge' => $delivery_charge,'product_amount' => $product_amount);
        }

        echo json_encode($response);
        exit;
    }

    public function edit_address()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{
            $this->form_validation->set_rules('billing_name', $this->lang->line('name_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('billing_mobile_no', $this->lang->line('phone_no_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('building_name', $this->lang->line('address_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('road_area_colony', $this->lang->line('road_area_colony_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('pincode', $this->lang->line('zipcode_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('city', $this->lang->line('city_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('state', $this->lang->line('state_place_lbl'), 'trim|required');
            $this->form_validation->set_rules('country', $this->lang->line('country_place_lbl'), 'trim|required');

            if($this->form_validation->run())  
            {
                if($_POST)
                {
                    if($row = $this->General_model->get_addresses($this->user_id)){
                        $data_arr = array('is_default' => 'false');

                        $data_usr = $this->security->xss_clean($data_arr);

                        $where = array('user_id ' => $this->user_id);

                        $updated_id = $this->General_model->updateByids($data_usr, $where,'tbl_addresses');
                    }

                    $data_arr = array(
                        'pincode' => $this->input->post('pincode'),
                        'building_name' => $this->input->post('building_name'),
                        'road_area_colony' => $this->input->post('road_area_colony'),
                        'city' => $this->input->post('city'),
                        'district' => $this->input->post('district'),
                        'state' => $this->input->post('state'),
                        'country' => $this->input->post('country'),
                        'landmark' => $this->input->post('landmark'),
                        'name' => $this->input->post('billing_name'),
                        'email' => $this->input->post('billing_email'),
                        'mobile_no' => $this->input->post('billing_mobile_no'),
                        'alter_mobile_no' => $this->input->post('alter_mobile_no'),
                        'address_type' => $this->input->post('address_type'),
                        'is_default' => 'true'
                    );

                    $data_usr = $this->security->xss_clean($data_arr);

                    $address_id = $this->General_model->update($data_usr, $this->input->post('address_id'),'tbl_addresses');

                    $message = array('message' => $this->lang->line('update_success'),'class' => 'success');
                    $this->session->set_flashdata('response_msg', $message);
                    $response = array('status' => 1,'msg' => $this->lang->line('update_success'));
                }
            }
            else  
            {  
                $response = array('status' => 0,'msg' => $this->lang->line('all_required_field_err'));
            }
        }

        echo json_encode($response);
        return;
    }

    public function my_cart()
    {
        $data['page_title'] = $this->lang->line('shoppingcart_lbl');
        $data['current_page'] = $this->lang->line('shoppingcart_lbl');
        $data['my_cart'] = $this->Api_model->get_cart($this->user_id);
        $this->template->load('site/template2', 'site/pages/my_cart', $data);
    }

    public function product_review()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $this->load->model('Product_model');

            $product_id = decrypt_url($this->input->post("product_id"));
            $rate = trim($this->input->post("rating"));
            $review_desc = stripslashes(trim($this->input->post("message")));

            $where = array('user_id' => $this->user_id, 'product_id' => $product_id);

            $rowOrd = $this->General_model->selectByids($where, 'tbl_order_items');

            if(!empty($rowOrd)){

                $row = $this->General_model->selectByids($where, 'tbl_rating');

                $order_id=$rowOrd[0]->order_id;

                if(empty($row)){

                    $data_arr = array(
                        'product_id' => $product_id,
                        'user_id' => $this->user_id,
                        'order_id' => $order_id,
                        'rating' => $rate,
                        'rating_desc' => $review_desc,
                        'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                    );

                    $data_usr = $this->security->xss_clean($data_arr);

                    $review_id = $this->General_model->insert($data_usr, 'tbl_rating');

                    if (!empty($_FILES['product_images'])) {
                        $files = $_FILES;
                        $cpt = count($_FILES['product_images']['name']);
                        for ($i = 0; $i < $cpt; $i++) {
                            $_FILES['product_images']['name'] = $files['product_images']['name'][$i];
                            $_FILES['product_images']['type'] = $files['product_images']['type'][$i];
                            $_FILES['product_images']['tmp_name'] = $files['product_images']['tmp_name'][$i];
                            $_FILES['product_images']['error'] = $files['product_images']['error'][$i];
                            $_FILES['product_images']['size'] = $files['product_images']['size'][$i];

                            $image = date('dmYhis') . '_' . rand(0, 99999) . "_review." . pathinfo($files['product_images']['name'][$i], PATHINFO_EXTENSION);

                            $config['file_name'] = $image;
                            $uploadPath = 'assets/images/review_images/';
                            $config['upload_path'] = $uploadPath;
                            $config['allowed_types'] = 'jpg|jpeg|png|gif';

                            $this->load->library('upload');
                            $this->upload->initialize($config);

                            if ($this->upload->do_upload('product_images')) {

                                $data_img = array(
                                    'parent_id' => $review_id,
                                    'image_file' => $image,
                                    'type' => 'review'
                                );

                                $data_img = $this->security->xss_clean($data_img);
                                $this->General_model->insert($data_img, 'tbl_product_images');
                            }
                        }
                    }

                    if($this->Product_model->set_product_review($product_id)){
                        $message = array('success' => '1', 'message' => $this->lang->line('review_submit'), 'class' => 'success');
                        $this->session->set_flashdata('response_msg', $message);
                        $response=array('status' => 1, 'msg' => $this->lang->line('review_submit'));
                    }
                    else{
                        $response=array('status' => 0, 'msg' => $this->lang->line('review_submit_err'));
                    }

                } else {
                    $data_arr = array(
                        'product_id' => $product_id,
                        'user_id' => $this->user_id,
                        'order_id' => $row[0]->order_id,
                        'rating' => $rate,
                        'rating_desc' => $review_desc
                    );

                    $data_usr = $this->security->xss_clean($data_arr);

                    $this->General_model->update($data_usr, $row[0]->id, 'tbl_rating');

                    $review_id = $row[0]->id;

                    if (!empty($_FILES['product_images'])) {
                        $files = $_FILES;
                        $cpt = count($_FILES['product_images']['name']);
                        for ($i = 0; $i < $cpt; $i++) {

                            $_FILES['product_images']['name'] = $files['product_images']['name'][$i];
                            $_FILES['product_images']['type'] = $files['product_images']['type'][$i];
                            $_FILES['product_images']['tmp_name'] = $files['product_images']['tmp_name'][$i];
                            $_FILES['product_images']['error'] = $files['product_images']['error'][$i];
                            $_FILES['product_images']['size'] = $files['product_images']['size'][$i];

                            $image = date('dmYhis') . '_' . rand(0, 99999) . "_review." . pathinfo($files['product_images']['name'][$i], PATHINFO_EXTENSION);

                            $config['file_name'] = $image;

                            $uploadPath = 'assets/images/review_images/';
                            $config['upload_path'] = $uploadPath;
                            $config['allowed_types'] = 'jpg|jpeg|png|gif';

                            $this->load->library('upload');
                            $this->upload->initialize($config);

                            if ($this->upload->do_upload('product_images')) {

                                $data_img = array(
                                    'parent_id' => $review_id,
                                    'image_file' => $image,
                                    'type' => 'review'
                                );

                                $data_img = $this->security->xss_clean($data_img);
                                $this->General_model->insert($data_img, 'tbl_product_images');
                            }
                        }
                    }

                    if($this->Product_model->set_product_review($product_id, 'edit'))
                    {
                        $message = array('success' => '1', 'message' => $this->lang->line('review_updated'), 'class' => 'success');
                        $this->session->set_flashdata('response_msg', $message);
                        $response=array('status' => 1, 'msg' => $this->lang->line('review_updated'));
                    }
                    else{
                        $response=array('status' => 0, 'msg' => $this->lang->line('review_submit_err'));
                    }
                }
            }
            else{
                $response=array('status' => 0, 'msg' => $this->lang->line('no_data_found_msg'));
            }
        }

        echo json_encode($response);
        exit();
    }

    public function edit_review()
    {

        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{
            $id = $this->input->post("review_id");

            $rate = trim($this->input->post("rating"));
            $review_desc = trim($this->input->post("message"));

            $data_arr = array(
                'rating' => $rate,
                'rating_desc' => $review_desc
            );

            $data_usr = $this->security->xss_clean($data_arr);

            $this->General_model->update($data_usr, $id, 'tbl_rating');

            if (!empty($_FILES['product_images'])) {
                $files = $_FILES;
                $cpt = count($_FILES['product_images']['name']);
                for ($i = 0; $i < $cpt; $i++) {
                    $_FILES['product_images']['name'] = $files['product_images']['name'][$i];
                    $_FILES['product_images']['type'] = $files['product_images']['type'][$i];
                    $_FILES['product_images']['tmp_name'] = $files['product_images']['tmp_name'][$i];
                    $_FILES['product_images']['error'] = $files['product_images']['error'][$i];
                    $_FILES['product_images']['size'] = $files['product_images']['size'][$i];

                    $image = date('dmYhis') . '_' . rand(0, 99999) . "_review." . pathinfo($files['product_images']['name'][$i], PATHINFO_EXTENSION);

                    $config['file_name'] = $image;

                    $uploadPath = 'assets/images/review_images/';
                    $config['upload_path'] = $uploadPath;
                    $config['allowed_types'] = 'jpg|jpeg|png|gif';

                    $this->load->library('upload');
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('product_images')) {

                        $data_img = array(
                            'parent_id' => $id,
                            'image_file' => $image,
                            'type' => 'review'
                        );

                        $data_img = $this->security->xss_clean($data_img);
                        $this->General_model->insert($data_img, 'tbl_product_images');
                    }
                }
            }

            $message = array('success' => '1', 'message' => $this->lang->line('review_updated'), 'class' => 'success');
            $this->session->set_flashdata('response_msg', $message);

            $response=array('status' => 1, 'msg' => $this->lang->line('review_updated'));
        }

        echo json_encode($response);
        exit();
    }

    public function remove_review_image()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{
            $id = $this->input->post("id");

            $row = $this->General_model->selectByid($id, 'tbl_product_images');

            if (file_exists('assets/images/review_images/' . $row->image_file)) {
                unlink('assets/images/review_images/' . $row->image_file);
                $mask = $row->id . '*_*';
                array_map('unlink', glob('assets/images/review_images/thumbs/' . $mask));

                $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $row->image_file);
                $mask = $thumb_img_nm . '*_*';
                array_map('unlink', glob('assets/images/review_images/thumbs/' . $mask));
            }

            $this->General_model->delete($id, 'tbl_product_images');

            $response = array('status' => 1, 'msg' => $this->lang->line('delete_success'));
        }

        echo json_encode($response);
        exit();
    }

    public function remove_review()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $id = decrypt_url($this->input->post("review_id"));

            $row_review=$this->General_model->selectByid($id, 'tbl_rating');

            if(!empty($row_review)){
                $row_img = $this->General_model->selectByids(array('parent_id' => $id, 'type' => 'review'), 'tbl_product_images');

                foreach ($row_img as $key => $value) {
                    if (file_exists('assets/images/review_images/' . $value->image_file))
                        unlink('assets/images/review_images/' . $value->image_file);

                    $this->General_model->delete($value->id, 'tbl_product_images');
                }

                $this->General_model->delete($id, 'tbl_rating');

                $response = array('status' => 1, 'msg' => $this->lang->line('delete_success'));
            }
            else{
                $response = array('status' => 1, 'msg' => $this->lang->line('no_data_found_msg'));   
            }
        }

        echo json_encode($response);
        exit();
    }

    public function my_reviews()
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $data = array();
        $data['page_title'] = $this->lang->line('myreviewrating_lbl');
        $data['current_page'] = $this->lang->line('myreviewrating_lbl');

        $where = array('user_id' => $this->user_id);

        $data['my_review'] = $this->Users_model->get_user_review($this->user_id);

        $this->template->load('site/template2', 'site/pages/my_reviews.php', $data);
    }

    public function product_reviews()
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $data = array();
        $data['page_title'] = $this->lang->line('cust_review_lbl');
        $data['current_page'] = $this->lang->line('cust_review_lbl');

        $product_slug =  $this->uri->segment(2);

        $config = array();
        $config["base_url"] = base_url() . 'product-reviews/' . $product_slug;
        $config["per_page"] = $this->page_limit;

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;

        $page = ($page - 1) * $config["per_page"];

        $where = array('product_slug' => $product_slug);

        $product_id =  $this->General_model->getIdBySlug($where, 'tbl_product');

        if ($this->input->get('sort') != '') {
            $data['reviews'] = $this->Api_model->get_product_review($product_id, $this->input->get('sort'));
        } else {
            $data['reviews'] = $this->Api_model->get_product_review($product_id, '', $config["per_page"], $page);
        }

        $config["total_rows"] = count($this->Api_model->get_product_review($product_id));

        $config['num_links'] = 2;
        $config['use_page_numbers'] = TRUE;
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

        $data['product_row'] = $this->General_model->selectByid($product_id, 'tbl_product');

        $this->template->load('site/template2', 'site/pages/product_reviews.php', $data);
    }

    protected function inner_apply_coupon($coupon_id, $cart_ids = '', $cart_type = 'main_cart')
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'success');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        if ($cart_type == 'main_cart') {
            $my_cart = $this->Api_model->get_cart($this->user_id);
        } else {
            $my_cart = $this->Api_model->get_cart($this->user_id, $cart_ids);
        }

        $total_amount = $you_save = $delivery_charge = 0;

        if (!empty($my_cart)) {

            foreach ($my_cart as $row_cart) {
                $total_amount += ($row_cart->selling_price * $row_cart->product_qty);
                $you_save += ($row_cart->you_save_amt * $row_cart->product_qty);
                $delivery_charge += $row_cart->delivery_charge;
            }

            $where = array('id' => $coupon_id);

            if ($row = $this->General_model->selectByids($where, 'tbl_coupon')) {

                $row = $row[0];

                $where = array('user_id ' => $this->user_id, 'coupon_id' => $row->id);

                $count_use = count($this->General_model->selectByids($where, 'tbl_order_details'));

                if ($row->coupon_limit_use >= $count_use) {
                    if ($row->coupon_per != '0') {
                        if ($row->cart_status == 'true') {

                            if ($total_amount >= $row->coupon_cart_min) {

                                $payable_amt = $discount = 0;

                                $discount = sprintf("%01.2f", (float)(($row->coupon_per / 100) * $total_amount));

                                if ($row->max_amt_status == 'true') {

                                    if ($discount > $row->coupon_max_amt) {
                                        $discount = $row->coupon_max_amt;

                                        $payable_amt = sprintf("%01.2f", (float)($total_amount - $discount)) + sprintf("%01.2f", (float)$delivery_charge);
                                    } else {

                                        $payable_amt = sprintf("%01.2f", (float)($total_amount - $discount)) + sprintf("%01.2f", (float)$delivery_charge);
                                    }
                                } else {
                                    $payable_amt = sprintf("%01.2f", (float)($total_amount - $discount)) + sprintf("%01.2f", (float)$delivery_charge);
                                }

                                $response = array('status' => '1', "price" => $total_amount, "payable_amt" => strval($payable_amt), "discount" => $row->coupon_per, "discount_amt" => strval($discount));
                            } else {
                                $response = array('status' => '0', 'msg' => $this->lang->line('insufficient_cart_amt'));
                            }
                        } else {

                            $payable_amt = $discount = 0;

                            $discount = sprintf("%01.2f", (float)(($row->coupon_per / 100) * $total_amount));

                            if ($row->max_amt_status == 'true') {
                                if ($discount > $row->coupon_max_amt) {
                                    $discount = $row->coupon_max_amt;

                                    $payable_amt = sprintf("%01.2f", (float)($total_amount - $discount)) + sprintf("%01.2f", (float)$delivery_charge);
                                } else {
                                    $payable_amt = sprintf("%01.2f", (float)($total_amount - $discount)) + sprintf("%01.2f", (float)$delivery_charge);
                                }
                            } else {
                                $payable_amt = sprintf("%01.2f", (float)($total_amount - $discount)) + sprintf("%01.2f", (float)$delivery_charge);
                            }

                            $response = array('status' => '1', "price" => $total_amount, "payable_amt" => strval($payable_amt), "discount" => $row->coupon_per, "discount_amt" => strval($discount));
                        }
                    } else {

                        if ($row->cart_status == 'true') {

                            if ($total_amount >= $row->coupon_cart_min) {

                                $discount = $row->coupon_amt;

                                $payable_amt = sprintf("%01.2f", $total_amount - $discount);

                                $response = array('status' => '1', "price" => $total_amount, "payable_amt" => strval($payable_amt), "discount" => $row->coupon_per, "discount_amt" => strval($discount));
                            } else {
                                $response = array('status' => '0', 'msg' => $this->lang->line('insufficient_cart_amt'));
                            }
                        } else {

                            $payable_amt = $discount = 0;

                            if ($total_amount >= $row->coupon_amt) {
                                $discount = sprintf("%01.2f", $row->coupon_amt);

                                $payable_amt = sprintf("%01.2f", (float)($total_amount - $row->coupon_amt)) + sprintf("%01.2f", (float)$delivery_charge);
                            } else {
                                $discount = '0';
                                $payable_amt = sprintf("%01.2f", (float)($total_amount + $delivery_charge));
                            }

                            $response = array('status' => '1', "price" => $total_amount, "payable_amt" => strval($payable_amt), "discount" => $row->coupon_per, "discount_amt" => strval($discount));
                        }
                    }
                } else {
                    $response = array('status' => '0', 'msg' => $this->lang->line('use_limit_over'));
                }
            } else {
                $response = array('status' => '0', 'msg' => $this->lang->line('no_coupon'));
            }
        } else {
            $response = array('status' => '0', 'msg' => $this->lang->line('empty_cart_lbl'));
        }

        return json_encode($response);
    }
}