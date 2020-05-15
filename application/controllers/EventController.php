<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class EventController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
	}


	public function create_event(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data ======================================
		$dataReceived['id'] = 'event_'.date('Ymdhisa');
		$dataReceived['cover_link'] = '#';
		$dataReceived['status'] = 1;

		$dbResult = $this->BasicQuery->insert('event', $dataReceived);
		if ($dbResult == true) {
			$JSON_return = $this->globalfunction->return_JSON_success("Success",$dataReceived);
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed",$dataReceived);
			echo $JSON_return;
		}

	}

	public function update(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		$id = $dataReceived['id'];

		$dbStat = $this->BasicQuery->update('event','id',$id,$dataReceived);

		if ($dbStat == true) {
			$this->success("Success",$dataReceived,'true');
		}else{
			$this->success("Failed",$dataReceived,'false');
		}
	}

	public function delete(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		$event_id = $dataReceived['event_id'];

		// Delete event
		$stat_delete = $this->BasicQuery->update(
								'event',
								'id', 
								$event_id,
								array(
										'status' => DELETED
								)
							);

		if ($stat_delete == true) {
			// Delete event pada bridge
			$stat_bridge = $this->BasicQuery->delete('bridge_event_course', array('event_id' => $event_id));

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

	public function add_course_event(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data
		$course_id = $dataReceived['course_id'];
		$event_id = $dataReceived ['event_id'];

		// create bridge
		$bridgeCond = array(
								'id' 		=> 'bridge_event_course_'.date('Ymdhisa'),
								'course_id' => $course_id,
								'event_id' 	=>$event_id
					);

		$dbResult = $this->BasicQuery->insert('bridge_event_course', $bridgeCond);
		if ($dbResult == true) {
			$JSON_return = $this->globalfunction->return_JSON_success("Success",$bridgeCond);
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed",$bridgeCond);
			echo $JSON_return;
		}

	}

	public function add_cover_event(){
		$this->globalfunction->header_CORS();

		// prepare data
		$event_id = $_POST['event_id'];

		$dir = DIR_EVENT . $event_id . '/';
		$public_dir = DIR_EVENT_PUBLIC . $event_id . '/';

		$cover_link = $this->globalfunction->resumable_upload($dir, $public_dir);

		$this->BasicQuery->update(
								'event',
								'id', 
								$event_id,
								array(
										'cover_link' => base_url().$cover_link
								)
							);
	}

	public function cover_delete(){

		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data
		$event_id = $dataReceived['event_id'];

 		if ($this->BasicQuery->update(
								'event',
								'id', 
								$event_id,
								array(
										'cover_link' => '#'
								)
							)) {
 			$JSON_return = $this->globalfunction->return_JSON_success("Deleted");
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed");
			echo $JSON_return;
		}
	}

	public function bridge_delete(){

		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data
		$event_id = $dataReceived['event_id'];
		$course_id = $dataReceived['course_id'];

 		if ($this->BasicQuery->delete('bridge_event_course', array('course_id' => $course_id, 'event_id' => $event_id))) {
 			$JSON_return = $this->globalfunction->return_JSON_success("Deleted");
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed");
			echo $JSON_return;
		}
	}


	public function registration(){ // user mendaftar event
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data ======================================
		$user_id = $dataReceived['user_id'];
		$event_id = $dataReceived['event_id'];

		// load data user
		$userCond = array('id' => $user_id,'role_id' => AS_STUDENT);
		$userCount = $this->BasicQuery->countAllResult('user',$userCond);

		if ($userCount != 0) {
			$user_data = $this->BasicQuery->selectAll('user',$userCond);

			// load data event
			$eventCond = array('id' => $event_id);
			$event_data = $this->BasicQuery->selectAll('event',$eventCond);

			// generate payment id
			$payment_id = 'pay_'.date('Ymdhisa');

			// generate event member id
			$member_id = 'eventmember_'.date('Ymdhisa');


			// process ===========================================
			// cek payment sudah ada apa belum
			$payCond = array('id_user' => $user_id, 'event_id' => $event_id);
			$payCount = $this->BasicQuery->countAllResult('payment',$payCond);
			if ($payCount == 0) {

				// create payment
				$payment_data=array(
										'id'				=> $payment_id,
										'id_user'			=> $user_id,
										'nominal'			=> $event_data['price'],
										'pay_nominal'		=> 0,
										'proof_of_payment'	=> '#',
										'pay_for'			=> 'event',
										'event_id'			=> $event_id,
										'status'			=> 0

				);

				$payment_insert = $this->BasicQuery->insert('payment', $payment_data);

				if ($payment_insert == true) {
					
					// registering event member
					$event_member_data = array(
													'id'				=> $member_id,
													'event_id'			=> $event_id,
													'user_id'			=> $user_id,
													'payment_id'		=> $payment_id
					);

					$member_insert = $this->BasicQuery->insert('event_member', $event_member_data);

					if ($member_insert == true) {
						
						// return data
						$data_return = array(
												'nominal' 		=> $payment_data['nominal'],
												'trans_code'	=> $payment_data['id'],
												'event'			=> $event_data
						);



						// Mailing
						$arrTo = array($user_data['email']);
						$subject = "Research Academy : Event";
						$body = "<p>Dear <b>".$user_data['fullname']."</b></p><br>".
								"<br><br>".
								"<p>Thank you for registering in the event ".$event_data['title']." under Research-Academy.org. </p><br><br>".
								"<p>Welcome to ".$event_data['title']." and we hope you may upgrade your research skills.</p><br><br>".
								"<br>".
								"<p>Yours Sincerely,</p><br>".
								"<p><b>Research-academy.org</b></p><br><br>"
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
		$event_member_id=$dataReceived['event_member_id'];

		// update
		$update_event_member = array(
			'confirmed'			=> 1
		);

		$update_payment = array(
			'status'			=> 2
		);

		if ($this->BasicQuery->update('event_member', 'id', $event_member_id, $update_event_member)) {

			$memberCond = array('id' => $event_member_id);
			$memberData = $this->BasicQuery->selectAll('event_member',$memberCond);

			if ($this->BasicQuery->update('payment', 'id', $memberData['payment_id'], $update_payment)) {

				// data user and course
				$user_data = $this->BasicQuery->selectAll('user',array("id" => $memberData['user_id']));
				$event_data = $this->BasicQuery->selectAll('event',array("id" => $memberData['event_id']));
				
				// Mailing
				$arrTo = array($user_data['email']);
				$subject = "Research Academy : Course";
				$body = "<p>Dear <b>".$user_data['fullname']."</b></p><br>".
						"<br><br>".
						"<p>Thank you for registering in the course ".$event_data['title']." under Research-Academy.org. </p><br><br>".
						"<p>We have verified your payment. You may now proceed to watch our tutorials and complete the assignments. A certificate of completion will be provided once our admin has verified assignments submission.
</p><br><br>".
						"<br>".
						"<p>Yours Sincerely,</p><br>".
						"<p><b>Research-academy.org</b></p><br><br>"
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
		$event_member_id=$dataReceived['event_member_id'];

		// update
		$update_event_member = array(
			'confirmed'			=> 0
		);

		$update_payment = array(
			'status'			=> 3
		);

		if ($this->BasicQuery->update('event_member', 'id', $event_member_id, $update_event_member)) {

			$memberCond = array('id' => $event_member_id);
			$memberData = $this->BasicQuery->selectAll('event_member',$memberCond);

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


	public function create_gallery_event(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data
		$event_id 	= $dataReceived['event_id'];
		$title 		= $dataReceived['title'];

		//process
		$insertgallery = array(
								'id' 			=> 'event_gallery_'.date('Ymdhisa'),
								'event_id' 		=> $event_id,
								'title' 		=> $title,
								'picture_link'	=> '#',
								'status'		=> 0

					);
		$dbResult = $this->BasicQuery->insert('event_gallery', $insertgallery);

		if ($dbResult == true) {
			$JSON_return = $this->globalfunction->return_JSON_success("Success.",$insertgallery);
				echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed to create");
			echo $JSON_return;
		}
	}

	public function upload_gallery_event(){
		$this->globalfunction->header_CORS();

		// prepare data
		$id = $_POST['gallery_id'];
		$event_id = $_POST['event_id'];

		$dir = DIR_EVENT . $event_id . '/';
		$public_dir = DIR_EVENT_PUBLIC . $event_id . '/';

		$picture_link = $this->globalfunction->resumable_upload($dir, $public_dir);


		
		$this->BasicQuery->update(
								'event_gallery',
								'id', 
								$id,
								array(
										'picture_link' => base_url().$picture_link,
										'status'		=> 1
								)
							);
	}

	public function delete_gallery_event(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data
		$gallery_id = $dataReceived['gallery_id'];

		$dbResult = $this->BasicQuery->update(
								'event_gallery',
								'id', 
								$gallery_id,
								array(
										'status'		=> 0
								)
							);

		if ($dbResult == true) {
			$JSON_return = $this->globalfunction->return_JSON_success("Success.");
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed to delete");
			echo $JSON_return;
		}
	}



}
