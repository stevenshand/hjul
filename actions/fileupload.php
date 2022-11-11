<?php

$orderId = $_POST["orderId"];
$msg = "file upload attempt";

if(isset($orderId) && ($_FILES) == true){

	$target_dir = "../files/".$orderId;
	
	if(!realPath($target_dir)){
		mkdir($target_dir);
	}

	$file = $_FILES['file'];
	$fileName = $_FILES['file']['name'];
	$finalPath = $target_dir."/".$fileName; 

	if( move_uploaded_file($_FILES['file']['tmp_name'], $finalPath) ){
        $response['status'] = 'ok';
    }else{
        $response['status'] = 'err';
    }
	
    $response['msg'] = $msg;
	echo json_encode($response);
}