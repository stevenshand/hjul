<?php

require __DIR__.'/configuration.php';
require __DIR__.'/functions/harefn.php';
require __DIR__.'/functions/invfn.php';
	
$id = $_GET["poid"];	
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$itemListQuery = 
"SELECT inv_po_lines.qty,
		inv_items.supplier_code
FROM 	inv_po_lines
LEFT JOIN inv_items
ON inv_po_lines.item_id = inv_items.id
WHERE po_id = ".$id;

if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($qty, $code );
$stmt->store_result();
$resultsSize = $stmt->num_rows;


$filename = "po_".$id."_csv.csv";

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);

$fp = fopen('php://output', 'w');	

while ($stmt->fetch()) {
	fwrite($fp, $code.",".$qty.PHP_EOL );
}

?>