<?php 

include_once ( __DIR__.'/configuration.php' );
include_once ( __DIR__.'/functions/harefn.php' );

$orderId = $_GET["orderId"];

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = "select id, UNIX_TIMESTAMP(date) as dte, comment from comms where order_id = ".$orderId." order by date DESC" ; 

if (!($statement = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$statement->execute()) {
    echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
}

$statement->bind_result( $id, $date, $comment );
$statement->store_result();
$resultsSize = $statement->num_rows;

?>


<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="paymentsModalLabel">Comments</h4>
</div>

<div class="modal-body">
	
	<div>
		<textarea id="newComment" name="newComment" cols="60" rows="6"></textarea>
		<button onclick="addComment(<?php echo $orderId ?>)">Add New Comment</button>
		<br>	
	</div>		
				<hr>	
		
		<?php while ($statement->fetch()) {?>
			<dl class="dl-horizontal">
			<dt><?php echo date( DATEFORMAT, $date ) ?></dt>
			<dd><?php echo $comment ?></dd>
			<dt></dt>
			<dd><a href="#" onclick="deleteComment(<?php echo $orderId ?>, <?php echo $id ?>);"><span class="pull-right glyphicon glyphicon-remove" aria-hidden="true"></a></dd>	
			</dl>
				<hr>	
		<?php }?>
</div>


