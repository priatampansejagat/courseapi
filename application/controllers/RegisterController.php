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

				// Mailing
				$arrTo = array($dbResult->data['email']);
				// $arrTo = array('aku4layy@gmail.com');
				$subject = "Research Academy : Registration";
				$body = "<br>Dear ".$dbResult->data['fullname']."<br><br><br>Thank you for registering at Research Academy. We are very pleased to welcome you to our learning platform.<br><br>Please note your<br>Username: ".$dbResult->data["username"]."<br>Password: ".$dataReceived["password"]."<br><br>Which you can use as a one key to all events and courses this platform offers.<br><br>Feel free to explore our events, courses and publications.  <br><br>If you wish to be one of our Research Academy instructor, please contact us at info@research-academy.org<br><br><br>Yours Sincerely,<br><b>Research Academy</b>";
				// $body = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";

				$mailing = $this->globalmail->simpleMail($arrTo, $subject, $body);

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


				$this->success('Login berhasil', $dbResult);


			}else{
				$this->failed('Username atau password salah');
			}
		}else{
			$this->failed('Username atau password salah');
		}

	}

	public function testMail(){

		$arrTo = array("aku4layy@gmail.com");
		$subject = "Ini Subject";
		$body = "Message";
		$mailing = $this->globalmail->simpleMail($arrTo, $subject, $body);
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
		if ($action == 'biography') {
			$update_user = $this->BasicQuery->update(
														'detail_user',
														'id_user',
														$user_id,
														array(
															'biography' => $dataUpdate
														)
				); 
		}else if ($action == 'update_password') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'password' => hash('sha3-512' , $dataUpdate)
														)
				); 
		}else if ($action == 'fullname') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'fullname' => $dataUpdate
														)
				); 
		}else if ($action == 'place_of_birth') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'place_of_birth' => $dataUpdate
														)
				); 
		}else if ($action == 'date_of_birth') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'date_of_birth' => $dataUpdate
														)
				); 
		}else if ($action == 'email') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'email' => $dataUpdate
														)
				); 
		}else if ($action == 'institution') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'institution' => $dataUpdate
														)
				); 
		}else if ($action == 'country') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'country' => $dataUpdate
														)
				); 
		}else if ($action == 'phone_number') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'phone_number' => $dataUpdate
														)
				); 
		}else if ($action == 'profesion') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'profesion' => $dataUpdate
														)
				); 
		}else if ($action == 'major_of_study') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'major_of_study' => $dataUpdate
														)
				); 
		}else if ($action == 'password') {
			$update_user = $this->BasicQuery->update(
														'user',
														'id',
														$user_id,
														array(
															'password' => hash('sha3-512' , $dataUpdate)
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

	public function forgot_password(){
		$dataReceived = $this->globalfunction->JSON_POST_asArr();

		// prepare_data
		$username = $dataReceived['username'];

		// generate new password
		$password = $this->generateRandomString(5);
		$hashed_password = hash('sha3-512' , $password);

		// user condition
		$userCond = array('username' => $username);

		// select user information
		$userInfo =  $this->BasicQuery->selectAll('user',$userCond);

		if ($userInfo == null) {
			$JSON_return = $this->globalfunction->return_JSON_failed("Username not found", $dataReceived);
			echo $JSON_return;
		}else{
			// update user password
			$update_user = $this->BasicQuery->update(
															'user',
															'username',
															$username,
															array(
																'password' =>$hashed_password
															)
					); 

			// send email
			// Mailing
			$arrTo = array($userInfo['email']);
			$subject = "Research Academy : Forgot Password";
			$body = "<br><b>Dear ".$userInfo['fullname'].",</b><br><br>You recently requested to reset your password for your <b>Research Academy</b> account. Please, use this new password to access your account and we recommend to change your password immediately after accessing the system.<br><br><br><b>".$password."</b>";
			

			$mailing = $this->globalmail->simpleMail($arrTo, $subject, $body);

			if ($update_user == true) {
				$JSON_return = $this->globalfunction->return_JSON_success("Update Success...", $dataReceived);
				echo $JSON_return;
			}else{
				$JSON_return = $this->globalfunction->return_JSON_failed("Failed to save data", $dataReceived);
				echo $JSON_return;
			}
		}
		

	}

	function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
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
