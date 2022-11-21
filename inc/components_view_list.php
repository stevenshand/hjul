<?php
require_once __DIR__.'/../functions/invfn.php';

$encodedOrderId = $_GET["orderId"];
$orderId = orderIdfromUUID($uuid);

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$itemListQuery =
	"SELECT 
		short_name,
		order_bom.qty,
		inv_categories.name as cn,
        inv_categories.category_group
FROM 	inv_items
LEFT JOIN inv_categories
ON inv_items.category = inv_categories.id 
LEFT JOIN order_bom
ON order_bom.item = inv_items.id
WHERE order_bom.orderId =".$orderId.
" AND inv_categories.category_group = 1    
GROUP BY order_bom.id 
 ORDER BY cn, short_name, variation";

//echo( $itemListQuery );

if (!($stmt = $mysqli->prepare($itemListQuery))) {
	echo "1 Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
	echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($item, $qty, $categoryName, $group );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

?>

<table class="table table-striped" style="width:100%">

<?php
while ($stmt->fetch()) {
	?>
	<tr>
		<td style="font-size:smaller;"><?php echo $categoryName ?></td>
		<td><?php echo $item  ?></td>
		<td><?php echo ( trimForCSV($qty) ) ?></td>
	</tr>
<?php }?>
</table>


