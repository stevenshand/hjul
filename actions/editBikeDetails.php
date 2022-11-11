<?php

require('../configuration.php');

$orderId = $_GET["orderId"];
$modelId = $_GET["modelId"];
$sizeId = $_GET["sizeId"];
$basePrice = $_GET["basePrice"];
$frameNumber = $_GET["frameNumber"];
$frameOnly = ( $_GET["frameOnly"] == "true" ? "1" : "0" );
$sizeConfirmed = ( $_GET["sizeConfirmed"] == "true" ? "1" : "0" );

// echo('<br>'.$orderId);
// echo('<br>'.$modelId);
// echo('<br>'.$sizeId);
// echo('<br>'.$basePrice);
// echo('<br>'.$frameOnly);
// echo('<br>'.$sizeConfirmed);
//
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderStatusUpdateStatement = "UPDATE orders set model = ?, size = ?, frame_only = ?, base_price = ?, frame_number = ?, size_confirmed = ? where id = ?";
if (!($stmt = $mysqli->prepare($orderStatusUpdateStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("iiissii", $modelId, $sizeId, $frameOnly, $basePrice, $frameNumber, $sizeConfirmed, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();
$mysqli->close();	

	
include ( __DIR__.'/../panels/bike_details_panel.php' );

?>