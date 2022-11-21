<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_GET["orderId"];
if( !$orderId )
	$orderId = $_POST["orderId"];

$edit = false;
if( isset($_GET["mode"] ) ){
    if( $_GET["mode"] == "EDIT" ){
        $edit = true;
    }
}

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
"SELECT notes
FROM orders 
WHERE id = ".$orderId;

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result( $notes );
$stmt->store_result();
$stmt->fetch();

?>

<div class="panel-heading">

<?php if ( $edit ) {?>
  	<a href="javascript:cancelOrderNotesEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enableOrderNotesEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>

<h3 class="panel-title">Order Notes</h3>
</div>

<div class="panel-body">

	<dl class="dl-horizontal">
	<dt>Notes</dt>
	    <?php if($edit) {?>
	<dd><textarea name="order_notes" id="order_notes" cols="45" rows="5"><?php echo $notes ?></textarea></dd>
	    <?php } else {?>	  
	<dd><?php echo nl2br(isset($notes) ? $notes : "") ?></dd>
		<?php } ?>
	</dl>	

   <?php if($edit) {?>
 <button onclick="javascript:editOrderNotes(<?php echo $orderId ?>);" style="float:right;">save</button>
   <?php } ?>

</div>

 <script>

  function editOrderNotes( orderId ){
	
	var orderNotes = $('#order_notes').val();   

	var parray = [
		{ name: "orderNotes", value: orderNotes },
		{ name: "orderId", value: orderId }
	];	
	
	var url = "actions/editOrderNotes.php";
	  
	$('#orderNotesPanel').load( url, parray, function(){
		  $('#orderNotesPanel').load( "panels/order_notes_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
	  }  );
  }	

  function enableOrderNotesEdit(){
  	  $('#orderNotesPanel').load( "panels/order_notes_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
  } 

  function cancelOrderNotesEdit(){
  	  $('#orderNotesPanel').load( "panels/order_notes_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
  } 
 </script>  

