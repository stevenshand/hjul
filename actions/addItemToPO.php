<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$poId = $_GET["po_id"];	
$itemId = $_GET["itemId"];
$qty = 1;

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$addItem = "insert into inv_po_lines ( po_id, item_id, qty ) values ( ?, ?, ? )";
if (!($stmt = $mysqli->prepare($addItem))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("iii", $poId, $itemId, $qty ) ) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
	
$header = 'Location: /purchaseOrder.php?po_id='.$poId; 

header($header, TRUE, 302);
exit();
?>