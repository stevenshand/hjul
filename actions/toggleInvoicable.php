<?php

require('../configuration.php');

$orderId = $_POST["orderId"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderStatusUpdateStatement = "UPDATE orders SET invoicable = 1 - invoicable where id = ?";
//$orderStatusUpdateStatement = "UPDATE orders set payment_pending = ? where id = ?";
if (!($stmt = $mysqli->prepare($orderStatusUpdateStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("i", $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();
$mysqli->close();	

$form_data['success'] = true;
$form_data['orderId'] = $orderId;

$form_data['msg'] = 'invoicable updated';
echo json_encode($form_data);

?>