<?php

require('../configuration.php');
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_GET["orderId"];

$addressLine1 = $_GET["addressLine1"];
$addressLine2 = $_GET["addressLine2"];
$addressLine3 = $_GET["addressLine3"];
$addressTown = $_GET["addressTown"];
$addressPostcode = $_GET["addressPostcode"];
$addressCountry = $_GET["addressCountry"];
$addressTel1 = $_GET["addressTel1"]; 
$addressTel2 = $_GET["addressTel2"]; 


$countries = fetchCountriesArray();
$country = $countries[$addressCountry];
 
//$geoData = fetchGeoData($addressTown,$country);

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$statement = "delete from address where order_id = ".$orderId;
if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
$stmt->execute();

$statement = "insert into address (line1, line2, line3, town, postcode, country, tel1, tel2, order_id, longitude, latitude )
	values( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";  

if (!($stmt = $mysqli->prepare($statement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("sssssississ", $addressLine1, $addressLine2, $addressLine3, $addressTown, $addressPostcode, $addressCountry, $addressTel1, $addressTel2, $orderId, $geoData['longitude'], $geoData['latitude'] )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();
$mysqli->close();	
	
include ( __DIR__.'/../panels/address_details_panel.php' );

?>