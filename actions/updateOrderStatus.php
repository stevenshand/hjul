<?php

require('../configuration.php');

$statusId = $_POST["statusId"];
$orderId = $_POST["orderId"];

if( !$statusId || !$orderId ){
//	header($header, TRUE, 302);
//	exit();	
}

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderStatusUpdateStatement = "UPDATE orders set status = ? where id = ?";
if (!($stmt = $mysqli->prepare($orderStatusUpdateStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("ii", $statusId, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();
$mysqli->close();	

$form_data['success'] = true;
$form_data['orderId'] = $orderId;
$form_data['statusId'] = $statusId;

$form_data['msg'] = 'status updated';
echo json_encode($form_data);

?>