<?php

require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';

/*
1 | ordered           
2 | materials on hand 
3 | fabrication       
4 | finishing         
5 | paint             
6 | assembly          
7 | complete          
8 | shipped
*/
	
$status = 8;// count orders with a status less than this. 

//get all orders where status less that $status

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

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

//echo( $bomItemQuery );

if (!($stmt = $mysqli->prepare($bomItemQuery))) {
    echo "1 Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->bind_param("i", $status )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}	

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($item, $qty, $itemId, $stock, $onOrder );
$stmt->store_result();


include 'inc/header.php';


?>

<div class="container">
	<h4>BOM report for all unshipped orders.</h4>
	<div class="row">
		<a href="exportbomreport.php">
			<button title="Export BOM Report" class="btn btn-default">Export</button>
		</a>
	</div>	
	<div class="row">
	<table class="table table-bordered table-condensed">
		<tr><th>Item</th><th>SKU</th><th>Required</th><th>In Stock</th><th>Delta</th><th>On Order</th></tr>
		<?php while ($stmt->fetch()) { ?>
			<tr>
				<td><?php echo $item ?></td>
				<td><?php echo $itemId ?></td>
				<td><?php echo $qty ?></td>
				<td><?php echo $stock ?></td>
				<?php $class=($stock-$qty<0 ? "bg-danger" : "" ) ?>
				<td class="<?php echo $class ?>"><?php echo $stock-$qty ?></td>
				<td><?php echo $onOrder ?></td>
			</tr>
		<?php } ?>
	</table>	
</div>

<?php $stmt->close(); ?>

<?php include 'inc/footer.php'; ?>
