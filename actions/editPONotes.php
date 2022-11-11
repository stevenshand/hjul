<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

//$header = 'Location: /inventory.php'; 	

$po_id = $_POST["pk"];
$notes = $_POST["value"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
	$err = "err1";
}

$insertStatement = "update inv_purchase_orders set notes = ? where id = ?";
if (!($stmt = $mysqli->prepare($insertStatement))) {
	$err = "err2";
}	

if (!$stmt->bind_param("si", $notes, $po_id )) {
	$err = "err3";
}	

if (!$stmt->execute()) {
    $err = $stmt->error;
}

$result = $stmt->execute();

$stmt->close();
$mysqli->close();

if( $err ){
	http_response_code(200);
	$form_data['msg'] = "an error occurred";
	$form_data['err'] = $err;
	$form_data['statement'] = $insertStatement;
	echo json_encode($form_data);
}else{
	http_response_code(200);
	$form_data['msg'] = "PO note updated";	
	$form_data['po_id'] = $po_id;	
	$form_data['note'] = $notes;	
	echo json_encode($form_data);
}

?>