<?php
require 'configuration.php';
require 'functions/harefn.php';

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderListQuery =
	
"SELECT orders.id, sname, orders.order_date, base_price
FROM orders
WHERE YEAR(orders.order_date) = 2015
-- AND orders.frame_only = 1
ORDER BY orders.id";

// -- WHERE orders.order_date > ( now() - interval 1 year )

//select id from orders where order_date > curdate() - interval 1 year;

// echo($orderListQuery );

if (!($stmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result( $orderId, $sname, $orderDate, $basePrice );
$stmt->store_result();
$resultsSize = $stmt->num_rows;


include 'inc/header.php';
?>

<script src="js/jquery.tabletoCSV.js"></script>

<div class="container">

	<div class="table-responsive">
		<table id="orderTable" class="table table-bordered table-condensed">
			<tbody>
			<tr>
				<th>Order Id</th><th>Name</th><th>Base Price</th>
			</tr>
			
			<?php $count = 0; $gtotal = 0 ?>
			<?php while ($stmt->fetch()) { ?>
			
			<?php
				$lineStyle = "";
				if( $basePrice > 0 ){
					$count = $count+1 ;
					$gtotal += $basePrice;
				}else{
					$lineStyle = " bg-danger";
				}
			?>	
			<tr class="<?php echo $lineStyle ?>">
				<td><a href="editorder.php?orderId=<?php echo $orderId ?>" target="_blank"><?php echo $orderId ?></a></td>
				<td><?php echo ( $fname." ".$sname ) ?></td>
				<td><?php echo ( money_format('%i', $basePrice) ) ?></td>
			</tr>
			
			<?php }?>
			<tr>
				<td><h4><?php echo $count ?> Orders</h4></td>
				<td></td>
				<td><h4>Total Revenue : £<?php echo number_format($gtotal,2) ?> <small>(inc VAT)</small></h4></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td><h4>Average : £<?php echo number_format( $gtotal/$count ,2) ?> <small>(inc VAT)</small></td>
			</tr>
		</tbody>			
		</table>	
		<h3><h3>
</div>		


<?php include '../inc/footer.php'; ?>
