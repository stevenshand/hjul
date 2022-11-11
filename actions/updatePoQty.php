<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$field = $_POST["name"];
$value = $_POST["value"];
$lineId = $_POST["pk"];
$err = "no error";	
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    // $err =  mysqli_connect_error();
	$err = "err1";
}

$statement = "update inv_po_lines set ".$field." = '".$value."' where id = ".$lineId;

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
	$form_data['msg'] = "an error occurred";
}else{
	http_response_code(200);
	$form_data['msg'] = "po qty updated";	
}
		

echo json_encode($form_data);

?>