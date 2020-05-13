<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class RegisterController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		// $this->load->library(['MySession','GlobalFunction']);
		$this->load->model(['AccountModel']);

		// $this->globalfunction= new GlobalFunction();		
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
		if ($dbResult->proc == 'true') {
			mkdir('./uploads/members/' . $dbResult->data['mantankampret'], 0777, TRUE);

			if ($this->BasicQuery->insert('detail_user', array(	'id' => 'detailuser_'.date('Ymdhisa'),
																'id_user' => $dbResult->data['id'],
																'student_card' => '#',
																'academic_member' => '#',
																'profile_picture' => '#'
											))) {
				echo (json_encode($dbResult));
			}else{
				$dbResult->status = 500;
    			$dbResult->proc = 'false';
				$dbResult->message = 'Gagal menyimpan data user';
    			echo (json_encode($dbResult));
			}
				
		}
		
	}

	public function login()
	{

		$jsonPOST = file_get_contents('php://input');
		$dataReceived = json_decode($jsonPOST, true);

		$selectCondition = array('username' => $dataReceived['username'], 'deleted' => ACTIVE);
		$dbResult = $this->BasicQuery->selectAll('user',$selectCondition);

		if ($dbResult != null ) {
			// echo(json_encode($dbResult));
			if ($dbResult['password'] == hash('sha3-512' , $dataReceived['password'])) {
				
				$this->mysession->setData('be_id',$dbResult['id']);
				$this->mysession->setData('be_username',$dbResult['username']);

				$dbResult['j3b5vhj23v5k2b3k52b3k5hb2hv3gh2cjgvhjvhfyuvjbvg2f3u5vjvv'] = $dbResult['role_id'];
				// unset($dbResult['id']);
				unset($dbResult['password']);
				unset($dbResult['deleted']);
				unset($dbResult['created_at']);
				unset($dbResult['updated_at']);
				unset($dbResult['role_id']);


				$this->testMail();

				$this->success('Login berhasil', $dbResult);


			}else{
				$this->failed('Username atau password salah');
			}
		}else{
			$this->failed('Username atau password salah');
		}

	}

	public function testMail(){

		$arrTo = array("aku4layy@gmail.com","falnau87@gmail.com");
		$subject = "Ini Subject";
		$body = "Jancok...";
		$tesmail = $this->globalmail->simpleMail($arrTo, $subject, $body);
	}

	public function update(){
		
		// header
		$this->globalfunction->header_CORS();

		// prepare data ======================================
		$action 		= $_POST['action'];
		$user_id		= $_POST['user_id'];

		// upload bukti transaksi
		$dir = DIR_MEMBER . $user_id . '/';
		$public_dir = DIR_MEMBER_PUBLIC . $user_id . '/';

		$upload = $this->globalfunction->resumable_upload($dir, $public_dir);

		if ($upload != 'false') {
			$update_detailuser = false;
			if ($action == 'student_card') {
				$update_detailuser = $this->BasicQuery->update(
														'detail_user',
														'id_user',
														$user_id,
														array(
															'student_card' => base_url().$upload
														)
				);
			}else if ($action == 'academic_member') {
				$update_detailuser = $this->BasicQuery->update(
														'detail_user',
														'id_user',
														$user_id,
														array(
															'academic_member' => base_url().$upload
														)
				);
			}else if ($action == 'profile_picture') {
				$update_detailuser = $this->BasicQuery->update(
														'detail_user',
														'id_user',
														$user_id,
														array(
															'profile_picture' => base_url().$upload
														)
				);
			}

			if ($update_detailuser == true) {
				$JSON_return = $this->globalfunction->return_JSON_success("Upload Success...", $user_id);
				echo $JSON_return;
			}else{
				$JSON_return = $this->globalfunction->return_JSON_failed("Failed to save data", $user_id);
				echo $JSON_return;
			}

		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed to upload file", $user_id);
			echo $JSON_return;
		}
		

	}

	public function update_text(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		$user_id = $dataReceived['user_id'];
		$action = $dataReceived['action'];
		$dataUpdate = $dataReceived['data_update'];

		$update_user = false;
		if ($action = 'biography') {
			$update_user = $this->BasicQuery->update(
														'detail_user',
														'id_user',
														$user_id,
														array(
															'biography' => $dataUpdate
														)
				); 
		}

		if ($update_user == true) {
			$JSON_return = $this->globalfunction->return_JSON_success("Update Success...", $dataReceived);
			echo $JSON_return;
		}else{
			$JSON_return = $this->globalfunction->return_JSON_failed("Failed to save data", $dataReceived);
			echo $JSON_return;
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
