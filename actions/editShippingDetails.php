<?php

require('../configuration.php');

$orderId = $_GET["orderId"];
$shippingPrice = $_GET["shippingPrice"];
$notes = $_GET["notes"];


$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$statement = "UPDATE orders set shipping_cost = ?, shipping_notes = ? where id = ?";

if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("ssi", $shippingPrice, $notes, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();
$mysqli->close();	

	
include ( __DIR__.'/../panels/shipping_details_panel.php' );

?>