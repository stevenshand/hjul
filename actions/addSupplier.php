<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$header = 'Location: /suppliers.php'; 	

$name = $_GET["name"];
$country = $_GET["country"];
$contact= $_GET["contact"];
$email = $_GET["email"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$insertStatement = "INSERT into inv_suppliers(name, contact_name, email_address, country ) values ( ?, ?, ?, ? )";
if (!($stmt = $mysqli->prepare($insertStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("sssi", $name, $contact, $email, $country )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	
$stmt->execute();
$stmt->close();
$mysqli->close();

header($header, TRUE, 302);
exit();

?>