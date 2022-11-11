<?php

require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';
	
$templateId= $_GET["templateId"];	

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

/*
$itemListQuery = 
"SELECT inv_items.id,
		short_name,
		description,
		variation,
		component_template.qty,
		supplier,
		inv_suppliers.name,
		cost,
		sum(cost*component_template.qty),
		category,
		inv_categories.name as cn,
		supplier_code
FROM 	inv_items
LEFT JOIN inv_categories
ON inv_items.category = inv_categories.id 
LEFT JOIN inv_suppliers
ON inv_items.supplier = inv_suppliers.id
LEFT JOIN component_template
ON component_template.item = inv_items.id
WHERE component_template.model =".$model.  	
" ORDER BY cn, short_name, variation";
*/

$itemListQuery = 
"SELECT component_template.template_id,
		component_template.item,
		component_template.qty,
		inv_categories.name,
		inv_items.short_name,
		inv_items.description,
		inv_suppliers.name,
		inv_items.supplier_code,
		inv_items.variation,
		inv_items.cost as cst
FROM 	component_template
LEFT JOIN inv_items
ON component_template.item = inv_items.id 
LEFT JOIN inv_categories
ON inv_items.category = inv_categories.id
LEFT JOIN inv_suppliers
ON inv_items.supplier = inv_suppliers.id
WHERE component_template.template_id =".$templateId.  	
" ORDER BY inv_categories.name, inv_items.short_name";

	// inv_items.category

//echo( $itemListQuery );

if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

// $stmt->bind_result($itemId, $item, $description, $variation, $qty, $supplierId, $supplier, $cost, $totalItemCost, $categoryId, $categoryName, $supplierCode );

$stmt->bind_result( $template_id, $itemId, $qty, $categoryName, $item, $description, $supplier, $supplierCode, $variation, $cost);


$stmt->store_result();
$resultsSize = $stmt->num_rows;

//$countries = fetchCountriesArray();
$allSuppliers = fetchAllSuppliers(true);
$allCategories = fetchAllCategories();
// $modelName = fetchModelName($model);
$templateName = fetchTemplateName($templateId);

include 'inc/header.php';
include "inc/modals/newTemplateBOMItemM.php";
include "inc/modals/copyTemplateBOMM.php";
?>

<div class="container">
	
	<div class="row">
		<div class="col-md-12">
			<h3>Component template for <?php echo $templateName ?></h3>
		</div>	
	</div>	


	<div class="row">
		<div class="col-md-12">
			<button title="Add New BOM Item" data-toggle="modal" data-target="#newTemplateBOMItemModal" class="btn btn-default">Add BOM Item</button>
			<button title="Copy This BOM" data-toggle="modal" data-target="#copyTemplateBOMModal" class="btn btn-default">Copy BOM</button>
			<button title="Show BOM Templates" class="btn btn-default"><a href="/editcomponenttemplates.php">Manage BOM Templates</a></button>
</div>
</div>


	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th></th><th>Category</th><th>SKU</th><th>Item</th><th>Supplier</th><th>Supplier Code</th><th>Variation</th><th>QTY</th><th>Cost</th><th>Total</th>
					</tr>
			
					<?php
						$stockValuation = 0;
						while ($stmt->fetch()) { 
							$stockValuation += ($qty*$cost);  
							
							?>
						<tr>
							<td class="center"><a href="actions/removeItemFromTemplateBOM.php?templateId=<?php echo($templateId) ?>&amp;itemId=<?php echo $itemId ?>&amp;qty=<?php echo $qty ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
							<td><?php echo $categoryName ?></td>

							<td><a href="/item.php?itemId=<?php echo $itemId ?>"><?php echo $itemId ?></a></td>
							
							<td data-toggle="tooltip" 
								data-html="true" 
								data-container="body" 
								data-placement="right" 
								title="<?php echo $description ?>"><?php echo $item  ?></td>
							
							<td><?php echo $supplier ?></td>

							<td><?php echo $supplierCode ?></td>
							
							<td><?php echo $variation ?></td>

							<td><?php echo $qty ?></td>
							
							<td>
								<a 	class="editable" 
									data-title="Cost Price" 
									data-name="cost" 
									data-pk="<?php echo $itemId ?>" 
									data-type="text" 
									data-value="<?php echo $cost ?>">
										<?php echo $cost ?>
									</a>
							</td>
							
							<td><?php echo ($qty*$cost) ?></td>								 
						</tr>
					<?php }?>
						<tr>
							<td class="text-right" colspan="9">Total</td>
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
    	url: 'actions/editItem.php',
    	showbuttons: false,
		success: function(response, newValue) {
			location.reload(true);
		}
	});
	
});
</script>

<?php $stmt->close(); ?>

<?php include 'inc/footer.php'; ?>
