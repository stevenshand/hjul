<?php

require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$itemListQuery = 
	"SELECT inv_items.id,
			short_name,
			description,
			variation,
			qty,
			qty_bak,
			supplier,
			inv_suppliers.name,
			cost,
			rrp,
			sum(cost*qty),
			category,
			inv_categories.name as cn,
			supplier_code
	FROM 	inv_items
	LEFT JOIN inv_categories
	ON inv_items.category = inv_categories.id 
	LEFT JOIN inv_suppliers
	ON inv_items.supplier = inv_suppliers.id
	GROUP BY inv_items.id
	ORDER BY cn, short_name, variation";
	
if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result( 	$itemId, 
						$item, 
						$description,
						$variation, 
						$qty, 
						$qty_bak,
						$supplierId, 
						$supplierName, 
						$cost, 
						$rrp, 
						$total, 
						$categoryId, 
						$categoryName,
						$supplierCode
					);


$stmt->store_result();
$resultsSize = $stmt->num_rows;

$list = 'SKU,Item,Catgeory,Supplier,Supplier Code,Description,Variation,QTY,QTY_PREV,Cost,Total'.PHP_EOL;

header('Content-Type: text/csv'); // you can change this based on the file type
header('Content-Disposition: attachment; filename="inventory_export.csv"');

while ($stmt->fetch()) { 
	$list = 	$list
					.trimForCSV($itemId).","
					.trimForCSV($item).","
					.trimForCSV($categoryName).","
					.trimForCSV($supplierName).","
					.trimForCSV($supplierCode).","
					.trimForCSV($description).","
					.trimForCSV($variation).","
					.trimForCSV($qty).","
					.trimForCSV($qty_bak).","
					.trimForCSV($cost).","
					.trimForCSV($total).PHP_EOL;
}	

echo $list;

exit();



?>