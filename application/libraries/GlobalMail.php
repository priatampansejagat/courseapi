<?php

// include("./libs/mail/src/PHPMailer.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class GlobalMail{

	var $CI;
	public function __construct(){
		$this->CI =& get_instance();

		// $this->CI->load->helper(array('path'));
		// $this->CI->load->file('libs/mail/src/PHPMailer.php');

		require_once(APPPATH."libraries/mail/src/Exception.php");
		require_once(APPPATH."libraries/mail/src/OAuth.php");
		require_once(APPPATH."libraries/mail/src/PHPMailer.php");
		require_once(APPPATH."libraries/mail/src/POP3.php");
		require_once(APPPATH."libraries/mail/src/SMTP.php");


		// Settings
		$this->mail = new PHPMailer;
		$this->mail->IsSMTP();
		$this->mail->CharSet = 'UTF-8';

		$this->mail->Host       = "mail.research-academy.org"; // SMTP server 
		$this->mail->SMTPAuth   = true;                  // enable SMTP authentication
		$this->mail->SMTPSecure = 'ssl';
		$this->mail->Port       = 465;                    // set the SMTP port for the GMAIL server
		$this->mail->Username   = "info@research-academy.org"; // SMTP account username example
		$this->mail->Password   = "Penelitihandal2024";        // SMTP account password example
	}

	
	public function simpleMail($arrTo, $subject, $body){

		// try {
			// penerima dan pengirim
			$this->mail->setFrom($this->mail->Username, 'Research Academy');

			foreach ($arrTo as $value) {
				$this->mail->addAddress($value);   
			}

			// content
			$this->mail->isHTML(true);                                  // Set email format to HTML
		    $this->mail->Subject = $subject;
		    $this->mail->Body    = $body;

		    // send
		    if(!$this->mail->send()){
	            return false;
	        }else{
	            return true;
	        }
		// } catch (Exception $e) {
		// 	return false;
		// }
		

		
	}

}
	

?>