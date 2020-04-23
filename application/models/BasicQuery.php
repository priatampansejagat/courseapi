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

    public function selectAllResult($table,$conditionArr){
        
         $this->db->select('*')->from($table)->where($conditionArr);
         $query = $this->db->get();
         return $query->result_array();

    }

    public function update($table, $condKey, $condVal, $dataUpdate){
    	$this->db->where($condKey, $condVal);
		if ($this->db->update($table, $dataUpdate)) {
			return true;
		}else{
			return false;
		}
    }

    public function insert($table, $dataArr){
    	if ($this->db->insert($table, $dataArr)) {
    		return true;
    	}else{
    		return false;
    	}
    }

    public function countAllResult($table,$conditionArr){
        
         $this->db->select('*')->from($table)->where($conditionArr);
         $count=$this->db->count_all_results();
         return $count;

    }

    public function delete($table,$dataArr){
        
        if ($this->db->delete($table, $dataArr)) {
            return true;
        }else{
            return false;
        }
    }

}


?>