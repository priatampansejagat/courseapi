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

		$this->load->model(['AccountModel', 'BasicQuery']);
	}

	public function register()
	{

		$jsonPOST = file_get_contents('php://input');
		$dataReceived = json_decode($jsonPOST, true);

		// echo(json_encode($dataReceived));

		$additional['PoS'] = '';
		$additional['PoA'] = '';
		$dbResult = $this->AccountModel->registrasi($dataReceived, $additional);

		echo (json_encode($dbResult));
	}

	public function login()
	{

		$jsonPOST = file_get_contents('php://input');
		$dataReceived = json_decode($jsonPOST, true);

		$selectCondition = array('username' => $dataReceived['username']);
		$dbResult = $this->BasicQuery->selectAll('user',$selectCondition);

		if (count($dbResult) != 0 || $dbResult != null ) {
			// echo(json_encode($dbResult));
			if ($dbResult['password'] == hash('sha3-512' , $dataReceived['password'])) {
				$this->unameTrue('Login berhasil', $dbResult);
			}else{
				$this->unameFalse('Username atau password salah');
			}
		}else{
			$this->unameFalse('Username atau password salah');
		}

	}

	public function unameTrue($message, $content = null){
		$obj=new stdClass;
		$obj->status = 200;
		$obj->data = array(
				'proc'		=> 'true',
				'message'	=> $message,
				'content'	=> $content
		);
		
		echo (json_encode($obj));
	}

	public function unameFalse($message, $content = null){
		$obj=new stdClass;
		$obj->status = 500;
		$obj->data = array(
				'proc'		=> 'false',
				'message'	=> $message,
				'content'	=> $content
		);
		
		echo (json_encode($obj));
	}

	
}
