<?php
require '../configuration.php';

$orderId = $_POST["orderId"];
$msg = "file upload attempt";

if(isset($orderId) && ($_FILES) == true){

    $target_dir = FILESDIR."/".$orderId;

    $msg = $msg." target dir:[".$target_dir."]";

	if(!realPath($target_dir)){
        $msg = $msg." - creating dir";
        mkdir($target_dir);
        $msg = $msg." - created dir";
        $msg = $msg." - isWritable:".(is_writable($target_dir) ? "Y" :"N" );
	}else{
        $msg = $msg." - dir exists";
    }

	$file = $_FILES['file'];
	$fileName = $_FILES['file']['name'];
	$finalPath = $target_dir."/".$fileName;

	if( move_uploaded_file($_FILES['file']['tmp_name'], $finalPath) ){
        $response['status'] = 'ok';
    }

    $response['msg'] = $msg;
	echo json_encode($response);
}