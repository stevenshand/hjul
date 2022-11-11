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
		tel2,
		latitude,
		longitude
FROM 	address 
WHERE order_id = ".$orderId;

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id,$line1,$line2,$line3,$town,$postcode,$country,$tel1,$tel2,$latitude,$longitude);
$stmt->store_result();
$stmt->fetch();

$countries = fetchCountriesArray();

$bgClass="";
if( ( !isset($town) || empty(trim($town) ) )
    || ( !isset($line1) || empty(trim($line1)) )
    || ( !isset($postcode) || empty(trim($postcode) ) )
    || ( !isset($country) || empty(trim($country)) ) )
	$bgClass=" bg-danger";

?>

<div class="panel-heading">

<?php if ( $edit ) {?>
  	<a href="javascript:cancelAddressDetailsEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enableAddressDetailEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>
	
    <h3 class="panel-title">Address</h3>
  </div>

<div class="panel-body<?php echo $bgClass ?>">

<dl class="dl-horizontal">
	
	<dt>Address Line 1</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="addressLine1" name="addressLine1" value="<?php echo $line1 ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $line1 ?></dd>
	<?php } ?>
	
	
	<dt>Address Line 2</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="addressLine2" name="addressLine2" value="<?php echo $line2 ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $line2 ?></dd>
    <?php } ?>
	
	<dt>Address Line 3</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="addressLine3" name="addressLine3" value="<?php echo $line3 ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $line3 ?></dd>
    <?php } ?>
	
	<dt>Town</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="addressTown" name="addressTown" value="<?php echo $town ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $town ?></dd>
    <?php } ?>
	
	
	<dt>Postcode</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="addressPostcode" name="addressPostcode" value="<?php echo $postcode ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $postcode ?></dd>
    <?php } ?>
	
	
	<dt>Country</dt>
    <?php if($edit) {?>
	<dd>
		
  	<select name="addressCountry" id="addressCountry">
  	<?php foreach ($countries as $key => $value) {
  		if( !$country ){$country = 230;}
  	?>
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
	<dd><input type="text" id="addressTel1" name="addressTel1" value="<?php echo $tel1 ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $tel1 ?></dd>
    <?php } ?>
	
	
	<dt>Telephone 2</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="addressTel2" name="addressTel2" value="<?php echo $tel2 ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $tel2 ?></dd>
    <?php } ?>
	
</dl>
 
 	<div class="small">
		<a 	target="_blank" 
			href="http://maps.google.com?ll=<?php echo $latitude ?>,<?php echo $longitude ?>&z=10">
			Lat:<?php echo $latitude ?>,Long:<?php echo $longitude ?>
		</a>
	</div>
 
   <?php if($edit) {?>
 <button onclick="javascript:editAddressDetails(<?php echo $orderId ?>);" style="float:right;">save</button>
   <?php } ?>
 
</div>

 <script>


  function editAddressDetails( orderId ){
	
	var addressLine1 = $('#addressLine1').val();   
	var addressLine2 = $('#addressLine2').val();   
	var addressLine3 = $('#addressLine3').val();   
	var addressTown = $('#addressTown').val();   
	var addressPostcode = $('#addressPostcode').val();   
	var addressCountry = $('#addressCountry').val();   
	var addressTel1 = $('#addressTel1').val();   
	var addressTel2 = $('#addressTel2').val();   
	
	
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
	var url = "actions/editAddressDetails.php?" + parameters;
	  
	$('#addressDetailsPanel').load( url );
  }	

  function enableAddressDetailEdit(){
  	  $('#addressDetailsPanel').load( "panels/address_details_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
  } 

  function cancelAddressDetailsEdit(){
  	  $('#addressDetailsPanel').load( "panels/address_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
  } 
 </script>  

