<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$searchString = $_GET["search"];	

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = "select 
			inv_items.id, 
			short_name, 
			cost, 
			inv_suppliers.name 
		from 
			inv_items 
		LEFT JOIN inv_suppliers
		ON inv_items.supplier = inv_suppliers.id	
		where 
			inv_items.id ='".$searchString."' OR short_name like '%".$searchString."%'";

// echo $query;

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $shortName, $cost, $supplier );
$stmt->store_result();
	
$resultsSize = $stmt->num_rows;

include('../BOMSearchResults.php');


?>