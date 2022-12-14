<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$header = 'Location: /categories.php'; 	

$name = $_GET["name"];
$group = $_GET["group"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$insertStatement = "INSERT into inv_categories(name, category_group) values ( ?, ? )";
if (!($stmt = $mysqli->prepare($insertStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	

if (!$stmt->bind_param("si", $name, $group )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	


$stmt->execute();
$itemId = $stmt->insert_id;
$stmt->close();

$mysqli->close();

header($header, TRUE, 302);
exit();

?>