<?php

require('../configuration.php');

$orderId = $_GET["orderId"];
$paymentId = $_GET["paymentId"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$deletePaymentStatement = "DELETE from payments where id = ".$paymentId;
if (!($stmt = $mysqli->prepare($deletePaymentStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
$stmt->execute();

include('../payments.php');

?>