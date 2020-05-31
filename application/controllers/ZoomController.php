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
				echo "<script type='text/javascript'> window.close(); </script>";
			}else{
				echo "Failed";
			}

		} catch(Exception $e) {
		    echo $e->getMessage();
		}
	}

	function activate_token(){

		// create a new cURL resource
		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, "https://zoom.us/oauth/authorize?response_type=code&client_id=".ZOOM_OAUTH_CLIENT_ID."&redirect_uri=".ZOOM_OAUTH_REDIRECT_URI);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
	}

	function create_meeting() {
		// try {
			// prepare data
			$dataReceived = $this->globalfunction->JSON_POST_asArr();
			$event_id = null;
			$course_id = null;
			$topic = $dataReceived['topic'];
			$start_time = $dataReceived['start_time'];
			$duration = $dataReceived['duration'];
			$password = $dataReceived['password'];

			if (isset($dataReceived['course_id'])) {
				$course_id = $dataReceived['course_id'];
			}
			if (isset($dataReceived['event_id'])){
				$event_id = $dataReceived['event_id'];
			}

			$zoomdata = $this->BasicQuery->selectAll('zoom', array( 'id' => 1 ));
			$access_token = $zoomdata['access_token'];

			$curl = curl_init();
			$data = array( 	"topic" => $topic,
			                "type" => 2,
			                "start_time" => $start_time,
			                "duration" => $duration, // 30 mins
			                "password" => $password
					        );

			$data_json = json_encode($data);

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.zoom.us/v2/users/me/meetings",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => $data_json,
			  CURLOPT_HTTPHEADER => array(
			    "authorization: Bearer ".$access_token,
			    "content-type: application/json"
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			  	$JSON_return = $this->globalfunction->return_JSON_failed("Failed", $dataReceived);
				echo $JSON_return;
			} else {
				// Simpan data ke DB
				$response_decode = json_decode($response,true);
				$data_meeting = array(
										'id' => 'zoommeeting_'.date('Ymdhisa'),
										'response'	=> $response,
										'course_id'	=> $course_id,
										'event_id'	=> $event_id,
										'join_url'	=> $response_decode['join_url'],
										'start_url'	=> $response_decode['start_url'],
										'topic'	 	=> $topic,
										'start_time' => $start_time,
										'duration' 	=> $duration,
										'password' 	=> $password,
										'type' 		=> 2,
										'status'	=> ACTIVE

				);
				$dbstat = $this->BasicQuery->insert( 'zoom_meetings',$data_meeting);

				if ($dbstat == true) {
					$JSON_return = $this->globalfunction->return_JSON_success("Success",$data_meeting);
					echo $JSON_return;
				}else{
					$JSON_return = $this->globalfunction->return_JSON_failed("Failed", $dataReceived);
					echo $JSON_return;
				}
			  	
			}




		// } catch(Exception $e) {
	 //        if( 401 == $e->getCode() ) {
	 //            $this->refresh_token();
	 //            $this->create_meeting();
	 //        }    
	 //    }

	}

	function delete_meeting(){

		try{
			$dataReceived = $this->globalfunction->JSON_POST_asArr();
			$event_id = null;
			$course_id = null;

			if (isset($dataReceived['course_id'])) {
				$course_id = $dataReceived['course_id'];
			}
			if (isset($dataReceived['event_id'])){
				$event_id = $dataReceived['event_id'];
			}

			$zoomdata = $this->BasicQuery->selectAll('zoom', array( 'id' => 1 ));
			$access_token = $zoomdata['access_token'];

			$curl = curl_init();
			curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.zoom.us/v2/meetings/".$meeting_id,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "DELETE",
				  CURLOPT_HTTPHEADER => array(
				    "authorization: Bearer ".$access_token,
				    "content-type: application/json"
				  ),
				));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			  	$JSON_return = $this->globalfunction->return_JSON_failed("Failed", $dataReceived);
				echo $JSON_return;
			} else {
				$delCond = array('course_id' => $course_id, 'event_id' => $event_id);
				$dbstat = $this->BasicQuery->delete( 'zoom_meetings', $delCond);

				if ($dbstat == true) {
					$JSON_return = $this->globalfunction->return_JSON_success("Success",$dataReceived);
					echo $JSON_return;
				}else{
					$JSON_return = $this->globalfunction->return_JSON_failed("Failed", $dataReceived);
					echo $JSON_return;
				}
			}
		
		} catch(Exception $e) {
	        if( 401 == $e->getCode() ) {
	            $this->refresh_token();
	            $this->create_meeting();
	        }    
	        echo $e->getMessage();
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
