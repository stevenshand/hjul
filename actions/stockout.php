<?php

require('../configuration.php');

$orderId = $_POST["orderId"];
$loc = $_POST["loc"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sql = "UPDATE orders SET stockout = 1 - stockout where id = ?";
if (!($stmt = $mysqli->prepare($sql))) {
    echo "1 Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}	
	
if (!$stmt->bind_param("i", $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

$stmt->execute();
$stmt->close();

$itemListQuery = 
"SELECT inv_items.id,
		short_name,
		description,
		variation,
		inv_items.qty,
		order_bom.qty,
		supplier,
		inv_suppliers.name,
		cost,
		sum(cost*order_bom.qty),
		category,
		inv_categories.name as cn,
		supplier_code
FROM 	inv_items
LEFT JOIN inv_categories
ON inv_items.category = inv_categories.id 
LEFT JOIN inv_suppliers
ON inv_items.supplier = inv_suppliers.id
LEFT JOIN order_bom
ON order_bom.item = inv_items.id
WHERE order_bom.orderId =".$orderId.  	
" GROUP BY inv_items.id
ORDER BY cn, short_name, variation";

//echo( $itemListQuery );

if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "2 Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($itemId, $item, $description, $variation, $stockLevel, $qty, $supplierId, $supplier, $cost, $totalItemCost, $categoryId, $categoryName, $supplierCode );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

while ($stmt->fetch()) { 
	updateStockLevel($itemId,$qty);
	if( $loc == 0 ){
		updateStockLocationLevel($itemId,$qty);
	}
}

$stmt->close();
$mysqli->close();	

$form_data['success'] = true;
$form_data['orderId'] = $orderId;

$form_data['msg'] = 'stockout updated';
echo json_encode($form_data);

function updateStockLevel( $itemId, $outQty ){

	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$sql = "update inv_items set qty = qty - ? where id = ?";

	if (!($stmt = $mysqli->prepare($sql))) {
	    echo "3 Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("ii", $outQty, $itemId ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();

	$sql = "insert into goods_out values ( ?, ?, ?, now() )";
	if (!($stmt = $mysqli->prepare($sql))) {
	    echo "4 Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("iii", $orderId, $itemId, $outQty ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();

}

function updateStockLocationLevel( $itemId, $outQty ){

	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$sql = "update inv_items set livi_stock = livi_stock - ? where id = ?";

	if (!($stmt = $mysqli->prepare($sql))) {
	    echo "5 Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("ii", $outQty, $itemId ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();

	$sql = "insert into goods_out values ( ?, ?, ?, now() )";
	if (!($stmt = $mysqli->prepare($sql))) {
	    echo "6 Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	if (!$stmt->bind_param("iii", $orderId, $itemId, $outQty ) ) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}	

	$stmt->execute();

}

?>