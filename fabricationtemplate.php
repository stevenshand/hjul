<?php

require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';
	
$model= $_GET["model"];	

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$itemListQuery = 
"SELECT fab_items.id,
		short_name,
		description,
		variation,
		fab_template.qty,
		supplier,
		inv_suppliers.name,
		cost,
		sum(cost*fab_template.qty),
		category,
		fab_categories.name as cn,
		supplier_code
FROM 	fab_items
LEFT JOIN fab_categories
ON fab_items.category = fab_categories.id 
LEFT JOIN inv_suppliers
ON fab_items.supplier = inv_suppliers.id
LEFT JOIN fab_template
ON fab_template.item = fab_items.id
WHERE fab_template.model =".$model.  	
" GROUP BY fab_items.id
ORDER BY cn, short_name, variation";

//echo( $itemListQuery );

if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($itemId, $item, $description, $variation, $qty, $supplierId, $supplier, $cost, $totalItemCost, $categoryId, $categoryName, $supplierCode );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

//$countries = fetchCountriesArray();
$allSuppliers = fetchAllSuppliers(true);
$allCategories = fetchAllCategories();
$modelName = fetchModelName($model);

include 'inc/header.php';
?>

<?php include "inc/modals/newFabTemplateBOMItemM.php" ?>

<div class="container">
	
	<div class="row">
		<div class="col-md-12">
			<h3>Fabrication template for <?php echo $modelName ?></h3>
			<button title="add new BOM Item" data-toggle="modal" data-target="#newBOMItemModal" class="btn btn-default">Add BOM Item</button>
		</div>	
	</div>	

	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th>Category</th><th>SKU</th><th>Item</th><th>Supplier</th><th>Supplier Code</th><th>Variation</th><th>QTY</th><th>Cost</th><th>Total</th>
					</tr>
			
					<?php
						$stockValuation = 0;
						while ($stmt->fetch()) { 
							$stockValuation += ($qty*$cost);  
							
							?>
						<tr>
							<td><?php echo $categoryName ?></td>

							<td><a href="/fabitem.php?itemId=<?php echo $itemId ?>"><?php echo $itemId ?></a></td>
							
							<td data-toggle="tooltip" 
								data-html="true" 
								data-container="body" 
								data-placement="right" 
								title="<?php echo $description ?>"><?php echo $item  ?></td>
							
							<td><?php echo $supplier ?></td>

							<td><?php echo $supplierCode ?></td>
							
							<td><?php echo $variation ?></td>

							<td><?php echo $qty ?></td>
							
							<td><?php echo $cost ?></td>
							
							<td><?php echo ($qty*$cost) ?></td>								 
						</tr>
					<?php }?>
						<tr>
							<td class="text-right" colspan="8">Total</td>
							<td colspan="9"><?php echo ( $stockValuation ) ?></td>
						</tr>
				</tbody>			
				</table>	
			</div>	
		</div>	
	</div>	
</div>	

<script>

$(document).ready(function() {
	$('[data-toggle="tooltip"]').tooltip();
	
	$('a.editable').editable({
    	url: 'actions/editFabItem.php',
    	showbuttons: false
    });
});
</script>

<?php $stmt->close(); ?>

<?php include 'inc/footer.php'; ?>
