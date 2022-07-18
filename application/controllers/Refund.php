<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Refund extends MY_Controller 
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
    }

    public function claim_refund()
    {
        if($this->user_id==0){
            $message = array('message' => $this->lang->line('login_required_error'),'class' => 'error');
            $this->session->set_flashdata('response_msg', $message);
            $response=array('status' => 2, 'msg' => $this->lang->line('login_required_error'));
        }
        else{
            
            $order_id = $this->input->post('order_id');
            $product_id = $this->input->post('product_id');
            $bank_id = $this->input->post('bank_acc_id');

            $data_arr = array(
                'bank_id' => $bank_id,
                'last_updated' => strtotime(date('d-m-Y h:i:s A', now())),
                'request_status' => '0'
            );

            $data_arr = $this->security->xss_clean($data_arr);

            if ($product_id != 0)
            {
                $where=array('order_id' => $order_id, 'product_id' => $product_id, 'user_id' => $this->user_id);
            }
            else{
                $where=array('order_id' => $order_id, 'user_id' => $this->user_id);
            }

            $row_refund=$this->General_model->selectByids($where, 'tbl_refund');

            if(!empty($row_refund))
            {
                $this->General_model->updateByids($data_arr, $where, 'tbl_refund');
                $message = array('message' => $this->lang->line('claim_msg'),'class' => 'success');
                $this->session->set_flashdata('response_msg', $message);
                $response = array('status' => 1, 'msg' => $this->lang->line('claim_msg'));
            }
            else {
                $response = array('status' => 0, 'msg' => $this->lang->line('no_data_found_msg'));
            }

        }

        echo json_encode($response);
        exit();
    }
}