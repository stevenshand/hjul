<?php


include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 'select id, sname from orders where payment_pending = 1';


if (!($stmt_cf = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt_cf->execute()) {
    echo "Execute failed: (" . $stmt_cf->errno . ") " . $stmt_cf->error;
}

$stmt_cf->bind_result($orderId, $sname);
$stmt_cf->store_result();

$pendingPayments = 0;

$pop = '';

while ($stmt_cf->fetch()) {
	$pending = ( totalCost($orderId) - totalPayments($orderId) );
	
	$pop = $pop.steph($pending).' - '.$sname.'<br>';
	$pendingPayments += $pending;
 }
?>

<div>
	<div>
		Payments Pending : <?php curry($pendingPayments) ?> <span data-toggle="tooltip" 
								data-html="true" 
								data-container="body" 
								data-placement="left" 
								title="<?php echo $pop ?>" class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
								
								
		&nbsp;&nbsp;&nbsp;Orderbook Value : <span id="orderBookTotal"></span>							
	</div>
</div>
