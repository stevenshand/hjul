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
"SELECT paint_details.id,
		paint_details,
		paint_confirmed
FROM 	paint_details 
LEFT JOIN orders 
ON orders.id = order_id 
WHERE order_id = ".$orderId;

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result( $detailId, $detail, $paintConfirmed );
$stmt->store_result();
$stmt->fetch();

$itemQuery = 
"SELECT id,
		description,
		amount
FROM 	line_items 
WHERE order_id = ".$orderId." AND category = 'pnt'";

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
  	<a href="javascript:cancelPaintDetailsEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enablePaintDetailEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>

<h3 class="panel-title">Paint</h3>
</div>

<div class="panel-body">

<?php if($edit) {?>
	<table id="paint_line_items" class="table table-striped table-bordered table-hover table-condensed">
		<th>Description</th><th>Amount</th><th>Action</th>
		<?php while ($stmt->fetch()) {?>
		<tr class="paint_line_item">
			<td><input class="paint_line_item_desc" type="text" value="<?php echo $description ?>"></td>
			<td>£<input class="paint_line_item_amount" type="text" value="<?php echo( $amount ) ?>"></td>
			<td><a onclick="deletePaintLineItem(this);"><span style="float:right;" class="glyphicon glyphicon-remove"></a></td>
		</tr>	
		<?php } ?>
		<tr class="paint_line_item">
			<td><input class="paint_line_item_desc" type="text" value=""></td>
			<td><input class="paint_line_item_amount" type="text" value=""></td>
			<td><a onclick="deletePaintLineItem(this);"><span style="float:right;" class="glyphicon glyphicon-remove"></a></td>
		</tr>	
		<tr id="paintItemAddButton">
			<td colspan="3"><a onclick="addPaintLineItem();"><span style="float:right;" class="glyphicon glyphicon-plus"></a></td>
		</tr>	
	</table>	
<?php } ?>

<dl class="dl-horizontal">
<dt>Notes</dt>
    <?php if($edit) {?>
<dd><textarea name="paint_details" id="paint_details" cols="45" rows="5"><?php echo $detail ?></textarea></dd>		
    <?php } else {?>	  
<dd><?php echo nl2br( isset($detail) ? $detail : "" ) ?></dd>
	<?php } ?>


	
<?php if(!$edit) {?>	  
	<?php while ($stmt->fetch()) {?>
		<dt><?php curry( $amount ) ?></dt>
		<dd><?php echo $description ?></dd>
	<?php } ?>	
<?php } ?>


<!-- paint_confirmed -->	
  <dt>Paint confirmed</dt>
  <?php if($edit) {?>
  <dd><input type="checkbox" <?php echo ( $paintConfirmed ? "checked" : "" ) ?> name="editPaintConfirmedField" id="editPaintConfirmedField"></dd>
  <?php } else {?>	  
  <dd><?php echo( $paintConfirmed ? "yes" : "no" ) ?></dd>
  <?php } ?>
<!-- paint_confirmed -->	

	
</dl>



	

   <?php if($edit) {?>
 <button onclick="javascript:editPaintDetails(<?php echo $orderId ?>);" style="float:right;">save</button>
   <?php } ?>

</div>

 <script>

  function editPaintDetails( orderId ){
	
	var paintDetails = $('#paint_details').val();   
	var paintConfirmed = $('#editPaintConfirmedField').is(':checked');  

	var parray = [
		{ name: "paintDetails", value: paintDetails },
		{ name: "paintConfirmed", value: paintConfirmed },
		{ name: "orderId", value: orderId },
		
	];	
	
	$('.paint_line_item').each( function(index) {
		
		var desc = $(this).find('.paint_line_item_desc').val();
		var amount = $(this).find('.paint_line_item_amount').val();
		if (desc.trim().length > 0)
			parray.push({ name: "li_"+desc, value: amount } )
	} );
	
			
	var url = "actions/editPaintDetails.php";
	  
	$('#paintDetailsPanel').load( url, parray, function(){
		  $('#orderDetailsPanel').load( "panels/order_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
	  }  );
  }	

  function deletePaintLineItem(element){
	  $(element).parent().parent().remove();
  }	

  function addPaintLineItem(){
	  var line = $('#paint_line_items').find('.paint_line_item').last();
	  var newLine = $(line).clone();
	  line.after(newLine);
  }	

  function enablePaintDetailEdit(){
  	  $('#paintDetailsPanel').load( "panels/paint_details_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
  } 

  function cancelPaintDetailsEdit(){
  	  $('#paintDetailsPanel').load( "panels/paint_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
  } 
 </script>  

