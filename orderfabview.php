<?php
	
require_once __DIR__.'/configuration.php';
require_once __DIR__.'/functions/harefn.php';

$orderId=$_GET["orderId"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderListQuery = 
	"SELECT 
	UNIX_TIMESTAMP(order_date) as dt, 
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
	notes,
	shipping_notes,
	shipping.shipping_date,
	shipping.method,
	shipping.tracking_number,
	tel1,
	tel2
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

//echo ($orderListQuery);

if (!($stmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($orderDate, $sname, $fname, $email, $frameNumber, $modelId, $modelName, $frameSize, $orderStatus, $frameOnly, $statusId, $fabDetails, $paintDetails, $assemblyDetails, $basePrice, $vatExempt, $notes, $shippingNotes, $shippingDate, $shippingMethod, $trackingNumber, $tel1, $tel2 );
$stmt->store_result();
$stmt->fetch();

$fabricationLineItems = getLineItems( $orderId, "fab" );
$paintLineItems = getLineItems( $orderId, "pnt" );
$assemblyLineItems = getLineItems( $orderId, "ass" );
$fabricationLineItemsTotal = getLineItemTotal($orderId, "fab" );
$paintLineItemsTotal = getLineItemTotal($orderId, "pnt" );
$assemblyLineItemsTotal = getLineItemTotal($orderId, "ass" );
$shippingCost = totalShipping($orderId); 
$totalPayments = totalPayments($orderId);
$totalPrice = $fabricationLineItemsTotal + $paintLineItemsTotal + $assemblyLineItemsTotal + $basePrice + $shippingCost;
$balance = $totalPrice-$totalPayments;

include 'inc/header.php';

?>
<br>
<div class="container"><!-- container -->

<div class="card" style="border:2px dashed #000;">
	<h1>
		<?php echo $modelName ?>
	</h1>
	<h1>
		<?php echo ( $frameSize == "TBC" ? "________" : $frameSize ) ?>
	</h1>
	<h1>
<!--TODO TARGET WEEK-->
		<?php echo $sname ?><span class="text-right">&nbsp;#<?php echo $orderId ?></span>
	</h1>		
</div>


<div class="row">
	<div class="col-md-12">
			
<div class="row">
	<div class="col-md-8">
		<h1>
			<?php echo $modelName ?>-<?php echo $frameSize ?>
		</h1>
		<h1>
			<?php echo $fname ?> <?php echo $sname ?> #<?php echo $orderId ?>
		</h1>
		
		<p>Frame Number :<span class="frame-number"><?php echo $frameNumber ?></span>	
		<hr>
	</div>		
	<div class="col-md-4">
		<svg id="barcode"></svg>
		<script>JsBarcode("#barcode", "<?php echo $orderId?>", {text:' ', width:3} );</script>
	</div>
</div>





				
				<dl class="fabview dl-horizontal">
					<?php if($fabDetails || sizeOf($fabricationLineItems) > 0 ) {?>
					<dt>Frame Details :</dt>
					<dd>
						<?php echo ( nl2br($fabDetails).($fabDetails ? "<br>" : "") ) ?>
						<?php foreach ($fabricationLineItems as $key => $value) {?>
							<?php echo $key ?> (£<?php echo money_format('%i', $value) ?>)<br>
						<?php }?>
					</dd>	
					<?php } else { ?>
						<p>Standard configuration, no additional fabrication specs</p>
					<?php } ?>	
				</dl>	
				<dl class="dl-horizontal">
					<dt>Full Bike or Frame? :</dt>
					<dd><?php echo ( $frameOnly ? "Frame Only" : "Full Bike" ) ?></dd>	
					
					<?php if($paintDetails || sizeOf($paintLineItems) > 0 ) {?>
					<dt>Paint/Colour Info :</dt>
					<dd>
						<?php echo ( nl2br($paintDetails).($paintDetails ? "<br>" : "") ) ?>
						<?php foreach ($paintLineItems as $key => $value) {?>
						<?php echo $key ?> (£<?php echo money_format('%i', $value) ?>)<br>
						<?php }?>
					</dd>	
					<?php } ?>

					<?php if($assemblyDetails || sizeOf($assemblyLineItems) > 0 ) {?>
					<dt>Assembly Details :</dt>
					<dd>
						<?php echo ( nl2br($assemblyDetails).($assemblyDetails ? "<br>" : "") ) ?>
						<?php foreach ($assemblyLineItems as $key => $value) {?>
						<?php echo $key ?> (£<?php echo money_format('%i', $value) ?>)<br>
						<?php }?>
					</dd>	
					<?php } ?>
					
					<?php if($notes) {?>
					<dt>Notes :</dt>
					<dd>
						<?php echo ( nl2br($notes) ) ?>
					</dd>	
					<?php } ?>
				</dl>				
			</div>	



	</div>	

	<?php include "inc/qc/".$modelId.".php" ?>	
		

</div><!-- container -->	




<script>
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
})
</script>
<?php include 'inc/footer.php'; ?>
