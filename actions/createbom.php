<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_GET["orderId"];
$templateId = $_GET["templateId"];
// $model = fetchModelId($orderId);


$header = 'Location: /viewbom.php?orderId='.$orderId; 	

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$selectStatement = "select model, item, qty, template_id from component_template where template_id = ".$templateId;


if (!($stmt = $mysqli->prepare($selectStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($model, $item, $qty, $templateId );
$stmt->store_result();
$resultsSize = $stmt->num_rows;


while ($stmt->fetch()) {
	addBOMLine( $orderId, $model, $item, $qty, $templateId );	
}


function addBOMLine($orderId, $model, $item, $qty, $templateId ){
	// echo( "adding line :".$orderId.", ".$model.", ".$item.", ".$qty );

	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$insertStatement = "insert into order_bom ( orderId, model, item, qty, template_id ) values ( ?, ?, ?, ?, ? )";
	
	if (!($stmt = $mysqli->prepare($insertStatement))) {
		$err = "err2";
	}	

	if (!$stmt->bind_param("iiiii", $orderId, $model, $item, $qty, $templateId )) {
		$err = "err3";
	}	

	if (!$stmt->execute()) {
	    $err = $stmt->error;
	}

	return;
	
}

$stmt->close();
$mysqli->close();

header($header, TRUE, 302);
exit();

?>