<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Frontend extends MY_Controller 
{
	public function __construct(){
        parent::__construct();
        $this->load->model('Users_model');
    }

	public function login()
	{
		$response=array();

		$preview_url=$this->input->post('preview_url');

        if(strpos($this->input->post('preview_url'), 'reset-password') !== false || strpos($this->input->post('preview_url'), 'register') !== false){
            $preview_url='';
        }

        $this->form_validation->set_rules('email', $this->lang->line('email_require_lbl'), 'required');  
        $this->form_validation->set_rules('password', $this->lang->line('password_require_lbl'), 'required');

        if($this->form_validation->run())  
        {
            if($_POST){

                if($this->web_settings->g_captcha=='true'){

                    if(!empty($this->input->post('g-recaptcha-response'))){

                        $secretKey = $this->web_settings->g_captcha_secret_key;

                        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$this->input->post('g-recaptcha-response')); 

                        $responseData = json_decode($verifyResponse); 

                        if(!$responseData->success){

                            $response=array('status' => '0','message' => $this->lang->line('robot_verify_failed'));
                            echo json_encode($response);
                            exit;
                        }
                    }
                    else{
                        $response=array('status' => '0','message' => $this->lang->line('check_captch_err'));
                        echo json_encode($response);
                        exit;
                    }
                }

                $rowData=$this->Users_model->auth_user_login(array('user_type' => 'Normal','user_email' => $this->input->post('email')));

                if($rowData){

                    if($rowData->user_password==md5($this->input->post('password'))){

                        if($rowData->status=='1')
                        {
                            $data = array(
                                'user_id' => $rowData->id,
                                'user_type' =>$rowData->user_type,
                                'user_name' => $rowData->user_name,
                                'user_email' =>$rowData->user_email,
                                'user_phone' =>$rowData->user_phone,
                                'is_user_login' => TRUE
                            );
                            $this->session->set_userdata($data);

                            $response = array('message' => $this->lang->line('login_success'),'status' => '1','class' => 'success','preview_url' => $preview_url);
                            $this->session->set_flashdata('response_msg', $response);
                        }
                        else{
                            $response = array('message' => $this->lang->line('acc_deactived'),'status' => '0'); 
                        }
                    }
                    else{

                        $response = array('message' => $this->lang->line('password_invaild'),'status' => '0');
                    }
                }else{
                    $response = array('message' => $this->lang->line('email_not_found'),'status' => '0');
                }
            }
        }
        else  
        {  
            $response = array('message' => $this->lang->line('all_required_field_err'),'status' => '0');
        }

        echo json_encode($response);
        exit();   
    }
    
    public function register()
    {
        if($this->user_id!=0){
            redirect('page_not_found','refresh');
        }

        $this->form_validation->set_rules('user_name', $this->lang->line('name_place_lbl'), 'trim|required');
        $this->form_validation->set_rules('user_email', $this->lang->line('email_place_lbl'), 'trim|required');
        $this->form_validation->set_rules('user_phone', $this->lang->line('phone_no_place_lbl'), 'trim|required');
        $this->form_validation->set_rules('user_password', $this->lang->line('password_place_lbl'), 'trim|required');

        if($this->form_validation->run())  
        {
            if($_POST){

                $rowData=$this->Users_model->auth_user_login(array('user_type' => 'Normal','user_email' => $this->input->post('user_email')));

                if(empty($rowData)){
                    $data = array(
                        'user_name'  => $this->input->post('user_name'),
                        'user_email'  => $this->input->post('user_email'),
                        'user_phone'  => $this->input->post('user_phone'),
                        'user_password'  => md5($this->input->post('user_password')),
                        'created_at'  =>  strtotime(date('d-m-Y h:i:s A'))
                    );

                    $data = $this->security->xss_clean($data);

                    if($this->General_model->insert($data, 'tbl_users')){

                        $dataEmail = array(
                            'register_type' => 'Normal',
                            'user_name' => $this->input->post('user_name')
                        );

                        $subject = $this->settings->app_name.' - '.$this->lang->line('register_mail_lbl');

                        $body = $this->load->view('emails/welcome_mail.php',$dataEmail,TRUE);

                        send_email($this->input->post('user_email'), $this->input->post('user_name'), $subject, $body);

                        $message = array('message' => $this->lang->line('register_success'),'class' => 'success');
                        $this->session->set_flashdata('response_msg', $message);

                    }
                    else{
                        $message = array('message' => $this->lang->line('register_failed'),'class' => 'error');
                        $this->session->set_flashdata('response_msg', $message);
                    }
                }
                else{
                    $message = array('message' => $this->lang->line('email_exist_error'),'class' => 'error');
                    $this->session->set_flashdata('response_msg', $message);
                }

                redirect('login-register', 'refresh');
            }
        }
        else  
        {  
            $message = array('message' => $this->lang->line('all_required_field_err'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('register', 'refresh');
        }
    }

    public function check_email()
    {
        $name=$this->input->post('user_name');
        $email=$this->input->post('user_email');

        $rowData=$this->Users_model->auth_user_login(array('user_type' => 'Normal','user_email' => $email));

        if(!empty($rowData)){
            $response=array('success' => '0','msg' => $this->lang->line('email_exist_error'));
        }
        else{
            if($this->checkSpam($email)){

                $random_code=rand(1000,5000);

                $where = array('user_email ' => $email);

                $row_verify=$this->Users_model->check_verify_code($where);

                if(!empty($row_verify)){
                    $data = array(
                        'user_email' => $email,
                        'verify_code' => $random_code,
                        'created_at' => strtotime(date('d-m-Y h:i:s A',now())),
                        'is_verify' => '0',
                    );

                    $data_usr = $this->security->xss_clean($data);

                    $updated_id=$this->General_model->updateByids($data_usr, $where,'tbl_verify_code');

                }
                else{
                    $data = array(
                        'user_email' => $email,
                        'verify_code' => $random_code,
                        'created_at' => strtotime(date('d-m-Y h:i:s A',now()))
                    );

                    $data_usr = $this->security->xss_clean($data);

                    $last_id = $this->General_model->insert($data_usr, 'tbl_verify_code');

                }

                $dataEmail = array('email' => $email,'otp' => $random_code);

                $subject = $this->settings->app_name.' - '.$this->lang->line('email_verify_heading_lbl');

                $body = $this->load->view('admin/emails/email_verify.php',$dataEmail,TRUE);

                if(send_email($email, $name, $subject, $body)){

                    if($this->input->post('is_resend')=='false'){
                        $response=array('success' => '1','msg' => $this->lang->line('verification_code_sent'));     
                    }
                    else{
                        $response=array('success' => '1','msg' => $this->lang->line('verification_code_resent')); 
                    }
                }
                else{
                    $response=array('success' => '0','msg' => $this->lang->line('email_not_sent'));
                }
            }
            else{
                $response=array('success' => '0','msg' => $this->lang->line('invalid_email_format'));
            } 
        }

        echo json_encode($response);
        exit;
    }

    public function sent_code(){

        $name=$this->input->post('name');
        $email=$this->input->post('email');

        if($this->checkSpam($email)){

            $random_code=rand(1000,5000);

            $where = array('user_email ' => $email);

            $row_verify=$this->Users_model->check_verify_code($where);

            if(!empty($row_verify)){

                $data = array(
                    'user_email' => $email,
                    'verify_code' => $random_code,
                    'created_at' => strtotime(date('d-m-Y h:i:s A',now())),
                    'is_verify' => '0',
                );

                $data = $this->security->xss_clean($data);

                $updated_id=$this->General_model->updateByids($data, $where,'tbl_verify_code');

            }
            else{
                $data = array(
                    'user_email' => $email,
                    'verify_code' => $random_code,
                    'created_at' => strtotime(date('d-m-Y h:i:s A',now()))
                );

                $data = $this->security->xss_clean($data);

                $last_id = $this->General_model->insert($data, 'tbl_verify_code');

            }

            $dataEmail = array('email' => $email,'otp' => $random_code);

            $subject = $this->settings->app_name.' - '.$this->lang->line('email_verify_heading_lbl');

            $body = $this->load->view('admin/emails/email_verify.php',$dataEmail,TRUE);

            if(send_email($email, $name, $subject, $body)){

                $response=array('success' => '1','msg' => $this->lang->line('verification_code_resent')); 
            }
            else{
                $response=array('success' => '0','msg' => $this->lang->line('email_not_sent'));
            }
        }
        else{
            $response=array('success' => '0','msg' => $this->lang->line('invalid_email_format'));
        }

        echo json_encode($response);
        exit;
    }

    private function checkSpam($email)
    {
        $this->load->library('genuinemail');
        $check = $this->genuinemail->check($email);
        if($check===TRUE) return true;
        return false;
    }

    public function verify_code(){

        $email=$this->input->post('email');

        $code=$this->input->post('code');

        $where = array('user_email' => $email, 'verify_code' => $code, 'is_verify' => '0');

        $row_verify=$this->Users_model->check_verify_code($where);

        if(!empty($row_verify)){

            $data = array('is_verify' => '1');

            $data = $this->security->xss_clean($data);

            $where = array('user_email' => $email);

            $updated_id=$this->General_model->updateByids($data, $where,'tbl_verify_code');

            $response=array('success' => 1, 'msg' => '');
        }
        else{
            $response=array('success' => 0, 'msg' => $this->lang->line("invalid_code_lbl"));
        }

        echo json_encode($response);
        exit();
    }

    public function forgot_password(){

        $rowData=$this->Users_model->auth_user_login(array('user_type' => 'Normal','user_email' => $this->input->post('email')));

        if (!empty($rowData)) {

            $this->load->helper('string');

            $requestToken=random_string('alnum', 16);

            $where=array('email' => $rowData->user_email);

            $rowReset=$this->General_model->selectByids($where, 'tbl_password_reset');

            if(!empty($rowReset)){
                $this->General_model->deleteByids($where, 'tbl_password_reset');
            }

            $reset_url=base_url('reset-password?requestToken='.$requestToken);

            $dataEmail = array(
                'name' => $rowData->user_name,
                'email' => $rowData->user_email,
                'requestToken' => $requestToken,
                'reset_url' => $reset_url
            );

            $subject = $this->settings->app_name.' - '.$this->lang->line('reset_password_request_lbl');

            $body = $this->load->view('admin/emails/reset_password.php',$dataEmail,TRUE);

            if(send_email($rowData->user_email, $rowData->user_name, $subject, $body))
            {
                $expires_in = strtotime(date('Y-m-d h:i').' + 20 minute');

                $dataEmail = array(
                    'requestToken' => $requestToken,
                    'email' => $rowData->user_email,
                    'request_on' => strtotime(date('d-m-Y h:i:s A',now())),
                    'expires_in' => $expires_in,
                    'ip_address' => $this->input->ip_address()
                );

                $dataReset = $this->security->xss_clean($dataEmail);

                $this->General_model->insert($dataReset, 'tbl_password_reset');

                $message = array('status' => '1','message' => $this->lang->line('password_sent'),'class' => 'success', 'redirectTo' => base_url('login-register'));
                $this->session->set_flashdata('response_msg', $message);
            }
            else{
                $message = array('status' => '0','message' => $this->lang->line('email_not_sent'),'class' => 'error');
            }
        }
        else{
            $message = array('status' => '0','message' => $this->lang->line('email_not_found'),'class' => 'error');
        }

        echo json_encode($message);

    }

    public function reset_password_form()
    {
        $this->form_validation->set_rules('new_password', $this->lang->line('new_password_lbl'), 'trim|required');
        $requestToken = $this->input->post('requestToken');

        if ($this->form_validation->run() == TRUE) {
            $where = array('requestToken' => $requestToken, 'status' => '1');

            $rowReset = $this->General_model->selectByids($where, 'tbl_password_reset');

            if (!empty($rowReset)) {

                $rowReset = $rowReset[0];

                $currentDatetime = strtotime(date('Y-m-d h:i'));

                if ($rowReset->expires_in < $currentDatetime) {

                    if ($requestToken != '') {
                        redirect('reset-password?requestToken=' . $requestToken, 'refresh');
                    } else {
                        redirect('reset-password', 'refresh');
                    }
                    exit();
                } else {
                    $data_update = array(
                        'user_password'  =>  md5(trim($this->input->post('new_password')))
                    );

                    $data_update = $this->security->xss_clean($data_update);

                    $this->General_model->updateByids($data_update, array('user_email' => $rowReset->email), 'tbl_users');

                    $data_update = array('status'  =>  0);

                    $data_update = $this->security->xss_clean($data_update);

                    $this->General_model->updateByids($data_update, array('requestToken' => $requestToken), 'tbl_password_reset');

                    $message = array('message' => $this->lang->line('change_password_msg'), 'class' => 'success');
                    $this->session->set_flashdata('response_msg', $message);

                    redirect('/', 'refresh');
                }
            } else {
                if ($requestToken != '') {
                    redirect('reset-password?requestToken=' . $requestToken, 'refresh');
                } else {
                    redirect('reset-password', 'refresh');
                }
                exit();
            }
        } else {
            $message = array('message' => $this->lang->line('all_required_field_err'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
        }

        if ($requestToken != '') {
            redirect('reset-password?requestToken=' . $requestToken, 'refresh');
        } else {
            redirect('reset-password', 'refresh');
        }
        exit();
    }

    public function contact_form()
    {

        if ($this->web_settings->g_captcha == 'true') {

            if (!empty($this->input->post('g-recaptcha-response'))) {

                $secretKey = $this->web_settings->g_captcha_secret_key;

                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $this->input->post('g-recaptcha-response'));

                $responseData = json_decode($verifyResponse);

                if ($responseData->success) {

                    $data_arr = array(
                        'contact_name' => $this->input->post('name'),
                        'contact_email' => $this->input->post('email'),
                        'contact_subject' => addslashes($this->input->post('subject_id')),
                        'contact_msg' => addslashes($this->input->post('message')),
                        'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                    );

                    $data_usr = $this->security->xss_clean($data_arr);

                    $this->General_model->insert($data_usr, 'tbl_contact_list');

                    $data_arr = array_merge($data_arr, array("subject" => $this->General_model->selectByidParam($this->input->post('subject_id'), 'tbl_contact_sub', 'title')));;

                    $admin_name = $this->General_model->selectByidsParam(array('id' => 1), 'tbl_admin', 'username');

                    $subject = $this->settings->app_name . '-' . $this->lang->line('contact_form_lbl');

                    $body = $this->load->view('admin/emails/contact_form.php', $data_arr, TRUE);

                    if (send_email($this->settings->app_email, $admin_name, $subject, $body)) {
                        $message = array('status' => 1, 'msg' => $this->lang->line('contact_msg_success'));
                    } else {
                        $message = array('status' => 0, 'msg' => $this->lang->line('error_data_save'));
                    }
                } else {

                    $message = array('status' => 0, 'msg' => $this->lang->line('robot_verify_failed'));
                }
            } else {

                $message = array('status' => 0, 'msg' => $this->lang->line('check_captch_err'));
            }
        } else {
            $data_arr = array(
                'contact_name' => $this->input->post('name'),
                'contact_email' => $this->input->post('email'),
                'contact_subject' => addslashes($this->input->post('subject_id')),
                'contact_msg' => addslashes($this->input->post('message')),
                'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
            );

            $data_usr = $this->security->xss_clean($data_arr);

            $last_id = $this->General_model->insert($data_usr, 'tbl_contact_list');

            $data_arr = array_merge($data_arr, array("subject" => $this->General_model->selectByidParam($this->input->post('subject_id'), 'tbl_contact_sub', 'title')));;

            $admin_name = $this->General_model->selectByidsParam(array('id' => 1), 'tbl_admin', 'username');

            $subject = $this->settings->app_name . '-' . $this->lang->line('contact_form_lbl');

            $body = $this->load->view('admin/emails/contact_form.php', $data_arr, TRUE);

            if (send_email($this->settings->app_email, $admin_name, $subject, $body)) {
                $message = array('status' => 1, 'msg' => $this->lang->line('contact_msg_success'));
            } else {
                $message = array('status' => 0, 'msg' => $this->lang->line('error_data_save'));
            }
        }

        echo json_encode($message);
    }

    public function quick_view()
    {
        $this->load->model("Product_model");

        $product_id = $this->input->post('product_id');

        $row=$this->Product_model->getSingle($product_id);

        $title = $desc = $old_price = $price = $size_view = '';

        if (strlen($row->product_title) > 20) {
            $title = substr(stripslashes($row->product_title), 0, 20);
        } else {
            $title = $row->product_title;
        }

        if (strlen($row->product_desc) > 100) {
            $desc = substr(strip_tags(stripslashes($row->product_desc)), 0, 100) . '...<a href="' . site_url('product/' . $row->product_slug) . '" title="' . $this->lang->line('show_more_lbl') . '">' . $this->lang->line('show_more_lbl') . '</a>';
        } else {
            $desc = strip_tags($row->product_desc);
        }

        if ($row->product_mrp > $row->selling_price) {
            $price = '<span class="new-price" style="margin-left:0px">' . CURRENCY_CODE . ' ' . number_format($row->selling_price, 2) . '</span><span class="old-price">' . CURRENCY_CODE . ' ' . number_format($row->product_mrp) . '</span>';
        } else {
            $price = '<span class="new-price">' . CURRENCY_CODE . ' ' . number_format($row->selling_price, 2) . '</span>';
        }

        $full_img = '';

        if ($row->status == 0) {
            $price .= '<p style="color: red;font-weight: 500; margin-bottom: 5px">' . $this->lang->line('unavailable_lbl') . '</p>';

            $full_img = '<div class="unavailable_override"><p>' . $this->lang->line('unavailable_lbl') . '</p></div>';
        }

        $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $row->featured_image);

        $img_file = $this->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $row->featured_image, 250, 250);

        $img_file_sm = $this->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $row->featured_image, 100, 100);


        $full_img .= '<div id="quick_' . $row->id . '" class="tab-pane fade in active">
        <div class="modal-img img-full"> <img src="'.base_url($img_file). '" alt="'.$row->product_slug.'" title="'.$row->product_slug.'"> </div>
        </div>';

        $thumb_img = '<li class="active img_click"><a data-toggle="tab" href="#quick_' . $row->id . '" title="'.$row->product_slug.'"><img src="' . base_url($img_file_sm). '" alt="'.$row->product_slug.'" title="'.$row->product_slug.'"></a></li>';


        $img_file2 = $this->_generate_thumbnail('assets/images/products/', $row->id, $row->featured_image2, 250, 250);

        $img_file2_sm = $this->_generate_thumbnail('assets/images/products/', $row->id, $row->featured_image2, 100, 100);

        $full_img .= '<div id="featured_img" class="tab-pane fade">
        <div class="modal-img img-full"> <img src="'.base_url($img_file2).'" alt="'.$row->product_slug.'" title="'.$row->product_slug.'"> </div>
        </div>';

        $thumb_img .= '<li class="img_click"><a data-toggle="tab" href="#featured_img" title="featured_image"><img src="' . base_url($img_file2_sm). '" alt="'.$row->product_slug.'" title="'.$row->product_slug.'"></a></li>';

        $where = array('parent_id' => $product_id, 'type' => 'product');

        $row_img = $this->General_model->selectByids($where, 'tbl_product_images');

        $i = 1;
        foreach ($row_img as $key => $value) {

            $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $value->image_file);

            $img_big = $this->_generate_thumbnail('assets/images/products/gallery/', $thumb_img_nm, $value->image_file, 250, 250);

            $img_small = $this->_generate_thumbnail('assets/images/products/gallery/', $thumb_img_nm, $value->image_file, 100, 100);

            $full_img .= '<div id="quick_gallery_' . $key . '" class="tab-pane fade">
            <div class="modal-img img-full"> <img src="'.base_url($img_big).'" alt="'.$row->product_slug.'" title="'.$row->product_slug.'"> </div>
            </div>';

            $thumb_img .= '<li class="img_click"><a data-toggle="tab" href="#quick_gallery_' . $key . '" title="'.$key.'"><img src="' . base_url($img_small). '" alt="'.$row->product_slug.'" title="'.$row->product_slug.'"></a></li>';
        }

        $size = $selected_size = $size_view = '';
        if ($row->product_size != '') {

            $i = 1;
            foreach (explode(',', $row->product_size) as $key => $value) {

                $class = 'radio_btn';

                if ($this->check_cart($row->id, $this->user_id)) {

                    $cart_size = $this->get_single_info(array('product_id' => $row->id, 'user_id' => $this->user_id), 'product_size', 'tbl_cart');


                    if ($cart_size == $value) {
                        $class = 'radio_btn selected';
                    } else {
                        $class = 'radio_btn';
                    }
                } else {
                    if ($i == 1) {
                        $class = 'radio_btn selected';
                    } else {
                        $class = 'radio_btn';
                    }
                }

                if ($i == 1) {
                    $selected_size = $value;
                    $size .= '<div class="' . $class . '" data-value="' . $value . '">' . $value . '</div>';
                    $i = 0;
                } else {
                    $size .= '<div class="' . $class . '" data-value="' . $value . '">' . $value . '</div>';
                }
            }

            $size_chart = (file_exists('assets/images/products/' . $row->size_chart) and $row->size_chart != '') ? base_url('assets/images/products/' . $row->size_chart) : "";

            if ($size_chart != '') {
                $size_view .= '<p style="font-weight: 600;margin:5px 0px">' . $this->lang->line('size_lbl') . ': </p>
                <div class="radio-group" style="margin-bottom:10px">
                ' . $size . '
                <input type="hidden" id="radio-value" name="product_size" value="' . $selected_size . '" />

                </div><a href="" class="size_chart" data-img="' . $size_chart . '" title="size_chart"><img src="' . base_url('assets/images/size_chart.png') . '" title="'.$this->lang->line('size_chart_lbl').'" alt="'.$this->lang->line('size_chart_lbl').'" style="width:20px;height:20px;margin-right:4px;"> ' . $this->lang->line('size_chart_lbl') . '</a><br/><br/>';
            } else {

                $size_view .= '
                <div class="clearfix"></div>
                <p style="font-weight: 600;margin:5px 0px">' . $this->lang->line('size_lbl') . '</p>
                <div class="radio-group">
                ' . $size . '
                <input type="hidden" id="radio-value" name="product_size" value="' . $selected_size . '" />
                </div><br/>';
            }
        }

        $share_url = site_url('product/' . $row->product_slug);

        $product_qty = ($this->check_cart($row->id, $this->user_id)) ? $this->get_single_info(array('product_id' => $row->id, 'user_id' => $this->user_id), 'product_qty', 'tbl_cart') : 1;

        $max_unit_buy = ($row->max_unit_buy) ? $row->max_unit_buy : 1;

        $button_lbl = ($this->check_cart($row->id, $this->user_id)) ? $this->lang->line('update_cart_btn') : $this->lang->line('add_cart_btn');

        $button_cart = '<button class="quantity-button" type="submit" style="display:inline-block">' . $button_lbl . '</button>';

        $response['status'] = 1;

        $preview_url = '';

        if (isset($_SERVER['HTTP_REFERER'])) {
            $preview_url = str_replace(base_url() . 'site/register', '', $_SERVER['HTTP_REFERER']);
        }

        $is_avail = ($row->status == 0) ? 'style="display:none"' : '';

        $response['max_unit_buy']=$row->max_unit_buy;

        $response['html_code'] = '<div class="modal-details">
        <div class="row"> 
        <div class="col-md-5 col-sm-5"> 
        <div class="tab-content" style="overflow:hidden">
        ' . $full_img . '
        </div>
        <div class="modal-product-tab">
        <ul class="modal-tab-menu-active">
        ' . $thumb_img . '
        </ul>
        </div>
        </div>
        <div class="col-md-7 col-sm-7">
        <div class="product-info">
        <h2>' . $title . '</h2>
        <div class="product-price">' . $price . '</div>
        <div class="add-to-cart quantity">
        </div>
        <div class="cart-description">
        <p>' . $desc . '</p>
        </div>
        <form class="add-quantity" action="" method="post" id="cartForm" ' . $is_avail . '>
        ' . $size_view . '
        <input type="hidden" name="preview_url" value="'.$preview_url.'">
        <input type="hidden" name="product_id" value="'.encrypt_url($product_id).'" />
        <div class="quantity" style="display: inline-block;margin-top: 0;top: -4px;">
        <input type="hidden" name="max_unit_buy" value="' . $max_unit_buy . '" class="max_unit_buy">
        <div class="buttons_added">
        <input type="button" value="-" class="minus">
        <input class="input-text product_qty" name="product_qty" value="' . $product_qty . '" type="text" min="1" max="' . $max_unit_buy . '" onkeypress="return isNumberKey(event)" readonly="">
        <input type="button" value="+" class="plus">
        </div>
        </div>
        ' . $button_cart . '
        </form>
        <div class="social-share">
        <h3 style="text-transform: initial;">' . $this->lang->line('share_lbl') . '</h3>
        <ul class="socil-icon2">
        <li><a href="https://www.facebook.com/sharer/sharer.php?u=' . $share_url . '" target="_blank" title="facebook"><i class="fa fa-facebook"></i></a></li>
        <li><a href="https://twitter.com/intent/tweet?text=' . $title . '&amp;url=' . $share_url . '" target="_blank" title="twitter"><i class="fa fa-twitter"></i></a></li>
        <li><a href="http://pinterest.com/pin/create/button/?url=' . $share_url . '&media=' . base_url() . $img_file . '&description=' . $title . '" target="_blank" title="pinterest"><i class="fa fa-pinterest"></i></a></li>
        <li><a href="whatsapp://send?text=' . $share_url . '" target="_blank" title="whatsapp" data-action="share/whatsapp/share"><i class="fa fa-whatsapp"></i></a></li>
        </ul>

        </div>
        </div>
        </div>
        </div>
        </div>';

        echo json_encode($response);
    }

    public function logout(){

        $array_items = array('user_id', 'user_type', 'user_name', 'user_email', 'user_phone', 'is_user_login', 'token', 'token', 'success_msg', 'response_msg', 'cart_msg', 'order_unique_id', 'single_pre_url', 'razorpay_order_id', 'order_id', 'data_email');

        $this->session->unset_userdata($array_items);

        if (isset($_SERVER['HTTP_REFERER']))
            redirect($_SERVER['HTTP_REFERER']);
        else
            redirect('/', 'refresh');
    }
}