<?php

require('../configuration.php');

$orderId = $_GET["orderId"];
$shippingId = $_GET["shippingId"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$statement = "DELETE from shipping where id = ".$shippingId;
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
$stmt->execute();


$query = "select id, UNIX_TIMESTAMP(shipping_date) as sd, method, tracking_number from shipping where order_id = ".$orderId; 

//echo $query;

if (!($statement = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$statement->execute()) {
    echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
}

$statement->bind_result( $id, $shippingDate, $method, $trackingNumber );
$statement->store_result();
$resultsSize = $statement->num_rows;

if( $resultsSize < 1 ){
	$statement = "update orders set status = 6 where id = ?";
	if (!($stmt = $mysqli->prepare($statement))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	

	
	if (!$stmt->bind_param("i", $orderId ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();
}
	
	

include('../shipping.php');

?>