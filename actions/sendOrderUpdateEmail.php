<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

//$header = 'Location: /inventory.php'; 	

$email = $_POST["email"];
$message = $_POST["message"];
$orderId = $_POST["orderId"];

$uuid = UUIDFromOrderId($orderId);
$orderViewLink = $BASEURL+"/vieworder.php?orderId=".$uuid;

$message = nl2br(str_replace( "[link]", $orderViewLink, $message ));

$sender_address="no_reply@willowbike.com";
$to_address=$email;

$headers = "From: ".$sender_address."\r\n";
$headers .= "Reply-To: ".$sender_address."\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$headers .= "CC: info@willowbike.com\r\n";
$subject = "Willow Bike - Order Update";
$success = mail( $to_address, $subject, $message, $headers );


if( $err || !$success ){
	http_response_code(200);
	$form_data['msg'] = "an error occurred";
	$form_data['err'] = $err;
	echo json_encode($form_data);
}else{
	http_response_code(200);
	$form_data['msg'] = "email sent";	
	$form_data['email'] = $email;	
	$form_data['message'] = $message;	
	$form_data['orderId'] = $orderId;	
	$form_data['orderViewLink'] = $orderViewLink;	
	echo json_encode($form_data);
}

?>