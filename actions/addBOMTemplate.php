<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$templateName = $_POST["template"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
	$err = "err1";
}

$insertStatement = "INSERT into component_template_details(name) values ( ? )";
if (!($stmt = $mysqli->prepare($insertStatement))) {
	$err = "err2";
}	

if (!$stmt->bind_param("s", $templateName )) {
	$err = "err3";
}	

if (!$stmt->execute()) {
    $err = $stmt->error;
}

$stmt->close();
$mysqli->close();

if( $err ){
	http_response_code(200);
	$form_data['msg'] = "an error occurred";
	$form_data['err'] = $err;
	echo json_encode($form_data);
}else{
	http_response_code(200);
	$form_data['msg'] = "template added";	
	echo json_encode($form_data);
}

?>