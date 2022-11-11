<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$templateId = $_GET["templateId"];	
$itemId = $_GET["itemId"];	
$qty = $_GET["qty"];	

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$addItem = "delete from component_template where template_id = ".$templateId." AND item = ".$itemId." AND qty = ".$qty.' limit 1';

if (!($stmt = $mysqli->prepare($addItem))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
$stmt->execute();
	
$header = 'Location: /componenttemplate.php?templateId='.$templateId; 

header($header, TRUE, 302);
exit();
?>