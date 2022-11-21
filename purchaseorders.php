<?php
require_once 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sortField = "supplier";
if( isset($_GET["sortField"]) )
    $sortField = $_GET["sortField"];

$poListQuery = 
"SELECT id,
		supplier,
		reference,
		UNIX_TIMESTAMP(create_date),
		UNIX_TIMESTAMP(sent_date),
		UNIX_TIMESTAMP(received_date),
		UNIX_TIMESTAMP(expected_date),
		author,
		location,
		status
FROM 	inv_purchase_orders
ORDER BY ".$sortField." ASC"; 

//echo ($poListQuery);

if (!($stmt = $mysqli->prepare($poListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $supplier, $reference, $createDate, $sentDate, $receivedDate, $expectedDate, $author, $location, $status );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

$allSuppliers = fetchSupplierNames();
$statuses = fetchStatuses();
$userList = userList();

include 'inc/header.php';

?>

<?php include "inc/modals/newPOM.php" ?>


<div class="container">
	<div class="row">
		<div class="col-md-6">
			<button title="Add New Purchase Order" data-toggle="modal" data-target="#newPOModal" class="btn btn-default">Add Purchase Order</button>
			<button title="Export Open POs" id="exportpos" class="btn btn-default">Export Open POs</button>
		</div>	
		<div class="col-md-6 text-right">
			Sort By :
			<select name="sortField" id="sortField">
				<option <?php echo ($sortField =='supplier' ? 'selected' : '' ) ?> value="supplier">Supplier</option>
				<option <?php echo ($sortField =='create_date' ? 'selected' : '' ) ?> value="create_date">Creation Date</option>
				<option <?php echo ($sortField =='expected_date' ? 'selected' : '' ) ?> value="expected_date">Expected Date</option>
			</select>	
		</div>	
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th>Delete</th><th>Supplier</th><th>Create Date</th><th>Expected Date</th><th>Status</th><th>Sent Date</th><th>Received Date</th><th>Author</th><th>Location</th><th>Reference</th><td></td>
					</tr>
			
					<?php while ($stmt->fetch()) { ?>
						<tr>
							<td>
								<a href="actions/deletePurchaseOrder.php?po_id=<?php echo $id ?>"><span class="glyphicon glyphicon-remove"></span></a>
							</td>
							
							<td><?php echo $allSuppliers[$supplier] ?></td>

							<td>
								<a class="editable"
								data-title="Creation Date" 
								data-name="create_date" 
								data-pk="<?php echo $id ?>" 
								data-type="date" 
								data-value="<?php echo date( INPUTFIELDDATEFORMAT, $createDate )  ?>"
								data-placement="right">
								<?php echo date( INPUTFIELDDATEFORMAT, $createDate )  ?></a>
							</td>
							
							<td>
								<?php
								$strl = strlen( $expectedDate );
								?>
								<a class="editable"
								data-title="Expected Date" 
								data-name="expected_date" 
								data-pk="<?php echo $id ?>" 
								data-type="date" 
								data-value="<?php echo ( $strl > 0 ? date( INPUTFIELDDATEFORMAT, $expectedDate ) : null ) ?>"
								data-placement="right">
								<?php echo ( $strl > 0 ? date( INPUTFIELDDATEFORMAT, $expectedDate ) : "" ) ?></a>
							</td>

							<td>
								<a 	class="editable" 
									data-type="select" 
									data-title="Status" 
									data-source="data/simpleStatusList.php" 
									data-name="status" 
									data-pk="<?php echo $id ?>" 
									data-value="<?php echo $status ?>"> 
										<?php echo $statuses[$status] ?>
								</a>
							</td>	

							<td>
								<a class="editable"
								data-title="Sent Date" 
								data-name="sent_date" 
								data-pk="<?php echo $id ?>" 
								data-type="date"
								data-value="<?php echo isset($sentDate) ? date( INPUTFIELDDATEFORMAT, $sentDate ) : null ?>"
								data-placement="right">
								<?php echo ( isset($sentDate) ? date( INPUTFIELDDATEFORMAT, $sentDate ) : "n/a" ) ?>
							</a>
							</td>
							
							<td>
								<a class="editable"
								data-title="Recieved Date" 
								data-name="received_date" 
								data-pk="<?php echo $id ?>" 
								data-type="date" 
								data-value="<?php echo isset($receivedDate) ? date( INPUTFIELDDATEFORMAT, $receivedDate ) : date( INPUTFIELDDATEFORMAT ) ?>"
								data-placement="right">
								<?php echo ( isset($receivedDate) ? date( INPUTFIELDDATEFORMAT, $receivedDate ) : "n/a" ) ?>
							</a>
							</td>
							
							<td>
								<a class="editable"
								data-title="Author" 
								data-name="author" 
								data-pk="<?php echo $id ?>" 
								data-type="text" 
								data-value="<?php echo $author ?>">
								<?php echo $author  ?></a>
							</td>

							<td>
								<a class="editable"
								data-autotext="always"
								data-title="Location" 
								data-name="location" 
								data-pk="<?php echo $id ?>" 
								data-type="select"
								data-source="[{value: 1, text: 'Moat Hall'}]" 
								data-value="<?php echo $location ?>">
								<?php echo $location ?></a>
							</td>
							
							<td>
								<a class="editable"
								data-title="Reference" 
								data-name="reference" 
								data-pk="<?php echo $id ?>" 
								data-type="text" 
								data-value="<?php echo $reference ?>">
								<?php echo $reference  ?></a>
							</td>
							
							<td>
								<a href="purchaseOrder.php?po_id=<?php echo $id ?>"><span class="glyphicon glyphicon-list"></span></a>
							</td>
							
						</tr>
					<?php }?>
				</tbody>			
				</table>	
			</div>	
		</div>	
	</div>	
</div>	

<script>

$("#exportpos").click(function(){
	var sort = $('#sortField').val();
  	location = "/exportpos.php?sortField=" + sort;
});

$(document).ready(function() {
    $('a.editable').editable({
    	url: 'actions/editPO.php',
    	showbuttons: false,
		datepicker:{ toggleActive: true, defaultViewDate : null }
    });
	
	$('#sortField').change( function(){
		var url = "purchaseorders.php";
		var sort = $('#sortField').val();
		window.location.href = url + "?sortField=" + sort;
	})
});
</script>

<?php $stmt->close(); ?>

<?php include 'inc/footer.php'; ?>
