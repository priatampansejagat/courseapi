<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class RegisterController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		// $this->load->library(['MySession','MyFlash']);

		$this->load->model(['AccountModel']);
	}

	public function register()
	{

		$obj->status = 200;
		$obj->data = $_POST;
		echo (json_encode($obj));

		// $additional['PoS'] = '';
		// $additional['PoA'] = '';

		// $dbResult = $this->AccountModel->registrasi($_POST,$additional);

		// echo(json_encode($dbResult));


	}

	public function login()
	{

		$dbResult = $this->AccountModel->login($_POST);

		echo (json_encode($dbResult));
	}
}
