<?php

require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$status = 8;// count orders with a status less than this. 

$bomItemQuery = 
"SELECT 
		inv_items.short_name,
		sum(order_bom.qty) as sumqy,
		inv_items.id,
		inv_items.qty,
		inv_po_lines.qty
FROM 	order_bom
LEFT JOIN inv_items
ON inv_items.id = order_bom.item 
LEFT JOIN orders
ON orders.id = order_bom.orderId
LEFT JOIN status
ON status.id = orders.status
LEFT JOIN inv_po_lines
ON inv_po_lines.item_id = order_bom.item
WHERE orders.status < ?
GROUP BY order_bom.item
ORDER BY sumqy DESC";
	
if (!($stmt = $mysqli->prepare($bomItemQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->bind_param("i", $status )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($item, $required, $itemId, $instock, $onOrder );
$stmt->store_result();

$list = 'Item,SKU,Required,In Stock,Delta,On Order'.PHP_EOL;

header('Content-Type: text/csv'); // you can change this based on the file type
header('Content-Disposition: attachment; filename="inventory_export.csv"');

while ($stmt->fetch()) { 
	$delta = $instock-$required;
	$list = 	$list
					.trimForCSV($item).","
					.trimForCSV($itemId).","
					.trimForCSV($required).","
					.trimForCSV($instock).","
					.trimForCSV($delta).","
					.trimForCSV($onOrder).PHP_EOL;
}	

echo $list;

exit();



?>