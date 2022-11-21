<?php

require 'configuration.php';
require 'functions/harefn.php';

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$targetDatesQuery = "SELECT min(target_date), max(target_date) FROM orders where status < 7";

if (!($stmt = $mysqli->prepare($targetDatesQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($minTargetDate, $maxTargetDate);
$stmt->store_result();
$stmt->fetch();

include 'inc/header.php';
?>
	
<div class="container" id="sortable_cards">

<?php

    $startDateForDisplayRaw = strtotime($minTargetDate);
    $endDateForDisplayRaw = strtotime($maxTargetDate);
	for( $targetWeek = $startDateForDisplayRaw; $targetWeek<=($endDateForDisplayRaw+(60*60*24*7*4)); $targetWeek+=(60*60*24*7) ) {

	$orderListQuery =
	"SELECT orders.id, 
			orders.model,
			sname,
			fname,
			models.name,
			sizes.size,
			status.status,
			orders.status,
			week(order_date),
			target_locked,
			frame_only
	FROM orders	
	LEFT JOIN 
			models
	ON orders.model = models.id	
	LEFT JOIN 
			status
	ON orders.status = status.id	
	LEFT JOIN 
			sizes 
	ON orders.size = sizes.id	
	where (week(target_date) = ".date('W',$targetWeek)." 
	AND year(target_date) = ".date('Y',$targetWeek)." 
	AND orders.status < 7) 
	ORDER BY order_date";


if (!($orderStmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$orderStmt->execute()) {
    echo "Execute failed: (" . $orderStmt->errno . ") " . $orderStmt->error;
}

$orderStmt->bind_result($orderId, $modelId, $sname, $fname, $model, $size, $status, $statusId, $orderWeekNum, $targetLocked, $frameOnly );
$orderStmt->store_result();

?>

<div class="row sortable  weekrow<?php echo($targetWeek == nowWeek() ? " bg-success" : "") ?>" data-weekNumber="<?php echo $targetWeek ?>" id="row<?php echo $targetWeek ?>">
    <div class="col-md-2">
    	<h4>Week <?php echo date('W',$targetWeek) ?></h4>
    	<p><?php echo date(DATEFORMAT, $targetWeek) ?></p>
	</div>

<?php while ($orderStmt->fetch()) { ?>

    <div class="col-md-2 <?php echo ( $targetLocked ? "locked" : "card" ) ?>" data-orderId="<?php echo $orderId ?>">
		<div class="panel panel-default modelcard <?php echo ( $targetLocked ? "locked" : "" ) ?> <?php echo ( "status_".$statusId ) ?>">
			<div class="panel-heading">
			    <h3 class="panel-title"><a href="/editorder.php?orderId=<?php echo $orderId ?>"><?php echo $fname ?> <?php echo $sname ?></a></h3>
			  </div>
		  <div class="panel-body">
		    <span><b><?php echo $model ?>&nbsp;<?php echo ($frameOnly ? "(f)" : "" ) ?></b><span><br>
			<span><?php echo $status ?></span>
			<span class="pull-right"><?php echo $size ?></span><br>
		  <div class="card-footer"></div>
		  </div>
		</div>
    </div>
<?php } ?>


	   
</div>

<hr class="weekDivider">

<script>


$( "#row<?php echo $targetWeek ?>" ).sortable({
    items:".card",
    connectWith:".weekrow",
    receive: dropped
});

function dropped(event, ui){
	
	let orderId = ui["item"].attr("data-orderId");
	let updatedWeekNumber = $(event.target).attr("data-weekNumber");

	$.ajax({
	  method: "POST",
	  url: "actions/updateTargetWeek.php",
	  data: { orderId: orderId, targetWeek: updatedWeekNumber }
	})
	  .done(function( msg ) {
	    console.log( "Data Saved: " + msg );
	  });

}

</script>
<?php } ?>

</div>

	 	 
	  
<?php
	include 'inc/footer.php';
?>
