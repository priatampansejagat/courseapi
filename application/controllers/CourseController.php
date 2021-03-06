<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class CourseController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		// $this->load->library(['MySession','globalfunction']);
		// $this->load->model(['BasicQuery']);

		// $this->globalfunction= new globalfunction();		
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

	public function update(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		$id = $dataReceived['id'];

		$dbStat = $this->BasicQuery->update('course','id',$id,$dataReceived);

		if ($dbStat == true) {
			$this->success("Success",$dataReceived,'true');
		}else{
			$this->success("Failed",$dataReceived,'false');
		}
	}

	public function delete(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		$course_id = $dataReceived['course_id'];

		// Delete course
		$stat_delete = $this->BasicQuery->update(
								'course',
								'id', 
								$course_id,
								array(
										'status' => DELETED
								)
							);

		if ($stat_delete == true) {
			// Delete course pada event
			$stat_bridge = $this->BasicQuery->delete('bridge_event_course', array('course_id' => $course_id));

			if ($stat_bridge == true) {
				$JSON_return = $this->globalfunction->return_JSON_success("Success.",$dataReceived);
				echo $JSON_return;
			}else{
				$JSON_return = $this->globalfunction->return_JSON_failed("Failed", $dataReceived);
				echo $JSON_return;
			}
			
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed", $dataReceived);
			echo $JSON_return;
		}
	}

	public function create_chapter(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();
		// $JSON_return = $this->globalfunction->return_JSON_success("Success.",$dataReceived);
		// echo $JSON_return;

		// cek sequence
		$cekCond = array(
							'course_id' => $dataReceived['course_id'], 
							'sequence' => $dataReceived['sequence']
						);
		$count_seq = $this->BasicQuery->countAllResult('course_chapter',$cekCond);
		if ($count_seq == 0) {

			$data_insert = array(
									'id' 			=> 'chapter_'.date('Ymdhisa'),
									'course_id'		=> $dataReceived['course_id'],
									'sequence'		=> $dataReceived['sequence'],
									'tittle'		=> $dataReceived['title'],
									'description'	=> $dataReceived['description'],
									'video_link'	=> '#'
				);

			$dbResult = $this->BasicQuery->insert('course_chapter', $data_insert);
			if ($dbResult == true) {
				$JSON_return = $this->globalfunction->return_JSON_success("Success.",$data_insert);
				echo $JSON_return;
			}else{
				$JSON_return = $this->globalfunction->return_JSON_failed("Failed to save data", $dataReceived);
				echo $JSON_return;
			}

		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Duplicate Sequence", $dataReceived);
			echo $JSON_return;
		}

	} 

	public function deletechapter(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// Prepare data
		$chapter_id = $dataReceived['chapter_id'];

		$dbResult = $this->BasicQuery->delete('course_chapter', array('id' => $chapter_id));

		if ($dbResult == true) {
			$JSON_return = $this->globalfunction->return_JSON_success("Success.",$dataReceived);
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("failed", $dataReceived);
			echo $JSON_return;
		}
	}

	public function video_chapter(){
		
		$this->globalfunction->header_CORS();

		$dir = DIR_COURSE . $_POST['course_id'] . '/';
		$public_dir = DIR_COURSE_PUBLIC . $_POST['course_id'] . '/';

		$vid_link = $this->globalfunction->resumable_upload($dir, $public_dir);

		$this->BasicQuery->update(
									'course_chapter',
									'id', 
									$_POST['chapter_id'],
									array(
											'video_link' => base_url().$vid_link
									)
								);
	}

	public function registration(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data ======================================
		$user_id = $dataReceived['user_id'];
		$course_id = $dataReceived['course_id'];

		// load data user
		$userCond = array('id' => $user_id,'role_id' => AS_STUDENT);
		$userCount = $this->BasicQuery->countAllResult('user',$userCond);
		if ($userCount != 0) {
			$user_data = $this->BasicQuery->selectAll('user',$userCond);

			// load data course
			$courseCond = array('id' => $course_id);
			$course_data = $this->BasicQuery->selectAll('course',$courseCond);

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



						// Mailing
						$arrTo = array($user_data['email']);
						$subject = "Research Academy : Course";
						$body = "<p>Dear <b>".$user_data['fullname']."</b></p><br>".
								"<br><br>".
								"<p>Thank you for registering in the course: ".$course_data['title']." under <b>Research Academy.</b>  </p><br><br>".
								"<p>Please follow the instructions given in the course including the assignments so that you may receive a certificate of completion at the end of the course.</p><br><br>".
								"<p>We will notify you once your payment has been verified in order to start the course.</p><br><br>".
								"<br><br>".
								"<p>Yours Sincerely,</p><br>".
								"<p><b>Research Academy</b></p>"
						;

						$mailing = $this->globalmail->simpleMail($arrTo, $subject, $body);



						$JSON_return = $this->globalfunction->return_JSON_success("Success to register",$data_return);
						echo $JSON_return;

					}else{
						$JSON_return = $this->globalfunction->return_JSON_failed("Failed to register",$payment_data);
						echo $JSON_return;
					}

				}else{
					$JSON_return = $this->globalfunction->return_JSON_failed("Failed to register",$payment_data);
					echo $JSON_return;
				}

			}else{
				$payment_data = $this->BasicQuery->selectAll('payment',$payCond);
				$JSON_return = $this->globalfunction->return_JSON_failed("Menunggu pembayaran",$payment_data);
				echo $JSON_return;
			}
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("User tidak terdaftar");
			echo $JSON_return;
		}
		
	}

	public function registration_confirm(){ // by admin
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data
		$course_member_id=$dataReceived['course_member_id'];

		// update
		$update_course_member = array(
			'confirmed'			=> ACTIVE
		);

		$update_payment = array(
			'status'			=> CONFIRMED
		);

		if ($this->BasicQuery->update('course_member', 'id', $course_member_id, $update_course_member)) {

			$memberCond = array('id' => $course_member_id);
			$memberData = $this->BasicQuery->selectAll('course_member',$memberCond);

			if ($this->BasicQuery->update('payment', 'id', $memberData['payment_id'], $update_payment)) {


				// data user and course
				$user_data = $this->BasicQuery->selectAll('user',array("id" => $memberData['user_id']));
				$course_data = $this->BasicQuery->selectAll('course',array("id" => $memberData['course_id']));
				
				// Mailing
				$arrTo = array($user_data['email']);
				$subject = "Research Academy : Course";
				$body = "<p>Dear <b>".$user_data['fullname']."</b></p><br>".
						"<br><br>".
						"<p>Thank you for registering in the course ".$course_data['title']." under <b>Research Academy.</b> </p><br><br>".
						"<p>We have verified your payment. You may now proceed to watch our tutorials and complete the assignments. A certificate of completion will be provided once our admin has verified assignments submission.
</p><br><br>".
						"<br><br>".
						"<p>Yours Sincerely,</p><br>".
						"<p><b>Research Academy</b></p>"
				;

				$mailing = $this->globalmail->simpleMail($arrTo, $subject, $body);



				$JSON_return = $this->globalfunction->return_JSON_success("Success.");
				echo $JSON_return;
			}else{
				$JSON_return = $this->globalfunction->return_JSON_failed("Failed to confirm");
				echo $JSON_return;
			}
		} else {
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed to confirm");
			echo $JSON_return;
		}

	}

	public function registration_decline(){ // by admin
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data
		$course_member_id=$dataReceived['course_member_id'];

		// update
		$update_course_member = array(
			'confirmed'			=> INACTIVE
		);

		$update_payment = array(
			'status'			=> DELETED
		);

		if ($this->BasicQuery->update('course_member', 'id', $course_member_id, $update_course_member)) {

			$memberCond = array('id' => $course_member_id);
			$memberData = $this->BasicQuery->selectAll('course_member',$memberCond);

			if ($this->BasicQuery->update('payment', 'id', $memberData['payment_id'], $update_payment)) {
				$JSON_return = $this->globalfunction->return_JSON_success("Success.");
				echo $JSON_return;
			}else{
				$JSON_return = $this->globalfunction->return_JSON_failed("Failed to decline");
				echo $JSON_return;
			}
		} else {
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed to decline");
			echo $JSON_return;
		}

	}

	public function cert_upload(){
		$this->globalfunction->header_CORS();

		$dir = DIR_COURSE . $_POST['course_id'] . '/';
		$public_dir = DIR_COURSE_PUBLIC . $_POST['course_id'] . '/';

		$cert_link = $this->globalfunction->resumable_upload($dir, $public_dir);

		$this->BasicQuery->update(
								'course',
								'id', 
								$_POST['course_id'],
								array(
										'certificate' => base_url().$cert_link
								)
							);

	}

	public function cert_delete(){

		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data
		$course_id = $dataReceived['course_id'];

 		if ($this->BasicQuery->update(
								'course',
								'id', 
								$course_id,
								array(
										'certificate' => '#'
								)
							)) {
 			$JSON_return = $this->globalfunction->return_JSON_success("Deleted");
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed");
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
