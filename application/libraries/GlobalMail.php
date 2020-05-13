<?php

// include("./libs/mail/src/PHPMailer.php");
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

		$this->mail = new PHPMailer();
		// Settings
		$this->mail->IsSMTP();
		$this->mail->CharSet = 'UTF-8';

		$this->mail->Host       = "mail.research-academy.org"; // SMTP server 
		$this->mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
		$this->mail->SMTPAuth   = true;                  // enable SMTP authentication
		$this->mail->Port       = 465;                    // set the SMTP port for the GMAIL server
		$this->mail->Username   = "testmail@research-academy.org"; // SMTP account username example
		$this->mail->Password   = "goplay1212**";        // SMTP account password example
	}

	
	public function simpleMail($arrTo, $arrAttachment, $subject, $body){
		try{

			// penerima dan pengirim
			$this->mail->setFrom($this->mail->Username, 'Mailer');

			foreach ($arrTo as $value) {
				$this->mail->addAddress($value);   
			}

			foreach ($arrAttachment as $value) {
				$this->mail->addAttachment($value);
			}


			// content
			// $this->mail->isHTML(true);                                  // Set email format to HTML
		    $this->mail->Subject = $subject;
		    $this->mail->Body    = $body;

		    // send
		    $mail->send();

			return true;
		}catch (Exception $e){
			return false;
		}
	}

}
	

?>