<?php

$orderId = $_GET["orderId"];
$shippingMethod = $_GET["shippingMethod"];
$shippingDate = $_GET["shippingDate"];
$trackingNumber = $_GET["tracking"];


require('../configuration.php');


$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$statement = "insert into shipping ( order_id, shipping_date, method, tracking_number ) values ( ?, ?, ?, ? )";
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	

	
if (!$stmt->bind_param("isss", $orderId, $shippingDate, $shippingMethod, $trackingNumber ) ) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();

include('../shipping.php');

?>