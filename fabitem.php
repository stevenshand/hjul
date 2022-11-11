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
"SELECT fab_items.id,
		short_name,
		description,
		variation,
		qty,
		cost,
		rrp,
		supplier,
		inv_suppliers.name,
		supplier_code,
		fab_items.category,
		fab_categories.name
FROM 	fab_items
LEFT JOIN inv_suppliers
ON fab_items.supplier = inv_suppliers.id
LEFT JOIN fab_categories
ON fab_items.category = fab_categories.id
WHERE fab_items.id=".$itemId; 

if (!($stmt = $mysqli->prepare($itemQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($itemId, $item, $description, $variation, $qty, $cost, $rrp, $supplierId, $supplier, $supplierCode, $categoryId, $categoryName );

$stmt->store_result();
$stmt->fetch();

include 'inc/header.php';

?>

<?php include "inc/modals/newItemM.php" ?>

<div class="container">
	
	<div class="row">
		<div class="col-md-12">
			<button title="add new Item" data-toggle="modal" data-target="#newItemModal" class="btn btn-default">Add New Item</button>
			<button title="View Suppliers" class="btn btn-default"><a href="/suppliers.php">View Suppliers</a></button>
			<button title="View Items" class="btn btn-default"><a href="/fabinventory.php">View Fabrication Items</a></button>
		</div>	
	</div>	

	<hr>
	
	<div class="row">
		<div class="col-md-6">
			<dl class="dl-horizontal editable">

				<dt>Short Name</dt>
				<dd data-type="text" data-title="Item Shortname" data-name="short_name" ><?php echo $item ?></dd>

				<dt>Category</dt>
				<dd data-type="select" data-title="Category" data-source="data/simpleFabCategoryList.php" data-name="category" data-value="<?php echo $categoryId ?>"></dd>

				<dt>Description</dt>
				<dd data-type="textarea" data-showbuttons="true" data-title="Description" data-name="description" ><?php echo $description ?></dd>

				<dt>Supplier</dt>
				<dd data-type="select" data-title="Supplier" data-source="data/simpleSupplierList.php" data-name="supplier" data-value="<?php echo $supplierId ?>"></dd>

				<dt>Supplier Code</dt>
				<dd data-type="text" data-title="Supplier Code" data-name="supplier_code" data-value="<?php echo $supplierCode ?>"></dd>

				<dt>Variation</dt>
				<dd data-type="text" data-title="Variation" data-name="variation" ><?php echo $variation ?></dd>

				<dt>RRP</dt>
				<dd data-type="text" data-title="RRP" data-name="rrp" ><?php echo $rrp ?></dd>

				<dt>Cost</dt>
				<dd data-type="text" data-title="Cost" data-name="cost" ><?php echo $cost ?></dd>

				<dt>Quantity in Stock</dt>
				<dd data-type="text" data-title="Quantity" data-name="qty" ><?php echo $qty ?></dd>

			</dl>	
			<h4 class="text-center">SKU:<?php echo $itemId ?></h4>
		</div>	
	</div>	
</div>	

<script>

$(document).ready(function() {
    $('dl.editable dd').editable({
    	url: 'actions/editFabItem.php',
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
