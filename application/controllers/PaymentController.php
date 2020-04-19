<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class PaymentController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
	}


	public function confirm()
	{
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data ======================================
		$payment_id 		= $dataReceived['payment_id'];
		$payment_nominal 	= $dataReceived['payment_nominal'];

		// data payment
		$payCond = array('id' => $payment_id);
		$payment_data = $this->BasicQuery->selectAll('payment', $payCond);

		// update payment
		$payment_data_update = array(
			'pay_nominal'		=> $payment_nominal,
			'status'			=> 0
		);

		if ($this->BasicQuery->update('payment', 'id', $payment_id, $payment_data_update)) {

			$JSON_return = $this->globalfunction->return_JSON_success("Success.", $payment_data_update);
			echo $JSON_return;
		} else {
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed to save information", $payment_data);
			echo $JSON_return;
		}
	}

	public function confirm_file()
	{
		// header
		$this->globalfunction->header_CORS();

		// prepare data ======================================
		$payment_id 		= $_POST['payment_id'];

		// data payment
		$payCond = array('id' => $payment_id);
		$payment_data = $this->BasicQuery->selectAll('payment', $payCond);

		// upload bukti transaksi
		$dir = DIR_MEMBER . $payment_data['id_user'] . '/';
		$public_dir = DIR_MEMBER_PUBLIC . $payment_data['id_user'] . '/';

		$upload = $this->globalfunction->resumable_upload($dir, $public_dir);


		// $upload = $this->globalfunction->saveImg('./uploads/members/'.$payment_data['id_user'].'/' , 'payment_proof');


		if ($upload != 'false') {

			// $public_link_img = BASE_URL_API . $upload[1];

			$updatePayment = $this->BasicQuery->update(
														'payment',
														'id',
														$payment_id,
														array(
															'status' => 1,
															'proof_of_payment' => base_url().$upload
														)
			);

			if ($updatePayment == true) {

				$payment_data = $this->BasicQuery->selectAll('payment', $payCond);

				$JSON_return = $this->globalfunction->return_JSON_success("Upload Success... Please wait for admin approval.", $payment_data);
				echo $JSON_return;
			} else {
				$JSON_return = $this->globalfunction->return_JSON_failed("Failed to save information", $payment_data);
				echo $JSON_return;
			}
		} else {
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed to upload file", $payment_data);
			echo $JSON_return;
		}
	}


	// public function confirm(){
	// 	$dataReceived = $this->globalfunction->JSON_POST_asArr();

	// 	// prepare data ======================================
	// 	$payment_id 		= $dataReceived['payment_id'];
	// 	$payment_nominal 	= $dataReceived['payment_nominal'];

	// 	// data payment
	// 	$payCond = array('id' => $payment_id);
	// 	$payment_data = $this->BasicQuery->selectAll('payment',$payCond);



	// 	// upload bukti transaksi
	// 	$upload = $this->globalfunction->saveImg('./uploads/members/'.$payment_data['id_user'].'/' , 'payment_proof');
	// 	if ($upload[0]==true) {

	// 		$public_link_img = BASE_URL_API . $upload[1];

	// 		// update payment
	// 		$payment_data_update = array(
	// 								'pay_nominal'		=> $pay_nominal,
	// 								'proof_of_payment'	=> $public_link_img,
	// 								'status'			=> 0

	// 		);

	// 		if ($this->BasicQuery->update('payment', 'id', $payment_id, $payment_data_update)) {

	// 			$JSON_return = $this->globalfunction->return_JSON_success("Upload Success... Please wait for admin approval.",$payment_data_update);
	// 			echo $JSON_return;

	// 		}else{
	// 			$JSON_return = $this->globalfunction->return_JSON_failed("Failed to save information", $payment_data);
	// 			echo $JSON_return;
	// 		}


	// 	}else{
	// 		$JSON_return = $this->globalfunction->return_JSON_failed("Failed to upload file",$payment_data);
	// 		echo $JSON_return;
	// 	}

	// }





}
