<?php
require 'configuration.php';
require 'functions/harefn.php';


$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$date = $_GET["date"];
if( !$date ){
	$date = date('t-m-Y');
}

$dateparts = explode( "-", $date );
$day = $dateparts[0];
$month = $dateparts[1];
$year = $dateparts[2];

$orderListQuery =
	
"SELECT payment_amount, 
		UNIX_TIMESTAMP(payment_date), 
		payments.order_id, 
		shipping_date, 
		sname, 
		fname, 
		vat_exempt,
		cancelled
FROM 	payments
LEFT JOIN 
		shipping
ON shipping.order_id = payments.order_id
LEFT JOIN 
		orders
ON orders.id = payments.order_id
WHERE ( payment_date <= '".$date."' AND ( shipping_date is null OR ( shipping_date > '".$date."' ) ) )
ORDER BY orders.id";

// echo($orderListQuery );

if (!($stmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result( $amount, $payment_date, $orderId, $shipping_date, $sname, $fname, $vatExempt, $cancelled );
$stmt->store_result();
$resultsSize = $stmt->num_rows;


include 'inc/header.php';
?>

<script src="js/jquery.tabletoCSV.js"></script>

<div class="container">

	<p>Date : <strong><?php echo($date)?></strong></p>


	<div>
		Up to end : 
		<select id="dateSelect">

			<option <?php echo( $date == '2022-12-31' ? 'selected' : '' ) ?> value="2022-12-31">December 2022</option>
			<option <?php echo( $date == '2022-11-30' ? 'selected' : '' ) ?> value="2022-11-30">November 2022</option>
			<option <?php echo( $date == '2022-10-31' ? 'selected' : '' ) ?> value="2022-10-31">October 2022</option>
			<option <?php echo( $date == '2022-09-30' ? 'selected' : '' ) ?> value="2022-09-30">September 2022</option>
			<option <?php echo( $date == '2022-08-31' ? 'selected' : '' ) ?> value="2022-08-31">August 2022</option>
			<option <?php echo( $date == '2022-07-31' ? 'selected' : '' ) ?> value="2022-07-31">July 2022</option>
			<option <?php echo( $date == '2022-06-30' ? 'selected' : '' ) ?> value="2022-06-30">June 2022</option>
			<option <?php echo( $date == '2022-05-31' ? 'selected' : '' ) ?> value="2022-05-31">May 2022</option>
			<option <?php echo( $date == '2022-04-30' ? 'selected' : '' ) ?> value="2022-04-30">April 2022</option>
			<option <?php echo( $date == '2022-03-31' ? 'selected' : '' ) ?> value="2022-03-31">March 2022</option>
			<option <?php echo( $date == '2022-02-28' ? 'selected' : '' ) ?> value="2022-02-28">February 2022</option>
			<option <?php echo( $date == '2022-01-31' ? 'selected' : '' ) ?> value="2022-01-31">January 2022</option>
			<option <?php echo( $date == '2021-12-31' ? 'selected' : '' ) ?> value="2021-12-31">December 2021</option>
			<option <?php echo( $date == '2021-11-30' ? 'selected' : '' ) ?> value="2021-11-30">November 2021</option>
			<option <?php echo( $date == '2021-10-31' ? 'selected' : '' ) ?> value="2021-10-31">October 2021</option>
			<option <?php echo( $date == '2021-09-30' ? 'selected' : '' ) ?> value="2021-09-30">September 2021</option>
			<option <?php echo( $date == '2021-08-31' ? 'selected' : '' ) ?> value="2021-08-31">August 2021</option>
			<option <?php echo( $date == '2021-07-31' ? 'selected' : '' ) ?> value="2021-07-31">July 2021</option>
			<option <?php echo( $date == '2021-06-30' ? 'selected' : '' ) ?> value="2021-06-30">June 2021</option>
			<option <?php echo( $date == '2021-05-31' ? 'selected' : '' ) ?> value="2021-05-31">May 2021</option>
			<option <?php echo( $date == '2021-04-30' ? 'selected' : '' ) ?> value="2021-04-30">April 2021</option>
			<option <?php echo( $date == '2021-03-31' ? 'selected' : '' ) ?> value="2021-03-31">March 2021</option>
			<option <?php echo( $date == '2021-02-28' ? 'selected' : '' ) ?> value="2021-02-28">February 2021</option>
			<option <?php echo( $date == '2021-01-31' ? 'selected' : '' ) ?> value="2021-01-31">January 2021</option>
			<option <?php echo( $date == '2020-12-31' ? 'selected' : '' ) ?> value="2020-12-31">December 2020</option>
			<option <?php echo( $date == '2020-11-30' ? 'selected' : '' ) ?> value="2020-11-30">November 2020</option>
			<option <?php echo( $date == '2020-10-31' ? 'selected' : '' ) ?> value="2020-10-31">October 2020</option>
			<option <?php echo( $date == '2020-09-30' ? 'selected' : '' ) ?> value="2020-09-30">September 2020</option>
			<option <?php echo( $date == '2020-08-31' ? 'selected' : '' ) ?> value="2020-08-31">August 2020</option>
			<option <?php echo( $date == '2020-07-31' ? 'selected' : '' ) ?> value="2020-07-31">July 2020</option>
			<option <?php echo( $date == '2020-06-30' ? 'selected' : '' ) ?> value="2020-06-30">June 2020</option>
			<option <?php echo( $date == '2020-05-31' ? 'selected' : '' ) ?> value="2020-05-31">May 2020</option>
			<option <?php echo( $date == '2020-04-30' ? 'selected' : '' ) ?> value="2020-04-30">April 2020</option>
			<option <?php echo( $date == '2020-03-31' ? 'selected' : '' ) ?> value="2020-03-31">March 2020</option>
			<option <?php echo( $date == '2020-02-28' ? 'selected' : '' ) ?> value="2020-02-28">February 2020</option>
			<option <?php echo( $date == '2020-01-31' ? 'selected' : '' ) ?> value="2020-01-31">January 2020</option>
			<option <?php echo( $date == '2019-12-31' ? 'selected' : '' ) ?> value="2019-12-31">December 2019</option>
			<option <?php echo( $date == '2019-11-30' ? 'selected' : '' ) ?> value="2019-11-30">November 2019</option>
			<option <?php echo( $date == '2019-10-31' ? 'selected' : '' ) ?> value="2019-10-31">October 2019</option>
			<option <?php echo( $date == '2019-09-30' ? 'selected' : '' ) ?> value="2019-09-30">September 2019</option>
			<option <?php echo( $date == '2019-08-31' ? 'selected' : '' ) ?> value="2019-08-31">August 2019</option>
			<option <?php echo( $date == '2019-07-31' ? 'selected' : '' ) ?> value="2019-07-31">July 2019</option>
			<option <?php echo( $date == '2019-06-30' ? 'selected' : '' ) ?> value="2019-06-30">June 2019</option>
			<option <?php echo( $date == '2019-05-31' ? 'selected' : '' ) ?> value="2019-05-31">May 2019</option>
			<option <?php echo( $date == '2019-04-30' ? 'selected' : '' ) ?> value="2019-04-30">April 2019</option>
			<option <?php echo( $date == '2019-03-31' ? 'selected' : '' ) ?> value="2019-03-31">March 2019</option>
			<option <?php echo( $date == '2019-02-28' ? 'selected' : '' ) ?> value="2019-02-28">February 2019</option>
			<option <?php echo( $date == '2019-01-31' ? 'selected' : '' ) ?> value="2019-01-31">January 2019</option>
			<option <?php echo( $date == '2018-12-30' ? 'selected' : '' ) ?> value="2018-12-30">December 2018</option>
			<option <?php echo( $date == '2018-11-30' ? 'selected' : '' ) ?> value="2018-11-30">November 2018</option>
			<option <?php echo( $date == '2018-10-30' ? 'selected' : '' ) ?> value="2018-10-30">October 2018</option>
			<option <?php echo( $date == '2018-09-30' ? 'selected' : '' ) ?> value="2018-09-30">September 2018</option>
			<option <?php echo( $date == '2018-08-30' ? 'selected' : '' ) ?> value="2018-08-30">August 2018</option>
			<option <?php echo( $date == '2018-07-30' ? 'selected' : '' ) ?> value="2018-07-30">July 2018</option>
			<option <?php echo( $date == '2018-06-30' ? 'selected' : '' ) ?> value="2018-06-30">June 2018</option>
			<option <?php echo( $date == '2018-05-30' ? 'selected' : '' ) ?> value="2018-05-30">May 2018</option>
			<option <?php echo( $date == '2018-04-30' ? 'selected' : '' ) ?> value="2018-04-30">April 2018</option>
			<option <?php echo( $date == '2018-03-31' ? 'selected' : '' ) ?> value="2018-03-31">March 2018</option>
			<option <?php echo( $date == '2018-02-28' ? 'selected' : '' ) ?> value="2018-02-28">February 2018</option>
			<option <?php echo( $date == '2018-01-31' ? 'selected' : '' ) ?> value="2018-01-31">January 2018</option>
		</select>	
	</div>
	
	&nbsp;
	
	
	<div class="table-responsive">
		<table id="orderTable" class="table table-bordered table-condensed">
			<tbody>
			<tr>
				<th>Order Id</th><th>Name</th><th>Gross</th><th>Net</th><th>VAT Exempt</th><th>Payment Date</th><th>Ship Date</th>
			</tr>
			
			<?php $ntotal = 0; $gtotal = 0 ?>
			<?php while ($stmt->fetch()) { ?>
			
			<?php
				$net = $vatExempt ? $amount : ( $amount/1.2 ); 
				$ntotal += $net;
				$gtotal += $amount;
				$bgstyle = ( $cancelled ? "bg-danger" : "" );
			?>	
			<tr class="<?php echo $bgstyle ?>">
				<td><a href="editorder.php?orderId=<?php echo $orderId ?>" target="_blank"><?php echo $orderId ?></a></td>
				<td><?php echo ( ($cancelled ? "[CANCELLED] " : "" ).$fname." ".$sname ) ?></td>
				<td><?php curry( $amount) ?></td>
				<td><?php curry( $net) ?></td>
				<td><?php echo ( $vatExempt ? "Y" : "N" ) ?></td>
				<td><?php echo date( DATEFORMAT, $payment_date ) ?></td>
				<td><?php echo $shipping_date ?></td>
			</tr>
			
			<?php }?>
			<tr>
				<td></td>
				<td></td>
				<td><h4><?php echo number_format($gtotal,2) ?></h4></td>
				<td><h4><?php echo number_format($ntotal,2) ?></h4></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>			
		</table>	
		<h3><h3>
</div>		

<script>
	
	$('#dateSelect').change( function(e){
		//console.log(this.value);
		window.location = "rep_incomeinadvance.php?date="+this.value;
	});
	
</script>	


<?php include '../inc/footer.php'; ?>
