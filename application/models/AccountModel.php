<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AccountModel extends CI_Model {

    public function __construct(){
            parent::__construct();
            $this->load->database();
            $this->default = $this->load->database('default', TRUE);
    }



    public function registrasi($dtInput,$dtAdditional){

    	$id=date('Ymdhi-sa');
    	// echo($id);

        $this->db->select('*')->from('user')->where('id',$id);
    	$count=$this->db->count_all_results();

    	if ($count == 0) {

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
		    						'country'				=> $dtInput['country'].'-'.$dtInput['country_input'],
		    						'phone_number'			=> $dtInput['mobile_number'],
		    						'profesion'				=> 'inputan belum tersedia',
		    						'major_of_study'		=> $dtInput['field_of_study'].'-'.$dtInput['study_input'],
		    						'status'				=> $dtInput['status'] //'status'				=> $dtInput['status'].' - '.$dtInput['status_input']
		    		);

    			// echo(json_encode($savetodb));
	    		if ($this->db->insert('user',$savetodb)) {
	    			return ['true','Proses berhasil'];
	    		}else{
	    			return ['false','Gagal menyimpan, silahkan menghubungi admin'];
	    		}

    		}else{
    			return ['false','Username tidak tersedia'];
    		}

    	}else{
    		$this->registrasi($dtInput,$dtAdditional);
    	}

    }


    public function login($dtInput){

        $this->db->select('*')->from('user')->where('username',$dtInput['uname']);
    	$count=$this->db->count_all_results();

    	if ($count == 0) {

    		$this->db->select('*');
	        $this->db->from('user');
	        $this->db->where('username',$dtInput['uname']);
	        $selectUser = $this->db->get()->result_array();

    		$if (hash('sha3-512' , $dtInput['password']) == $selectUser['password']) {
    			return ['true','Login berhasil',$selectUser];
    		}else{
    			return ['false','Username atau password salah'];
    		}

    	}else{
    		return ['false','Username atau password salah'];
    	}

    }


}


?>