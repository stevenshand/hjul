<?php

include_once ( __DIR__.'/../configuration.php' );
include_once ( __DIR__.'/../functions/harefn.php' );

$orderId = $_GET["orderId"];

$edit = false;
if( isset($_GET["mode"] ) ){
    if( $_GET["mode"] == "EDIT" ){
        $edit = true;
    }
};

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = "select id, UNIX_TIMESTAMP(shipping_date) as sd, method, tracking_number from shipping where order_id = ".$orderId; 

if (!($statement = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$statement->execute()) {
    echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
}

$statement->bind_result( $id, $shippingDate, $method, $trackingNumber );
$statement->store_result();
$resultsSize = $statement->num_rows;

$shippingCost = totalShipping($orderId);
$shippingNotes = getShippingNotes($orderId);

?>

  <div class="panel-heading">
	  
<?php if ( $edit ) {?>
  	<a href="javascript:cancelShippingDetailsEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enableShippingDetailEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>
	  
    <h3 class="panel-title">Shipping details</h3>
  </div>
  
  
  <div class="panel-body">
	<table class="table table-striped table-bordered table-hover table-condensed">
		<tbody>
		<tr>
			<th>Shipping Date</th><th>Method</th><th>Tracking #</th>
		</tr>

		<?php while ($statement->fetch()) {?>
    		<tr>
				<td><?php echo date( DATEFORMAT, $shippingDate ) ?></td>
				<td><?php echo $method ?></td>
				<td><?php echo $trackingNumber ?></td>
			</tr>
		<?php }?>
	</tbody>			
	</table>
	<dl class="dl-horizontal">
		<dt>Shipping Cost</dt>
		<?php if($edit) {?>
		<dd>£<input type="number" name="shippingPrice" id="shippingPrice" value ="<?php echo $shippingCost ?>"></dd>
		<?php } else {?>
		<dd>£<?php echo $shippingCost ?></dd>
		<?php } ?>
			
		</dl>	


	<dl class="dl-horizontal">
		<dt>Shipping Notes</dt>
		<?php if($edit) {?>
		<dd><textarea name="shiping_notes" id="shipping_notes" cols="45" rows="5"><?php echo $shippingNotes ?></textarea></dd>		
		<dd><a href="" id="popShimpMsg" >populate overseas text</a></dd>
		<?php } else {?>
		<dd><?php echo nl2br( isset( $shippingNotes) ? $shippingNotes : "" ) ?></dd>
		<?php } ?>
			
		</dl>	

		
		  <?php if($edit) {?>
		<button onclick="javascript:editShippingDetails(<?php echo $orderId ?>);" style="float:right;">save</button>&nbsp;
		  <?php } ?>
		
	    <div><small>Bike box size 153x26x89cm Frame box 100x21x77cm</small></div>
		
</div> 

 <script>
	 
	 $('#popShimpMsg').click( function(event){
	 	event.preventDefault();
		$('#shipping_notes').val('Shipping to be calculated at time of disptach.');
	 });	 

  function editShippingDetails( orderId ){
		 
	var shippingPrice = $('#shippingPrice').val();   
	var notes = $('#shipping_notes').val();   
	
	if ( !(/^\d{0,5}(\.\d{0,2})?$/.test(shippingPrice)) ){
		alert( "Shipping price amount should be in format XXXn(.XX)")
		return false;
	}	
	
	var parray = [
		{ name: "shippingPrice", value: shippingPrice },
		{ name: "notes", value: notes },
		{ name: "orderId", value: orderId }
	];	

  	var parameters = $.param( parray, true );
	var url = "actions/editShippingDetails.php?" + parameters;
	  
	  $('#shippingDetailsPanel').load( url, function(){
		  $('#orderDetailsPanel').load( "panels/order_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
	  } )
  }	

  function enableShippingDetailEdit(){
  	  $('#shippingDetailsPanel').load( "panels/shipping_details_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
  } 

  function cancelShippingDetailsEdit(){
  	  $('#shippingDetailsPanel').load( "panels/shipping_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
  } 
 </script>  	

