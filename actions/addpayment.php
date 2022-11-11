<?php

require('../configuration.php');

$orderId = $_GET["orderId"];
$paymentAmount = $_GET["paymentAmount"];
$paymentDate = $_GET["paymentDate"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$addPaymentStatement = "insert into payments ( order_id, payment_date, payment_amount ) values ( ?, ?, ? )";
if (!($stmt = $mysqli->prepare($addPaymentStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
/*date("Y-m-d")*/
if (!$stmt->bind_param("iss", $orderId, $paymentDate, $paymentAmount ) ) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();

include('../payments.php');

?>