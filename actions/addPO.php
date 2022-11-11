<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$header = 'Location: /purchaseorders.php'; 	

$supplier = $_GET["supplier"];
$createDate = date(INPUTFIELDDATEFORMAT);
$author = $_GET["user"];
$reference = $_GET["reference"];
$location = $_GET["location"];
$status = 1;

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$statement = "INSERT into inv_purchase_orders(create_date, supplier, reference, author, status, location ) values ( ?, ?, ?, ?, ?, ? )";
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("ssssii", $createDate, $supplier, $reference, $author, $status, $location )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	
$stmt->execute();
$stmt->close();
$mysqli->close();

header($header, TRUE, 302);
exit();

?>