<?php


class GlobalFunction{

	var $CI;
	public function __construct()
	{
		$this->CI =& get_instance();
	}

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

	// function _log($str) {

	//     // log to the output
	//     $log_str = date('d.m.Y').": {$str}\r\n";
	//     echo $log_str;

	//     // log to file
	//     if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
	//         fputs($fp, $log_str);
	//         fclose($fp);
	//     }
	// }

	// function rrmdir($dir) {
	//     if (is_dir($dir)) {
	//         $objects = scandir($dir);
	//         foreach ($objects as $object) {
	//             if ($object != "." && $object != "..") {
	//                 if (filetype($dir . "/" . $object) == "dir") {
	//                     rrmdir($dir . "/" . $object); 
	//                 } else {
	//                     unlink($dir . "/" . $object);
	//                 }
	//             }
	//         }
	//         reset($objects);
	//         rmdir($dir);
	//     }
	// }

	// function createFileFromChunks($temp_dir, $fileName, $chunkSize, $totalSize,$total_files) {

	//     // count all the parts of this file
	//     $total_files_on_server_size = 0;
	//     $temp_total = 0;
	//     foreach(scandir($temp_dir) as $file) {
	//         $temp_total = $total_files_on_server_size;
	//         $tempfilesize = filesize($temp_dir.'/'.$file);
	//         $total_files_on_server_size = $temp_total + $tempfilesize;
	//     }
	//     // check that all the parts are present
	//     // If the Size of all the chunks on the server is equal to the size of the file uploaded.
	//     if ($total_files_on_server_size >= $totalSize) {
	//     // create the final destination file 
	//         if (($fp = fopen($temp_dir.'/'.$fileName, 'w')) !== false) {
	//             for ($i=1; $i<=$total_files; $i++) {
	//                 fwrite($fp, file_get_contents($temp_dir.'/'.$fileName.'.part'.$i));
	//                 _log('writing chunk '.$i);
	//             }
	//             fclose($fp);
	//         } else {
	//             _log('cannot create the destination file');
	//             return false;
	//         }

	//         // rename the temporary directory (to avoid access from other 
	//         // concurrent chunks uploads) and than delete it
	//         if (rename($temp_dir, $temp_dir.'_UNUSED')) {
	//             rrmdir($temp_dir.'_UNUSED');
	//         } else {
	//             rrmdir($temp_dir);
	//         }
	//     }

	// }

}
	

?>