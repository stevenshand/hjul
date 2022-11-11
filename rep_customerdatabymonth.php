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
"SELECT email,
		orders.id as orderId,
		order_date as dt,
		models.name,
		sizes.size as framesize,
		frame_only,
		fname,
		sname,
		line1,
		line2,
		line3,
		town,
		postcode,
		if( ( instr( models.name, 'rohloff' ) > 0 ), 'yes', 'no' ) 
FROM 	orders 
LEFT JOIN 
		address
ON orders.id = order_id		
LEFT JOIN 
		models
ON orders.model = models.id		
LEFT JOIN 
		shipping
ON orders.id = shipping.order_id		
LEFT JOIN 
		sizes
ON orders.size = sizes.id 
WHERE month(order_date) = ".$month." AND year(order_date) = ".$year."  
GROUP BY orders.id ORDER BY order_date ASC";

if (!($stmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($email, $orderId, $orderDate, $modelName, $frameSize, $frameOnly, $fname, $sname, $line1, $line2, $line3, $town, $postcode, $rohloff );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

include 'inc/header.php';
?>

<script src="js/jquery.tabletoCSV.js"></script>

<div class="container">

	<div class="row">
		<div class="col-md-12">
			
			<?php 	$dateObj   = DateTime::createFromFormat('!m', $month);
					$monthName = $dateObj->format('F'); 
			?>
			
			<h3>Customer Data for <?php echo ( $monthName." ".$year ) ?></span></h3>
				<button id="export" data-export="export">export</button>
				<br>
				<br>
				<table class="table" id="abtable" style="font-size:smaller">
				<tr>
					<th>ID</th>
					<th>Email</th>
					<th>Firstname</th>
					<th>Surname</th>
					<th>Model</th>
					<th>Rohloff</th>
					<th>Size</th>
					<th>Frame Only</th>
					<th>Order Date</th>
					<th>Shipping Date</th>
					<th>Line 1</th>
					<th>Line 2</th>
					<th>Line 3</th>
					<th>Town</th>
					<th>Postcode</th>
					<th>Country</th>
					<th>Price</th>
				</tr>
				<?php while ($stmt->fetch()) { 
					$totalPrice = fetchTotalPrice($orderId);
					$totalCost = totalCost($orderId);
					$shippedDate = fetchShippedDate($orderId);	
					$country = fetchCountry($orderId);	
				?>	
					
				<tr>
					<td><a href = "/editorder.php?orderId=<?php echo $orderId ?>"><?php echo $orderId ?></a></td>
					<td><?php echo $email ?></td>
					<td><?php echo $fname ?></td>
					<td><?php echo $sname ?></td>
					<td><?php echo $modelName ?></td>
					<td><?php echo $rohloff ?></td>
					<td><?php echo $frameSize ?></td>
					<td><?php echo ( $frameOnly ? "yes" : "no" ) ?></td>
					<td><?php echo $orderDate ?></td>
					<td><?php echo $shippedDate ?></td>
					<td><?php echo $line1 ?></td>
					<td><?php echo $line2 ?></td>
					<td><?php echo $line3 ?></td>
					<td><?php echo $town ?></td>
					<td><?php echo $postcode ?></td>
					<td><?php echo $country ?></td>
					<td><?php echo $totalPrice ?></td>
				</tr>
				<?php } ?>	
			</table>
		</div>
		
	</div>
</div>		

<script>
	$("#export").click(function(){
	  $("#abtable").tableToCSV();
  	});
	
</script>	

<?php include '../inc/footer.php'; ?>

