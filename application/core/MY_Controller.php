<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public $settings = '';
    public $web_settings = '';
    public $app_settings = '';

    public $vendor_dir = 'assets/vendor/';
    public $img_dir = 'assets/img/';

    public $user_id = 0;

    public $user_image='';

    public $current_user_data = NULL;

    protected $frontend_pages = 'site/pages/';

    protected $page_limit = 20;

    public function __construct()
    {
        ini_set('MAX_EXECUTION_TIME', '-1');

        parent::__construct();

        $this->load->model('Setting_model');
        $this->load->model('Common_model', 'General_model');

        $this->load->helper("date");
        $this->load->library('user_agent');

        $this->settings = $this->Setting_model->get_details();
        $this->web_settings = $this->Setting_model->get_web_details();
        $this->app_settings = $this->Setting_model->get_android_details();

        define('APP_CURRENCY', $this->settings->app_currency_code);
        define('CURRENCY_CODE', $this->settings->app_currency_html_code);

        $curr_date = date('d-m-Y');

        $this->General_model->deleteByids(array("DATE_FORMAT(FROM_UNIXTIME(`created_at`), '%e-%l-%Y') <" => $curr_date), 'tbl_cart_tmp');

        if ($this->session->userdata('user_id')) {

            $this->user_id = $this->session->userdata('user_id');

            $this->current_user_data = $this->General_model->selectByid($this->user_id, 'tbl_users');

            if($this->current_user_data->user_image!='' || !file_exists('assets/images/users/'.$this->current_user_data->user_image)){

                $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->current_user_data->user_image);

                $this->user_image= base_url($this->_generate_thumbnail('assets/images/users/', $thumb_img_nm, $this->current_user_data->user_image, 200, 200));
            }
            else{
                $this->user_image=base_url('assets/images/photo.jpg');
            }
        }
    }

    public function get_redirect_url()
    {
        $currentURL = current_url();
        $params = &$_SERVER['QUERY_STRING'];
        return (!empty($params)) ? $currentURL . '?' . $params : $currentURL;
    }

    public function number_format_short($n, $precision = 1, $number = false, $want_suffix = false)
    {
        if ($n < 900) {
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else if ($n < 900000000) {
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } else if ($n < 900000000000) {
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }

        if ($number) {
            return $n_format;
        } else if ($want_suffix) {
            return $suffix;
        } else {
            return $n_format . $suffix;
        }
    }
    
    public function _generate_thumbnail($path, $thumb_name, $fileName, $width, $height)
    {
        $this->load->library("CompressImage");

        $source_path = $path . $fileName;

        if ($fileName != '') {
            if (file_exists($source_path)) {

                $ext = pathinfo($fileName, PATHINFO_EXTENSION);

                $thumb_name = $thumb_name . '_' . $width . 'x' . $height . '.' . $ext;

                $thumb_path = $path . 'thumbs/' . $thumb_name;

                if (!file_exists($thumb_path)) {
                    $this->load->library('image_lib');
                    $config['image_library']  = 'gd2';
                    $config['source_image']   = $source_path;
                    $config['new_image']      = $thumb_path;
                    $config['create_thumb']   = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['width']          = $width;
                    $config['height']         = $height;
                    $this->image_lib->initialize($config);
                    if (!$this->image_lib->resize()) {
                        echo $this->image_lib->display_errors();
                    }
                }

                return $thumb_path;

                $this->image_lib->clear();
            }
        } else {
            return '';
        }
    }

    public function get_category()
    {
        return $this->General_model->selectWhere('tbl_category', array('status' => '1'), 'DESC', 'id');
    }

    public function get_sub_category($cat_id)
    {
        return $this->General_model->selectWhere('tbl_sub_category', array('status' => '1', 'category_id' => $cat_id), 'DESC', 'id');
    }

    public function get_cart($limit = 0, $cart_ids = '')
    {
        $this->load->model('Api_model');
        return $this->Api_model->get_cart($this->user_id, $cart_ids, 'DESC', $limit);
    }

    public function getCount($table, $where = '')
    {
        if ($where == '')
            return $this->General_model->get_count($table);
        else
            return $this->General_model->get_count_by_ids($where, $table);
    }

    public function is_favorite($user_id, $product_id)
    {

        $where = array('user_id ' => $user_id, 'product_id' => $product_id);

        $count = count($this->General_model->selectByids($where, 'tbl_wishlist'));

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function check_cart($product_id, $user_id, $size = '')
    {
        if ($size == '') {
            $where = array('product_id' => $product_id, 'user_id' => $user_id);
        } else {
            $where = array('product_id' => $product_id, 'user_id' => $user_id, 'product_size' => $user_id);
        }

        if ($this->General_model->selectByids($where, 'tbl_cart')) {
            return true;
        } else {
            return false;
        }
    }

    public function get_single_info($ids, $param, $table_nm)
    {
        $data = $this->General_model->selectByids($ids, $table_nm);
        if (!empty($data)) {
            return $data[0]->$param;
        } else {
            return '';
        }
    }
}
