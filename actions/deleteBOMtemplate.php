<?php

require('../configuration.php');

$templateId = $_GET["templateId"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$deleteTemplateStatement = "DELETE from component_template_details where id = ".$templateId;
if (!($stmt = $mysqli->prepare($deleteTemplateStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
$stmt->execute();


$deleteTemplateEntriesStatement = "DELETE from component_template where template_id = ".$templateId;
if (!($stmt = $mysqli->prepare($deleteTemplateEntriesStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
$stmt->execute();


$header = 'Location: /editcomponenttemplates.php'; 	

header($header, TRUE, 302);
exit();

?>