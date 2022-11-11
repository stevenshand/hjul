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


$rohloffQuery = "SELECT rohloffSerial
FROM 	rohloff_serial 
WHERE orderId = ".$orderId; 

if (!($stmt = $mysqli->prepare($rohloffQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result( $rohloffSerial );
$stmt->store_result();
$stmt->fetch();

$query = 
"SELECT id,
		fab_details
FROM 	fabrication_details 
WHERE order_id = ".$orderId;

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result( $detailId, $detail );
$stmt->store_result();
$stmt->fetch();

$itemQuery = 
"SELECT id,
		description,
		amount
FROM 	line_items 
WHERE order_id = ".$orderId." AND category = 'fab'";

if (!($stmt = $mysqli->prepare($itemQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $description, $amount);
$stmt->store_result();
$itemsSize = $stmt->num_rows;

?>

<div class="panel-heading">

<?php if ( $edit ) {?>
  	<a href="javascript:cancelFabDetailsEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enableFabDetailEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>

<h3 class="panel-title">Fabrication</h3>
</div>

<div class="panel-body">

<?php if($edit) {?>
	<table id="fab_line_items" class="table table-striped table-bordered table-hover table-condensed">
		<th>Description</th><th>Amount</th><th>Action</th>
		<?php while ($stmt->fetch()) {?>
		<tr class="fab_line_item">
			<td><input class="fab_line_item_desc" type="text" value="<?php echo $description ?>"></td>
			<td>£<input class="fab_line_item_amount" type="text" value="<?php echo( $amount ) ?>"></td>
			<td><a onclick="deleteLineItem(this);"><span style="float:right;" class="glyphicon glyphicon-remove"></a></td>
		</tr>	
		<?php } ?>
		<tr class="fab_line_item">
			<td><input class="fab_line_item_desc" type="text" value=""></td>
			<td>£<input class="fab_line_item_amount" type="text" value=""></td>
			<td><a onclick="deleteLineItem(this);"><span style="float:right;" class="glyphicon glyphicon-remove"></a></td>
		</tr>	
		<tr id="itemAddButton">
			<td colspan="3"><a onclick="addLineItem();"><span style="float:right;" class="glyphicon glyphicon-plus"></a></td>
		</tr>	
	</table>	
<?php } ?>


<dl class="dl-horizontal">
<dt>Notes</dt>
    <?php if($edit) {?>
<dd><textarea name="fab_details" id="fab_details" cols="45" rows="5"><?php echo $detail ?></textarea></dd>		
    <?php } else {?>	  
<dd><?php echo nl2br( isset($detail) ? $detail : "" ) ?></dd>
	<?php } ?>


	
<?php if(!$edit) {?>	  
	<?php while ($stmt->fetch()) {?>
		<dt><?php curry( $amount ) ?></dt>
		<dd><?php echo $description ?></dd>
	<?php } ?>	
<?php } ?>
	
</dl>

<dl class="dl-horizontal">
<dt>Rohloff Serial</dt>
<?php if($edit) {?>
<dd><input id="rohloff_serial" class="fab_line_item_desc" type="text" value="<?php echo ( $rohloffSerial > 0 ? $rohloffSerial : '' ) ?>"></dd>
<?php } else {?>	  
<dd><?php echo nl2br( $rohloffSerial > 0 ? $rohloffSerial : '' ) ?></dd>		
<?php } ?>	
</dl>



   <?php if($edit) {?>
 <button onclick="javascript:editFabDetails(<?php echo $orderId ?>);" style="float:right;">save</button>
   <?php } ?>

</div>

 <script>

  function editFabDetails( orderId ){
	
	var fabDetails = $('#fab_details').val();   
	var rohloffSerial = $('#rohloff_serial').val();   

	var parray = [
		{ name: "fabDetails", value: fabDetails },
		{ name: "rohloffSerial", value: rohloffSerial },
		{ name: "orderId", value: orderId }
	];	
	
	$('.fab_line_item').each( function(index) {
		
		var desc = $(this).find('.fab_line_item_desc').val();
		var amount = $(this).find('.fab_line_item_amount').val();
		if (desc.trim().length > 0)
			parray.push({ name: "li_"+desc, value: amount } )
	} );
	
			
	var url = "actions/editFabricationDetails.php";
	  
	$('#fabDetailsPanel').load( url, parray, function(){
		  $('#orderDetailsPanel').load( "panels/order_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
	  }  );
  }	

  function deleteLineItem(element){
	  $(element).parent().parent().remove();
  }	

  function addLineItem(){
	  var line = $('#fab_line_items').find('.fab_line_item').last();
	  var newLine = $(line).clone();
	  line.after(newLine);
  }	

  function enableFabDetailEdit(){
  	  $('#fabDetailsPanel').load( "panels/fabrication_details_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
  } 

  function cancelFabDetailsEdit(){
  	  $('#fabDetailsPanel').load( "panels/fabrication_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
  } 
 </script>  

