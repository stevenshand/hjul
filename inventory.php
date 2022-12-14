<?php

require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';
	
$categoryChoice= isset($_GET["categoryChoice"]) ? $_GET["categoryChoice"] : 0;	
$supplierChoice= isset($_GET["supplierChoice"]) ? $_GET["supplierChoice"] : 0;	

$searchField = ( isset( $_GET["search"] ) ? $_GET["search"] : "" );
$searchString =" AND (1 > 0) ";
if( !empty(trim($searchField) ) ){
	$searchField = trim($searchField); 
	$searchString =" AND ( inv_items.id LIKE '%".$searchField."%' OR inv_items.sku LIKE '%".$searchField."%' OR  inv_items.supplier_code LIKE '%".$searchField."%' OR inv_items.description LIKE '%".$searchField."%' OR inv_items.short_name LIKE '%".$searchField."%' ) ";
}	


		
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$supplierChoiceQuery = " 1 > 0 ";
if( $supplierChoice > 0 ){
	$supplierChoiceQuery = " inv_suppliers.id =".$supplierChoice;
}

$categoryChoiceQuery = " 1 > 0 ";
if( $categoryChoice > 0 ){
	$categoryChoiceQuery = " inv_categories.id =".$categoryChoice;
}

$itemListQuery = 
"SELECT inv_items.id,
		short_name,
		description,
		variation,
		qty,
		supplier,
		inv_suppliers.name,
		cost,
		rrp,
		sum(cost*qty),
		category,
		inv_categories.name as cn,
		supplier_code,
		inv_location.location,
		inv_items.location,
		origin
FROM 	inv_items
LEFT JOIN inv_categories
ON inv_items.category = inv_categories.id 
LEFT JOIN inv_location
ON inv_items.location = inv_location.id 
LEFT JOIN inv_suppliers
ON inv_items.supplier = inv_suppliers.id ".
" WHERE (".
$supplierChoiceQuery.
" AND ".	
$categoryChoiceQuery.
") AND category != 37".
$searchString.
" GROUP BY inv_items.id
ORDER BY cn, short_name, variation";

//echo( $itemListQuery );

if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($itemId, $item, $description, $variation, $qty, $supplierId, $supplier, $cost, $rrp, $totalItemCost, $categoryId, $categoryName, $supplierCode, $location, $locationId, $origin );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

$countries = fetchCountriesArray();
$allSuppliers = fetchAllSuppliers(true);
$allCategories = fetchAllCategories();
$invLocations = fetchInvLocations();
$componentTemplates = fetchComponentTemplates();

include 'inc/header.php';
?>



<script src="js/jquery.tabletoCSV.js"></script>

<?php include "inc/modals/newItemM.php" ?>

<div class="container">
	
	<div class="row">
		<div class="col-md-10">
			<button title="add new Item" data-toggle="modal" data-target="#newItemModal" class="btn btn-default">Add Item</button>

			<a href="/stock_location.php" class="btn btn-default">Stock Location</a>

			<a href="/suppliers.php" class="btn btn-default">View Suppliers</a>
			
			<a href="/categories.php" class="btn btn-default">Categories</a>

			<a href="/purchaseorders.php" class="btn btn-default">View Purchase Orders</a>
			
			<a href="/bomreport.php" class="btn btn-default">Manage BOMs</a>
			
			<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			    BOM Templates <span class="caret"></span>
			 </button>
			<ul class="dropdown-menu">
				<li><a href="/editcomponenttemplates.php">Manage Templates</a></li>
				
				<?php  foreach ( $componentTemplates as $componentTemplate ){ ?>
		
				<li><a href="/componenttemplate.php?templateId=<?php echo $componentTemplate->id ?>"><?php echo $componentTemplate->name ?></a></li>

				<?php }?>
				
			</ul>
			</div>
			
			
			
			<!--
			<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			    OLD Component Templates <span class="caret"></span>
			 </button>
			<ul class="dropdown-menu">
			<li><a href="/componenttemplate.php?model=2">Stoater Rohloff</a></li>
			<li><a href="/componenttemplate.php?model=14">Stoater 2017</a></li>
			<li><a href="/componenttemplate.php?model=3">Stooshie</a></li>
			<li><a href="/componenttemplate.php?model=4">Stooshie Rohloff</a></li>
			<li><a href="/componenttemplate.php?model=15">Bahookie 2017</a></li>
			<li><a href="/componenttemplate.php?model=16">Bahookie Single 2017</a></li>
			<li><a href="/componenttemplate.php?model=17">Bahookie Rohloff 2017</a></li>
			<li><a href="/componenttemplate.php?model=18">Bahookie DB Rohloff 2017</a></li>
			<li><a href="/componenttemplate.php?model=23">Daunder Rohloff</a></li>
			<li><a href="/componenttemplate.php?model=19">Tam Rohloff</a></li>
			<li><a href="/componenttemplate.php?model=20">Tam Expedition</a></li>
			</ul>
			</div>
			-->	
				
				
			<button type="button" id="new_export" class="btn btn-default" data-export="new_export">Export</button>
		</div>	
			
		<div class="col-md-2">
			<span class="text-right">Stock Value : ??</span><span id="stockValue"><?php echo $stockValue ?></span>
		</div>	
	</div>	

	<div class="row">
		<div class="col-md-12">
			<div class="table-controls">
				<div class="row">
					<div class="col-md-11">
						<form id="inventoryListFilter" class="form-inline" action="inventory.php" method="GET">
							<label for="categoryList">Show</label>
					&nbsp;&nbsp;<select name="categoryChoice" id="categoryList">
									<option value="0">all categories</option>
									<?php foreach ($allCategories as $key => $value) {?>
									<option <?php echo ( $key==$categoryChoice ? "selected" : "") ?> value="<?php echo $key ?>"><?php echo $value ?></option>
									<?php }?>
								</select>
				&nbsp;&nbsp;<label for="categoryList">from</label>
					&nbsp;&nbsp;<select name="supplierChoice"  id="supplierList">
								<option value="0">all suppliers</option>
								<?php foreach ($allSuppliers as $sup) {?>
								<option <?php echo ( $sup->value==$supplierChoice ? "selected" : "") ?> value="<?php echo $sup->value ?>"><?php echo $sup->text ?></option>
								<?php }?>
								</select>
								
								&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<input id="search" name="search" type="search" value="<?php echo $searchField ?>" placeholder="Search...">&nbsp;&nbsp;
								
					&nbsp;&nbsp;<button type="submit" class="glyphicon glyphicon-refresh"></button>   
						</form>	
					</div>
				</div>
			</div>	
		</div>	
	</div>	

	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table id="inventoryTable" class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th>SKU</th><th class="col-md-3">Item</th><th>Category</th><th>Supplier</th><th>Supplier Code</th><th>Description</th><th>Variation</th><th>Location</th><th>Origin</th><th>RRP</th><th>QTY</th><th>Cost</th><th>Total</th>
					</tr>
			
					<?php
						$stockValuation = 0;
						while ($stmt->fetch()) { 
							$stockValuation += $totalItemCost;  
							
							?>
						<tr>
							<td><a href="/item.php?itemId=<?php echo $itemId ?>"><?php echo $itemId ?></a></td>
							<td><a 	class="editable" 
									href="/item.php?itemId=<?php echo $itemId ?>"
									data-type="text"
									data-title="Short Name" 
									data-name="short_name" 
									data-pk="<?php echo $itemId ?>">
										<?php echo $item  ?>
								</a>
							</td>
							
							<td><a 	class="editable" 
									data-type="select" 
									data-title="Category" 
									data-source="data/simpleCategoryList.php" 
									data-name="category" 
									data-pk="<?php echo $itemId ?>" 
									data-prepend="" 
									data-value="<?php echo $categoryId ?>" 
									href="item.php?itemId=<?php echo $itemId ?>">
										<?php echo $categoryName ?>
								</a>
							</td>
							
							<td><a 	class="editable" 
									data-type="select" 
									data-title="Supplier" 
									data-source="data/simpleSupplierList.php" 
									data-name="supplier" 
									data-pk="<?php echo $itemId ?>" 
									data-prepend="" 
									data-value="<?php echo $supplierId ?>" 
									href="view_supplier.php?supplierId=<?php echo $supplierId ?>">
										<?php echo $supplier ?>
								</a>
							</td>
							<td><a 	class="editable" 
									data-type="text" 
									data-title="Supplier Code" 
									data-name="supplier_code" 
									data-pk="<?php echo $itemId ?>" 
									data-value="<?php echo $supplierCode ?>" 
									href="item.php?itemId=<?php echo $itemId ?>">
										<?php echo $supplierCode ?>
								</a>
							</td>

							<td><?php echo $description  ?></td>
							
							
							<td>
								<a 	class="editable" 
									data-title="Variation" 
									data-name="variation" 
									data-pk="<?php echo $itemId ?>" 
									data-type="text" 
									data-value="<?php echo $variation ?>">
										<?php echo $variation ?>
									</a>
							</td>

							<td><a 	class="editable" 
									data-type="select" 
									data-title="Location" 
									data-source="data/locationList.php" 
									data-name="location" 
									data-pk="<?php echo $itemId ?>" 
									data-prepend="" 
									data-value="<?php echo $locationId ?>">
										<?php echo $location ?>
								</a>
							</td>

                            <td><a 	class="editable"
									data-type="select"
									data-title="Origin"
									data-source="data/countryList.php"
									data-name="Origin"
									data-pk="<?php echo $itemId ?>"
									data-prepend=""
									data-value="<?php echo $origin ?>">
										<?php
                                            if( isset($origin) )
                                                echo $countries[$origin]
                                        ?>
								</a>
							</td>
							
							<td>
								<a 	class="editable" 
									data-title="RRP" 
									data-name="rrp" 
									data-pk="<?php echo $itemId ?>" 
									data-type="text" 
									data-value="<?php echo $rrp ?>">
										<?php echo $rrp ?>
									</a>
							</td>
							
							<td>
								<a 	class="editable" 
									data-title="Quantity" 
									data-name="qty" 
									data-pk="<?php echo $itemId ?>" 
									data-type="text" 
									data-value="<?php echo $qty ?>">
										<?php echo $qty ?>
									</a>
								
								
							</td>
							
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
							<td><?php echo $totalItemCost ?></td>								 
						</tr>
					<?php }?>
						<tr>
							<td class="text-right" colspan="11">Total</td>
							<td colspan="2"><?php echo ( $stockValuation ) ?></td>
						</tr>
				</tbody>			
				</table>	
			</div>	
		</div>	
	</div>	

<script>
	


$(document).ready(function() {
    $('a.editable').editable({
    	url: 'actions/editItem.php',
    	showbuttons: false
    });
	
	$('#stockValue').html('<?php echo $stockValuation ?>');
});

$("#new_export").click(function(){
  location = "/inventory_export.php";
});


$("#export").click(function(){
  $("#inventoryTable").tableToCSV(true);
});


$('#categoryList').change( function(){
	$('#inventoryListFilter').submit();
 } );

 $('#supplierList').change( function(){
 	$('#inventoryListFilter').submit();
  } );


$( "#newItemForm" ).submit(function( event ) {
	event.preventDefault();
	
  var form = $( this );
  var itemData = form.serializeArray();
 
  $.post( "/actions/addItem.php", itemData, function(data){ 
	  console.log(data);	  
	  var reponseData = $.parseJSON(data);
      console.log(reponseData);
	  var itemId = reponseData.itemId;		
	  if( reponseData.err ){
		  alert( 'error :' + reponseData.err );
	  }else{
		 location = "/inventory.php";
	  }
   } );
});	

</script>

<?php $stmt->close(); ?>

<?php include 'inc/footer.php'; ?>
