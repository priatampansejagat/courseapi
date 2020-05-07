<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class CertificateController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function cert_enable(){

		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data
		$user_id = $dataReceived['user_id'];
		$course_id = $dataReceived['course_id'];
		$event_id = $dataReceived['event_id'];

		// cek apakah data ada atau nggak
		$count_cert = $this->BasicQuery->countAllResult('user_certificate',array(	'user_id' => $user_id,
																					'course_id'=> $course_id,
																					'event_id' => $event_id
																				));

		$dbstat = false; // cek apakah berhasil updat/insert atau gagal
		if ($count_cert == 0) {
			$dbstat = $this->BasicQuery->insert( 'user_certificate',array(
		 																	'id' => 'cert_'.date('Ymdhisa'),
								 											'user_id' => $user_id,
								 											'course_id' => $course_id,
								 											'event_id' => $event_id,
								 											'status' => 1
		 																));
		}else{
			// select 
			$certData = $this->BasicQuery->selectAll('user_certificate', 
																			array(	'user_id' => $user_id,
																					'course_id' => $course_id,
																					'event_id' => $event_id
																		));
			$dbstat = $this->BasicQuery->update(	'user_certificate',
													'id', 
													$certData['id'],
													array(
															'status' => 1
													)
												);
		}

		if ($dbstat == true) {
			$JSON_return = $this->globalfunction->return_JSON_success("Success",$dataReceived);
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed", $dataReceived);
			echo $JSON_return;
		}

	}


	public function cert_disable(){

		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare data
		$user_id = $dataReceived['user_id'];
		$course_id = $dataReceived['course_id'];
		$event_id = $dataReceived['event_id'];

		// cek apakah data ada atau nggak
		$count_cert = $this->BasicQuery->countAllResult('user_certificate',array(	'user_id' => $user_id,
																					'course_id'=> $course_id,
																					'event_id' => $event_id
																				));

		$dbstat = false; // cek apakah berhasil updat/insert atau gagal
		if ($count_cert == 0) {
			$dbstat = $this->BasicQuery->insert( 'user_certificate',array(
		 																	'id' => 'cert_'.date('Ymdhisa'),
								 											'user_id' => $user_id,
								 											'course_id' => $course_id,
								 											'event_id' => $event_id,
								 											'status' => 0
		 																));
		}else{
			// select 
			$certData = $this->BasicQuery->selectAll('user_certificate', 
																			array(	'user_id' => $user_id,
																					'course_id' => $course_id,
																					'event_id' => $event_id
																		));
			$dbstat = $this->BasicQuery->update(	'user_certificate',
													'id', 
													$certData['id'],
													array(
															'status' => 0
													)
												);
		}

		if ($dbstat == true) {
			$JSON_return = $this->globalfunction->return_JSON_success("Success",$dataReceived);
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed", $dataReceived);
			echo $JSON_return;
		}

	}

	

	
}
