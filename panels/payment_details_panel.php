<?php
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = "select id, UNIX_TIMESTAMP(payment_date), payment_amount from payments where order_id = ".$orderId; 

if (!($statement = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$statement->execute()) {
    echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
}

$statement->bind_result( $id, $paymentDate, $paymentAmount );
$statement->store_result();
$resultsSize = $statement->num_rows;

?>

  <div class="panel-heading">
	
	<a href="javascript:alert('edit this on main page please :-)' );"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	  
    <h3 class="panel-title">Payments</h3>
  </div>
  <div class="panel-body">
	<table class="table table-striped table-bordered table-hover table-condensed">
		<tbody>
		<tr>
			<th>Payment Date</th><th>Amount</th>
		</tr>

  		<?php $total = 0;?>	
  		<?php while ($statement->fetch()) {
  			$total+=$paymentAmount;
  		?>
      		<tr>
  				<td><?php echo date( DATEFORMAT, $paymentDate ) ?></td>
  				<td><?php echo curry($paymentAmount) ?></td>
  			</tr>
  		<?php }?>
  			<tr>
  				<td class="text-right">Total</td>
  				<td><strong><?php echo curry($total) ?></strong></td>
  			</tr>
	</tbody>			
	</table>
</div> 
