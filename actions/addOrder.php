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
$frameOnly = $_GET["frameOnly"];
$orderDate = date(INPUTFIELDDATEFORMAT);
$targetWeek = nowWeek()+LEADTIME;

$frameOnly = ( isset($_GET['frameOnly'] ) ? "1" : "0" );
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderAddInsertStatement = "INSERT into orders(fname, sname, email, model, size, status, order_date, target_week, frame_only, uuid ) values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, (select uuid()) )";
if (!($stmt = $mysqli->prepare($orderAddInsertStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("sssiiisii", $fname, $sname, $email, $modelId, $sizeId, $statusId,  $orderDate, $targetWeek, $frameOnly )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	
$stmt->execute();
$stmt->close();
$mysqli->close();
header($header, TRUE, 302);
exit();

?>