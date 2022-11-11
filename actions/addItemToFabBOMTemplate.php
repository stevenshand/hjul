<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$itemId = $_POST["itemId"];
$model = $_POST["model"];
$qty = $_POST["qty"];


$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$addItem = "insert into fab_template ( item, qty, model ) values ( ?, ?, ? )";
if (!($stmt = $mysqli->prepare($addItem))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("iii", $itemId, $qty, $model ) ) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
	
if( $err ){
	http_response_code(200);
	$form_data['msg'] = "an error occurred, check SKU uniqueness";
	$form_data['err'] = $err;
}else{
	http_response_code(200);
	$form_data['msg'] = "item added";	
	$form_data['itemId'] = $itemId;	
	$form_data['model'] = $model;	
	$form_data['qty'] = $qty;	
}

echo json_encode($form_data);

?>