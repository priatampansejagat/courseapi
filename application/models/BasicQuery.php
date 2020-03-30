<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BasicQuery extends CI_Model {

    public function __construct(){
            $this->load->database();
    }


    public function selectAll($table,$conditionArr){
    	
    	 $this->db->select('*')->from($table)->where($conditionArr);
    	 $query = $this->db->get();
    	 return $query->row_array();

    }

}


?>