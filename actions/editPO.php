<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$dateFields = array("create_date", "sent_date", "received_date");

$field = $_POST["name"];
$value = $_POST["value"];
$id = $_POST["pk"];
$err = "no error";	

$form_data['msg'] = "PO TEST";

$form_data['field'] = $field;
$form_data['value'] = $value;
$form_data['err'] = $err;

$statement = "update inv_purchase_orders set ".$field." = '".$value."' where id = ".$id;

if( $field == "expected_date" && strlen( $value ) < 1 ){
	$statement = "update inv_purchase_orders set ".$field." = NULL where id = ".$id;
}


$form_data['statement'] = $statement;

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
	$err = mysqli_connect_error();
}


if (!($stmt = $mysqli->prepare($statement))) {
   $err = $err." ".($mysqli->errno);
   $err = $err." ".($mysqli->error);
}	
	

$result = $stmt->execute();
$stmt->close();
$mysqli->close();

if( !$result ){
	http_response_code(412);
	$form_data['msg'] = "an error occurred, probably duplicate value";
}else{
	http_response_code(200);
	$form_data['msg'] = "PO updated";	
}
		
echo json_encode($form_data);
?>