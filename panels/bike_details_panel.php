<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_GET["orderId"];
$edit = false;
if( isset($_GET["mode"] ) ){
    if( $_GET["mode"] == "EDIT" ){
        $edit = true;
    }
}

$sizes = fetchSizesArray();
$models = fetchModelsArray();

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
	"SELECT orders.id as orderId,
			model,
			size,
			frame_only,
			base_price,
			frame_number,
			size_confirmed
	FROM 	orders 
	WHERE orders.id = ".$orderId;

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($orderId, $modelId, $sizeId, $frameOnly, $basePrice, $frameNumber, $sizeConfirmed );
$stmt->store_result();
$stmt->fetch();

?>

  <div class="panel-heading">
	  
<?php if ( $edit ) {?>
  	<a href="javascript:cancelBikeDetailsEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enableBikeDetailEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>
	
	
    <h3 class="panel-title">Bike details</h3>
  </div>
  <div class="panel-body">


<dl class="dl-horizontal">
  
  <!-- model -->	
  <dt>Model</dt>
  
  <?php if ($edit) {?>
  <dd>
	<select name="editModelField" id="editModelField">
	<?php foreach ($models as $key => $value) {?>
		<option <?php echo ( $key==$modelId ? "selected" : "") ?> value="<?php echo $key ?>"><?php echo $value ?></option>
	<?php }?>
	</select>
  </dd>
  <?php } else {?>
  <dd>
	<?php echo ($models[$modelId]) ?>
  </dd>
  <?php } ?>
  <!-- model -->	
  
  <!-- size -->	  
  <dt>Size</dt>
  <?php if($edit) {?>
  <dd>
	<select name="editSizeField" id="editSizeField">
	<?php foreach ($sizes as $key => $value) {?>
		<option <?php echo ( $key==$sizeId ? "selected" : "") ?> value="<?php echo $key ?>"><?php echo $value ?></option>
	<?php }?>
	</select>
  </dd>
  <?php } else {?>	  
  <dd>
	<?php echo ($sizes[$sizeId]) ?>
  </dd>
  <?php } ?>
  <!-- size -->	  


<!-- size_confirmed -->	
  <dt>Size confirmed</dt>
  <?php if($edit) {?>
  <dd><input type="checkbox" <?php echo ( $sizeConfirmed ? "checked" : "" ) ?> name="editSizeConfirmedField" id="editSizeConfirmedField"></dd>
  <?php } else {?>	  
  <dd><?php echo( $sizeConfirmed ? "yes" : "no" ) ?></dd>
  <?php } ?>
<!-- size_confirmed -->	


  <!-- frameNumber -->	  
  <dt>Frame #</dt>
  <?php if($edit) {?>
  <dd><input type="text" length="5" id="editFrameNumberField" name="editFrameNumberField" value="<?php echo $frameNumber ?>"></dd>
  <?php } else {?>	  
  <dd>
	<?php echo $frameNumber ?>
  </dd>
  <?php } ?>
  <!-- frameNumber -->	  

<!-- frame only -->	
  <dt>Frame only</dt>
  <?php if($edit) {?>
  <dd><input type="checkbox" <?php echo ( $frameOnly ? "checked" : "" ) ?> name="editFrameOnlyField" id="editFrameOnlyField"></dd>
  <?php } else {?>	  
  <dd><?php echo( $frameOnly ? "yes" : "no" ) ?></dd>
  <?php } ?>
<!-- frame only -->	


<!-- base price -->	
  <dt>Price</dt>
  <?php if($edit) {?>
  <dd><input min="1" step="any" type="number" name="editBasePriceField" id ="editBasePriceField" value="<?php echo $basePrice ?>"></dd>
  <?php } else {?>	  
  <dd><?php curry($basePrice) ?></dd>
  <?php } ?>
<!-- base price -->	

</dl>

  <?php if($edit) {?>
<button onclick="javascript:editBikeDetails(<?php echo $orderId ?>);" style="float:right;">save</button>
  <?php } ?>

  </div>
  
 <script>

  function editBikeDetails( orderId ){
	
	var modelId = $('#editModelField').val();   
	var sizeId = $('#editSizeField').val();   
	var frameOnly = $('#editFrameOnlyField').is(':checked');  
	var sizeConfirmed = $('#editSizeConfirmedField').is(':checked');  
	var frameNumber = $('#editFrameNumberField').val();  
	 
	var basePrice = $('#editBasePriceField').val();   
	
	if ( !(/^\d{0,5}(\.\d{0,2})?$/.test(basePrice)) ){
		alert( "Base price amount should be in format XXXn(.XX)")
		return false;
	}	
	
	var parray = [
		{ name: "modelId", value: modelId },
		{ name: "basePrice", value: basePrice },
		{ name: "sizeId", value: sizeId },
		{ name: "frameOnly", value: frameOnly },
		{ name: "sizeConfirmed", value: sizeConfirmed },
		{ name: "frameNumber", value: frameNumber },
		{ name: "orderId", value: orderId }
	];	

  	var parameters = $.param( parray, true );
	var url = "actions/editBikeDetails.php?" + parameters;
	  
	  $('#bikeDetailsPanel').load( url, function(){
		  $('#orderDetailsPanel').load( "panels/order_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
	  } )
  }	

  function enableBikeDetailEdit(){
  	  $('#bikeDetailsPanel').load( "panels/bike_details_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
  } 

  function cancelBikeDetailsEdit(){
  	  $('#bikeDetailsPanel').load( "panels/bike_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
  } 
 </script>  
