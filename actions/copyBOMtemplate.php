<?php

require('../configuration.php');

$templateId = $_GET["templateId"];
$templateName = $_GET["templateName"];

if(empty ($templateName) ){
	$templateName = 'new template';
}

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$insertStatement = "INSERT into component_template_details(name) values ( ? )";
if (!($stmt = $mysqli->prepare($insertStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	

if (!$stmt->bind_param("s", $templateName )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$newTemplateId = $stmt->insert_id;
$stmt->close();

$insertStatement2 = "INSERT INTO component_template ( model, item, qty, template_id ) 
				SELECT model, item, qty, ".$newTemplateId." as template_id from component_template where template_id = ".$templateId;

if (!($stmt = $mysqli->prepare($insertStatement2))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
$stmt->execute();
$stmt->close();

$mysqli->close();

$header = 'Location: /editcomponenttemplates.php'; 	

header($header, TRUE, 302);
exit();

?>