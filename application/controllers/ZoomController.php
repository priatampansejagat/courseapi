<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ZoomController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');

	}

	public function index(){

		try {
		    $url = 'https://zoom.us/oauth/token';
		    $data = array( 	"grant_type" => "authorization_code",
				            "code" => $_GET['code'],
				            "redirect_uri" => ZOOM_OAUTH_REDIRECT_URI);
		 

		    $options = array(
			    'http' => array(
			        'header'  => 	"Content-type: application/x-www-form-urlencoded\r\n".
			        				"Authorization: Basic ". base64_encode(ZOOM_OAUTH_CLIENT_ID.':'.ZOOM_OAUTH_CLIENT_SECRET),
			        'method'  => 'POST',
			        'content' => http_build_query($data)
			    )
			);

			$context  = stream_context_create($options);
			$result = file_get_contents($url, true, $context);

			// echo($result);

			
		    $token = json_decode($result, true);
		 	
		 	// cek token
		    $count = $this->BasicQuery->countAllResult('zoom',array());

		    if ($count == 0) {
		    	$dbstat = $this->BasicQuery->insert( 'zoom',$token);
		    }else{
		    	$dbstat = $this->BasicQuery->update( 'zoom',
													'id', 
													1,
													$token);
		    }

		} catch(Exception $e) {
		    echo $e->getMessage();
		}


	}



	

	
}
