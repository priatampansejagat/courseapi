<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ZoomController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');

	}

	public function index(){ //FUNCTION INI BISA JUGA DIKATAKAN LOGIN KARENA MENGAMBIL TOKEN PERTAMA KALI

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
			$result = file_get_contents($url, false, $context);

		    $token = json_decode($result, true);
		 	
		 	// cek token
		    $count = $this->BasicQuery->countAllResult('zoom',array());

		    $dbstat=false;
		    if ($count == 0) {
		    	$dbstat = $this->BasicQuery->insert( 'zoom',$token);
		    }else{
		    	$dbstat = $this->BasicQuery->update( 'zoom',
													'id', 
													1,
													$token);
		    }

		    if ($dbstat == true) {
				$JSON_return = $this->globalfunction->return_JSON_success("Success",$dataReceived);
				echo $JSON_return;
			}else{
				$JSON_return = $this->globalfunction->return_JSON_failed("Failed", $dataReceived);
				echo $JSON_return;
			}

		} catch(Exception $e) {
		    echo $e->getMessage();
		}
	}


	function create_meeting() {
		try {
			$zoomdata = $this->BasicQuery->selectAll('zoom', array( 'id' => 1 ));
			$access_token = $zoomdata['access_token'];

			$url = 'https://zoom.us/v2/users/'.ZOOM_OAUTH_CLIENT_ID.'/meetings';
			$data = array( 	"topic" => "PERCOBAAN ZOOM 13th",
			                "type" => 2,
			                "start_time" => "2020-05-30T20:00:00",
			                "duration" => "30", // 30 mins
			                "password" => "162534"
					        );
			$json_data = json_encode($data);

			$options = array(
			    'http' => array(
			        'header'  => 	"Content-type: application/json\r\n".
			        				"Authorization: Bearer ". base64_encode($access_token),
			        'method'  => 'POST',
			        'content' => $json_data
			    )
			);

			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);

			echo $result;

		} catch(Exception $e) {
	        if( 401 == $e->getCode() ) {
	            $this->refresh_token();
	            $this->create_meeting();
	        }    
	    }

	}


	function refresh_token(){
		// Get refresh_token
		$zoomdata = $this->BasicQuery->selectAll('zoom', array( 'id' => 1 ));
		$refresh_token = $zoomdata['refresh_token'];

		// get new token
		$url = 'https://zoom.us/oauth/token';
		$data = array( 	"grant_type" => "refresh_token",
				        "refresh_token" => $refresh_token
				        );
		 

	    $options = array(
		    'http' => array(
		        'header'  => 	"Content-type: application/x-www-form-urlencoded\r\n".
		        				"Authorization: Basic ". base64_encode(ZOOM_OAUTH_CLIENT_ID.':'.ZOOM_OAUTH_CLIENT_SECRET),
		        'method'  => 'POST',
		        'content' => http_build_query($data)
		    )
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		$token = json_decode($result, true);
	 	
	 	// cek token
	    $count = $this->BasicQuery->countAllResult('zoom',array());

	    $dbstat=false;
	    if ($count == 0) {
	    	$dbstat = $this->BasicQuery->insert( 'zoom',$token);
	    }else{
	    	$dbstat = $this->BasicQuery->update( 'zoom',
												'id', 
												1,
												$token);
	    }

	    if ($dbstat == true) {
	    	return true;
	    }else{
	    	return false;
	    }

	}



	

	
}
