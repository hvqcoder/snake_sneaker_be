<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Checkout extends MY_Controller
{
    protected $order_unique_id = null;

    protected $order_id = null;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Users_model');

        $this->load->model('Api_model');

        $this->load->library("CompressImage");
    }

    public function checkout()
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $data['page_title'] = 'Checkout';
        $data['current_page'] = 'Checkout';
        $data['my_cart'] = $this->Api_model->get_cart($this->user_id, '', 'DESC', 0, array('cart_status' => 1));
        $data['addresses'] = $this->General_model->get_addresses($this->user_id);
        $data['buy_now'] = 'false';

        $where = array('user_id' => $this->user_id, 'cart_type' => 'main_cart');

        $rowCoupon = $this->General_model->selectByids($where, 'tbl_applied_coupon');

        $data['coupon_id'] = (count($rowCoupon)) ? $rowCoupon[0]->coupon_id : 0;

        $this->load->model('Coupon_model');

        $data['coupon_list'] = $this->Coupon_model->coupon_list();

        if (!empty($data['my_cart']) && ($this->session->flashdata('order_unique_id') == '')) {
            $this->template->load('site/template2', 'site/pages/checkout', $data);
        } else {
            $message = array('message' => $this->lang->line('ord_placed_empty_lbl'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('/', 'refresh');
        }
    }

    public function buy_now()
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $this->load->model('Coupon_model');

        $product_slug =  $this->input->get('product');

        $chkout_ref =  $this->input->get('chkout_ref');

        $size =  (!empty($this->input->get('size'))) ? $this->input->get('size') : '0';
        $qty =  $this->input->get('qty');

        $where = array('product_slug' => $product_slug);

        $product_id =  $this->General_model->getIdBySlug($where, 'tbl_product');

        $cart_exist = $this->General_model->cart_items($product_id, $this->user_id);

        if ($cart_exist == 0) {

            $data_arr = array(
                'product_id' => $product_id,
                'user_id' => $this->user_id,
                'product_qty' => $qty,
                'product_size' => $size,
                'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
            );

            $data_usr = $this->security->xss_clean($data_arr);

            $this->General_model->insert($data_usr, 'tbl_cart');
        }

        if (empty($this->input->get('order_unique_id'))) {
            $where = array('user_id' => $this->user_id, 'product_id' => $product_id, 'cart_unique_id' => $chkout_ref);

            $my_tmp_cart = $this->General_model->selectByids($where, 'tbl_cart_tmp');

            if (empty($my_tmp_cart)) {
                $data_arr = array(
                    'product_id' => $product_id,
                    'user_id' => $this->user_id,
                    'product_qty' => $qty,
                    'product_size' => $size,
                    'cart_unique_id' => $chkout_ref,
                    'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                );

                $data_usr = $this->security->xss_clean($data_arr);

                $cart_id = $this->General_model->insert($data_usr, 'tbl_cart_tmp');
            } else {
                $data_arr = array(
                    'product_qty' => $qty,
                    'product_size' => $size,
                );

                $data_usr = $this->security->xss_clean($data_arr);

                $this->General_model->updateByids($data_usr, $where, 'tbl_cart_tmp');

                $cart_id = $my_tmp_cart[0]->id;
            }

            $data['page_title'] = 'Checkout';
            $data['current_page'] = 'Checkout';
            $data['my_cart'] = $this->Api_model->get_cart($this->user_id, $cart_id);
            $data['addresses'] = $this->General_model->get_addresses($this->user_id);
            $data['buy_now'] = 'true';

            $where = array('user_id' => $this->user_id, 'cart_type' => 'temp_cart', 'cart_id' => $cart_id);

            $rowCoupon = $this->General_model->selectByids($where, 'tbl_applied_coupon');

            $data['coupon_id'] = (count($rowCoupon)) ? $rowCoupon[0]->coupon_id : 0;

            $data['coupon_list'] = $this->Coupon_model->coupon_list();

            $this->template->load('site/template2', 'site/pages/checkout', $data);
        } else {
            $message = array('message' => $this->lang->line('ord_placed_empty_lbl'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('/', 'refresh');
        }
    }

    public function apply_coupon()
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            unset($message);
            $response = array('status' => -2, 'msg' => $this->lang->line('login_required_error'));
        } else {

            $this->load->helper("date");

            $coupon_id = $this->input->post("coupon_id");
            $cart_ids = $this->input->post("cart_ids");
            $cart_type = $this->input->post("cart_type");

            if ($cart_type == 'main_cart') {
                $where = array('user_id' => $this->user_id, 'cart_type' => $cart_type);
                $rowAppliedCoupon = $this->General_model->selectByids($where, 'tbl_applied_coupon');
                $my_cart = $this->Api_model->get_cart($this->user_id);
            } else {
                $where = array('user_id' => $this->user_id, 'cart_type' => $cart_type, 'cart_id' => $cart_ids);
                $rowAppliedCoupon = $this->General_model->selectByids($where, 'tbl_applied_coupon');
                $my_cart = $this->Api_model->get_cart($this->user_id, $cart_ids);
            }

            $total_amount = $you_save = $delivery_charge = 0;

            if (!empty($my_cart)) {

                $save_msg = '';

                if (count($rowAppliedCoupon) == 0) {
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
                                $payable_amt = $discount = 0;

                                $discount = amount_format(($row->coupon_per / 100) * $total_amount);

                                if ($row->cart_status == 'true') {

                                    if ($total_amount >= $row->coupon_cart_min) {

                                        if ($row->max_amt_status == 'true') {
                                            if ($discount > $row->coupon_max_amt) {
                                                $discount = $row->coupon_max_amt;
                                            }
                                        }

                                        $payable_amt = amount_format(($total_amount - $discount) + $delivery_charge);

                                        if ($discount != 0) {
                                            $save_msg = str_replace('###', CURRENCY_CODE . ' ' . sprintf("%01.2f", ($discount + $you_save)), $this->lang->line('coupon_save_msg_lbl'));
                                        } else {
                                            $save_msg = '';
                                        }

                                        $response = array('status' => '1', 'msg' => $this->lang->line('applied_coupon'), 'coupon_id' => $row->id, 'you_save_msg' => $save_msg, "price" => amount_format($total_amount), "payable_amt" => strval($payable_amt));
                                    } else {
                                        $response = array('status' => '0', 'msg' => $this->lang->line('insufficient_cart_amt'));
                                    }
                                } else {

                                    if ($row->max_amt_status == 'true') {

                                        if ($discount > $row->coupon_max_amt) {
                                            $discount = $row->coupon_max_amt;
                                        }
                                    }

                                    $payable_amt = amount_format(($total_amount - $discount) + $delivery_charge);

                                    if ($discount != 0) {
                                        $save_msg = str_replace('###', CURRENCY_CODE . ' ' .amount_format($discount + $you_save), $this->lang->line('coupon_save_msg_lbl'));
                                    } else {
                                        $save_msg = '';
                                    }

                                    $response = array('status' => '1', 'msg' => $this->lang->line('applied_coupon'), 'coupon_id' => $row->id, 'you_save_msg' => $save_msg, "price" => amount_format($total_amount), "payable_amt" => strval($payable_amt));
                                }
                            } else {

                                if ($row->cart_status == 'true') {

                                    if ($total_amount >= $row->coupon_cart_min) {

                                        $discount = amount_format($row->coupon_amt);

                                        $payable_amt = amount_format(($total_amount - $discount) + $delivery_charge);

                                        if ($discount > 0) {

                                            $save_msg = str_replace('###', CURRENCY_CODE . ' ' . amount_format($discount + $you_save), $this->lang->line('coupon_save_msg_lbl'));
                                        } else {
                                            $save_msg = '';
                                        }

                                        $response = array('status' => '1', 'msg' => $this->lang->line('applied_coupon'), 'coupon_id' => $row->id, 'you_save_msg' => $save_msg, "price" => amount_format($total_amount), "payable_amt" => strval($payable_amt));
                                    } else {
                                        $response = array('status' => '0', 'msg' => $this->lang->line('insufficient_cart_amt'));
                                    }
                                } else {

                                    $payable_amt = $discount = 0;

                                    if ($total_amount >= $row->coupon_amt) {

                                        $discount = amount_format($row->coupon_amt);
                                    } else {
                                        $discount = 0;
                                    }

                                    $payable_amt = amount_format(($total_amount - $discount) + $delivery_charge);

                                    if ($discount > 0) {
                                        $save_msg = str_replace('###', CURRENCY_CODE . ' ' . amount_format($discount + $you_save), $this->lang->line('coupon_save_msg_lbl'));
                                    } else {
                                        $save_msg = '';
                                    }

                                    $response = array('status' => '1', 'msg' => $this->lang->line('applied_coupon'), 'coupon_id' => $row->id, 'you_save_msg' => $save_msg, "price" => amount_format($total_amount), "payable_amt" => strval($payable_amt));
                                }
                            }
                        } else {
                            $response = array('status' => '0', 'msg' => $this->lang->line('use_limit_over'));
                        }
                    } else {
                        $response = array('status' => '0', 'msg' => $this->lang->line('no_coupon'));
                    }

                    if ($response['status']) {

                        $data_coupon = array(
                            'user_id' => $this->user_id,
                            'cart_type' => $cart_type,
                            'cart_id' => $cart_ids,
                            'coupon_id' => $coupon_id,
                            'applied_on' => strtotime(date('d-m-Y h:i:s A', now()))
                        );

                        $data_coupon = $this->security->xss_clean($data_coupon);

                        $this->General_model->insert($data_coupon, 'tbl_applied_coupon');
                    }
                } else {

                    if ($rowAppliedCoupon[0]->coupon_id == $coupon_id) {

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
                                    $payable_amt = $discount = 0;

                                    $discount = sprintf("%01.2f", ($row->coupon_per / 100) * $total_amount);

                                    if ($row->cart_status == 'true') {

                                        if ($total_amount >= $row->coupon_cart_min) {
                                            if ($row->max_amt_status == 'true') {
                                                if ($discount > $row->coupon_max_amt) {
                                                    $discount = $row->coupon_max_amt;
                                                    $payable_amt = sprintf("%01.2f", ($total_amount - $discount) + $delivery_charge);
                                                } else {
                                                    $payable_amt = sprintf("%01.2f", ($total_amount - $discount) + $delivery_charge);
                                                }
                                            } else {
                                                $payable_amt = sprintf("%01.2f", ($total_amount - $discount) + $delivery_charge);
                                            }

                                            if ($discount != 0) {
                                                $save_msg = str_replace('###', CURRENCY_CODE . ' ' . amount_format($discount + $you_save), $this->lang->line('coupon_save_msg_lbl'));
                                            } else {
                                                $save_msg = '';
                                            }

                                            $response = array('status' => '1', 'msg' => $this->lang->line('applied_coupon'), 'coupon_id' => $row->id, 'you_save_msg' => $save_msg, "price" => amount_format($total_amount), "payable_amt" => strval($payable_amt));
                                        } else {
                                            $response = array('status' => '0', 'msg' => $this->lang->line('insufficient_cart_amt'));
                                        }
                                    } else {

                                        if ($row->max_amt_status == 'true') {
                                            if ($discount > $row->coupon_max_amt) {
                                                $discount = $row->coupon_max_amt;
                                            }
                                        }

                                        $payable_amt = amount_format(($total_amount - $discount) + $delivery_charge);

                                        if ($discount != 0) {
                                            $save_msg = str_replace('###', CURRENCY_CODE . ' ' . amount_format($discount + $you_save), $this->lang->line('coupon_save_msg_lbl'));
                                        } else {
                                            $save_msg = '';
                                        }

                                        $response = array('status' => '1', 'msg' => $this->lang->line('applied_coupon'), 'coupon_id' => $row->id, 'you_save_msg' => $save_msg, "price" => amount_format($total_amount), "payable_amt" => strval($payable_amt));
                                    }
                                } else {

                                    if ($row->cart_status == 'true') {

                                        if ($total_amount >= $row->coupon_cart_min) {
                                            $discount = amount_format($row->coupon_amt);

                                            $payable_amt = amount_format(($total_amount - $discount) + $delivery_charge);

                                            if ($discount > 0) {

                                                $save_msg = str_replace('###', CURRENCY_CODE . ' ' . amount_format($discount + $you_save), $this->lang->line('coupon_save_msg_lbl'));
                                            } else {
                                                $save_msg = '';
                                            }

                                            $response = array('status' => '1', 'msg' => $this->lang->line('applied_coupon'), 'coupon_id' => $row->id, 'you_save_msg' => $save_msg, "price" => amount_format($total_amount), "payable_amt" => strval($payable_amt));
                                        } else {
                                            $response = array('status' => '0', 'msg' => $this->lang->line('insufficient_cart_amt'));
                                        }
                                    } else {

                                        $payable_amt = $discount = 0;

                                        if ($total_amount >= $row->coupon_amt) {

                                            $discount = amount_format($row->coupon_amt);
                                        } else {
                                            $discount = 0;
                                        }

                                        $payable_amt = amount_format(($total_amount - $discount) + $delivery_charge);

                                        if ($discount > 0) {
                                            $save_msg = str_replace('###', CURRENCY_CODE . ' ' . amount_format($discount + $you_save), $this->lang->line('coupon_save_msg_lbl'));
                                        } else {
                                            $save_msg = '';
                                        }

                                        $response = array('status' => '1', 'msg' => $this->lang->line('applied_coupon'), 'coupon_id' => $row->id, 'you_save_msg' => $save_msg, "price" => amount_format($total_amount), "payable_amt" => strval($payable_amt));
                                    }
                                }
                            } else {
                                $response = array('status' => '0', 'msg' => $this->lang->line('use_limit_over'));
                            }
                        } else {
                            $response = array('status' => '0', 'msg' => $this->lang->line('no_coupon'));
                        }

                        if ($response['status'] == 0) {
                            $where = array('user_id' => $this->user_id, 'cart_type' => $cart_type, 'coupon_id' => $coupon_id);
                            $this->General_model->deleteByids($where, 'tbl_applied_coupon');
                        }
                    } else {
                        $response = array('status' => '0', 'msg' => $this->lang->line('already_applied_coupon'));
                    }
                }
            } else {
                $response = array('status' => '-1', 'msg' => $this->lang->line('empty_cart_lbl'));
            }
        }

        echo json_encode($response);
    }

    public function remove_coupon($cart_type = 'main_cart')
    {

        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response = array('status' => 0, 'msg' => $this->lang->line('login_required_error'));
        } else {

            $coupon_id = $this->input->post("coupon_id");
            $cart_id = $this->input->post("cart_ids");

            if ($cart_type == 'main_cart') {
                $my_cart = $this->Api_model->get_cart($this->user_id);
                $where = array('user_id' => $this->user_id, 'cart_type' => $cart_type);
            } else {
                $my_cart = $this->Api_model->get_cart($this->user_id, $cart_id);
                $where = array('user_id' => $this->user_id, 'cart_type' => $cart_type, 'cart_id' => $cart_id, 'coupon_id' => $coupon_id);
            }

            $this->General_model->deleteByids($where, 'tbl_applied_coupon');

            if (!empty($my_cart)) {

                $total_cart_amt = $delivery_charge = $you_save = 0;

                foreach ($my_cart as $key => $value) {
                    $total_cart_amt += $value->selling_price * $value->product_qty;
                    $delivery_charge += $value->delivery_charge;
                    $you_save += $value->you_save_amt * $value->product_qty;
                }

                if ($you_save != 0) {
                    $save_msg = str_replace('###', CURRENCY_CODE . ' ' . amount_format($you_save), $this->lang->line('coupon_save_msg_lbl'));
                } else {
                    $save_msg = '';
                }

                $response = array('status' => '1', 'msg' => $this->lang->line('remove_coupon'), 'you_save_msg' => $save_msg, "payable_amt" => amount_format($total_cart_amt + $delivery_charge));
            } else {
                $message = array('message' => $this->lang->line('empty_cart_lbl'), 'class' => 'danger');
                $this->session->set_flashdata('response_msg', $message);
                $response = array('status' => '-1', 'msg' => $this->lang->line('empty_cart_lbl'));
            }
        }
        echo json_encode($response);
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

                                $discount = amount_format(($row->coupon_per / 100) * $total_amount);

                                if ($row->max_amt_status == 'true') {

                                    if ($discount > $row->coupon_max_amt) {
                                        $discount = $row->coupon_max_amt;   
                                    }
                                }

                                $payable_amt = amount_format(($total_amount - $discount) + $delivery_charge);

                                $response = array('status' => '1', "price" => $total_amount, "payable_amt" => strval($payable_amt), "discount" => $row->coupon_per, "discount_amt" => strval($discount));
                            } else {
                                $response = array('status' => '0', 'msg' => $this->lang->line('insufficient_cart_amt'));
                            }
                        } else {

                            $payable_amt = $discount = 0;

                            $discount = amount_format(($row->coupon_per / 100) * $total_amount);

                            if ($row->max_amt_status == 'true') {
                                if ($discount > $row->coupon_max_amt) {
                                    $discount = $row->coupon_max_amt;
                                }
                            }

                            $payable_amt = amount_format(($total_amount - $discount) + $delivery_charge);

                            $response = array('status' => '1', "price" => $total_amount, "payable_amt" => strval($payable_amt), "discount" => $row->coupon_per, "discount_amt" => strval($discount));
                        }
                    } else {

                        if ($row->cart_status == 'true') {

                            if ($total_amount >= $row->coupon_cart_min) {

                                $discount = $row->coupon_amt;

                                $payable_amt = amount_format($total_amount - $discount);

                                $response = array('status' => '1', "price" => $total_amount, "payable_amt" => strval($payable_amt), "discount" => $row->coupon_per, "discount_amt" => strval($discount));
                            } else {
                                $response = array('status' => '0', 'msg' => $this->lang->line('insufficient_cart_amt'));
                            }
                        } else {

                            $payable_amt = $discount = 0;

                            if ($total_amount >= $row->coupon_amt) {
                                $discount = amount_format($row->coupon_amt);   
                            }

                            $payable_amt = amount_format(($total_amount - $discount) + $delivery_charge);

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

    protected function get_order_unique_id()
    {
        $code_feed = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyv0123456789";
        $code_length = 8;
        $final_code = "";
        $feed_length = strlen($code_feed);

        for ($i = 0; $i < $code_length; $i++) {
            $feed_selector = rand(0, $feed_length - 1);
            $final_code .= substr($code_feed, $feed_selector, 1);
        }
        return $final_code;
    }

    protected function get_cart_details($cart_type = 'main_cart', $cart_ids = '')
    {
        if ($cart_type == 'main_cart') {
            $my_cart = $this->Api_model->get_cart($this->user_id);
        } else {
            $my_cart = $this->Api_model->get_cart($this->user_id, $cart_ids);
        }

        $is_avail = true;

        $total_cart_amt = $delivery_charge = $you_save = 0;

        if (!empty($my_cart)) {
            foreach ($my_cart as $key => $value) {
                if ($value->cart_status == 0) {
                    $is_avail = false;
                }

                $total_cart_amt += $value->selling_price * $value->product_qty;
                $delivery_charge += $value->delivery_charge;
                $you_save += $value->you_save_amt * $value->product_qty;
            }

            if (!$is_avail) {
                $response = array('status' => -2, 'message' => $this->lang->line('some_product_unavailable_lbl'));
            } else {
                $response = array('status' => 1, 'total_cart_amt' => $total_cart_amt, 'delivery_charge' => $delivery_charge, 'you_save' => $you_save);
            }
        } else {
            $response = array('status' => -1, 'msg' => $this->lang->line('ord_placed_empty_lbl'));
        }

        return $response;
    }

    protected function save_order_items($order_id = '', $delivery_charge = 0, $my_cart = array())
    {
        $items_arr = array();

        foreach ($my_cart as $key => $value) {

            $total_price = ($value->product_qty * $value->selling_price);

            $product_mrp = $value->selling_price;

            $data_order = array(
                'order_id'  =>  $order_id,
                'user_id' => $this->user_id,
                'product_id'  =>  $value->product_id,
                'product_title'  =>  $value->product_title,
                'product_qty'  =>  $value->product_qty,
                'product_mrp'  =>  $value->product_mrp,
                'product_price'  =>  $product_mrp,
                'you_save_amt'  =>  $value->you_save_amt,
                'product_size'  =>  $value->product_size,
                'total_price'  =>  $total_price,
                'delivery_charge'  =>  $value->delivery_charge,
                'pro_order_status' => '1'
            );

            $this->session->set_userdata("product_id", $value->product_id);

            $data_ord_detail = $this->security->xss_clean($data_order);

            $this->General_model->insert($data_ord_detail, 'tbl_order_items');

            $thumb_img_nm = preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->General_model->selectByidsParam(array('id' => $value->product_id), 'tbl_product', 'featured_image'));

            $img_file = $this->_generate_thumbnail('assets/images/products/', $thumb_img_nm, $this->General_model->selectByidsParam(array('id' => $value->product_id), 'tbl_product', 'featured_image'), 300, 300);

            $p_items['product_url'] = base_url('product/' . $value->product_slug);
            $p_items['product_title'] = $value->product_title;

            $p_items['product_img'] = base_url($img_file);

            $p_items['product_qty'] = $value->product_qty;
            $p_items['product_price'] = $product_mrp;
            $p_items['delivery_charge'] = $delivery_charge;
            $p_items['product_size'] = $value->product_size;

            $product_color = $this->General_model->selectByidsParam(array('id' => $value->product_id), 'tbl_product', 'color');

            if ($product_color != '') {

                $color_arr = explode('/', $product_color);
                $color_name = $color_arr[0];
                $product_color = $color_name;
            }

            $p_items['product_color'] = $product_color;

            $p_items['delivery_date'] = date('d M, Y') . '-' . date('d M, Y', strtotime('+7 days'));

            array_push($items_arr, $p_items);
        }

        return $items_arr;
    }

    protected function send_order_email($data_email)
    {
        $subject = $this->settings->app_name . ' - ' . $this->lang->line('ord_summary_lbl');

        $body = $this->load->view('emails/order_summary.php', $data_email, TRUE);

        if (send_email($data_email['users_email'], $data_email['users_name'], $subject, $body)) {

            if ($this->settings->app_order_email != '') {
                $subject = $this->settings->app_name . ' - ' . $this->lang->line('new_ord_lbl');

                $body = $this->load->view('emails/admin_order_summary.php', $data_email, TRUE);
                send_email($this->settings->app_order_email, $data_email['admin_name'], $subject, $body);
            }
        }
    }
}
