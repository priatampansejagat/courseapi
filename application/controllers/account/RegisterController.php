<?php
defined('BASEPATH') or exit('No direct script access allowed');

// include('fungsi.php');

class RegisterController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->library(['MySession','GlobalFunction']);
		$this->load->model(['AccountModel', 'BasicQuery']);

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
		if ($dbResult->data['proc'] == 'true') {
			if (mkdir('./uploads/members/' . $dbResult->dataInput['mantankampret'], 0777, TRUE)) {
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

		if (count($dbResult) != 0 && $dbResult != null ) {
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

				$this->mysession->setData('be_id',$dbResult['id']);
				$this->mysession->setData('be_username',$dbResult['username']);

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

	public function update(){
		$input = $_POST;

		// update per data
		if ($this->mysession->checkData('be_id')) {
			$id = $this->mysession->getData('be_id');

			// update tabel user -------------------------------------------------------------------------
			// update place_of_birth
			if ($input['place_of_birth'] != '' && $input['place_of_birth'] != null) {
				$dataUpdate = array(
					'place_of_birth' => $input['place_of_birth']
				);	
				$this->BasicQuery->update('user', 'id', $id, $dataUpdate);
				
				if(!$queryreturn){


				}

			}

			// update date_of_birth
			if ($input['date_of_birth'] != '' && $input['date_of_birth'] != null) {
				$dataUpdate = array(
					'date_of_birth' => $input['date_of_birth']
				);	
				$this->BasicQuery->update('user', 'id', $id, $dataUpdate);
				
				if(!$queryreturn){


				}

			}

			// update email
			if ($input['email'] != '' && $input['email'] != null) {
				$dataUpdate = array(
					'email' => $input['email']
				);	
				$this->BasicQuery->update('user', 'id', $id, $dataUpdate);
				
				if(!$queryreturn){


				}

			}

			// update country
			if ($input['country'] != '' && $input['country'] != null) {
				$dataUpdate = array(
					'country' => $input['country']
				);	
				$this->BasicQuery->update('user', 'id', $id, $dataUpdate);
				
				if(!$queryreturn){


				}

			}

			// update phone_number
			if ($input['phone_number'] != '' && $input['phone_number'] != null) {
				$dataUpdate = array(
					'phone_number' => $input['phone_number']
				);	
				$this->BasicQuery->update('user', 'id', $id, $dataUpdate);
				
				if(!$queryreturn){


				}

			}

			// update profesion
			if ($input['profesion'] != '' && $input['profesion'] != null) {
				$dataUpdate = array(
					'profesion' => $input['profesion']
				);	
				$this->BasicQuery->update('user', 'id', $id, $dataUpdate);
				
				if(!$queryreturn){


				}

			}

			// update institution
			if ($input['institution'] != '' && $input['institution'] != null) {
				$dataUpdate = array(
					'institution' => $input['institution']
				);	
				$this->BasicQuery->update('user', 'id', $id, $dataUpdate);
				
				if(!$queryreturn){


				}

			}

			// update major_of_study
			if ($input['major_of_study'] != '' && $input['major_of_study'] != null) {
				$dataUpdate = array(
					'major_of_study' => $input['major_of_study']
				);	
				$this->BasicQuery->update('user', 'id', $id, $dataUpdate);
				
				if(!$queryreturn){


				}

			}

			// update status
			if ($input['status'] != '' && $input['status'] != null) {
				$dataUpdate = array(
					'status' => $input['status']
				);	
				$this->BasicQuery->update('user', 'id', $id, $dataUpdate);
				
				if(!$queryreturn){


				}

			}


			// update tabel detail_user ----------------------------------------------------------
			// cek kosong atau nggak
			$selectCondition = array('id_user' => $id);
			$dbResult = $this->BasicQuery->selectAll('detail_user', $selectCondition);
			if (count($dbResult) != 0 && $dbResult != null) {
				
				// update student_card
				if ($input['student_card'] != '' && $input['student_card'] != null) {

					$link_img = $this->globalfunction->saveImg('./uploads/members/'.$id.'/' , 'student_card');
					$public_link_img = $this->globalfunction->api_url() . $link_img;
					$dataUpdate = array(
						'student_card' => $public_link_img
					);	
					$this->BasicQuery->update('detail_user', 'id_user', $id, $dataUpdate);
					
					if(!$queryreturn){

					}

				}

				// update academic_member
				if ($input['academic_member'] != '' && $input['academic_member'] != null) {

					$link_img = $this->globalfunction->saveImg('./uploads/members/'.$id.'/' , 'academic_member');
					$public_link_img = $this->globalfunction->api_url() . $link_img;
					$dataUpdate = array(
						'academic_member' => $public_link_img
					);	
					$this->BasicQuery->update('detail_user', 'id_user', $id, $dataUpdate);
					
					if(!$queryreturn){

					}

				}

				// update proof_of_payment
				if ($input['proof_of_payment'] != '' && $input['proof_of_payment'] != null) {

					$link_img = $this->globalfunction->saveImg('./uploads/members/'.$id.'/' , 'proof_of_payment');
					$public_link_img = $this->globalfunction->api_url() . $link_img;
					$dataUpdate = array(
						'proof_of_payment' => $public_link_img
					);	
					$this->BasicQuery->update('detail_user', 'id_user', $id, $dataUpdate);
					
					if(!$queryreturn){

					}

				}

				// update profile_picture
				if ($input['profile_picture'] != '' && $input['profile_picture'] != null) {

					$link_img = $this->globalfunction->saveImg('./uploads/members/'.$id.'/' , 'profile_picture');
					$public_link_img = $this->globalfunction->api_url() . $link_img;
					$dataUpdate = array(
						'profile_picture' => $public_link_img
					);	
					$this->BasicQuery->update('detail_user', 'id_user', $id, $dataUpdate);
					
					if(!$queryreturn){

					}

				}

			}else{//jika belum ada data, maka harus buat baru alias insert

				// update student_card
				if ($input['student_card'] != '' && $input['student_card'] != null) {

					$link_img = $this->globalfunction->saveImg('./uploads/members/'.$id.'/' , 'student_card');
					$public_link_img = $this->globalfunction->api_url() . $link_img;
					$dataUpdate = array(
						'id'			=> date('Ymdhisa'),
						'id_user'		=> $id,
						'student_card' => $public_link_img

					);	
					$queryreturn = $this->BasicQuery->insert('detail_user', $dataUpdate);
					
					if(!$queryreturn){

					}

				}

				// update academic_member
				if ($input['academic_member'] != '' && $input['academic_member'] != null) {

					$link_img = $this->globalfunction->saveImg('./uploads/members/'.$id.'/' , 'academic_member');
					$public_link_img = $this->globalfunction->api_url() . $link_img;
					$dataUpdate = array(
						'id'			=> date('Ymdhisa'),
						'id_user'		=> $id,
						'academic_member' => $public_link_img

					);	
					$queryreturn = $this->BasicQuery->insert('detail_user', $dataUpdate);
					
					if(!$queryreturn){

					}

				}

				// update proof_of_payment
				if ($input['proof_of_payment'] != '' && $input['proof_of_payment'] != null) {

					$link_img = $this->globalfunction->saveImg('./uploads/members/'.$id.'/' , 'proof_of_payment');
					$public_link_img = $this->globalfunction->api_url() . $link_img;
					$dataUpdate = array(
						'id'			=> date('Ymdhisa'),
						'id_user'		=> $id,
						'proof_of_payment' => $public_link_img

					);	
					$queryreturn = $this->BasicQuery->insert('detail_user', $dataUpdate);
					
					if(!$queryreturn){

					}

				}

				// update profile_picture
				if ($input['profile_picture'] != '' && $input['profile_picture'] != null) {

					$link_img = $this->globalfunction->saveImg('./uploads/members/'.$id.'/' , 'profile_picture');
					$public_link_img = $this->globalfunction->api_url() . $link_img;
					$dataUpdate = array(
						'id'			=> date('Ymdhisa'),
						'id_user'		=> $id,
						'profile_picture' => $public_link_img

					);	
					$queryreturn = $this->BasicQuery->insert('detail_user', $dataUpdate);
					
					if(!$queryreturn){

					}

				}

				

			}
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
