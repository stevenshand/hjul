<?php
require 'configuration.php';
require 'functions/harefn.php';

setlocale(LC_MONETARY,"en_GB");

$year = $_GET["year"];
$month = $_GET["month"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderListQuery = 
"SELECT orders.id as orderId,
		fname,
		sname,
		models.name,
		sizes.size as framesize,
		shipping.shipping_date,
		vat_exempt,
		shipping_location,
		reconciled
FROM 	orders
LEFT JOIN 
		sizes
ON orders.size = sizes.id
LEFT JOIN 
		models
ON orders.model = models.id	 
LEFT JOIN
		shipping
ON orders.id = shipping.order_id
where year(shipping.shipping_date) = ".$year." and month(shipping.shipping_date) = ".$month."
GROUP BY orders.id";

// echo($orderListQuery );

if (!($stmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($orderId, $fname, $sname, $model, $size, $shipping_date, $vatExempt, $shippingLocation, $reconciled );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

$total = 0;

$time = mktime(0, 0, 0, $month);
$monthName = strftime("%b", $time);

include 'inc/header.php';
?>

<script src="js/jquery.tabletoCSV.js"></script>

<div class="container">

	<h4>Bikes shipped by Date</h4><h5><?php echo $monthName ?> <?php echo $year ?></h5>
	
	<div class="table-responsive">
		<table id="orderTable" class="table table-bordered table-condensed">
			<tbody>
			<tr>
				<th>Order Id</th><th>reconciled</th><th>Name</th><th>Bike</th><th>Size</th><th>Cost</th><th>Ship Date</th><th>Shipped From</th>
			</tr>
			
			<?php while ($stmt->fetch()) { ?>
				
			<?php 
				$cost = fetchTotalPrice($orderId); 
				if( !$vatExempt ){
					$cost = $cost/1.2;
				}
				$total = $total + $cost;	
			?>	
			
				
			<tr>
				<td><a href="editorder.php?orderId=<?php echo $orderId ?>" target="_blank"><?php echo $orderId ?></a></td>
				<td><?php echo ( $reconciled ? 'yes' : 'no' ) ?></td>
				<td><?php echo ( $fname." ".$sname ) ?></td>
				<td><?php echo ( $model ) ?></td>
				<td><?php echo ( $size ) ?></td>
				<td><?php echo ( number_format($cost,2) ) ?></td>
				<td><?php echo $shipping_date ?></td>
				<td><?php echo ( $shippingLocation == 0 ? "Liv" : "L-Spa" ) ?></td>
			</tr>
			
			<?php }?>
			<tr>
				<td><b><?php echo $resultsSize ?> bikes shipped</b></th><td></td><td></td><td></td><td><b>Total (ex VAT)</b></td><td><b><?php echo number_format($total,2) ?></b></td><td></td><td></td>
			</tr>
		</tbody>			
		</table>	
</div>		


<?php include '../inc/footer.php'; ?>
