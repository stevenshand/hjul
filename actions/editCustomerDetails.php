<?php

require('../configuration.php');

$orderId = $_GET["orderId"];
$sname = $_GET["sname"];
$fname = $_GET["fname"];
$email = $_GET["email"];
$vatExempt = ( $_GET["vatExempt"] == "true" ? "1" : "0" );
$internal = ( $_GET["internal"] == "true" ? "1" : "0" );

// echo('<br>'.$orderId);
// echo('<br>'.$modelId);
// echo('<br>'.$sizeId);
// echo('<br>'.$basePrice);
// echo('<br>'.$frameOnly);
// echo('<br>'.$internal);


$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderStatusUpdateStatement = "UPDATE orders set fname = ?, sname = ?, vat_exempt = ?, internal = ?, email = ? where id = ?";
if (!($stmt = $mysqli->prepare($orderStatusUpdateStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("ssiisi", $fname, $sname, $vatExempt, $internal, $email, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();
$mysqli->close();	

	
include ( __DIR__.'/../panels/customer_details_panel.php' );

?>