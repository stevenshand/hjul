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


$query = 
"SELECT id,
		line1,
		line2,
		line3,
		town,
		postcode,
		country,
		tel1,
		tel2
FROM 	shipping_address 
WHERE order_id = ".$orderId;

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id,$line1,$line2,$line3,$town,$postcode,$country,$tel1,$tel2);
$stmt->store_result();
$stmt->fetch();

$countries = fetchCountriesArray();

?>

<div class="panel-heading">

<?php if ( $edit ) {?>
  	<a href="javascript:cancelShippingAddressDetailsEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enableShippingAddressDetailEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>

    <h3 class="panel-title">Shipping Address</h3>
  </div>

<div class="panel-body">

<dl class="dl-horizontal">
	
	<dt>Address Line 1</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="s_addressLine1" name="s_addressLine1" value="<?php echo $line1 ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $line1 ?></dd>
	<?php } ?>
	
	
	<dt>Address Line 2</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="s_addressLine2" name="s_addressLine2" value="<?php echo $line2 ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $line2 ?></dd>
    <?php } ?>
	
	
	<dt>Address Line 3</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="s_addressLine3" name="s_addressLine3" value="<?php echo $line3 ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $line3 ?></dd>
    <?php } ?>

	<dt>Town</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="s_addressTown" name="s_addressTown" value="<?php echo $town ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $town ?></dd>
    <?php } ?>
	
	
	<dt>Postcode</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="s_addressPostcode" name="s_addressPostcode" value="<?php echo $postcode ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $postcode ?></dd>
    <?php } ?>
	
	
	<dt>Country</dt>
    <?php if($edit) {?>
	<dd>
		
  	<select name="s_addressCountry" id="s_addressCountry">
  	<?php foreach ($countries as $key => $value) {?>
  		<option <?php echo ( $key==$country ? "selected" : "") ?> value="<?php echo $key ?>"><?php echo $value ?></option>
  	<?php }?>
  	</select>
    </dd>	
		
	</dd>	
    <?php } else {?>
        <dd><?php echo ( isset($country) ? $countries[$country] : "" ) ?></dd>
    <?php } ?>
	
	
	<dt>Telephone 1</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="s_addressTel1" name="s_addressTel1" value="<?php echo $tel1 ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $tel1 ?></dd>
    <?php } ?>
	
	
	<dt>Telephone 2</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="s_addressTel2" name="s_addressTel2" value="<?php echo $tel2 ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $tel2 ?></dd>
    <?php } ?>
	
</dl>
 
   <?php if($edit) {?>
 <button onclick="javascript:editShippingAddressDetails(<?php echo $orderId ?>);" style="float:right;">save</button>
   <?php } ?>
 
</div>

 <script>

  function editShippingAddressDetails( orderId ){
	
	var addressLine1 = $('#s_addressLine1').val();   
	var addressLine2 = $('#s_addressLine2').val();   
	var addressLine3 = $('#s_addressLine3').val();   
	var addressTown = $('#s_addressTown').val();   
	var addressPostcode = $('#s_addressPostcode').val();   
	var addressCountry = $('#s_addressCountry').val();   
	var addressTel1 = $('#s_addressTel1').val();   
	var addressTel2 = $('#s_addressTel2').val();   
	
	
	var parray = [
		{ name: "addressLine1", value: addressLine1 },
		{ name: "addressLine2", value: addressLine2 },
		{ name: "addressLine3", value: addressLine3 },
		{ name: "addressTown", value: addressTown },
		{ name: "addressPostcode", value: addressPostcode },
		{ name: "addressCountry", value: addressCountry },
		{ name: "addressTel1", value: addressTel1 },
		{ name: "addressTel2", value: addressTel2 },
		{ name: "orderId", value: orderId }
	];	

  	var parameters = $.param( parray, true );
	var url = "actions/editShippingAddressDetails.php?" + parameters;
	  
	$('#shippingAddressDetailsPanel').load( url );
  }	

  function enableShippingAddressDetailEdit(){
  	  $('#shippingAddressDetailsPanel').load( "panels/shipping_address_details_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
  } 

  function cancelShippingAddressDetailsEdit(){
  	  $('#shippingAddressDetailsPanel').load( "panels/shipping_address_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
  } 
 </script>  


