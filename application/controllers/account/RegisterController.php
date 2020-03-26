<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// include('fungsi.php');

class RegisterController extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->helper('url');
		// $this->load->library(['MySession','MyFlash']);

		$this->load->model(['AccountModel']);
		
	}

	public function register() {

		$jsonPOST = file_get_contents('php://input');
		$dataReceived = json_decode($jsonPOST,true);

		// echo(json_encode($dataReceived));

		$additional['PoS'] = '';
		$additional['PoA'] = '';
		$dbResult = $this->AccountModel->registrasi($dataReceived,$additional);

		echo(json_encode($dbResult));

	}

	public function login() {

		$dbResult = $this->AccountModel->login($_POST);

		echo(json_encode($dbResult));
		

	}

}
