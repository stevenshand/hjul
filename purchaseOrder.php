<?php

require __DIR__.'/configuration.php';
require __DIR__.'/functions/harefn.php';
require __DIR__.'/functions/invfn.php';
	
$id = $_GET["po_id"];	
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$poQuery = 
"SELECT id,
		supplier,
		reference,
		create_date,
		expected_date,
		status,
		author,
		location, 
		notes
FROM 	inv_purchase_orders
WHERE id = ".$id;

if (!($po_stmt = $mysqli->prepare($poQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$po_stmt->execute()) {
    echo "Execute failed: (" . $po_stmt->errno . ") " . $po_stmt->error;
}

$po_stmt->bind_result($itemId, $supplier, $reference, $createDate, $expectedDate, $status, $author, $location, $notes );
$po_stmt->store_result();
$po_stmt->fetch();

$suppliers = fetchSupplierNames();
$statuses = fetchStatuses();
$allSuppliers = fetchAllSuppliers(true);


include 'inc/header.php';
?>


<?php include "inc/modals/newItemM.php" ?>


<div class="container"><!--container -->
	
	<div class="row">
		<div class="col-md-10">
			<button id="receive" title="Add Items" class="btn btn-default">Receive</button>
			<a title="View Purchase Orders" class="btn btn-default" href="/purchaseorders.php">View Purchase Orders</a>
		</div>	
		<div class="col-md-2">
			<span class="text-right">
				<button onClick="printPO();" id="print" title="Print Purchase Order" class="btn btn-default">Print</button>
			</span>	
		</div>	
	</div>	

	<div class="row">
		
		<div class="col-md-12">
			<div id="potable" class="table-responsive">
				
				<?php include "inc/purchaseordertable.php" ?>
					
			</div><!-- table-responsive -->	

		<div class="row">
			<div class="col-md-8">
				<div>
					<form>
						<h4>Notes</h4>
						<p style="padding:5px;border:#c0c0c0 solid 1px;" id="notes" data-pk="<?php echo $id ?>" data-type="textarea" data-showbuttons="true" data-title="Notes" ><?php echo $notes ?></p>
					</form>	
				</div>	
			</div>	
			
			
			<div class="col-md-4 text-right">
				<a href="exportpo.php?poid=<?php echo $id ?>" target="_blank" title="Export PO" class="btn btn-default">Export Purchase Order</a>			
			</div>			
		</div>			
			
		</div><!-- col-md-10 -->	
		
		
	</div><!-- row -->	
	
	<!-- <?php echo $expectedDate ?> -->
	
	<div class="row">
		<div class="col-md-3">
			<dl class="dl-horizontal">
				<dt>Supplier</dt>
				<dd><?php echo $suppliers[$supplier] ?></dd>
				<dt>Reference</dt>
				<dd><?php echo $reference ?></dd>
				<dt>Creation Date</dt>
				<dd><?php echo $createDate ?></dd>
				<dt>Expected Date</dt>
				<dd><p id="expectedDate" 
						data-type="date" 
						data-pk="<?php echo $id ?>" 
						data-title="Select date"
						data-value="<?php echo isset($receivedDate) ? date( INPUTFIELDDATEFORMAT, $receivedDate ) : "" ?>">
						<?php echo $expectedDate ?>
						</p></dd>
			</dl>	
		</div>	
		<div class="col-md-3">
			<dl class="dl-horizontal">
				<dt>State</dt>
				<dd><?php echo $statuses[$status] ?></dd>
				<dt>Author</dt>
				<dd><?php echo $author ?></dd>
				<dt>Location</dt>
				<dd><?php echo ( $location == 1 ? "Moat Hall" : "Other" ) ?></dd>
			</dl>	
		</div>	
	</div>	

</div><!--container -->	


<?php $stmt->close(); ?>
<?php $po_stmt->close(); ?>

<!-- //2021-09-28 -->

<script>
	$(document).ready(function() {
	    $('#notes').editable({
	    	url: 'actions/editPONotes.php'
	    });
		    $('#expectedDate').editable({
		        url: 'actions/editPOExpectedDate.php',
				format: 'yyyy-mm-dd',    
		        datepicker: {
		                weekStart: 1
		   }
		});
	});
	
	function printPO(){
	//	alert('still to implement');
		window.print();
	}
	
	
</script>	

<?php include 'inc/footer.php'; ?>
