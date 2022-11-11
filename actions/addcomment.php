<?php

$orderId = $_GET["orderId"];
$comment = $_GET["comment"];


require('../configuration.php');


$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$statement = "insert into comms ( order_id, comment, date ) values ( ?, ?, NOW() )";
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	

	
if (!$stmt->bind_param("is", $orderId, $comment ) ) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();

include('../comments.php');

?>