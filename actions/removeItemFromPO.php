<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$lineId = $_GET["lineId"];	
$poId = $_GET["po_id"];	

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$addItem = "delete from inv_po_lines where id = ".$lineId;

if (!($stmt = $mysqli->prepare($addItem))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
$stmt->execute();
	
$header = 'Location: /purchaseOrder.php?po_id='.$poId; 

header($header, TRUE, 302);
exit();
?>