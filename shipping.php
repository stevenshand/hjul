<?php 

include_once ( __DIR__.'/configuration.php' );
include_once ( __DIR__.'/functions/harefn.php' );

$orderId = $_GET["orderId"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = "select id, UNIX_TIMESTAMP(shipping_date) as sd, method, tracking_number from shipping where order_id = ".$orderId; 

//echo $query;

if (!($statement = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$statement->execute()) {
    echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
}

$statement->bind_result( $id, $shippingDate, $method, $trackingNumber );
$statement->store_result();
$resultsSize = $statement->num_rows;

?>


<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="paymentsModalLabel">Shipping</h4>
</div>

<div class="modal-body">
		
	<table class="table table-striped table-bordered table-hover table-condensed">
		<tbody>
		<tr>
			<th>Shipping Date</th><th>Method</th><th>Tracking #</th><th>Actions</th>
		</tr>
		
		<?php while ($statement->fetch()) {?>
    		<tr>
				<td><?php echo date( DATEFORMAT, $shippingDate ) ?></td>
				<td><?php echo $method ?></td>
				<td><?php echo $trackingNumber ?></td>
				<td><a href="#" onclick="deleteShipping(<?php echo $orderId ?>, <?php echo $id ?>);"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
			</tr>
		<?php }?>
    		<tr>
				<td><input name="shippingDate" id="newShippingDate" type="date"></td>
				<td>
					<select name="shippingMethod" id="newShippingMethod">
						<option>Collection</option>
						<option>TNT</option>
						<option>Parcel Force</option>
						<option>DHL</option>
						<option>UPS</option>
						<option>FEDEX</option>
					</select>
				</td>		
				<td><input name="trackingNumber" id="tracking" type="text"></td>
				<td><button onclick="addShipping(<?php echo $orderId ?>)">Add Shipping</button></td>
			</tr>
	</tbody>			
	</table>
</div>


