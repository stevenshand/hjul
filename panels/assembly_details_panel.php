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
"SELECT id,
		assembly_details
FROM 	assembly_details 
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
WHERE order_id = ".$orderId." AND category = 'ass'";

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
  	<a href="javascript:cancelAssemblyDetailsEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enableAssemblyDetailEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>

<h3 class="panel-title">Assembly</h3>
</div>

<div class="panel-body">

<?php if($edit) {?>
	<table id="assembly_line_items" class="table table-striped table-bordered table-hover table-condensed">
		<th>Description</th><th>Amount</th><th>Action</th>
		<?php while ($stmt->fetch()) {?>
		<tr class="assembly_line_item">
			<td><input class="assembly_line_item_desc" type="text" value="<?php echo $description ?>"></td>
			<td>£<input class="assembly_line_item_amount" type="text" value="<?php echo( $amount ) ?>"></td>
			<td><a onclick="deleteAssemblyLineItem(this);"><span style="float:right;" class="glyphicon glyphicon-remove"></a></td>
		</tr>	
		<?php } ?>
		<tr class="assembly_line_item">
			<td><input class="assembly_line_item_desc" type="text" value=""></td>
			<td>£<input class="assembly_line_item_amount" type="text" value=""></td>
			<td><a onclick="deleteAssemblyLineItem(this);"><span style="float:right;" class="glyphicon glyphicon-remove"></a></td>
		</tr>	
		<tr id="assemblyItemAddButton">
			<td colspan="3"><a onclick="addAssemblyLineItem();"><span style="float:right;" class="glyphicon glyphicon-plus"></a></td>
		</tr>	
	</table>	
<?php } ?>

    <?php if($edit) {?>
		<h4>Notes</h4>
		<textarea name="assembly_details" id="assembly_details" cols="60" rows="5"><?php echo $detail ?></textarea>		
    <?php }?>	  


<dl class="dl-horizontal">	
<?php if(!$edit) {?>	  
<dt>Notes</dt>
<dd><?php echo nl2br( isset($detail) ? $detail : "" ) ?></dd>
	<?php while ($stmt->fetch()) {?>
		<dt><?php curry( $amount ) ?></dt>
		<dd><?php echo $description ?></dd>
	<?php } ?>	
<?php } ?>
</dl>

   <?php if($edit) {?>
 <button onclick="javascript:editAssemblyDetails(<?php echo $orderId ?>);" style="float:right;">save</button>
   <?php } ?>

</div>

 <script>

  function editAssemblyDetails( orderId ){
	
	var assemblyDetails = $('#assembly_details').val();   

	var parray = [
		{ name: "assemblyDetails", value: assemblyDetails },
		{ name: "orderId", value: orderId }
	];	
	
	$('.assembly_line_item').each( function(index) {
		
		var desc = $(this).find('.assembly_line_item_desc').val();
		var amount = $(this).find('.assembly_line_item_amount').val();
		if (desc.trim().length > 0)
			parray.push({ name: "li_"+desc, value: amount } )
	} );
	
			
	var url = "actions/editAssemblyDetails.php";
	  
	$('#assemblyDetailsPanel').load( url, parray, function(){
		  $('#orderDetailsPanel').load( "panels/order_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
	  }  );
  }	

  function deleteAssemblyLineItem(element){
	  $(element).parent().parent().remove();
  }	

  function addAssemblyLineItem(){
	  var line = $('#assembly_line_items').find('.assembly_line_item').last();
	  var newLine = $(line).clone();
	  $(newLine).find(".assembly_line_item_desc").val("");
	  $(newLine).find(".assembly_line_item_amount").val("");
	  line.after(newLine);
  }	

  function enableAssemblyDetailEdit(){
  	  $('#assemblyDetailsPanel').load( "panels/assembly_details_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
  } 

  function cancelAssemblyDetailsEdit(){
  	  $('#assemblyDetailsPanel').load( "panels/assembly_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
  } 
 </script>  

