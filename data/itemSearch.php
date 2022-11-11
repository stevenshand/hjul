<?php 

require '../configuration.php';
require '../functions/harefn.php';
require '../functions/invfn.php';

$term = $_GET["term"];	

if( strlen($term) < 3 ){
	echo("");
}else{

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$itemSearchQuery = 
	"SELECT short_name, id, variation, sku, supplier_code, cost FROM inv_items 
		where 
		short_name like '%".$term."%'
		OR
		sku like '%".$term."%'
		OR
		id like '%".$term."%'
		OR
		description like '%".$term."%'
		OR
		supplier_code like '%".$term."%'
		"; 

	if (!($stmt = $mysqli->prepare($itemSearchQuery))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	
	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$stmt->bind_result($item, $id, $variation, $sku, $supplierCode, $cost);
	$stmt->store_result();
	$resultsSize = $stmt->num_rows;
	
	$items = array();
	while ($stmt->fetch()) {
		// $label = $sku.", ".$item.", ".$variation.", ".$supplierCode.", ".$cost;
		$line['label'] = $id." - ".$item;
		$line['value'] = $id;
		$line['item'] = $item;
		$line['id'] = $id;
		array_push( $items, $line );
	}
	
	echo json_encode($items);
}


?>