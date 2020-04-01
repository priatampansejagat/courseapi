<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MyFlash {
    var $CI;
    public function __construct($params = array())
    {
        $this->CI =& get_instance();

        $this->CI->load->helper('url');
		$this->CI->load->library('session');
		$this->CI->config->item('base_url');
        
    }

    public function setMessage($status,$value){
        $data = array(
                        'status'    => $status,
                        'value'     => $value
        );

        $this->CI->session->set_flashdata('message', $data);
    }

    public function setValueArray($valueArray){

        $this->CI->session->set_flashdata('valueArray', $valueArray);
    }

    public function getFlash($key){
      return $this->CI->session->flashdata($key);
    }


}

?>