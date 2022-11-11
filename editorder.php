<?php
	
require_once __DIR__.'/configuration.php';
require_once __DIR__.'/functions/harefn.php';

$orderId = $_GET["orderId"];

$statuses = fetchStatusArray();
$models = fetchModelsArray();
$sizes = fetchSizesArray();
$uuid = UUIDFromOrderId($orderId);

$hasBOM = hasBOM($orderId);

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
"SELECT cancelled
FROM 	orders 
WHERE orders.id = ".$orderId;

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($cancelled );
$stmt->store_result();
$stmt->fetch();

$componentTemplates = fetchComponentTemplates();

include 'inc/header.php';
?>

<!-- email modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div id="emailModalContainer" class="modal-content">
		</div>
	</div>
</div>
<!-- email modal -->

<!-- delete modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div id="deleteModalContainer" class="modal-content">
		</div>
	</div>
</div>
<!-- delete modal -->


<div class="container"><!-- container -->
	<div class="row"><!-- main header -->
		<div class="col-md-6">
			<h1>Order Details<?php echo $cancelled ? ' - CANCELLED' : '' ?></h1>
			<h4>Order #<?php echo $orderId ?></h4>
		</div>		
		<div class="col-md-6">
			<span class="pull-right">
				<a href="/orderfabview.php?orderId=<?php echo $orderId ?>">
					<span title="Fabrication View" class="glyphicon glyphicon-flash"></span>
				</a>&nbsp;&nbsp;
	        	<a href="/orderpaintview.php?orderId=<?php echo $orderId ?>">
					<span title="Paint View" class="glyphicon glyphicon-tint"></span>
				</a>&nbsp;&nbsp;
	        	<a href="/orderassview.php?orderId=<?php echo $orderId ?>">
					<span title="Assembly View" class="glyphicon glyphicon-wrench"></span>
				</a>&nbsp;&nbsp;
				<a target="_blank" href="vieworder.php?orderId=<?php echo($uuid) ?>">
					<span title="Customer View" class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
				</a>
						
		</span>
		
		<br>
		
		<?php if ( $hasBOM ) {?>
			<a class="btn btn-default" href="viewbom.php?orderId=<?php echo $orderId ?>" role="button">View BOM</a>
		<?php }else{ ?>
			
	
			<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			    Apply BOM Template <span class="caret"></span>
			 </button>
			<ul class="dropdown-menu">
				
				<?php  foreach ( $componentTemplates as $componentTemplate ){ ?>
		
				<li><a href="/actions/createbom.php?orderId=<?php echo $orderId ?>&templateId=<?php echo $componentTemplate->id ?>"><?php echo $componentTemplate->name ?></a></li>

				<?php }?>
				
			</ul>
			</div>	
	
						
				<a class="btn btn-default" href="actions/createbom.php?orderId=<?php echo $orderId ?>" role="button">Create BOM</a>
		<?php } ?> 
				
			<a class="btn btn-default" href="commercialinvoice.php?orderId=<?php echo $orderId ?>" target="_blank" role="button">View CI</a>
				
		</div>		
	</div>		
	<div class="row"><!-- start main structure -->
		<div class="col-md-6"><!-- LH column -->
			<div id="customerDetailsPanel" class="panel panel-default"> 
				<?php include ('panels/customer_details_panel.php') ?>
			</div>	
			<div id="orderDetailsPanel" class="panel panel-default"> 
				<?php include ('panels/order_details_panel.php') ?>
			</div>	
			<div id="paymentDetailsPanel" class="panel panel-default">
				<?php include ('panels/payment_details_panel.php') ?>
			</div>
			<div id="addressDetailsPanel" class="panel panel-default"> 
				<?php include ('panels/address_details_panel.php') ?>
			</div>	
			<div id="shippingAddressDetailsPanel" class="panel panel-default"> 
				<?php include ('panels/shipping_address_details_panel.php') ?>
			</div>	
			<a class="btn btn-default" data-toggle="modal" data-orderid="<?php echo $orderId ?>" data-target="#deleteModal" role="button">Delete Order</a>
			
		</div>	

		<div class="col-md-6"><!-- RH column -->
			<div id="bikeDetailsPanel" class="panel panel-default"> 
				<?php include ('panels/bike_details_panel.php') ?>
			</div>	
			<div id="fabDetailsPanel" class="panel panel-default"> 
				<?php include ('panels/fabrication_details_panel.php') ?>
			</div>	
			<div id="paintDetailsPanel" class="panel panel-default"> 
				<?php include ('panels/paint_details_panel.php') ?>
			</div>	
			<div id="assemblyDetailsPanel" class="panel panel-default"> 
				<?php include ('panels/assembly_details_panel.php') ?>
			</div>	
			<div id="orderNotesPanel" class="panel panel-default"> 
				<?php include ('panels/order_notes_panel.php') ?>
			</div>	
			<div id="shippingDetailsPanel" class="panel panel-default">
				<?php include ('panels/shipping_details_panel.php') ?>
			</div>
			<div id="filesPanel" class="panel panel-default">
				<?php include ('panels/files_panel.php') ?>
			</div>
			
			<a class="btn btn-default" data-toggle="modal" data-orderid="<?php echo $orderId ?>" data-target="#emailModal" role="button">Send Email</a>
		</div>	

	</div><!-- end main structure -->	
</div><!-- container -->	


<script>
	
	$('#emailModal').on('show.bs.modal', function (e) {
		var orderId = e.relatedTarget.getAttribute('data-orderId');
		$("#emailModalContainer").load( "inc/modals/emailUpdateTemplate.php?orderId="+ orderId );
	})

	$('#deleteModal').on('show.bs.modal', function (e) {
		var orderId = e.relatedTarget.getAttribute('data-orderId');
		$("#deleteModalContainer").load( "inc/modals/deleteOrder.php?orderId="+ orderId );		
	})


</script>	

<?php include 'inc/footer.php'; ?>
