<?php 

require 'configuration.php';
require 'functions/harefn.php';

$orderId = $_GET["orderId"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$paymentsListQuery = 
"SELECT 
	payments.id, 
	UNIX_TIMESTAMP(payment_date) as pd, 
	payment_amount 
FROM payments
LEFT JOIN orders
ON orders.id = order_id
WHERE order_id = ".$orderId; 

//echo $paymentsListQuery;

if (!($paymentsStatement = $mysqli->prepare($paymentsListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$paymentsStatement->execute()) {
    echo "Execute failed: (" . $paymentsStatement->errno . ") " . $paymentsStatement->error;
}

$paymentsStatement->bind_result($paymentId, $paymentDate, $paymentAmount );
$paymentsStatement->store_result();
$paymentsResultsSize = $paymentsStatement->num_rows;

$summaryQuery = 
"SELECT 
	orders.payment_pending, 
	orders.invoicable,
	sum( orders.shipping_cost + orders.base_price  + 
		( ( select COALESCE(sum(amount),0) from line_items where line_items.order_id = orders.id) )
	) as totalPrice
FROM orders
WHERE orders.id = ".$orderId; 

if (!($summaryStatement = $mysqli->prepare($summaryQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$summaryStatement->execute()) {
    echo "Execute failed: (" . $summaryStatement->errno . ") " . $summaryStatement->error;
}

$summaryStatement->bind_result($paymentPending, $invoicable, $totalPrice );
$summaryStatement->fetch();
//$summaryStatement->store_result();

?>


<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="paymentsModalLabel">Payments</h4>
</div>

<div class="modal-body">
		
	<table class="table table-striped table-bordered table-hover table-condensed">
		<tbody>
		<tr>
			<th>Payment Date</th><th>Amount</th><th>Actions</th>
		</tr>
		<?php $totalPaid = 0;?>	
		<?php while ($paymentsStatement->fetch()) {
			$totalPaid+=$paymentAmount;
		?>
    		<tr>
				<td><?php echo date( DATEFORMAT, $paymentDate ) ?></td>
				<td><?php echo curry( $paymentAmount) ?></td>
				<td><a href="#" onclick="deletePayment(<?php echo $orderId ?>, <?php echo $paymentId ?>);"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
			</tr>
		<?php }?>
    		<tr>
				<td><input name="paymentDate" id="newPaymentDate" value="<?php echo date("Y-m-d");?>" type="date"></td>
				<td><input name="paymentAmount" id="newPaymentAmount" min="1" step="any" value="100.00" type="number"></td>
				<td><button onclick="addPayment(<?php echo $orderId ?>)">Add Payment</button></td>
			</tr>
			<tr>
				<td class="text-right">Total Order Value</td>
				<td><strong><?php echo curry($totalPrice) ?></strong></td>
				<td></td>
			</tr>	
			<tr>
				<td class="text-right">Total Paid</td>
				<td><strong><?php echo curry($totalPaid) ?></strong></td>
				<td></td>
			</tr>	
			<tr>
				<td class="text-right">Total Outstanding</td>
				<td><strong><?php echo curry($totalPrice - $totalPaid)  ?></strong></td>
				<td></td>
			</tr>	
	</tbody>			
	</table>
	<hr>
	<div><p>awaiting payment? <input type="checkbox" <?php echo ( $paymentPending ? "checked" : "" )?> onclick="togglePaymentPending(<?php echo $orderId ?>);"></p>
	<div><p>invoicable? <input type="checkbox" <?php echo ( $invoicable ? "checked" : "" )?> onclick="toggleInvoicable(<?php echo $orderId ?>);"></p>
	
</div>


