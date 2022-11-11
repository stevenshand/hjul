<?php
	
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	
	
require ( '../configuration.php' );
include_once ( 'harefn.php' ); 
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderListQuery = 
"SELECT address.id,
		town,
		country,
		country_name,
		longitude,
		latitude
FROM 	address 
LEFT JOIN
		countries
ON address.country = countries.id where latitude is NULL";

// echo($orderListQuery );

if (!($stmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($addressId, $town, $country, $country_name, $longitude, $latitude );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

while ($stmt->fetch()) { ?>

	<p><?php echo($addressId) ?>, <?php echo($town) ?>, <?php echo($country_name) ?>, <?php echo($longitude) ?>, <?php echo($latitude) ?></p>
	
	
<?php 

$points = fetchGeoData( $town, $country_name );
sleep(2);

updateGeoData($points, $addressId );
} ?>


<?php

function updateGeoData($points, $addressId){
	echo( 'updating geo :' );
	pre( $points );
	pre( $addressId );
	
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$insertStatement = "update address set longitude = ?, latitude = ? where id = ?";
	if (!($stmt = $mysqli->prepare($insertStatement))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("ssi", $points['longitude'], $points['latitude'], $addressId )) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();

	$stmt->close();
	$mysqli->close();
	 
}

?>
