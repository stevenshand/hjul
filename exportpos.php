<?php

require __DIR__.'/configuration.php';
require __DIR__.'/functions/harefn.php';
require __DIR__.'/functions/invfn.php';

$sortField = $_GET["sortField"] ? $_GET["sortField"] : "supplier";

$sortCriteria = "inv_suppliers.name ASC"; 	

if( $sortField == "expected_date" ){
	$sortCriteria = "inv_purchase_orders.expected_date ASC"; 	
}else if ( $sortField == "create_date" ){
	$sortCriteria = "inv_purchase_orders.create_date ASC"; 	
}	

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$poItems = 
"SELECT 	inv_purchase_orders.id,
			inv_purchase_orders.create_date,
			inv_items.supplier_code,
			inv_suppliers.name,
			item_id,
			inv_items.short_name,
			inv_po_lines.qty,	
			inv_items.cost,
			(inv_items.cost*inv_po_lines.qty),
			inv_po_status.status,
			inv_purchase_orders.reference,
			inv_purchase_orders.expected_date
FROM 	inv_po_lines
LEFT JOIN inv_items
ON inv_items.id = inv_po_lines.item_id
LEFT JOIN inv_suppliers
ON inv_suppliers.id = inv_items.supplier
LEFT JOIN inv_purchase_orders
ON inv_purchase_orders.id = inv_po_lines.po_id
LEFT JOIN inv_po_status
ON inv_po_status.id = inv_purchase_orders.status
WHERE inv_purchase_orders.status = 2
ORDER BY ".$sortCriteria." , 
inv_purchase_orders.id ASC";



if (!($stmt = $mysqli->prepare($poItems))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($po_id, $createDate, $supplierCode, $supplierName, $itemId, $shortName, $qty, $cost, $total, $status, $reference, $expectedDate ) ;
$stmt->store_result();
$resultsSize = $stmt->num_rows;

$filename = "open_purchase_orders.csv";
header('Content-type: text/csv');
header('Content-Disposition: attachment; filename='.$filename);

$fp = fopen('php://output', 'w');


fwrite($fp, "Date,Reference, Expected, Supplier, SKU, Supplier Code, Item,Qty,Cost,Total,Status".PHP_EOL );
while ($stmt->fetch()) {
	$line = 	 	 trimForCSV($createDate).","
				 	.trimForCSV($reference).","
					.trimForCSV($expectedDate).","
					.trimForCSV($supplierName).","
					.trimForCSV($itemId).","
					.trimForCSV($supplierCode).","
					.trimForCSV($shortName).","
					.trimForCSV($qty).","
					.trimForCSV($cost).","
					.trimForCSV($total).","
					.trimForCSV($status);
	fwrite($fp, $line.PHP_EOL );
}

?>

