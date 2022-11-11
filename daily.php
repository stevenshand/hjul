<?php
require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';


$dateP = $_GET["date"];	

if( isset( $dateP ) ){
	$dailyDate = $dateP; 
}else{
	$dailyDate = date(INPUTFIELDDATEFORMAT, time()); 	
}

//$yest = date_create_from_format(INPUTFIELDDATEFORMAT, $dateP );
// $yestDisp = $yest->format(INPUTFIELDDATEFORMAT);

	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
"SELECT id, activity_date, department, order_id, activity from timesheet where activity_date = DATE(".$dailyDate.") order by activity_date"; 

echo ($query);

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $date, $dept, $order, $activity );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

include 'inc/header.php';

?>


<div class="container">
	
	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<h3>Date: <?php echo $dailyDate ?></h3>
				<h4>Fab</h4>
				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th>Date</th><th>Dept</th><th>Order</th><th>Activity</th><th>Status</th>
					</tr>
						
					<?php while ($stmt->fetch()) { ?>
						<tr>
							<td><?php echo $id ?></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
						</tr>
					<?php }?>
				</tbody>			
				</table>
				
				<h4>Paint</h4>
				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th>Date</th><th>Dept</th><th>Order</th><th>Activity</th><th>Status</th>
					</tr>
						
					<?php while ($stmt->fetch()) { ?>
						<tr>
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
						</tr>
					<?php }?>
				</tbody>			
				</table>
					
				<h4>Assembly</h4>
				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th>Date</th><th>Dept</th><th>Order</th><th>Activity</th><th>Status</th>
					</tr>
						
					<?php while ($stmt->fetch()) { ?>
						<tr>
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
						</tr>
					<?php }?>
				</tbody>			
				</table>
			</div>	
		</div>	
	</div>	
</div>	

<script>

$(document).ready(function() {
});
</script>

<?php $stmt->close(); ?>

<?php include 'inc/footer.php'; ?>
