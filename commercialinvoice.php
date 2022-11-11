<?php
	
require_once __DIR__.'/configuration.php';
require_once __DIR__.'/functions/harefn.php';

$orderId=$_GET["orderId"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$shippingAddressQuery = 
	"SELECT 
	shipping_address.line1,
	shipping_address.line2,
	shipping_address.town,
	shipping_address.postcode,
	shipping_address.country,
	shipping_address.tel1,
	shipping_address.tel2
	FROM shipping_address 
	WHERE order_id = ".$orderId;

if (!($stmt = $mysqli->prepare($shippingAddressQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($s_line1, $s_line2, $s_town, $s_postcode, $s_country, $s_tel1, $s_tel2 );

$stmt->store_result();
$stmt->fetch();



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
	address.line1,
	address.line2,
	address.line3,
	address.town,
	address.postcode,
	address.country,
	address.tel1,
	address.tel2
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

$stmt->bind_result($orderDate, $targetWeek, $sname, $fname, $email, $frameNumber, $modelId, $modelName, $frameSize, $orderStatus, $frameOnly, $statusId, $fabDetails, $paintDetails, $assemblyDetails, $basePrice, $vatExempt, 
$shippingNotes, $shippingDate, $shippingMethod, $trackingNumber, 
$line1, $line2, $line3, $town, $postcode, $country, $tel1, $tel2 );

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
$countries = fetchCountriesArray();

$commoditycode = $frameOnly ? "87149110" : "87120030"; 
$description = $frameOnly ? "WILLOW BICYCLE FRAME" : "WILLOW BICYCLE"; 
$weight = $frameOnly ? "3kg" : "12gk"; 
$totalValue = $totalPrice - $shippingCost;

include 'inc/header.php';

?>
<br>
<div class="container commercialinvoice"><!-- container -->

<div class="row">
	<div class="col-xs-12">
		<h1>Commercial Invoice - Willow Bike</h1>
	</div>
</div>

<div class="row">
	
	<div class="col-xs-6">
		<div class="panel">
			
		<table>                                                
			<tr><th colspan="2">Sender Address</th></tr> 
			<tr><td width="30%">Company Name:</td> <td>Willow Bike</td></tr>
			<tr><td>Address 1:</td><td>Moat Hall Cottage</td></tr>
			<tr><td>Address 2:</td><td>Balmulzier Road</td></tr>
			<tr><td>Town:</td><td>Slamannan</td></tr>
			<tr><td>Postcode:</td><td>FK1 3EW</td></tr>
			<tr><td>Country:</td><td>United Kingdom</td></tr>
			<tr><td>Contact:</td><td>Steven Shand</td></tr>
			<tr><td>Telephone:</td><td>+44 (0)7789 430 720</td></tr>
			<tr><td>Email:</td><td>contact@willowbike.com</td></tr>       
		</table>	
			
		</div>	
		
		<div class="panel">

			<table>                                                
				<tr><th colspan="2">Reciever Address</th></tr> 
				<tr><td width="30%">Company Name:</td> <td><?php echo ($sname." ".$fname )?></td></tr>
				<tr><td>Address 1:</td><td><?php echo $line1 ?></td></tr>
				<tr><td>Address 2:</td><td><?php echo $line2 ?></td></tr>
				<tr><td>Address 3:</td><td><?php echo $line3 ?></td></tr>
				<tr><td>Town:</td><td><?php echo $town ?></td></tr>
				<tr><td>Postcode:</td><td><?php echo $postcode ?></td></tr>
				<tr><td>Country:</td><td><?php echo $countries[$country] ?></td></tr>
				<tr><td>Contact:</td><td><?php echo ($sname." ".$fname )?></td></tr>
				<tr><td>Telephone:</td><td><?php echo $tel1 ?></td></tr>
				<tr><td>Email:</td><td><?php echo $email ?></td></tr>
			</table>	

		</div>
	</div>	
	
	<div class="col-xs-6">
		<div class="panel">


			<table>                                                
				<tr><th colspan="2">Details</th></tr>              
				<tr><td width="30%">Invoice Number:</td><td><?php echo ($orderId."-".$sname )?></td></tr>
				<tr><td>Shipping Date:</td><td><?php echo $shippingDate ?></td></tr>
				<tr><td>Consignment Number:</td><td><?php echo $trackingNumber ?></td></tr>
				<tr><td>Purchase Order Number:</td><td>N/A</td></tr>
				<tr><td>Invoice Currency:</td>     <td>GBP Pound Sterling</td></tr>
				<tr><td>Reason for Exportation:</td><td>Sale</td></tr>
				<tr><td>Sender VAT Number:</td>        <td>GB127323925000</td></tr>
				<tr><td>Receiver VAT Number:</td><td></td></tr>
				<tr><td>Inco Terms:</td><td>DAP: delivered at place</td></tr>
			</table>	
			</div>
		
		<div class="panel">
			
			<table>                                                
				<tr><th colspan="2">Delivery Address (if different from receiver)</th></tr>              
				<tr><td width="30%">Company Name:</td><td><?php echo ($sname." ".$fname )?></td></tr>
				<tr><td>Address 1:</td><td><?php echo $s_line1 ?></td></tr>
				<tr><td>Address 2:</td><td><?php echo $s_line2 ?></td></tr>
				<tr><td>Address 3:</td><td><?php echo $s_line3 ?></td></tr>
				<tr><td>Town</td><td><?php echo $s_town ?></td></tr>
				<tr><td>Postcode</td><td><?php echo $s_postcode ?></td></tr>
				<tr><td>Country:</td><td><?php echo $countries[$s_country] ?></td></tr>
				<tr><td>Contact:</td><td><?php echo ($sname." ".$fname )?></td></tr>
				<tr><td>Telephone:</td><td><?php echo $s_tel1 ?></td></tr>
				<tr><td>Email:</td><td><?php echo $email ?></td></tr>
			</table>
			
		</div>
	</div>	
</div>	

<div class="row">
<table>
	<tr><th>Desription</th><th>Quantity</th><th>Weight</th><th>Value</th><th>Tarrif Code</th><th>Country of Origin</th><th>Total Weight</th><th>Total Value</th></tr>
	<tr>
		<td><?php echo $description ?></td>
		<td>1</td>
		<td><?php echo $weight ?></td>
		<td>£<?php echo $totalValue ?></td>
		<td><?php echo $commoditycode ?></td>
		<td>United Kingdom</td>
		<td><?php echo $weight ?></td>
		<td>£<?php echo $totalValue ?></td>
	</tr>
	<tr><td colspan="6"></td><td>Freight Charges</td><td><strong>£<?php echo $shippingCost ?></strong></td></tr>
	<tr><td colspan="6"></td><td>Invoice Total</td><td><strong>£<?php echo $totalPrice ?></strong></td></tr>
</table>	
	
<br>
	
<table>
	<tr><td colspan="3">I DECLARE THAT TO THE BEST OF MY KNOWLEDGE THE INFORMATION ON THIS INVOICE IS TRUE AND CORRECT</th></td>
	<tr><td>Shipper Name and Job Title</td><td>Signature</td><td>Date</td></tr>
	<tr><td width="30%" height="20px">Steve Shand - MD</td><td></td><td></td></tr>
</table>	
	
	
</div>	

</div><!-- container -->	

<?php include 'inc/footer.php'; ?>

