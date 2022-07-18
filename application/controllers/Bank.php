<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bank extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
    }

    public function saved_bank_accounts()
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        $data = array();
        $data['page_title'] = $this->lang->line('saved_bank_lbl');
        $data['current_page'] = $this->lang->line('saved_bank_lbl');

        $data['bank_details'] = $this->General_model->selectByids(array('user_id' => $this->user_id,'is_deleted' => 'N'), 'tbl_bank_details');

        $this->template->load('site/template2', 'site/pages/saved_cards.php', $data);
    }

    public function add_new_bank()
    {
    	if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{
        	extract($this->input->post());

        	$where = array('user_id' => $this->user_id, 'account_no' => $account_no, 'bank_ifsc' => $bank_ifsc);

        	$row = $this->General_model->selectByids($where, 'tbl_bank_details');

        	if(empty($row)){

        		if ($this->input->post('is_default') != '') {
                    $is_default = 1;
                } else {
                    $is_default = 0;
                }

                $where = array('user_id' => $this->user_id);
                $row_data = $this->General_model->selectByids($where, 'tbl_bank_details');

                if (count($row_data) > 0) {
                    if ($is_default == 1) {
                        $data_arr = array(
                            'is_default' => 0
                        );

                        $data_arr = $this->security->xss_clean($data_arr);

                        $this->General_model->updateByids($data_arr, array('user_id' => $this->user_id), 'tbl_bank_details');
                    }
                } else {
                    $is_default = 1;
                }

                $data_arr = array(
                    'user_id' => $this->user_id,
                    'bank_holder_name' => $holder_name,
                    'bank_holder_phone' => $holder_mobile,
                    'bank_holder_email' => $holder_email,
                    'account_no' => $account_no,
                    'account_type' => $account_type,
                    'bank_ifsc' => $bank_ifsc,
                    'bank_name' => $bank_name,
                    'is_default' => $is_default,
                    'created_at' => strtotime(date('d-m-Y h:i:s A', now()))
                );

                $data_usr = $this->security->xss_clean($data_arr);

                $this->General_model->insert($data_usr, 'tbl_bank_details');

                $row_data = $this->General_model->selectByids($where, 'tbl_bank_details','is_default');

                $bank_list='';

                foreach ($row_data as $key => $value) {

                    $is_checked='';

                    if($value->is_default == '1'){
                        $is_checked='checked="checked"';
                    }

                	$bank_list.='<div class="address_details_item"><label class="container"><input type="radio" name="bank_acc_id" class="address_radio" value="'.$value->id.'" '.$is_checked.'><span class="checkmark"></span></label><div class="address_list"><label class="badge badge-success" style="position: absolute;right: 0;font-weight: 500;">'.ucfirst($value->account_type).'</label><span style="margin-bottom: 0px">'.$value->bank_name.'</span><p style="margin-bottom: 0px">'.$this->lang->line('bank_acc_no_lbl').': '.$value->account_no.'</p><p style="margin-bottom: 10px">'.$value->bank_holder_name.'</p></div></div>';
                }

                $response=array('status' => 1, 'msg' => $this->lang->line('add_msg'), 'bank_list' => $bank_list);
        	}
        	else{
        		$response=array('status' => 0, 'msg' => $this->lang->line('login_required_error'));
        	}

        }

        echo json_encode($response);
        exit();
    }

    public function edit_bank_account()
    {
        if ($this->user_id == 0) {
            $message = array('message' => $this->lang->line('login_required_error'), 'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            redirect('login-register', 'refresh');
        }

        extract($this->input->post());

        $bank_id = $this->input->post('bank_id');

        $this->form_validation->set_rules('bank_name', 'Enter bank name', 'trim|required');
        $this->form_validation->set_rules('account_no', 'Enter bank account number', 'trim|required');
        $this->form_validation->set_rules('account_type', 'Select account type', 'trim|required');
        $this->form_validation->set_rules('holder_name', 'Enter bank holder name', 'trim|required');
        $this->form_validation->set_rules('holder_mobile', 'Enter holder mobile number', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $message = array('message' => $this->lang->line('input_required'), 'success' => '0');
        } else {

            if ($this->input->post('is_default') != '') {
                $is_default = 1;
            } else {
                $is_default = 0;
            }

            $where = array('user_id' => $this->user_id);
            $row_data = $this->General_model->selectByids($where, 'tbl_bank_details');

            if (count($row_data) > 0) {
                if ($is_default == 1) {
                    $data_arr = array(
                        'is_default' => 0
                    );

                    $data_arr = $this->security->xss_clean($data_arr);

                    $this->General_model->updateByids($data_arr, array('user_id' => $this->user_id), 'tbl_bank_details');
                }
            } else {
                $is_default = 1;
            }

            $data_arr = array(
                'user_id' => $this->user_id,
                'bank_holder_name' => $holder_name,
                'bank_holder_phone' => $holder_mobile,
                'bank_holder_email' => $holder_email,
                'account_no' => $account_no,
                'account_type' => $account_type,
                'bank_ifsc' => $bank_ifsc,
                'bank_name' => $bank_name,
                'is_default' => $is_default
            );

            $data_usr = $this->security->xss_clean($data_arr);

            $this->General_model->update($data_usr, $bank_id, 'tbl_bank_details');

            if ($this->input->post('is_default') == '') {
                $where = array('user_id' => $this->user_id, 'id <>' => $bank_id);
                $max_id = $this->General_model->getMaxId('tbl_bank_details', $where);

                $data_arr = array(
                    'is_default' => 1
                );

                $data_arr = $this->security->xss_clean($data_arr);

                $this->General_model->update($data_arr, $max_id, 'tbl_bank_details');
            }

            $message = array('message' => $this->lang->line('update_success'),'class' => 'success');
            $this->session->set_flashdata('response_msg', $message);
        }

        redirect('saved-bank-accounts', 'refresh');
    }

    public function remove_bank_account()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{

            $id = $this->input->post("bank_id");

            $where=array('id' => $id, 'user_id' => $this->user_id, 'is_deleted' => 'N');

            $row = $this->General_model->selectByids($where, 'tbl_bank_details');

            if(!empty($row)){

                $data_arr = array('is_deleted' => 'Y');

                $data_arr = $this->security->xss_clean($data_arr);

                $this->General_model->updateByids($data_arr, array('id' => $id, 'user_id' => $this->user_id), 'tbl_bank_details');

                $message = array('message' => $this->lang->line('delete_success'),'class' => 'success');
                $this->session->set_flashdata('response_msg', $message);
                $response = array('status' => 1, 'msg' => $this->lang->line('delete_success'));
            }
            else{
                $response=array('status' => 0, 'msg' => $this->lang->line('no_data_found_msg'));
            }
        }

        echo json_encode($response);
        exit();
    }
}