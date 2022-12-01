<?php

require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';

$orderId= $_GET["orderId"];	

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$itemListQuery = 
"SELECT inv_items.id,
		short_name,
		description,
		variation,
		inv_items.qty,
		order_bom.qty,
		order_bom.id,
		supplier,
		inv_suppliers.name,
		cost,
		(cost*order_bom.qty),
		category,
		inv_categories.name as cn,
		supplier_code,
		inv_po_lines.qty
FROM 	inv_items
LEFT JOIN inv_categories
ON inv_items.category = inv_categories.id 
LEFT JOIN inv_suppliers
ON inv_items.supplier = inv_suppliers.id
LEFT JOIN order_bom
ON order_bom.item = inv_items.id
LEFT JOIN inv_po_lines
ON inv_po_lines.item_id = inv_items.id
WHERE order_bom.orderId =".$orderId.
" GROUP BY order_bom.id 
 ORDER BY cn, short_name, variation";

//echo( $itemListQuery );

if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "1 Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($itemId, $item, $description, $variation, $stockLevel, $qty, $lineId, $supplierId, $supplier, $cost, $totalItemCost, $categoryId, $categoryName, $supplierCode, $onorder );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

$orderListQuery = 
	"SELECT 
	UNIX_TIMESTAMP(order_date) as dt, 
	target_week, 
	sname, 
	fname,
	email,
	frame_number,
	model, 
	models.name, 
	sizes.size as framesize, 
	status.status, 
	frame_only, 
	orders.status,
	fab_details,
	paint_details,
	assembly_details,
	base_price,
	vat_exempt,
	shipping_notes,
	shipping.shipping_date,
	shipping.method,
	shipping.tracking_number,
	orders.model,
	tel1,
	tel2,
	stockout,
	shipping_location
	FROM orders 
	LEFT JOIN status ON orders.status = status.id 
	LEFT JOIN models ON orders.model = models.id 
	LEFT JOIN sizes ON orders.size = sizes.id 
	LEFT JOIN fabrication_details ON orders.id = fabrication_details.order_id 
	LEFT JOIN paint_details ON orders.id = paint_details.order_id 
	LEFT JOIN assembly_details ON orders.id = assembly_details.order_id 
	LEFT JOIN shipping ON orders.id = shipping.order_id 
	LEFT JOIN address ON orders.id = address.order_id 
	WHERE orders.id = ".$orderId;

	if (!($stmt2 = $mysqli->prepare($orderListQuery))) {
	    echo "2 Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt2->execute()) {
	    echo "Execute failed: (" . $stmt2->errno . ") " . $stmt2->error;
	}

	$stmt2->bind_result($orderDate, $targetWeek, $sname, $fname, $email, $frameNumber, $model, $modelName, $frameSize, $orderStatus, $frameOnly, $statusId, $fabDetails, $paintDetails, $assemblyDetails, $basePrice, $vatExempt, $shippingNotes, $shippingDate, $shippingMethod, $trackingNumber, $modelId, $tel1, $tel2m, $stockOut, $shippingLocation );
	$stmt2->store_result();
	$stmt2->fetch();

//$countries = fetchCountriesArray();
$allSuppliers = fetchAllSuppliers(true);
$allCategories = fetchAllCategories();

$modelName = fetchModelName($modelId);

include 'inc/header.php';
?>

<?php include "inc/modals/newBOMItemM.php" ?>

<div class="container">
	
	<div class="row">
		<div class="col-md-8">
			<p>Bill of materials for <?php echo ( $fname." ".$sname ) ?> - <?php echo $frameSize ?> <?php echo $modelName ?><p>
			<p>Shipping from <?php echo ( $shippingLocation == 0 ? "Moat Hall" : "Other" ) ?></p>	
		</div>	
		<div class="col-md-6">
			<?php if(!$stockOut) {?>
			<button title="add new BOM Item" data-toggle="modal" data-target="#newBOMItemModal" class="btn btn-default">Add BOM Item</button>
			
			
			<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Stock Out<span class="caret"></span>
			 </button>
			  <ul class="dropdown-menu">
			    <li><a href="" id="stockout_loc0">Stock out - Moat Hall</a></li>
			    <li><a href="" id="stockout_loc1">Stock out - Other</a></li>
			  </ul>
			</div>
				
			<!-- <button id="stockout" class="btn btn-default">stock out</button>	 -->
			<?php }else{ ?>
			<h3>Stock has been booked out.</h3>	
			<?php } ?>
			</div>
			
		<div class="col-md-6 text-right">
			<button id="export" data-export="export" class="btn btn-default">export</button>	
			<a class="btn btn-default" href="editorder.php?orderId=<?php echo $orderId ?>" role="button">Back to Order</a>
		</div>	
	</div>	

	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table style="font-size:smaller;" id="bomTable" class="table table-bordered table-condensed table-striped">
					<tbody>
					<tr>
						<?php if(!$stockOut) {?>
						<th></th>
						<?php } ?>
                        <th>Category</th><th>SKU</th><th>Item</th><th>Supplier</th><th>Supplier Code</th><th>Variation</th><th>QTY</th><th>In Stock</th><th>On Order</th><th>Cost</th><th>Total</th>
					</tr>
			
					<?php
						$stockValuation = 0;
						while ($stmt->fetch()) {
							$stockValuation += $totalItemCost;

							?>
						<tr>
						<?php if(!$stockOut) {?>
							<td class="center"><a href="actions/removeItemFromBOM.php?orderId=<?php echo($orderId) ?>&amp;lineId=<?php echo $lineId ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
						<?php } ?>
                            <td style="font-size:smaller;"><?php echo $categoryName ?></td>

							<td><a href="/item.php?itemId=<?php echo $itemId ?>"><?php echo $itemId ?></a></td>

							<td data-toggle="tooltip"
								data-html="true"
								data-container="body"
								data-placement="right"
								title="<?php echo $description ?>"><?php echo $item  ?></td>

							<td style="font-size:smaller;"><?php echo $supplier ?></td>

							<td style="font-size:smaller;"><?php echo $supplierCode ?></td>

							<td style="font-size:smaller;"><?php echo $variation ?></td>

							<?php $stockClass = ( $stockLevel < $qty ? "danger" : "" ); ?>

							<td class="<?php echo $stockClass ?>"><?php echo ( trimForCSV($qty) ) ?>
							</td>


							<td class="<?php echo $stockClass ?>"><?php echo ( trimForCSV($stockLevel) ) ?></td>
							<td><?php echo $onorder ?></td>

							<td ><?php curry($cost) ?></td>

							<td><?php echo curry($totalItemCost) ?></td>
						</tr>
					<?php }?>
						<tr>
							<td class="text-right" colspan="<?php echo ($stockOut ? '9': '10')?>">Total</td>
							<td colspan="10"><?php echo curry($stockValuation) ?></td>
						</tr>
				</tbody>			
				</table>	
			</div>	
		</div>	
	</div>	
</div>	

<script src="js/jquery.tabletoCSV.js"></script>

<script>

$(document).ready(function() {
	$('[data-toggle="tooltip"]').tooltip();
});

$("#export").click(function(){
  $("#bomTable").tableToCSV();
});


$("#stockout_loc0").click(function(){
	stockout(0);
	return false;
});

$("#stockout_loc1").click(function(){
	stockout(1);
	return false;
});

function stockout(loc){
  	var itemData = {
		orderId : <?php echo $orderId ?>,
  		loc:loc
	};	
	
  	$.post( "/actions/stockout.php", itemData, function(data){
  			  var responseData = $.parseJSON(data);
  		      console.log(responseData);
  			  // var itemId = reponseData.itemId;
  			  if( responseData.err ){
  				  alert( 'error :' + responseData.err );
  			  }else{
  				  location.reload();
  		  }
  		   } );
};

</script>

<?php $stmt->close(); ?>

<?php include 'inc/footer.php'; ?>
