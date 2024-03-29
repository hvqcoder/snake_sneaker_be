<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coupon extends CI_Controller {

    private $redirectUrl=NULL;

    public function __construct()
    {
        parent::__construct();
        check_login_user();
        $this->load->helper('image'); 
        $this->load->model('common_model');
        $this->load->model('Coupon_model');
        $this->load->model('Product_model');

        $currentURL = current_url();
        $params   = $_SERVER['QUERY_STRING'];
        $this->redirectUrl = (!empty($params)) ? $currentURL . '?' . $params : $currentURL;
    }

    function index()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('coupons_lbl');
        $data['current_page'] = $this->lang->line('coupons_lbl');

        if($this->input->get('search_value')!='')
        {
            $keyword=addslashes(trim($this->input->get('search_value')));
        }
        else{
            $keyword='';
        }

        $row=$this->Coupon_model->coupon_list('id','DESC','','', $keyword);

        $config = array();
        $config["base_url"] = base_url() . 'admin/coupon';
        $config["total_rows"] = count($row);
        $config["per_page"] = 12;

        $config['num_links'] = 2;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $config['full_tag_open'] = '<ul class="pagination">';
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

        $page = ($this->input->get('page')) ? $this->input->get('page') : 1;

        $page=($page-1) * $config["per_page"];
        
        $data["links"] = $this->pagination->create_links();
        $data['coupon_list'] = $this->Coupon_model->coupon_list('id', 'DESC', $config["per_page"], $page, $keyword);

        $data["redirectUrl"] = $this->redirectUrl;

        $this->template->load('admin/template', 'admin/page/coupon', $data); // :blush:
    } 

    function addForm()
    {
        $redirect=$_GET['redirect'].(isset($_GET['page']) ? '&page='.$_GET['page'] : '');

        $this->form_validation->set_rules('coupon_code', $this->lang->line('coupon_code_place_lbl'), 'trim|required');

        if($this->form_validation->run() == FALSE)
        {
            $messge = array('message' => $this->lang->line('input_required'),'class' => 'error');
                $this->session->set_flashdata('response_msg', $messge);

            if(isset($_GET['redirect'])){
                redirect($redirect, 'refresh');
            }
            else{
                redirect(base_url() . 'admin/coupon/add', 'refresh');
            }
        }
        else
        {
            $config['upload_path'] =  'assets/images/coupons/';
            $config['allowed_types'] = 'jpg|png|jpeg|PNG|JPG|JPEG';

            $image = date('dmYhis').'_'.rand(0,99999).".".pathinfo($_FILES['file_name']['name'], PATHINFO_EXTENSION);

            $config['file_name'] = $image;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('file_name')) {
                $messge = array('message' => $this->upload->display_errors(),'class' => 'error');
                $this->session->set_flashdata('response_msg', $messge);

                if(isset($_GET['redirect'])){
                    redirect($redirect, 'refresh');
                }
                else{
                    redirect(base_url() . 'admin/coupon/add', 'refresh');
                }
            } 
            else
            {  
                $upload_data = $this->upload->data();
            }

            $this->load->helper("date");

            $data = array(
                'coupon_desc'  => $this->input->post('coupon_desc'),
                'coupon_code'  => $this->input->post('coupon_code'),
                'coupon_image'  => $image,
                'coupon_per'  => $this->input->post('coupon_per'),
                'coupon_amt'  => $this->input->post('coupon_amt'),
                'max_amt_status'  => $this->input->post('max_amt_status'),
                'coupon_max_amt'  => $this->input->post('coupon_max_amt'),
                'cart_status'  => $this->input->post('cart_status'),
                'coupon_cart_min'  => $this->input->post('coupon_cart_min'),
                'coupon_limit_use'  => $this->input->post('coupon_limit_use'),
                'created_at'  =>  strtotime(date('d-m-Y h:i:s A'))
            );

            $data = $this->security->xss_clean($data);

            if($this->common_model->insert($data, 'tbl_coupon')){
                $messge = array('message' => $this->lang->line('add_msg'),'class' => 'success');
                $this->session->set_flashdata('response_msg', $messge);

            }
            else{
                $messge = array('message' => $this->lang->line('add_error'),'class' => 'error');
                $this->session->set_flashdata('response_msg', $messge);
            }
            
            if(isset($_GET['redirect'])){
                redirect($redirect, 'refresh');
            }
            else{
                redirect(base_url() . 'admin/coupon/add', 'refresh');
            }
        }
    }

    public function coupon_form()
    {
        $data = array();

        $id =  $this->uri->segment(4);

        $data['page_title'] = $this->lang->line('coupons_lbl');
        if($id==''){
            $data['current_page'] = $this->lang->line('add_coupon_lbl');
        }
        else{
            $data['coupon'] = $this->Coupon_model->single_coupon($id);

            $data['current_page'] = $this->lang->line('edit_coupon_lbl');
        }
        $this->template->load('admin/template', 'admin/page/coupon_form', $data); // :blush:
    }

    //-- update users info
    public function editForm($id)
    {
        $redirect=$_GET['redirect'].(isset($_GET['page']) ? '&page='.$_GET['page'] : '');

        $data = $this->Coupon_model->single_coupon($id);

        if($_FILES['file_name']['error']!=4)
        {
            
            unlink('assets/images/coupons/'.$data[0]->coupon_image);

            $mask = $data[0]->id.'*_*';
            array_map('unlink', glob('assets/images/coupons/thumbs/'.$mask));

            $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $data[0]->coupon_image);
            $mask = $thumb_img_nm.'*_*';
            array_map('unlink', glob('assets/images/coupons/thumbs/'.$mask));  

            $config['upload_path'] =  'assets/images/coupons/';
            $config['allowed_types'] = 'jpg|png|jpeg|PNG|JPG|JPEG';

            $image = date('dmYhis').'_'.rand(0,99999).".".pathinfo($_FILES['file_name']['name'], PATHINFO_EXTENSION);

            $config['file_name'] = $image;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('file_name')) {
                $messge = array('message' => $this->upload->display_errors(),'class' => 'error');
                $this->session->set_flashdata('response_msg', $messge);

                if(isset($_GET['redirect'])){
                    redirect($redirect, 'refresh');
                }
                else{
                    redirect(base_url() . 'admin/coupon/edit/'.$id, 'refresh');
                }
            }

        }
        else{
            $image=$data[0]->coupon_image;
        }

        $this->load->helper("date");

        $data = array(
            'coupon_desc'  => $this->input->post('coupon_desc'),
            'coupon_code'  => $this->input->post('coupon_code'),
            'coupon_image'  => $image,
            'coupon_per'  => $this->input->post('coupon_per'),
            'coupon_amt'  => $this->input->post('coupon_amt'),
            'max_amt_status'  => ($this->input->post('max_amt_status')) ? $this->input->post('max_amt_status') : 'false',
            'coupon_max_amt'  => $this->input->post('coupon_max_amt'),
            'cart_status'  => $this->input->post('cart_status'),
            'coupon_cart_min'  => $this->input->post('coupon_cart_min'),
            'coupon_limit_use'  => $this->input->post('coupon_limit_use')
        );

        $data = $this->security->xss_clean($data);

        if($this->common_model->update($data, $id,'tbl_coupon')){
            $messge = array('message' => $this->lang->line('update_msg'),'class' => 'success');
            $this->session->set_flashdata('response_msg', $messge);
        }
        else{
            $messge = array('message' => $this->lang->line('update_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $messge);
        }

        if(isset($_GET['redirect'])){
            redirect($redirect, 'refresh');
        }
        else{
            redirect(base_url() . 'admin/coupon/edit/'.$id, 'refresh');
        }
    }

    public function delete($id)
    {
        if($this->Coupon_model->delete($id)){
            $response=array('status' => 1, 'msg' => $this->lang->line('deleted_data_lbl'));
        }
        else{
            $response=array('status' => 0, 'msg' => $this->lang->line('something_went_wrong_err'));
        }

        echo json_encode($response);
        exit();
    }
}