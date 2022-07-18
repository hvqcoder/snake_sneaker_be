<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends MY_Controller 
{
	public function __construct(){
        parent::__construct();
        $this->load->model('Api_model');
    }

    public function home()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('home_lbl');
        $data['current_page'] = $this->lang->line('home_lbl');

        $data['brands_list'] = $this->Api_model->brand_list();

        $data['category_list'] = $this->Api_model->category_list();

        $data['offers_list'] = $this->Api_model->offers_list();

        $data['banner_list'] = $this->Api_model->banner_list();

        $data['todays_deal'] = $this->Api_model->products_filter('today_deal', '0');

        $data['home_categories'] = $this->General_model->selectByids(array('set_on_home' => '1', 'status' => '1'), 'tbl_category', 'category_name', 'ASC');

        $data['latest_products'] = $this->Api_model->products_filter('latest_products', '', 10, 0);

        $data['top_rated_products'] = $this->Api_model->products_filter('top_rated_products', '', 10, 0);

        $data['recent_viewed_products'] = $this->Api_model->products_filter('recent_viewed_products', '', 10, 0, '', '', '', '', '', '', $this->user_id);

        $this->template->load('site/template', 'site/pages/home', $data);
    }

    public function get_home_sub_category($cat_id, $limit)
    {
        $this->load->model('Sub_Category_model');
        return $this->Sub_Category_model->get_home_subcategories($cat_id, $limit);
    }

    public function get_cat_sub_product($category_id, $sub_category_id = '')
    {
        return $this->Api_model->products_filter('productList_cat_sub', $sub_category_id, '10', '0', '', '', '', 'newest');
    }

    public function banners(){

        $data = array();
        $data['page_title'] = $this->lang->line('banner_lbl');
        $data['current_page'] = $this->lang->line('banner_lbl');
        $data['banner_list'] = $this->Api_model->banner_list();

        $this->template->load('site/template2', $this->frontend_pages.'banners', $data);
    }

    public function category()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('category_lbl');
        $data['current_page'] = $this->lang->line('category_lbl');
        $data['category_list'] = $this->Api_model->category_list();
        $this->template->load('site/template2', 'site/pages/category', $data);
    }

    public function sub_category()
    {
        $this->load->model('Category_model');

        $segment = $this->uri->total_segments();

        $category_slug = $this->uri->segment($segment);

        $row_category = $this->Category_model->getCategoryBySlug($category_slug, 'tbl_category');

        if(empty($row_category)){
            $this->template->load('site/template2', 'site/pages/404');
            return;
        }

        $data = array();
        
        $data['page_title'] = $this->lang->line('subcategory_lbl');
        
        $data['current_page'] = $row_category->category_name;

        $data['sub_category_list'] = $this->Api_model->sub_category_list($row_category->id);
        
        $data['category_slug'] = $row_category->category_slug;

        $data['sharing_img'] = base_url('assets/images/category/' . $row_category->category_image);

        $this->template->load('site/template2', 'site/pages/sub_category', $data);
    }

    public function brand()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('brands_lbl');
        $data['current_page'] = $this->lang->line('brands_lbl');
        $data['brands_list'] = $this->Api_model->brand_list();
        $this->template->load('site/template2', 'site/pages/brand', $data);
    }

    public function offers()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('offer_lbl');
        $data['current_page'] = $this->lang->line('offer_lbl');
        $data['offers_list'] = $this->Api_model->offers_list();

        $this->template->load('site/template2', 'site/pages/offers', $data);
    }

	public function login(){

        if($this->user_id!=0){
            redirect('/', 'refresh');
            $message = array('message' => $this->lang->line('login_success'),'class' => 'success');
            $this->session->set_flashdata('response_msg', $message);
        }

        $data = array();

        $data['page_title'] = $this->lang->line('login_register_lbl');
        $data['current_page'] = $this->lang->line('login_register_lbl');

        $this->load->view($this->frontend_pages.'login_register', $data);
    }

    public function register(){

        if($this->user_id!=0){
            redirect('/', 'refresh');
            $message = array('message' => $this->lang->line('login_success'),'class' => 'success');
            $this->session->set_flashdata('response_msg', $message);
        }

        $data = array();

        $data['page_title'] = $this->lang->line('register_lbl');
        $data['current_page'] = $this->lang->line('register_lbl');

        $this->load->view($this->frontend_pages.'register', $data);
    }

    public function reset_password_page()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('reset_password_lbl');
        $data['current_page'] = $this->lang->line('reset_password_lbl');

        $requestToken = $this->input->get('requestToken');

        $where = array('requestToken' => $requestToken, 'status' => '1');

        $rowReset = $this->General_model->selectByids($where, 'tbl_password_reset');

        if (!empty($rowReset)) {

            $rowReset = $rowReset[0];

            $currentDatetime = strtotime(date('Y-m-d h:i'));

            if ($rowReset->expires_in < $currentDatetime) {
                $data['link_err'] = $this->lang->line('reset_pass_link_exp');
            } else {
                $data['link_err'] = '';
            }
        } else {
            $data['link_err'] = $this->lang->line('reset_pass_link_err');
        }

        $this->template->load('site/template2', 'site/pages/reset_password', $data);
    }

    public function forgot_password(){

        if($this->user_id!=0){
            redirect('/', 'refresh');
            $message = array('message' => $this->lang->line('login_success'),'class' => 'success');
            $this->session->set_flashdata('response_msg', $message);
        }

        $data = array();

        $data['page_title'] = $this->lang->line('forgot_password_lbl');
        $data['current_page'] = $this->lang->line('forgot_password_lbl');
        $this->load->view($this->frontend_pages.'forgot_password', $data);
    }

    public function about_us()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('about_us_lbl');
        $data['current_page'] = $this->lang->line('about_us_lbl');
        $data['settings_row'] = $this->web_settings->about_content;

        $this->template->load('site/template2', 'site/pages/page', $data);
    }

    public function faq()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('faq_lbl');
        $data['current_page'] = $this->lang->line('faq_lbl');
        $data['faq_row'] = $this->General_model->selectByids(array('status' => '1', 'type' => 'faq'), 'tbl_faq');
        $this->template->load('site/template2', 'site/pages/faq', $data);
    }

    public function payments()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('payment_lbl');
        $data['current_page'] = $this->lang->line('payment_lbl');
        $data['faq_row'] = $this->General_model->selectByids(array('status' => '1', 'type' => 'payment'), 'tbl_faq');
        $this->template->load('site/template2', 'site/pages/faq', $data);
    }

    public function terms_of_use()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('term_of_use_lbl');
        $data['current_page'] = $this->lang->line('term_of_use_lbl');
        $data['settings_row'] = $this->web_settings->terms_of_use_content;
        $this->template->load('site/template2', 'site/pages/page', $data);
    }

    public function privacy()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('privacy_lbl');
        $data['current_page'] = $this->lang->line('privacy_lbl');
        $data['settings_row'] = $this->web_settings->privacy_content;
        $this->template->load('site/template2', 'site/pages/page', $data);
    }

    public function delete_instruction()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('delete_instruction_lbl');
        $data['current_page'] = $this->lang->line('delete_instruction_lbl');
        $data['settings_row'] = $this->settings->delete_instruction;
        $this->template->load('site/template2', 'site/pages/page', $data);
    }

    public function cancellation()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('cancellation_lbl');
        $data['current_page'] = $this->lang->line('cancellation_lbl');
        $data['settings_row'] = $this->web_settings->cancellation_content;
        $this->template->load('site/template2', 'site/pages/page', $data);
    }

    public function refund_return_policy()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('refund_return_policy_lbl');
        $data['current_page'] = $this->lang->line('refund_return_policy_lbl');
        $data['settings_row'] = $this->web_settings->refund_return_policy;
        $this->template->load('site/template2', 'site/pages/page', $data);
    }

    public function contact_us()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('contactus_lbl');
        $data['current_page'] = $this->lang->line('contactus_lbl');
        $data['contact_subjects'] = $this->General_model->select('tbl_contact_sub', 'DESC');
        $this->template->load('site/template2', 'site/pages/contact_us', $data);
    }

    public function page_not_found()
    {
        $data = array();
        $data['page_title'] = $this->lang->line('page_not_found_lbl');
        $data['current_page'] = $this->lang->line('page_not_found_lbl');
        $this->template->load('site/template2', 'site/pages/404', $data);
    }

    public function page_404()
    {
        $data = array();
        $data['page_title'] = '404';
        $data['current_page'] = '404';
        $this->load->view('site/pages/page_404', $data);
    }
}