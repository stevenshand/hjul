<?php

require '../../configuration.php';
require 'definitions.php';

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderListQuery = 
"SELECT address.order_id,
		longitude,
		latitude
FROM 	address";

// echo($orderListQuery );

if (!($stmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($orderId, $longitude, $latitude );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

$surveyData = array();

while ($stmt->fetch()) { 
	$weight = 1;
	
	array_push( $surveyData, new Feature($weight,$orderId,"",$latitude,$longitude) );
}
	
$featureCollection = new FeatureCollection();
$featureCollection->setFeatures($surveyData); 

echo json_encode($featureCollection);

?>
