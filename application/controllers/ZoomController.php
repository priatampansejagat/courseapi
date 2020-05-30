<?php
defined('BASEPATH') or exit('No direct script access allowed');

use GuzzleHttp\Client;

class ZoomController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');

		require_once(APPPATH."libraries/guzzle/src/Client.php");

	}

	public function index(){

		try {
		    $client = new Client(['base_uri' => 'https://zoom.us']);
		 
		    $response = $client->request('POST', '/oauth/token', [
		        "headers" => [
		            "Authorization" => "Basic ". base64_encode(ZOOM_OAUTH_CLIENT_ID.':'.ZOOM_OAUTH_CLIENT_SECRET)
		        ],
		        'form_params' => [
		            "grant_type" => "authorization_code",
		            "code" => $_GET['code'],
		            "redirect_uri" => ZOOM_OAUTH_REDIRECT_URI
		        ],
		    ]);
		 
		    $token = json_decode($response->getBody()->getContents(), true);
		 	
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
