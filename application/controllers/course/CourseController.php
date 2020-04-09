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
		// $dataReceived['event'] = 'course_'.date('Ymdhisa');

		$dbResult = $this->BasicQuery->insert('course', $dataReceived);
		if ($dbResult) {
			$this->success("Success",$dataReceived);
		}else{
			$this->success("Failed",$dataReceived);
		}
		
	}

	

	public function success($message, $content = null){
		$obj=new stdClass;
		$obj->status = 200;
		$obj->proc = 'true';
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
