<?php
	
require_once __DIR__.'/configuration.php';
require_once __DIR__.'/functions/harefn.php';

$uuid=$_GET["orderId"];
$orderId = orderIdfromUUID($uuid);

if(!$uuid || !$orderId){
	echo( "error fetching order" );
	exit();
}

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
	models.name,
	models.brand,
	models.display_name,
	sizes.size as framesize, 
	status.status, 
	frame_only, 
	orders.status,
	orders.notes,
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
	tel2,
	rohloff_serial.rohloffSerial
	FROM orders 
	LEFT JOIN status ON orders.status = status.id 
	LEFT JOIN models ON orders.model = models.id 
	LEFT JOIN sizes ON orders.size = sizes.id 
	LEFT JOIN fabrication_details ON orders.id = fabrication_details.order_id 
	LEFT JOIN paint_details ON orders.id = paint_details.order_id 
	LEFT JOIN assembly_details ON orders.id = assembly_details.order_id 
	LEFT JOIN shipping ON orders.id = shipping.order_id 
	LEFT JOIN address ON orders.id = address.order_id 
	LEFT JOIN rohloff_serial ON orders.id = rohloff_serial.orderId 
	WHERE orders.id = ".$orderId;

// echo ($orderListQuery);

if (!($stmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($orderDate, $targetWeek, $sname, $fname, $email, $frameNumber, $modelName, $brandId, $modelDisplayName, $frameSize, $orderStatus, $frameOnly, $statusId, $notes, $fabDetails, $paintDetails, $assemblyDetails, $basePrice, $vatExempt, $shippingNotes, $shippingDate, $shippingMethod, $trackingNumber, $tel1, $tel2, $rohloffSerial );
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
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">ORDER DETAILS</h3>
			</div>
			<div class="panel-body">
				
				<dl class="dl-horizontal">
					
					<dt>Name : </dt>
					<dd class="bigger"><?php echo $fname ?> <?php echo $sname ?></dd>
					
					<dt>Email : </dt>
					<dd><a href="mailto:<?php echo $email ?>" target="_blank"><?php echo $email ?></a></dd>
					<dt>Address : </dt>
					<dd><?php echo getDisplayAddress( $orderId, null ) ?></dd>
					
					<dt>Telephone 1 : </dt>
					<dd><?php echo $tel1 ?></dd>

					<dt>Telephone 2 : </dt>
					<dd><?php echo $tel2 ?></dd>
					
					<dt>Delivery Address : </dt>
					<dd><?php echo getDisplayAddress( $orderId, true ) ?></dd>
				</dl>
				<hr>
				<dl class="dl-horizontal">
					<dt>Model :</dt>
					<dd class="bigger"><?php echo $modelDisplayName ?></dd>	

					<dt>Size :</dt>
					<dd><?php echo $frameSize ?></dd>	

					<dt>Full Bike or Frame? :</dt>
					<dd><?php echo ( $frameOnly ? "Frame Only" : "Full Bike" ) ?></dd>	

					<dt>Frame Number :</dt>
					<dd><?php echo $frameNumber ?></dd>	

					<?php if($rohloffSerial ) {?>
					<dt>Rohloff Serial # :</dt>
					<dd><?php echo $rohloffSerial ?></dd>	
					<?php } ?>

					<?php if($fabDetails || sizeOf($fabricationLineItems) > 0 ) {?>
					<dt>Frame Details :</dt>
					<dd>
						<?php echo ( nl2br($fabDetails).($fabDetails ? "<br>" : "") ) ?>
						<?php foreach ($fabricationLineItems as $key => $value) {?>
							<?php echo $key ?> (<?php curry($value) ?>)<br>
						<?php }?>
					</dd>	
					<?php } ?>
					
					<?php if($paintDetails || sizeOf($paintLineItems) > 0 ) {?>
					<dt>Paint/Colour Info :</dt>
					<dd>
						<?php echo ( nl2br($paintDetails).($paintDetails ? "<br>" : "") ) ?>
						<?php foreach ($paintLineItems as $key => $value) {?>
						<?php echo $key ?> (<?php echo curry($value) ?>)<br>
						<?php }?>
					</dd>	
					<?php } ?>

					<?php if($assemblyDetails || sizeOf($assemblyLineItems) > 0 ) {?>
					<dt>Assembly Details :</dt>
					<dd>
						<?php echo ( nl2br($assemblyDetails).($assemblyDetails ? "<br>" : "") ) ?>
						<?php foreach ($assemblyLineItems as $key => $value) {?>
						<?php echo $key ?> (<?php curry($value) ?>)<br>
						<?php }?>
					</dd>	
					<?php } ?>
				</dl>
				
			</div>
			
								
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Notes</h3>
		</div>
		<div class="panel-body">	
			<div class="row">	
				<div class="col-md-12">
				   <?php echo $notes ?> 	   
				</div>   
			</div>   
		</div>   
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Images</h3>
		</div>
	
		<div class="row">	
		   <div class="sc-column thegal">
			   
			   <?php
			   $files = imageFileList($orderId); 
			   foreach ($files as $key => $value) { 
				   if(!is_dir($value)) {?>
		   			<div class="col-sm-4">
		   				<a href="files/<?php echo($orderId) ?>/<?php echo($key) ?>" class="thumbnail">
		   					<img src="files/<?php echo($orderId) ?>/<?php echo($key) ?>" >
		   				</a>
		   			</div>
			   <?php } }?>	   
					   
		   </div>	   
		</div>   
	</div>	

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Files</h3>
		</div>
	
		<div class="row">	
		   <div class="sc-column">
			   
		   		<div class="col-sm-4">
				   <?php
				   $files = userFileList($orderId); 
				   foreach ($files as $key => $value) { 
					   if(!is_dir($value)) {?>
						<span>
							<a target="_blank" href="files/<?php echo($orderId) ?>/<?php echo($key) ?>">&nbsp;<?php echo($key) ?></a>
						</span><br>
				   <?php } }?>	   
				</div>
				   
		   </div>	   
		</div>   
	</div>	

</div>	


		<div class="col-md-6">
			
			
			<div class="panel panel-default">
			  <div class="panel-heading">
				<span style="float:right" class="small">Today : <?php echo date(DATEFORMAT) ?> (week  <?php echo nowWeek() ?>)</span>
			    <h3 class="panel-title">ORDER STATUS</h3>
			  </div>
			  <div class="panel-body">
				<table class="table">
					<tr><th>Stage</th><th>Target Week</th><th>Status</th></tr>
					<tr class="<?php echo ( $statusId == 1 ? "bg-success" : "" ) ?>">
						<td>Order Placed</td>
						<td>week <?php echo wkNum($orderDate) ?></td>
						<td>
							<span class="glyphicon glyphicon-<?php echo ( $statusId > 0 ? "check" : "unchecked" ) ?>"></span>
						</td>
					</tr>

					<tr class="<?php echo ( $statusId == 2 ? "bg-success" : "" ) ?>">
						<td>Materials selected</td>
						<td></td>
						<!-- <td>week <?php echo ( ($targetWeek-4 > 0) ? $targetWeek-4: $targetWeek-4+52 ) ?></td> -->
						<td><span class="glyphicon glyphicon-<?php echo ( $statusId > 1 ? "check" : "unchecked" ) ?>"></span></td>
					</tr>
					
					<tr class="<?php echo ( $statusId == 3 ? "bg-success" : "" ) ?>">
						<td>Fabrication</td>
						<td></td>
						<!-- <td>week <?php echo ( ($targetWeek-3 > 0) ? $targetWeek-3: $targetWeek-3+52 ) ?></td> -->
						<td><span class="glyphicon glyphicon-<?php echo ( $statusId > 3 ? "check" : "unchecked" ) ?>"></span></td>
					</tr>

					<tr class="<?php echo ( $statusId == 4 ? "bg-success" : "" ) ?>">
						<td>Pre-paint finishing</td>
						<td></td>
						<!-- <td>week <?php echo ( ($targetWeek-2 > 0) ? $targetWeek-2: $targetWeek-2+52 ) ?></td> -->
						<td><span class="glyphicon glyphicon-<?php echo ( $statusId > 4 ? "check" : "unchecked" ) ?>"></span></td>
					</tr>

					<tr class="<?php echo ( $statusId == 5 ? "bg-success" : "" ) ?>">
						<td>Paint</td>
						<td></td>
						<!-- <td>week <?php echo ( ($targetWeek-1 > 0) ? $targetWeek-1: $targetWeek-1+52 ) ?></td> -->
						<td><span class="glyphicon glyphicon-<?php echo ( $statusId > 5 ? "check" : "unchecked" ) ?>"></span></td>
					</tr>
					
					<tr class="<?php echo ( $statusId == 6 ? "bg-success" : "" ) ?>">
						<td>Assembly</td>
						<td></td>
						<!-- <td>week <?php echo ( $targetWeek )  ?></td> -->
						<td><span class="glyphicon glyphicon-<?php echo ( $statusId > 6 ? "check" : "unchecked" ) ?>"></span></td>
					</tr>
					
					<tr class="<?php echo ( $statusId == 7 ? "bg-success" : "" ) ?>">
						<td>Completion</td>
						<td></td>
						<!-- <td>week <?php echo $targetWeek ?></td> -->
						<td><span class="glyphicon glyphicon-<?php echo ( $statusId > 7 ? "check" : "unchecked" ) ?>"></span></td>
					</tr>
				</table>
				<hr>
				<dl class="dl-horizontal">
					<dt>Courier : </dt>
					<dd><?php echo $shippingMethod ?></dd>
					<dt>Dispatch Date : </dt>
					<dd><?php echo $shippingDate ?></dd>
					<dt>Tracking No : </dt>
					<dd><?php echo $trackingNumber ?></dd>
					<dt>Shipping Notes : </dt>
					<dd><?php echo nl2br($shippingNotes) ?></dd>
				</dl>		
			  </div>
			</div>
					
			
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title">COST SUMMARY</h3>
			  </div>
			  <div class="panel-body">
			    <dl class="dl-horizontal">
					<dt>Base Price</dt>
					<dd><?php curry($basePrice) ?></dd>	
					<dt>Component Upgrades</dt>
					<dd><?php curry($assemblyLineItemsTotal) ?></dd>	
					<dt>Frame Upgrades</dt>
					<dd><?php curry($fabricationLineItemsTotal) ?></dd>	
					<dt>Paint Upgrades</dt>
					<dd><?php curry($paintLineItemsTotal) ?></dd>	
					<dt>Delivery Costs</dt>
					<dd><?php echo ( ( $shippingCost > 0 ) ? steph($shippingCost) : "TBC" ) ?></dd>	
				</dl>	
					<hr>
			    <dl class="dl-horizontal">
					<dt>Total Price</dt>
					<dd><?php curry($totalPrice) ?></dd>	
					<dt>Payments to Date</dt>
					<dd><?php curry($totalPayments) ?></dd>	
					<dt>Balance Due</dt>
					<dd><?php curry($balance) ?></dd>	
					<dt>VAT</dt>
					<dd><?php echo ($vatExempt ? "NO  VAT" : "inc VAT @ 20%" )?></dd>	
				</dl>	
			  </div>
			</div>
		</div>	
	</div>	

</div><!-- container -->	

<script>

	$(document).ready(function() {
	
	  $('.thegal').magnificPopup({
			zoom: {
			    enabled: true,
			    duration: 500,
			    easing: 'ease-in-out',
			    opener: function(openerElement) {
			      return openerElement.is('img') ? openerElement : openerElement.find('img');
			    }
			  },
			type:'image',
			delegate:'a',
			gallery: {
				enabled: true,
				preload: [1,2]
			  }
			});

	});
	
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
})
</script>
<?php include 'inc/footer.php'; ?>
