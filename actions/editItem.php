<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );


$field = $_POST["name"];
$value = $_POST["value"];
$itemId = $_POST["pk"];
$err = "no error";	
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    // $err =  mysqli_connect_error();
	$err = "err1";
}

$statement = "update inv_items set ".$field." = '".$value."' where id = ".$itemId;

if (!($stmt = $mysqli->prepare($statement))) {
   //$err = $err." ".($mysqli->errno);
   //$err = $err." ".($mysqli->error);
	$err = "err2";
}	
	

$result = $stmt->execute();
$stmt->close();
$mysqli->close();

if( !$result ){
	http_response_code(412);
	$form_data['msg'] = "an error occurred, probably duplicate value";
}else{
	http_response_code(200);
	$form_data['field'] = $field;
	$form_data['value'] = $value;
	$form_data['itemId'] = $itemId;
	$form_data['error'] = $err;
	$form_data['msg'] = "item updated";	
}
		
echo json_encode($form_data);

?>