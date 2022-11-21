<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$header = 'Location: /'; 	
$statusId = 1;
$fname = $_GET["fname"];
$sname = $_GET["sname"];
$email = $_GET["email"];
$modelId = $_GET["modelId"];
$sizeId = $_GET["sizeId"];
$frameOnly = isset( $_GET["frameOnly"]) ? $_GET["frameOnly"] : 0;

$orderDate = date(INPUTFIELDDATEFORMAT);
$targetDate = date( INPUTFIELDDATEFORMAT,  date(strtotime("+".LEADTIME." Months") ) );

$frameOnly = ( isset($_GET['frameOnly'] ) ? "1" : "0" );

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderAddInsertStatement = "INSERT into orders(fname, sname, email, model, size, status, order_date, target_date, frame_only, uuid ) values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, (select uuid()) )";
if (!($stmt = $mysqli->prepare($orderAddInsertStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->bind_param("sssiiissi", $fname, $sname, $email, $modelId, $sizeId, $statusId,  $orderDate, $targetDate, $frameOnly )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
$stmt->execute();
$stmt->close();
$mysqli->close();
header($header, TRUE, 302);
exit();

?>