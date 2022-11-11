<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_GET["orderId"];	
$lineId = $_GET["lineId"];	

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$addItem = "delete from order_bom where id = ".$lineId;

if (!($stmt = $mysqli->prepare($addItem))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
$stmt->execute();
	
$header = 'Location: /viewbom.php?orderId='.$orderId; 

header($header, TRUE, 302);
exit();
?>