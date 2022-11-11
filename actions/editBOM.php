<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_POST["orderId"];
$field = $_POST["name"];
$value = $_POST["value"];
$itemId = $_POST["pk"];
$err = "no error";	
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
	$err = "err1";
}

$statement = "update order_bom set ".$field." = '".$value."' where item = ".$itemId." and orderId = ".$orderId;

if (!($stmt = $mysqli->prepare($statement))) {
	$err = "err2";
}	
	

$result = $stmt->execute();
$stmt->close();
$mysqli->close();

if( !$result ){
	http_response_code(412);
	$form_data['msg'] = "an error occurred";
}else{
	http_response_code(200);
	$form_data['msg'] = "BOM item updated";	
}
		

echo json_encode($form_data);

?>