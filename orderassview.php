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

$stmt->bind_result($orderDate, $targetWeek, $sname, $fname, $email, $frameNumber, $modelId, $modelName, $frameSize, $orderStatus, $frameOnly, $statusId, $fabDetails, $paintDetails, $assemblyDetails, $basePrice, $vatExempt, $shippingNotes, $shippingDate, $shippingMethod, $trackingNumber, $tel1, $tel2 );
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

<div class="row">
	<div class="col-md-12">
				<h1>
					<?php echo $modelName ?>-<?php echo $frameSize ?>
				</h1>
				<h1>
					<?php echo $fname ?> <?php echo $sname ?> #<?php echo $orderId ?>
				</h1>
				<h4><?php echo ( $email ? $email : "n/a" ) ?>&nbsp;tel:<?php echo ( $tel1 ? $tel1 : "n/a" ) ?></h4>
				<h4></h4>
				<p>Frame Number : <?php echo ( $frameNumber ? $frameNumber : "n/a" ) ?></p>	
				
				<p>Target Delivery : WK:<?php echo $targetWeek ?>&nbsp;&nbsp;(<?php echo date('M d',strtotime(TARGETYEAR.'W'.$targetWeek)) ?>)</p>	
				<hr>
				<dl class="fabview dl-horizontal">
					<?php if($assemblyDetails || sizeOf($assemblyLineItems) > 0 ) {?>
					<dt>Assembly :</dt>
					<dd>
						<?php echo ( nl2br($assemblyDetails).($assemblyDetails ? "<br>" : "") ) ?>
						<?php foreach ($assemblyLineItems as $key => $value) {?>
						<?php echo $key ?> (£<?php echo money_format('%i', $value) ?>)<br>
						<?php }?>
					</dd>	
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

					<?php if($fabDetails || sizeOf($fabricationLineItems) > 0 ) {?>
					<dt>Frame Details :</dt>
					<dd>
						<?php echo ( nl2br($fabDetails).($fabDetails ? "<br>" : "") ) ?>
						<?php foreach ($fabricationLineItems as $key => $value) {?>
							<?php echo $key ?> (£<?php echo money_format('%i', $value) ?>)<br>
						<?php }?>
					</dd>	
					<?php } ?>

					
				</dl>				
			</div>	



	</div>	


</div><!-- container -->	




<script>
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
})
</script>
<?php include 'inc/footer.php'; ?>
