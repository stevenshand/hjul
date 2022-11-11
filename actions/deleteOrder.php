<?php

require('../configuration.php');

$orderId = $_GET["orderId"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
/*---- copy into archived table -----*/
$insertArchivedStmt = "
	insert into archived_orders select * from orders where id = ?;";
if (!($stmt = $mysqli->prepare($insertArchivedStmt))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}		
if (!$stmt->bind_param("i", $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}		
$stmt->execute();

/*---- delete from Rohloff Serials-----*/

$deleteRohloffSerial = "delete from rohloff_serial where orderId = ?;";
if (!($stmt = $mysqli->prepare($deleteRohloffSerial))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$stmt->bind_param("i", $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
$stmt->execute();

/*---- delete from Order BOM -----*/

$deleteOrderBOM = "delete from order_bom where orderId = ?;";
if (!($stmt = $mysqli->prepare($deleteOrderBOM))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$stmt->bind_param("i", $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
$stmt->execute();

/*---- delete from Order FAB BOM -----*/

$deleteOrderFABBOM = "delete from order_fab_bom where orderId = ?;";
if (!($stmt = $mysqli->prepare($deleteOrderFABBOM))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$stmt->bind_param("i", $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
$stmt->execute();

/*---- delete from Shipping -----*/

$deleteShipping = "delete from shipping where order_id = ?;";
if (!($stmt = $mysqli->prepare($deleteShipping))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$stmt->bind_param("i", $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
$stmt->execute();

/*---- delete from Shipping -----*/

$deleteShippingAddress = "delete from shipping_address where order_id = ?;";
if (!($stmt = $mysqli->prepare($deleteShippingAddress))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$stmt->bind_param("i", $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
$stmt->execute();

/*---- delete Order -----*/

$deleteOrder = "delete from orders where id = ?;";
if (!($stmt = $mysqli->prepare($deleteOrder))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$stmt->bind_param("i", $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
$stmt->execute();


$header = 'Location: /'; 	

header($header, TRUE, 302);
exit();

?>