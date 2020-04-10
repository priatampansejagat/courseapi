<?php


class GlobalFunction{

	public function url(){
		$http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') . '://';
		$finalUrl    = $http . 'research-academy.org'; 
		return $finalUrl;
	}

	public function JSON_POST_asArr(){
		$jsonPOST = file_get_contents('php://input');
		return json_decode($jsonPOST, true);
	}

	public function return_JSON_success($message, $content = null){
		$obj=new stdClass;
		$obj->status = 200;
		$obj->proc = 'true';
		$obj->message = $message;
		$obj->data = $content;

		return (json_encode($obj));
	}

	public function return_JSON_failed($message, $content = null){
		$obj=new stdClass;
		$obj->status = 500;
		$obj->proc = 'false';
		$obj->message = $message;
		$obj->data = $content;
		
		return (json_encode($obj));
	}

	public function saveImg($dir='./', $imgUpload){ //imgUpload adalah name pada form
		$nameImage=date('Ymdhisa');
		$target_dir = $dir;
		$target_file = $target_dir . basename($_FILES[$imgUpload]["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

		// Check if image file is a actual image or fake image
		// if(isset($_POST["submit"])) {
		//     $check = getimagesize($_FILES[$imgUpload]["tmp_name"]);
		//     if($check !== false) {
		//         echo "File is an image - " . $check["mime"] . ".";
		//         $uploadOk = 1;
		//     } else {
		//         echo "File is not an image.";
		//         $uploadOk = 0;
		//     }
		// }

		if ($_FILES[$imgUpload]["size"] > 1000000) { //1MB
			return [false, '1'];
		    $uploadOk = 0;
		}
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
			return [false, '2'];
		    $uploadOk = 0;
		}
		if ($uploadOk == 0) {
			return [false, '3'];
		} else {
			$target_file = $target_dir.$nameImage.'.'.$imageFileType;
		    if (move_uploaded_file($_FILES[$imgUpload]["tmp_name"], $target_file)) {
		    	return [true,$target_file];
		    } else {
		        return [false, '4'];
		    }
		}
	}

}
	

?>