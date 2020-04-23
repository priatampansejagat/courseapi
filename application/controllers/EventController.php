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



}
