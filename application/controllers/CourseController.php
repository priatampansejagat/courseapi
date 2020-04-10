<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class CourseController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		// $this->load->library(['MySession','GlobalFunction']);
		// $this->load->model(['BasicQuery']);

		// $this->globalfunction= new GlobalFunction();		
	}

	public function create()
	{

		$jsonPOST = file_get_contents('php://input');
		$dataReceived = json_decode($jsonPOST, true);

		// echo(json_encode($dataReceived));
		$dataReceived['id'] = 'course_'.date('Ymdhisa');
		$dataReceived['status'] = 1;
		$dataReceived['event_id'] = 'NULL';
		$dataReceived['price_promo'] = 'NULL';
		$dataReceived['redeem_code_required'] = 0;
		$dataReceived['redeem_code_for'] = 'NULL';
		$dataReceived['redeem_code'] = 'NULL';

		$dbResult = $this->BasicQuery->insert('course', $dataReceived);
		if ($dbResult == true) {
			$this->success("Success",$dataReceived,'true');
		}else{
			$this->success("Failed",$dataReceived,'false');
		}
		
	}

	public function registration(){
		$dataReceived = $this->GlobalFunction->JSON_POST_asArr();

		// prepare data ======================================
		$user_id = $dataReceived['user_id'];
		$course_id = $dataReceived['course_id'];

		// load data user
		$userCond = array('id' => $user_id,'role_id' => AS_STUDENT);
		$user_data = $this->BasicQuery->selectAllResult('user',$userCond);

		// load data course
		$courseCond = array('id' => $course_id);
		$course_data = $this->BasicQuery->selectAllResult('course',$courseCond);

		// generate payment id
		$payment_id = 'pay_'.date('Ymdhisa');

		// generate course member id
		$member_id = 'coursemember_'.date('Ymdhisa');


		// process ===========================================
		// cek payment sudah ada apa belum
		$payCond = array('id_user' => $user_id, 'course_id' => $course_id);
		$payCount = $this->BasicQuery->countAllResult('payment',$payCond);
		if ($payCount == 0) {

			// create payment
			$payment_data=array(
									'id'				=> $payment_id,
									'id_user'			=> $user_id,
									'nominal'			=> $course_data['price'],
									'pay_nominal'		=> 0,
									'proof_of_payment'	=> '#',
									'pay_for'			=> 'course',
									'course_id'			=> $course_id,
									'status'			=> 0

			);

			$payment_insert = $this->BasicQuery->insert('payment', $payment_data);

			if ($payment_insert == true) {
				
				// registering course member
				$course_member_data = array(
												'id'				=> $member_id,
												'course_id'			=> $course_id,
												'user_id'			=> $user_id,
												'finished_chapter'	=> '',
												'payment_id'		=> $payment_id
				);

				$member_insert = $this->BasicQuery->insert('course_member', $course_member_data);

				if ($member_insert == true) {
					
					// return data
					$data_return = array(
											'nominal' 		=> $payment_data['nominal'],
											'trans_code'	=> $payment_data['id'],
											'course'		=> $course_data
					);
					$JSON_return = $this->GlobalFunction->return_JSON_success("Success to register",$data_return);
					echo $JSON_return;

				}else{
					$JSON_return = $this->GlobalFunction->return_JSON_failed("Failed to register",$payment_data);
					echo $JSON_return;
				}

			}else{
				$JSON_return = $this->GlobalFunction->return_JSON_failed("Failed to register",$payment_data);
				echo $JSON_return;
			}

		}else{
			$payment_data = $this->BasicQuery->selectAll('payment',$payCond);
			$JSON_return = $this->GlobalFunction->return_JSON_failed("Menunggu pembayaran",$payment_data);
			echo $JSON_return;
		}


	}

	

	

	public function success($message, $content = null, $proc){
		$obj=new stdClass;
		$obj->status = 200;
		$obj->proc = $proc;
		$obj->message = $message;
		$obj->data = $content;

		echo (json_encode($obj));
	}

	public function failed($message, $content = null){
		$obj=new stdClass;
		$obj->status = 500;
		$obj->proc = 'false';
		$obj->message = $message;
		$obj->data = $content;
		
		echo (json_encode($obj));
	}

	
}
