<?php
require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';

$itemId=$_GET["itemId"];
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$itemQuery = 
"SELECT inv_items.id,
		short_name,
		description,
		variation,
		qty,
		livi_stock,
		cost,
		rrp,
		supplier,
		inv_suppliers.name,
		supplier_code,
		inv_items.category,
		inv_categories.name,
		inv_location.location,
		inv_items.location
FROM 	inv_items
LEFT JOIN inv_location
ON inv_items.location = inv_location.id
LEFT JOIN inv_suppliers
ON inv_items.supplier = inv_suppliers.id
LEFT JOIN inv_categories
ON inv_items.category = inv_categories.id
WHERE inv_items.id=".$itemId; 

if (!($stmt = $mysqli->prepare($itemQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($itemId, $item, $description, $variation, $qty, $livi_stock, $cost, $rrp, $supplierId, $supplier, $supplierCode, $categoryId, $categoryName, $location, $locationId );

$stmt->store_result();
$stmt->fetch();

$countries = fetchCountriesArray();

$bomQuery = 
	"SELECT 
		orderId, sname, fname, orders.model, orders.size, orders.shipping_location, status
	FROM order_bom
	LEFT JOIN 
		orders 
	ON orders.id = order_bom.orderId
	WHERE item = ".$itemId."
	AND orders.status < 8
	GROUP BY orders.id
	ORDER BY orders.status DESC"; 

if (!($bomStmt = $mysqli->prepare($bomQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$bomStmt->execute()) {
    echo "Execute failed: (" . $bomStmt->errno . ") " . $bomStmt->error;
}

$bomStmt->bind_result($orderId, $sname, $fname, $model, $size, $shippingLocation, $status);
$bomStmt->store_result();
$bomStmt->fetch();

$models = fetchModelsArray();
$sizes = fetchSizesArray();
$statuses = fetchStatusArray();

$stockOut = fetchStockOut($itemId);
$stockIn = fetchStockIn($itemId);



include 'inc/header.php';

?>

<?php include "inc/modals/newItemM.php" ?>

<div class="container">
	
	<div class="row">
		<div class="col-md-12">
			<button title="Add New Item" data-toggle="modal" data-target="#newItemModal" class="btn btn-default">Add New Item</button>
			<button title="View Suppliers" class="btn btn-default"><a href="/suppliers.php">View Suppliers</a></button>
			<button title="View Items" class="btn btn-default"><a href="/inventory.php">View Items</a></button>
		</div>	
	</div>	

	<hr>
	
	<div class="row">
		<div class="col-md-6">
			<dl class="dl-horizontal editable">

				<dt>Short Name</dt>
				<dd data-type="text" data-title="Item Shortname" data-name="short_name" ><?php echo $item ?></dd>

				<dt>Category</dt>
				<dd data-type="select" data-title="Category" data-source="data/simpleCategoryList.php" data-name="category" data-value="<?php echo $categoryId ?>"></dd>

				<dt>Description</dt>
				<dd data-type="textarea" data-showbuttons="true" data-title="Description" data-name="description" ><?php echo $description ?></dd>

				<dt>Supplier</dt>
				<dd data-type="select" data-title="Supplier" data-source="data/simpleSupplierList.php" data-name="supplier" data-value="<?php echo $supplierId ?>"></dd>

				<dt>Supplier Code</dt>
				<dd data-type="text" data-title="Supplier Code" data-name="supplier_code" data-value="<?php echo $supplierCode ?>"></dd>

				<dt>Variation</dt>
				<dd data-type="text" data-title="Variation" data-name="variation" ><?php echo $variation ?></dd>

				<dt>Location</dt>
				<dd data-type="select" data-title="Location" data-source="data/locationList.php" data-name="location" data-value="<?php echo $locationId ?>"></dd>

				<dt>RRP</dt>
				<dd data-type="text" data-title="RRP" data-name="rrp" ><?php echo $rrp ?></dd>

				<dt>Cost</dt>
				<dd data-type="text" data-title="Cost" data-name="cost" ><?php echo $cost ?></dd>

				<dt>Quantity in Stock</dt>
				<dd data-type="text" data-title="Quantity" data-name="qty" ><?php echo $qty ?></dd>

				<dt>Moat Hall Stock</dt>
				<dd data-type="text" data-title="Moat Hall Stock" data-name="livi_stock" ><?php echo $livi_stock ?></dd>
			</dl>	
			<h4 class="text-center">SKU:<?php echo $itemId ?></h4>
		</div>	
		
		<div class="col-md-6">
			<table id="orderTable" class="table table-bordered table-condensed">
				<tbody>
					<tr>
						<th>In BOM</th><th>Model</th><th>Size</th><th>Shipping</th><th>Status</th>
					</tr>	
						<?php while ($bomStmt->fetch()) { ?>
					<tr>
						<td><a href="viewbom.php?orderId=<?php echo $orderId ?>"><?php echo $fname ?> <?php echo $sname ?></a></td>
						<td><?php echo $models[$model] ?></td>		
						<td><?php echo $sizes[$size] ?></td>		
						<td><?php echo ( $shippingLocation == 0 ? "Moat Hall" : "Other" ) ?></td>		
						<td><?php echo $statuses[$status] ?></td>		
					</tr>
						<?php } ?>
				</tbody>	
			</table>	
		</div>


		<div class="col-md-6">
			<table id="goodsInTable" class="table table-bordered table-condensed">
				<tbody>
					<tr>
						<th>Stock In Date</th><th>Quantity</th>
					</tr>	
					<?php foreach ($stockIn as $move) {?>
					<tr>
						<td><?php echo date( DATEFORMAT, $move->date ) ?></td>
						<td><?php echo $move->qty ?></td>		
					</tr>
					<?php } ?>
				</tbody>	
			</table>	
		</div>

		<div class="col-md-6">
			<table id="goodsOutTable" class="table table-bordered table-condensed">
				<tbody>
					<tr>
						<th>Stock Out Date</th><th>Quantity</th>
					</tr>	
					<?php foreach ($stockOut as $move) {?>
					<tr>
						<td><?php echo date( DATEFORMAT, $move->date ) ?></td>
						<td><?php echo $move->qty ?></td>		
					</tr>
					<?php } ?>
				</tbody>	
			</table>	
		</div>

	</div>	
</div>	

<script>

$(document).ready(function() {
    $('dl.editable dd').editable({
    	url: 'actions/editItem.php',
    	showbuttons: false,
		pk : '<?php echo $itemId ?>',
		error: function(response, newValue) {
		    if(response.status === 500) {
		        return 'Service unavailable. Please try later.';
		    } else {
		        return "an error occurred, probably duplicate value";
		    }
		}
    });
});
</script>


<?php $stmt->close(); ?>

<?php include 'inc/footer.php'; ?>
