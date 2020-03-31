<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AccountModel extends CI_Model {

    public function __construct(){
            // parent::__construct();
            $this->load->database();
            // $this->default = $this->load->database('default', TRUE);
    }


    public function registrasi($dtInput,$dtAdditional){
    	$obj=new stdClass;
    	$id=date('Ymdhi-sa');
    	// echo($id);

        $this->db->select('*')->from('user')->where('id',$id);
    	$count=$this->db->count_all_results();

    	if ($count == 0) {

    		$dtInput['mantankampret']=$id;
    		$this->db->select('*')->from('user')->where('username',$dtInput['uname']);
    		$countuname=$this->db->count_all_results();

    		if ($countuname == 0) {
    			// echo($dtInput['uname']);
    			$savetodb = array(
		    						'id'					=> $id,
		    						'role_id'				=> '83bbe0cd25d8cc4b8c076497a57d4b6452e84946b9042dc7983a7806a1f636cf',
		    						'username'				=> $dtInput['uname'],
		    						'fullname'				=> $dtInput['fullname'],
		    						'password'				=> hash('sha3-512' , $dtInput['password']),
		    						'place_of_birth'		=> $dtInput['place_of_birth'],
		    						'date_of_birth'			=> $dtInput['date_of_birth'],
		    						'email'					=> $dtInput['email'],
		    						'institution'			=> $dtInput['institution'],
		    						'country'				=> $dtInput['country'],
		    						'phone_number'			=> $dtInput['mobile_number'],
		    						'profesion'				=> 'inputan belum tersedia',
		    						'major_of_study'		=> $dtInput['field_of_study'],
		    						'status'				=> $dtInput['status'] //'status'				=> $dtInput['status'].' - '.$dtInput['status_input']
		    		);

    			// echo(json_encode($savetodb));
	    		if ($this->db->insert('user',$savetodb)) {

	    			$obj->status = 200;
	    			$obj->data = array(
	    					'proc'		=> 'true',
	    					'message'	=> 'Proses berhasil'
	    			);
	    			$obj->dataInput = $dtInput;
	    			
	    			return $obj;
	    		}else{
	    			$obj->status = 500;
	    			$obj->data = array(
	    					'proc'		=> 'false',
	    					'message'	=> 'Gagal menyimpan, silahkan menghubungi admin'
	    			);
	    			$obj->dataInput = $dtInput;

	    			
	    			return $obj;
	    		}

    		}else{

    				$obj->status = 500;
	    			$obj->data = array(
	    					'proc'		=> 'false',
	    					'message'	=> 'Username tidak tersedia'
	    			);
	    			$obj->dataInput = $dtInput;

	    			
	    			return $obj;
    		}

    	}else{
    		$this->registrasi($dtInput,$dtAdditional);
    	}

    }


    // public function login($dtInput){

    //     $this->db->select('*')->from('user')->where('username',$dtInput['uname']);
    // 	$count=$this->db->count_all_results();

    // 	if ($count == 0) {

    // 		$this->db->select('*');
	   //      $this->db->from('user');
	   //      $this->db->where('username',$dtInput['uname']);
	   //      $selectUser = $this->db->get()->result_array();

    // 		$if (hash('sha3-512' , $dtInput['password']) == $selectUser['password']) {
    // 			return ['true','Login berhasil'];
    // 		}else{
    // 			return ['false','Username atau password salah'];
    // 		}

    // 	}else{
    // 		return ['false','Username atau password salah'];
    // 	}

    // }


}


?>