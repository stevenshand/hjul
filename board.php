<?php

require 'configuration.php';
require 'functions/harefn.php';

$view = ( isset( $_GET["view"] ) ? $_GET["view"] : "assembly" ) ;

$isSortable = false;

$frameType = "custom";

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$targetWeeksQuery = "SELECT min(target_week), max(target_week) FROM orders where status < 7";		

if (!($stmt = $mysqli->prepare($targetWeeksQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($minTargetWeek, $maxTargetWeek);
$stmt->store_result();
$stmt->fetch();

include 'inc/header.php';
?>
	
<div class="container" id="sortable_cards">

<div class="row navbuttons hidden-print">
	<div class="col-md-6 buttonContainer"> 
	<a href="?view=assembly" class="btn btn-default<?php echo ( $view == 'assembly' ? ' active' : '' ) ?>">Assembly</a>
	<a href="?view=paint" class="btn btn-default<?php echo ( $view == 'paint' ? ' active' : '' ) ?>">Paint</a>
	<a href="?view=fabrication" class="btn btn-default<?php echo ( $view == 'fabrication' ? ' active' : '' ) ?>">Fabrication</a>	
	</div>
</div>

<div class="row visible-print">
	<h3 style="text-transform:uppercase;"><?php echo $view ?> VIEW</h3>
</div>

<?php 
	
	for( $targetWeek = $minTargetWeek; $targetWeek<=$maxTargetWeek+4; $targetWeek++ ) {  

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
	where (target_week = ".$targetWeek." AND orders.status < 7) 
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

<?php 

	if( $view == "paint" ){
		$displayTargetWeek = $targetWeek - 1; 
	} else if ( $view == "fabrication" ){
		$displayTargetWeek = $targetWeek - 2; 
	} else{ 
		$displayTargetWeek = $targetWeek; 
	}	
	
?> 

<!-- <div>view : <?php echo $view?>, display targetWeek:<?php echo $displayTargetWeek ?></div> -->
<div class="row sortable  weekrow<?php echo($displayTargetWeek == nowWeek() ? " bg-success" : "") ?>" data-weekNumber="<?php echo $displayTargetWeek ?>" id="row<?php echo $displayTargetWeek ?>">
    <div class="col-md-1">
	<h3><?php echo sprintf('%02d', $displayTargetWeek ) ?></h3>
	<?php $year = '2018' ?>
	<h4><?php echo date('M d',strtotime($year.'W'.sprintf('%02d', $displayTargetWeek ))) ?></h4>	
	</div>	

<?php while ($orderStmt->fetch()) { ?>

<?php 

if( in_array( $modelId, array("5","6","7","15","16","17","18") ) ){
	$frameType="bahookie";
} elseif ( in_array( $modelId, array("1","14") ) ){
	$frameType="stoater";
} elseif ( in_array( $modelId, array("2,23") ) ){
	$frameType="stoaterPD";
} elseif ( in_array( $modelId, array("4") ) ){
	$frameType="stooshie";
}else{
	$frameType="custom";
}	

$orderTime = (nowWeek()-$orderWeekNum);
if( $orderTime<0 ){
	$orderTime = $orderTime+52; 
};

?>



    <div class="col-md-2 <?php echo ( $targetLocked ? "locked" : "card" ) ?>" data-orderId="<?php echo $orderId ?>">
		<div class="panel panel-default modelcard <?php echo ( $targetLocked ? "locked" : "" ) ?> <?php echo ( "status_".$statusId ) ?>">
			<div class="panel-heading">
			    <h3 class="panel-title"><a href="/editorder.php?orderId=<?php echo $orderId ?>"><?php echo $fname ?> <?php echo $sname ?></a></h3>
			  	<sup style="float:right;margin-right:0px;"><?php echo $orderTime ?></sup>
			  </div>
		  <div class="panel-body">
		    <span><b><?php echo $model ?>&nbsp;<?php echo ($frameOnly ? "(f)" : "" ) ?></b><span><br>
			<span><?php echo $status ?></span>
			<span class="pull-right"><?php echo $size ?></span><br>
		  <div class="card-footer <?php echo $frameType ?>"></div>
		  </div>
		</div>
    </div>
<?php } ?>


	   
</div>

<hr class="weekDivider">

<script>


if( <?php echo $view == "assembly" ? true : false ?> ){	
	$( "#row<?php echo $targetWeek ?>" ).sortable({
		items:".card",
		connectWith:".weekrow",
		receive: dropped
	});
}

function dropped(event, ui){
	
	var orderId = ui["item"].attr("data-orderId");
	var updatedWeekNumber = $(event.target).attr("data-weekNumber");

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
