<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH.'controllers/Pages.php';

class Product extends Pages 
{
	public function __construct(){
        parent::__construct();
    }

    public function banner_products()
    {
    	$this->load->model('Banner_model');
        $slug =  $this->uri->segment(2);

        $row_banner=$this->Banner_model->getBannerBySlug($slug);

        if (empty($row_banner)) {
            $this->template->load('site/template2', 'site/pages/404');
            return;
        }

        $data['banner_info'] = $row_banner;

        $data['page_title'] = $row_banner->banner_title;
        $data['current_page'] = ucwords($row_banner->banner_title);
        $data['sharing_img'] = base_url('assets/images/banner/' . $row_banner->banner_image);

        $id = $row_banner->id;

        $data['type'] = 'banner';
        $data['id'] = $row_banner->id;

        $base_url = base_url('banners/'.$slug);

        $row_all = $this->Api_model->products_filter('banner', $row_banner->id);

        $this->get_product_list('banner', $row_all, $row_banner->id, $base_url, $data);
    }

    public function todays_deals()
    {
        $data['page_title'] = $this->lang->line('todays_deal_lbl');
        $data['current_page'] = $this->lang->line('todays_deal_lbl');

        $base_url = base_url('todays-deals/');

        $row_all = $this->Api_model->products_filter('today_deal');

        $this->get_product_list('today_deal', $row_all, 0, $base_url, $data);
    }

    public function cat_sub_product($category_slug, $sub_category_slug = '')
    {
        $this->load->model('Category_model');
        $this->load->model('Sub_Category_model');

        $data['category_list'] = $this->General_model->selectWhere('tbl_category', array('status' => '1'), 'DESC', 'id');

        if ($sub_category_slug != '') {

            if (strcmp($category_slug, 'products') == 0) {

                $category_row = $this->General_model->selectByid($sub_category_slug, 'tbl_category');

                $data['page_title'] = $category_row->category_name;
                $data['current_page'] = ucwords($category_row->category_name);

                $data['sharing_img'] = base_url('assets/images/category/' . $category_row->category_image);

                $base_url = base_url() . 'category/products/' . $category_row->id;

                $row_all = $this->Api_model->products_filter('productList_cat', $category_row->id);

                $this->get_product_list('productList_cat', $row_all, $category_row->id, $base_url, $data);
            } else {

                $row_category = $this->Category_model->getCategoryBySlug($category_slug, 'tbl_category');

                $row_sub_category = $this->Sub_Category_model->getSubCategoryBySlug($sub_category_slug, 'tbl_sub_category');

                if (!empty($row_category) && !empty($row_sub_category)) {

                    $data['page_title'] = $row_category->category_name . ' | ' . $row_sub_category->sub_category_name;
                    $data['current_page'] = ucwords($row_category->category_name . ' | ' . $row_sub_category->sub_category_name);

                    $data['sharing_img'] = base_url('assets/images/sub_category/'.$row_sub_category->sub_category_image);

                    $base_url = base_url().'category/'.$category_slug.'/'.$sub_category_slug;

                    $row_all = $this->Api_model->products_filter('productList_cat_sub', $row_sub_category->id);

                    $this->get_product_list('productList_cat_sub', $row_all, $row_sub_category->id, $base_url, $data);
                } else {
                    $this->template->load('site/template2', 'site/pages/404');
                    return;
                }
            }
        } else {
            $this->template->load('site/template2', 'site/pages/404');
            return;
        }
    }

    public function brand_product($brand_slug)
    {
        $this->load->model('Brand_model');

        $brand_row = $this->Brand_model->getBrandBySlug($brand_slug);

        if (empty($brand_row)) {
            $this->template->load('site/template2', 'site/pages/404');
            return;
        }

        $data['page_title'] = $brand_row->brand_name;
        $data['current_page'] = ucwords($brand_row->brand_name);

        $data['sharing_img'] = base_url('assets/images/brands/' . $brand_row->brand_image);

        $base_url = base_url('brand/'.$brand_slug);

        $row_all = $this->Api_model->products_filter('brand', $brand_row->id);

        $this->get_product_list('brand', $row_all, $brand_row->id, $base_url, $data);
    }

    public function offer_products()
    {
        $this->load->model('Offers_model');

        $slug =  $this->uri->segment(2);

        $offer_row = $this->Offers_model->getOfferBySlug($slug);

        if (empty($offer_row)) {
            $this->template->load('site/template2', 'site/pages/404');
            return;
        }

        $data['page_title'] = $offer_row->offer_title;
        $data['current_page'] = ucwords($offer_row->offer_title);

        $data['sharing_img'] = base_url('assets/images/offers/' . $offer_row->offer_image);

        $base_url = base_url('offers/' . $slug);

        $row_all = $this->Api_model->products_filter('offer', $offer_row->id);

        $this->get_product_list('offer', $row_all, $offer_row->id, $base_url, $data);
    }

    public function top_rated_products()
    {
        $data['page_title'] = $this->lang->line('top_rated_product_lbl');
        $data['current_page'] = $this->lang->line('top_rated_product_lbl');

        $base_url = base_url('top-rated-products/');

        $row_all = $this->Api_model->products_filter('top_rated_products', 0);

        $this->get_product_list('top_rated_products', $row_all, 0, $base_url, $data);
    }

    public function latest_products()
    {

        $data['page_title'] = $this->lang->line('latest_product_lbl');
        $data['current_page'] = $this->lang->line('latest_product_lbl');

        $base_url = base_url('latest-products/');
        $row_all = $this->Api_model->products_filter('latest_products', 0);

        $this->get_product_list('latest_products', $row_all, 0, $base_url, $data);
    }

    public function recently_viewed_products()
    {

        $data['page_title'] = $this->lang->line('recent_view_lbl');
        $data['current_page'] = $this->lang->line('recent_view_lbl');

        $base_url = base_url('recently-viewed-products/');

        $row_all = $this->Api_model->products_filter('recent_viewed_products', 0, '', '', '', '', '', '', '', '', $this->user_id);

        $this->get_product_list('recent_viewed_products', $row_all, 0, $base_url, $data);
    }

    public function single_product($product_slug)
    {
        $this->load->model('Product_model');

        $product_row = $this->Product_model->getProductBySlug($product_slug);

        if (empty($product_row)) {
            $this->template->load('site/template2', 'site/pages/404');
            return;
        }

        $data = array();
        $data['page_title'] = $product_row->product_title;
        $data['current_page'] = $product_row->product_title;
        $data['product'] = $product_row;

        $where = array('user_id' => $this->user_id, 'product_id' => $product_row->id);

        $review = $this->General_model->selectByids($where, 'tbl_rating');

        if (!empty($review)) {

            unset($where);
            $where = array('type' => 'review', 'parent_id' => $review[0]->id);

            $images = $this->General_model->selectByids($where, 'tbl_product_images');

            array_push($review, $images);

            $data['my_review'] = $review;
        } else {
            $data['my_review'] = array();
        }

        $data['product_rating'] = $this->Api_model->get_product_review($product_row->id);

        unset($where);
        $where = array('category_id' => $product_row->category_id, 'sub_category_id' => $product_row->sub_category_id, 'id !=' => $product_row->id);

        $data['related_products'] = $this->General_model->selectByids($where, 'tbl_product');

        $data['recent_viewed_products'] = $this->Api_model->products_filter('recent_viewed_products', '', 10, 0, '', '', '', '', '', '', $this->user_id);

        unset($where);
        $where = array('user_id' => $this->user_id, 'product_id' => $product_row->id, 'pro_order_status' => '4');

        if (count($this->General_model->selectByids($where, 'tbl_order_items'))) {
            $data['is_purchased'] = true;
        } else {
            $data['is_purchased'] = false;
        }

        $data['sharing_img'] = base_url('assets/images/products/' . $data['product']->featured_image);

        $this->Product_model->_set_view($data['product']->id);

        $this->template->load('site/template2', 'site/pages/single_product', $data);

        if ($this->user_id != 0) {
            $data_recent = $this->General_model->selectByids(array('user_id' => $this->user_id, 'product_id' => $product_row->id), 'tbl_recent_viewed');

            if (empty($data_recent)) {

                $data_update = array(
                    'user_id' => $this->user_id,
                    'product_id' => $product_row->id,
                    'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                );

                $data_update = $this->security->xss_clean($data_update);

                $data_update = $this->General_model->insert($data_update, 'tbl_recent_viewed');
            } else {

                $data_update = array(
                    'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                );

                $data_update = $this->security->xss_clean($data_update);
                unset($where);
                $where = array('product_id ' => $product_row->id, 'user_id' => $this->user_id);

                $updated_id = $this->General_model->updateByids($data_update, $where, 'tbl_recent_viewed');
            }
        }
    }

    public function search()
    {
        $keyword = addslashes(trim($this->input->get('keyword')));

        $slug = $this->input->get('category') ? $this->input->get('category') : '';

        $where = array('category_slug' => $slug);

        $category_id =  ($slug != '') ? $this->General_model->getIdBySlug($where, 'tbl_category') : 0;

        $data = array();
        $data['page_title'] = $this->lang->line('search_short_lbl');
        $data['current_page'] = $this->lang->line('search_result_lbl') . ' ' . $keyword;

        $base_url = base_url('search-result/');

        $row_all = $this->Api_model->products_filter('search', '', '', '', '', '', '', '', '', $keyword, '', $category_id);

        $this->get_product_list('search', $row_all, 0, $base_url, $data, $keyword, $category_id);
    }

    private function get_product_list($type, $row_all, $id = 0, $base_url = '', $data = '', $keyword = '', $category = '')
    {
        if (empty($row_all)) {
            $this->template->load('site/template2', 'site/pages/no_products', $data);
            return;
        }

        $data['category_list'] = $this->General_model->selectWhere('tbl_category', array('status' => '1'), 'DESC', 'id');

        $price_arr = array();

        foreach ($row_all as $value) {
            $price = $value->selling_price;
            array_push($price_arr, $price);
        }

        $row = array();

        $this->load->library('pagination');

        $config = array();
        $config["base_url"] = $base_url;
        $config["per_page"] = $this->page_limit;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        $page = ($this->input->get('page')) ? $this->input->get('page') : 1;

        $page = ($page - 1) * $config["per_page"];

        $page2 = ($this->input->get('page')) ? $this->input->get('page') : 1;

        if (!empty($this->input->get('sortByBrand'))) {

            $brands_ids = $this->input->get('sortByBrand');

            unset($row_all, $row);

            $row_all = $this->Api_model->products_filter($type, $id, '', '', $brands_ids, '', '', '', '', $keyword, $this->user_id, $category);

            $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, $brands_ids, '', '', '', '', $keyword, $this->user_id, $category);

            if ($this->input->get('price_filter') != '') {

                unset($row_all, $row);

                $price_filter = (explode('-', $this->input->get('price_filter')));

                $min_price = $price_filter[0];
                $max_price = $price_filter[1];

                $row_all = $this->Api_model->products_filter($type, $id, '', '', $brands_ids, $min_price, $max_price, '', '', $keyword, $this->user_id, $category);

                $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, $brands_ids, $min_price, $max_price, '', '', $keyword, $this->user_id, $category);

                if ($this->input->get('sort') != '') {

                    unset($row_all, $row);

                    $row_all = $this->Api_model->products_filter($type, $id, '', '', $brands_ids, $min_price, $max_price, $this->input->get('sort'), '', $keyword, $this->user_id, $category);

                    $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, $brands_ids, $min_price, $max_price, $this->input->get('sort'), '', $keyword, $this->user_id, $category);

                    if (!empty($this->input->get('sortBySize'))) {

                        $sizes = implode(',', $this->input->get('sortBySize'));

                        $row_all = $this->Api_model->products_filter($type, $id, '', '', $brands_ids, $min_price, $max_price, $this->input->get('sort'), $sizes, $keyword, $this->user_id, $category);

                        $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, $brands_ids, $min_price, $max_price, $this->input->get('sort'), $sizes, $keyword, $this->user_id, $category);
                    }
                } else if (!empty($this->input->get('sortBySize'))) {

                    unset($row_all, $row);

                    $sizes = implode(',', $this->input->get('sortBySize'));

                    $row_all = $this->Api_model->products_filter($type, $id, '', '', $brands_ids, $min_price, $max_price, '', $sizes, $keyword, $this->user_id, $category);

                    $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, $brands_ids, $min_price, $max_price, '', $sizes, $keyword, $this->user_id, $category);
                }
            } else if (!empty($this->input->get('sortBySize'))) {

                unset($row_all, $row);

                $sizes = implode(',', $this->input->get('sortBySize'));

                $row_all = $this->Api_model->products_filter($type, $id, '', '', $brands_ids, '', '', '', $sizes, $keyword, $this->user_id, $category);

                $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, $brands_ids, '', '', '', $sizes, $keyword, $this->user_id, $category);

                if ($this->input->get('sort') != '') {

                    unset($row_all, $row);

                    $row_all = $this->Api_model->products_filter($type, $id, '', '', $brands_ids, '', '', $this->input->get('sort'), $sizes, $keyword, $this->user_id, $category);

                    $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, $brands_ids, '', '', $this->input->get('sort'), $sizes, $keyword, $this->user_id, $category);
                }
            } else if ($this->input->get('sort') != '') {

                unset($row_all, $row);

                $row_all = $this->Api_model->products_filter($type, $id, '', '', $brands_ids, '', '', $this->input->get('sort'), '', $keyword, $this->user_id, $category);

                $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, $brands_ids, '', '', $this->input->get('sort'), '', $keyword, $this->user_id, $category);
            }

        } else if ($this->input->get('price_filter') != '') {
            $price_filter = (explode('-', $this->input->get('price_filter')));

            $min_price = $price_filter[0];
            $max_price = $price_filter[1];

            unset($row_all, $row);

            $row_all = $this->Api_model->products_filter($type, $id, '', '', '', $min_price, $max_price, '', '', $keyword, $this->user_id, $category);

            $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, '', $min_price, $max_price, '', '', $keyword, $this->user_id, $category);

            if ($this->input->get('sort') != '') {

                unset($row_all, $row);

                $row_all = $this->Api_model->products_filter($type, $id, '', '', '', $min_price, $max_price, $this->input->get('sort'), '', $keyword, $this->user_id, $category);

                $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, '', $min_price, $max_price, $this->input->get('sort'), '', $keyword, $this->user_id, $category);

                if (!empty($this->input->get('sortBySize'))) {

                    unset($row_all, $row);

                    $sizes = implode(',', $this->input->get('sortBySize'));

                    $row_all = $this->Api_model->products_filter($type, $id, '', '', '', $min_price, $max_price, $this->input->get('sort'), $sizes, $keyword, $this->user_id, $category);

                    $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, '', $min_price, $max_price, $this->input->get('sort'), $sizes, $keyword, $this->user_id, $category);
                }
            } else if (!empty($this->input->get('sortBySize'))) {

                unset($row_all, $row);

                $sizes = implode(',', $this->input->get('sortBySize'));

                $row_all = $this->Api_model->products_filter($type, $id, '', '', '', $min_price, $max_price, '', $sizes, $keyword, $this->user_id, $category);

                $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, '', $min_price, $max_price, '', $sizes, $keyword, $this->user_id, $category);
            }
        } else if (!empty($this->input->get('sortBySize'))) {

            $sizes = implode(',', $this->input->get('sortBySize'));

            unset($row_all, $row);

            $row_all = $this->Api_model->products_filter($type, $id, '', '', '', '', '', '', $sizes, $keyword, $this->user_id, $category);

            $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, '', '', '', '', $sizes, $keyword, $this->user_id, $category);

            if ($this->input->get('sort') != '') {

                unset($row_all, $row);

                $row_all = $this->Api_model->products_filter($type, $id, '', '', '', '', '', $this->input->get('sort'), $sizes, $keyword, $this->user_id, $category);

                $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, '', '', '', $this->input->get('sort'), $sizes, $keyword, $this->user_id, $category);
            }
        } else if (!empty($this->input->get('sort'))) {

            unset($row_all, $row);

            $row_all = $this->Api_model->products_filter($type, $id, '', '', '', '', '', $this->input->get('sort'), '', $keyword, $this->user_id, $category);

            $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, '', '', '', $this->input->get('sort'), '', $keyword, $this->user_id, $category);

        } else {

            unset($row);

            $row = $this->Api_model->products_filter($type, $id, $config["per_page"], $page, '', '', '', '', '', $keyword, $this->user_id, $category);
        }

        $brands = array();
        $size = array();

        foreach ($row_all as $key => $value) {

            $brands[] = $value->brand_id;

            if ($value->product_size != '') {
                $size[] = $value->product_size;
            }
        }

        $size_arr = array();

        foreach ($size as $key => $value) {
            foreach (explode(',', $value) as $key1 => $value1) {
                $size_arr[] = trim($value1);
            };
        }

        if ($type != 'brand' and !empty($brands)) {

            $data['brand_count_items'] = array_count_values($brands);
            $data['brand_list'] = $this->General_model->selectByidsIN(array_unique($brands), 'tbl_brands');
        }

        $min = min($price_arr);
        $max = max($price_arr);

        $data['price_min'] = $min;
        $data['price_max'] = $max;

        asort($size_arr);

        $data['size_list'] = array_unique($size_arr);

        $config["total_rows"] = count($row_all);

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

        $data['product_list'] = $row;

        $start_count = ($page2 == 1) ? 1 : ($this->page_limit * ($page2 - 1) + 1);

        $total_count = count($row_all);
        $count = count($row) * $page2;

        $end_count = ($count < $this->page_limit) ? $total_count : $count;

        $data["show_result"] = 'Showing ' . $start_count . '–' . $end_count . ' of ' . count($row_all) . ' results';

        $this->template->load('site/template2', 'site/pages/products', $data);
    }
}