<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class UserController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function delete(){

		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// Prepare data
		$user_id = $dataReceived['user_id'];

		$dbResult = $this->BasicQuery->update('user', 'id', $user_id, array('deleted' => DELETED));

		if ($dbResult == true) {
			$JSON_return = $this->globalfunction->return_JSON_success("Success.",$dataReceived);
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("failed", $dataReceived);
			echo $JSON_return;
		}


	}

	public function delete_mentor(){

		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// Prepare data
		$user_id = $dataReceived['user_id'];
		
		$dbResult = $this->BasicQuery->update('user', 'id', $user_id, array('deleted' => DELETED));

		if ($dbResult == true) {

			// delete mentor on course
			$dbResult = $this->BasicQuery->update('course', 'mentor_id', $user_id, array('mentor_id' => ""));

			if ($dbResult == true) {
				$JSON_return = $this->globalfunction->return_JSON_success("Success.",$dataReceived);
				echo $JSON_return;
			}else{
				$JSON_return = $this->globalfunction->return_JSON_failed("failed", $dataReceived);
				echo $JSON_return;
			}
			
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("failed", $dataReceived);
			echo $JSON_return;
		}


	}

	

	
}
