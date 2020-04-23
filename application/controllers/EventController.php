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


	public function confirm(){
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



}
