<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Site extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Api_model');

        $this->load->model('Coupon_model');
        $this->load->model('Offers_model');
        $this->load->model('Banner_model');
        $this->load->model('Product_model');
        $this->load->model('Sub_Category_model');
        $this->load->model('Users_model');
        $this->load->model('Order_model');
        $this->load->library('pagination');

        
    }

    public function product_rating($product_id)
    {
        $res = array();

        $where = array('product_id ' => $product_id);

        if ($row_rate = $this->General_model->selectByids($where, 'tbl_rating')) {
            foreach ($row_rate as $key => $value) {
                $rate_db[] = $value;
                $sum_rates[] = $value->rating;
            }

            $rate_times = count($rate_db);
            $sum_rates = array_sum($sum_rates);
            $rate_value = $sum_rates / $rate_times;

            $res['rate_times'] = strval($rate_times);
            $res['total_rate'] = strval($sum_rates);
            $res['rate_avg'] = strval(round($rate_value));
        } else {
            $res['rate_times'] = "0";
            $res['total_rate'] = "0";
            $res['rate_avg'] = "0";
        }
        return json_encode($res);
    }

    private function user_total_save($user_id)
    {
        $res = array();

        $row = $this->Api_model->get_cart($user_id);

        $total_amt = $delivery_charge = $you_save = 0;

        foreach ($row as $key => $value) {

            $data_ofr = $this->calculate_offer($this->get_single_info(array('id' => $value->product_id), 'offer_id', 'tbl_product'), $value->product_mrp * $value->product_qty);

            $arr_ofr = json_decode($data_ofr);

            $total_amt += $arr_ofr->selling_price;

            $delivery_charge += $value->delivery_charge;

            $you_save += $arr_ofr->you_save;
        }

        $res['total_item'] = strval(count($row));
        $res['price'] = strval($total_amt);
        $res['delivery_charge'] = ($delivery_charge != 0) ? $delivery_charge : 'Free';
        $res['payable_amt'] = strval($total_amt + $delivery_charge);

        $res['you_save'] = strval($you_save);

        return json_encode($res);
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

    public function _create_thumbnail($path, $thumb_name, $fileName, $width, $height)
    {
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

    public function calculate_offer($offer_id, $mrp)
    {
        $res = array();
        if ($offer_id != 0) {
            $offer = $this->Offers_model->single_offer($offer_id);
            $res['selling_price'] = round($mrp - (($offer->offer_percentage / 100) * $mrp), 2);

            $res['you_save'] = round($mrp - $res['selling_price'], 2);
            $res['you_save_per'] = $offer->offer_percentage;
        } else {
            $res['selling_price'] = $mrp;
            $res['you_save'] = 0;
            $res['you_save_per'] = 0;
        }
        return json_encode($res);
    }

    public function get_pincode_data()
    {
        $url = "http://www.postalpincode.in/api/pincode/" . $this->input->post('pincode');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $url);

        $response = array();

        if ($result = curl_exec($ch)) {
            $result1 = json_decode($result);

            if ($result1->PostOffice != NULL) {

                $response['status'] = '1';

                foreach ($result1->PostOffice as $key => $value) {

                    $response['city'] = $value->Circle;
                    $response['district'] = $value->District;
                    $response['state'] = $value->State;
                    $response['country'] = $value->Country;
                    break;
                }
            } else {
                $response['status'] = '0';
                $response['massage'] = 'No data found !';
            }
        } else {
            $response['status'] = '0';
            $response['massage'] = 'No data found !';
        }

        echo json_encode($response);
        return;
    }
}
