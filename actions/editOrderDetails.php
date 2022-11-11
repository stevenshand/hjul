<?php

require('../configuration.php');

$orderId = $_GET["orderId"];
$orderDate = $_GET["orderDate"];
$targetWeek = $_GET["targetWeek"];
$targetLocked = $_GET["targetLocked"];
$reconciled = $_GET["reconciled"];
$status = $_GET["status"];
$location = $_GET["location"];
$shippingLocation = $_GET["shippingLocation"];
$promoCode = $_GET["promoCode"];
$lead = $_GET["lead"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$statement = "UPDATE orders set order_date = ?, target_week = ?, target_locked = ?, reconciled = ?, status = ?, location = ?, shipping_location = ?, promo_code = ?, lead = ? where id = ?";
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("siiiiiissi", $orderDate, $targetWeek, $targetLocked, $reconciled, $status, $location, $shippingLocation, $promoCode, $lead, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();
$mysqli->close();	
	
include ( __DIR__.'/../panels/order_details_panel.php' );

?>