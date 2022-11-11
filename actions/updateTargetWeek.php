<?php

require('../configuration.php');

$orderId = $_POST["orderId"];
$targetWeek = $_POST["targetWeek"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderStatusUpdateStatement = "UPDATE orders set target_week = ? where id = ?";
if (!($stmt = $mysqli->prepare($orderStatusUpdateStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("ii", $targetWeek, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();
$mysqli->close();	

$form_data['success'] = true;
$form_data['orderId'] = $orderId;
$form_data['targetWeek'] = $targetWeek;

$form_data['msg'] = 'targetWeek updated';
echo json_encode($form_data);

?>