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
"SELECT orders.id as orderId,
		sname,
		fname,
		email,
		vat_exempt,
		internal,
		cancelled
FROM 	orders 
WHERE orders.id = ".$orderId;

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($orderId, $sname, $fname, $email, $vatExempt, $internal, $cancelled );
$stmt->store_result();
$stmt->fetch();


?>

<div class="panel-heading" >
<?php if ( $edit ) {?>
  	<a href="javascript:cancelCustomerDetailsEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enableCustomerDetailEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>
	<h3 class="panel-title">Customer</h3>
	
</div>

<div class="panel-body">
	
<dl class="dl-horizontal">
  <!-- first name -->	
  <dt>First Name</dt>

<?php if ( $edit ) {?>
  <dd><input type="text" name="editFNameField" id ="editFNameField" value="<?php echo $fname ?>"></dd>

<?php } else {?>	  
  <dd><?php echo $fname ?></dd>
<?php } ?>
  
  <!-- first name -->	

  <!-- surname -->	
  <dt>Surname</dt>
<?php if ( $edit ) {?>
  <dd><input type="text" name="editSNameField" id ="editSNameField" value="<?php echo $sname ?>"></dd>
<?php } else {?>	  
  <dd><?php echo $sname ?></dd>
<?php } ?>
  <!-- surname -->	

  <!-- email -->	
  <dt>Email</dt>
<?php if ( $edit ) {?>
  <dd><input type="email" name="editEmailField" id ="editEmailField" value="<?php echo $email ?>"></dd>
<?php } else {?>	  
  <dd><?php echo $email ?> <small><a target="_blank" href="https://mail.google.com/mail/u/0/?tab=om#search/<?php echo $email ?>">[search inbox]</a></small></dd>
<?php } ?>
  <!-- email -->	

  <!-- vat exempt -->	
  <dt>VAT Exempt</dt>
  <?php if($edit) {?>
  <dd><input type="checkbox" <?php echo ( $vatExempt ? "checked" : "" ) ?> name="editVatExemptField" id="editVatExemptField"></dd>
  <?php } else {?>	  
 <dd><?php echo ( $vatExempt ? "yes" : "no" ) ?></dd>
  <?php } ?>
  <!-- vat exempt -->	

  <!-- internal order -->	
  <dt>Internal</dt>
  <?php if($edit) {?>
  <dd><input type="checkbox" <?php echo ( $internal ? "checked" : "" ) ?> name="editInternalField" id="editInternalField"></dd>
  <?php } else {?>	  
 <dd><?php echo ( $internal ? "yes" : "no" ) ?></dd>
  <?php } ?>
  <!-- internal order -->	


</dl>

  <?php if($edit) {?>
<button onclick="javascript:editCustomerDetails(<?php echo $orderId ?>);" style="float:right;">save</button>
  <?php } ?>
  
</div>

 <script>

  function editCustomerDetails( orderId ){
	
	var sname = $('#editSNameField').val();   
	var fname = $('#editFNameField').val();   
	var email = $('#editEmailField').val();   
	var vatExempt = $('#editVatExemptField').is(':checked');  
	var internal = $('#editInternalField').is(':checked');  

	
	var parray = [
		{ name: "orderId", value: orderId },
		{ name: "sname", value: sname },
		{ name: "fname", value: fname },
		{ name: "email", value: email },
		{ name: "vatExempt", value: vatExempt },
		{ name: "internal", value: internal }
	];

  	var parameters = $.param( parray, true );
	var url = "actions/editCustomerDetails.php?" + parameters;
	  
	  $('#customerDetailsPanel').load( url, function(){
		  $('#orderDetailsPanel').load( "panels/order_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
	  } )
  }	

  function enableCustomerDetailEdit(){
  	  $('#customerDetailsPanel').load( "panels/customer_details_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
  } 

  function cancelCustomerDetailsEdit(){
  	  $('#customerDetailsPanel').load( "panels/customer_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
  } 
 </script>  

