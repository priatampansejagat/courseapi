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

		// make directory
		if ($dbResult->data['proc'] == 'true') {
			if (mkdir('./uploads/' . $dbResult->dataInput['mantankampret'], 0777, TRUE)) {
				echo (json_encode($dbResult));
			}else{
				$dbResult->status = 500;
    			$dbResult->data = array(
    					'proc'		=> 'false',
    					'message'	=> 'Gagal menyimpan'
    			);
    			echo (json_encode($dbResult));
			}
		}
		
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
				
				// menentukan role
				if ($dbResult['role_id'] == '83bbe0cd25d8cc4b8c076497a57d4b6452e84946b9042dc7983a7806a1f636cf') {
					
					$dbResult['role_id'] = 'murid';

				}else if ($dbResult['role_id'] == 'fdd38312da2d5ddc4b90a49aaa2bcf52d586572db5ce37cb2630799476aa13e4') {
				
					$dbResult['role_id'] = 'admin';
				
				}else if ($dbResult['role_id'] == 'd730bb9677663feb30d4c4e9d273c7c9c713e4d5b8eebf9218a2f587dd7c5d9b') {
				
					$dbResult['role_id'] = 'guru';
				
				}
				 unset($dbResult['id']);
				 unset($dbResult['password']);
				 unset($dbResult['deleted']);
				 unset($dbResult['created_at']);
				 unset($dbResult['updated_at']);

				$this->success('Login berhasil', $dbResult);

			}else{
				$this->failed('Username atau password salah');
			}
		}else{
			$this->failed('Username atau password salah');
		}

	}

	public function success($message, $content = null){
		$obj=new stdClass;
		$obj->status = 200;
		$obj->data = array(
				'proc'		=> 'true',
				'message'	=> $message,
				'content'	=> $content
		);
		
		echo (json_encode($obj));
	}

	public function failed($message, $content = null){
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
