<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class AssignmentController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function course_assignment(){
		// header
		$this->globalfunction->header_CORS();

		// prepare data ======================================
		$user_id		= $_POST['user_id'];
		$corse_id		= $_POST['corse_id'];
		$event_id		= NULL;


		if (isset($_POST['event_id'])) {
			$event_id = $_POST['event_id'];
		}

		// upload bukti transaksi
		$dir = DIR_MEMBER . $user_id . '/';
		$public_dir = DIR_MEMBER_PUBLIC . $user_id . '/';

		$upload = $this->globalfunction->resumable_upload($dir, $public_dir);

		if ($upload != 'false') {
			
			// cek apakah sudah pernah upload
			$count_assignment = $this->BasicQuery->countAllResult('user_assignment', array('user_id'));

			$dbstat = false; // cek apakah berhasil updat/insert atau gagal
			if ($count_assignment == 0) {
			 	$dbstat = $this->BasicQuery->insert( 'user_assignment',array(
		 																	'id' => 'assignment_'.date('Ymdhisa'),
								 											'user_id' => $user_id,
								 											'course_id' = $course_id,
								 											'event_id' => $event_id,
								 											'assignment' => base_url().$upload,
								 											'status' => 1
		 																));

			}else{
				// select 
				$assignmentData = $this->BasicQuery->selectAll('user_assignment', 
																				array(	'user_id' => $user_id,
																						'course_id' => $course_id
																			));
				$dbstat = $this->BasicQuery->update(	'user_assignment',
														'id', 
														$assignmentData['id'],
														array(
																'assignment' => base_url().$upload
														)
													);
			}

			if ($dbstat == true) {
				$JSON_return = $this->globalfunction->return_JSON_success("Success",$_POST);
				echo $JSON_return;
			}else{
				$JSON_return = $this->globalfunction->return_JSON_failed("Failed to upload file", $_POST);
				echo $JSON_return;
			}

		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed to upload file", $_POST);
			echo $JSON_return;
		}

	}

	public function assignment_download(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// // prepare data ======================================
		// $user_id = $dataReceived['user_id'];
		// $course_id = $dataReceived['course_id'];

		// // select link assignment
		// $assignmentCond = array('user_id' => $user_id, 'course_id' => $course_id);
		// $dbResult = $this->BasicQuery->selectAll('user_assignment', $assignmentCond);

		// if ($dbResult == null) {
		// 	$JSON_return = $this->globalfunction->return_JSON_success("Success",'tes');
		// 	echo $JSON_return;
		// }else{
		// 	$JSON_return = $this->globalfunction->return_JSON_success("Success",$dbResult);
		// 	echo $JSON_return;
		// }
		
		$JSON_return = $this->globalfunction->return_JSON_success("Success",$dataReceived);
		echo $JSON_return;

	}

	
}
