<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

//$header = 'Location: /inventory.php'; 	

$supplierCode = $_POST["supplier_code"];
$item = $_POST["item"];
$category = $_POST["category"];
$description= $_POST["description"];
$variation= $_POST["variation"];
$location= $_POST["location"];
$supplier = $_POST["supplier"];
$cost = $_POST["cost"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
	$err = "err1";
}

$insertStatement = "INSERT into inv_items(short_name, description, variation, supplier, supplier_code, cost, category, location ) values ( ?, ?, ?, ?, ?, ?, ?, ? )";
if (!($stmt = $mysqli->prepare($insertStatement))) {
	$err = "err2";
}	

if (!$stmt->bind_param("sssssssi", $item, $description, $variation, $supplier, $supplierCode, $cost, $category, $location )) {
	$err = "err3";
}	

if (!$stmt->execute()) {
    $err = $stmt->error;
}

$itemId = $stmt->insert_id;

$stmt->close();
$mysqli->close();

if( $err ){
	http_response_code(200);
	$form_data['msg'] = "an error occurred, check SKU uniqueness";
	$form_data['err'] = $err;
	echo json_encode($form_data);
}else{
	http_response_code(200);
	$form_data['msg'] = "item added";	
	$form_data['itemId'] = $itemId;	
	echo json_encode($form_data);
}

?>