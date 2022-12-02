
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
		UNIX_TIMESTAMP(order_date) as dt,
		UNIX_TIMESTAMP(target_date) as td,
		location,
		shipping_location,
		target_locked,
		status.status,
		orders.status,
		reconciled,
		promo_code,
		vat_exempt
FROM 	orders 
LEFT JOIN 
		status
ON orders.status = status.id
WHERE orders.id = ".$orderId;

//echo ($query);

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($orderId, $orderDate, $targetDate, $location, $shipping_location, $targetLocked, $status, $statusId, $reconciled, $promo_code, $vat_exempt );
$stmt->store_result();
$stmt->fetch();

$totalCost = totalCost($orderId);

if (!isset($totalCost)){
    $totalCost = 0;
}
$totalPayments = totalPayments($orderId); 

$statuses = fetchStatusArray();

?>

<div class="panel-heading">

<?php if ( $edit ) {?>
  	<a href="javascript:cancelOrderDetailsEdit();"><span style="float:right;" class="glyphicon glyphicon-remove-circle"></span></a>
  <?php } else {?>
  	<a href="javascript:enableOrderDetailEdit();"><span style="float:right;" class="glyphicon glyphicon-edit"></span></a>
	<?php }?>

    <h3 class="panel-title">Order details</h3>
  </div>

<div class="panel-body">

<dl class="dl-horizontal">

<!-- Status -->
  <dt>Status</dt>
  <?php if ($edit) {?>
  <dd>
	<select class="statusModifier" name="status" id="status" >
		<?php foreach ($statuses as $key => $value) {?>
			<option <?php echo ( $key==$statusId ? "selected" : "") ?> value="<?php echo $key ?>"><?php echo $value ?></option>
		<?php }?>
	</select>
  </dd>
  <?php } else {?>
  <dd>
	<?php echo $status ?>
  </dd>
<?php } ?>


<!-- Location -->

<!-- Location -->
  	
	<dt>Location</dt>
	<?php if ($edit) {?>
    
	
	<dd>
		<select class="locationModifier" name="location" id="location" >
			<option <?php echo ( $location==0 ? "selected" : "") ?> value="0">Moat Hall</option>
			<option <?php echo ( $location==1 ? "selected" : "") ?> value="1">Other</option>
		</select>
	</dd>
	
	<?php } else {?>
	<dd>
  	<?php echo ( $location == 0 ? "Moat Hall" : "Other" ) ?>
    </dd>
<?php } ?>
<!-- Location -->

<!-- Location -->
  	
	<dt>Shipping Location</dt>
	<?php if ($edit) {?>
    
	
	<dd>
		<select class="shippingLocationModifier" name="shippingLocation" id="shippingLocation" >
			<option <?php echo ( $shipping_location==0 ? "selected" : "") ?> value="0">Moat Hall</option>
			<option <?php echo ( $shipping_location==1 ? "selected" : "") ?> value="1">Other</option>
		</select>
	</dd>
	
	<?php } else {?>
	<dd>
  	<?php echo ( $shipping_location == 0 ? "Moat Hall" : "Other" ) ?>
    </dd>
<?php } ?>
<!-- Location -->


<!-- order date -->
  <dt>Order Date</dt>
  <?php if ($edit) {?>
  <dd>
	  <input type="date" name="orderDate" id="orderDate" value="<?php echo Date( INPUTFIELDDATEFORMAT, $orderDate ) ?>">
  </dd>
  <?php } else {?>
  <dd>
	<?php echo Date( DATEFORMAT, $orderDate ) ?>, (week <?php echo wkNum($orderDate) ?>)
  </dd>
<?php } ?>
  
<!-- order date -->

<!-- target date -->
  <dt>Expected Completion</dt>
  <?php if ($edit) {?>
  <dd>
      <input type="date" name="targetDate" id="targetDate" value="<?php echo Date( INPUTFIELDDATEFORMAT, $targetDate ) ?>">
<!--	  <input type="number" name="targetWeek" id="targetWeek" value="--><?php //echo wkNum($targetDate) ?><!--">-->
  </dd>
  <?php } else {?>
  <dd>
      <?php echo Date( DATEFORMAT, $targetDate ) ?>, (week <?php echo wkNum($targetDate) ?>)
  </dd>
<?php } ?>
<!-- target date -->

<!-- completion date -->
  <dt>Target Date Locked?</dt>
  <?php if ($edit) {?>
  <dd>
	  <input type="checkbox" name="targetLocked" id="targetLocked" <?php echo ( $targetLocked ? "checked" : "" ) ?>>
  </dd>
  <?php } else {?>
  <dd>
	<?php echo ( $targetLocked ? "yes" : "no" ) ?>
  </dd>
<?php } ?>
<!-- completion date -->


<!-- reconciled date -->
  <dt>Reconciled?</dt>
  <?php if ($edit) {?>
  <dd>
	  <input type="checkbox" name="reconciled" id="reconciled" <?php echo ( $reconciled ? "checked" : "" ) ?>>
  </dd>
  <?php } else {?>
  <dd>
	<?php echo ( $reconciled ? "yes" : "no" ) ?>
  </dd>
<?php } ?>
<!-- reconciled date -->

<!-- Promo Code -->
	<dt>Promo Code</dt>
    <?php if($edit) {?>
	<dd><input type="text" id="promoCode" name="promoCode" value="<?php echo $promo_code ?>"></dd>	
    <?php } else {?>	  
	<dd><?php echo $promo_code ?></dd>
    <?php } ?>
<!-- Promo Code -->


<!-- total price -->
  <dt>Total Price</dt>
  <dd>
	  <?php echo curry( $totalCost) ?>
  </dd>
<!-- total price -->

  <dt>Total Payments</dt>
  <dd>
	  <?php echo curry( $totalPayments) ?>
  </dd>

  <dt>Balance</dt>
  <dd>
	  <?php echo curry($totalCost-$totalPayments) ?>
  </dd>

    <hr>

    <?php if ( $totalCost > 0 ) { ?>

    <dt>Total Cost</dt>
    <dd>
    <?php $bomCost = calculateOrderBOMCost($orderId) ?>
    <?php echo curry( $bomCost ) ?>
    </dd>

    <dt>Margin</dt>
    <dd>
    <?php
        $taxFactor = $vat_exempt ? 1 : 1.2;
        $margin = ( ( ($totalCost/$taxFactor)-$bomCost ) / ($totalCost/1.2) )*100  ?>
    <?php echo round($margin)."%" ?>
    </dd>

    <dt>Cash Margin</dt>
    <dd>
    <?php $cashMargin = ( ($totalCost/$taxFactor)-$bomCost )  ?>
    <?php echo curry( $cashMargin ) ?>
    </dd>

    <?php } ?>

</dl>
 
   <?php if($edit) {?>
 <button onclick="javascript:editOrderDetails(<?php echo $orderId ?>);" style="float:right;">save</button>
   <?php } ?>
 
</div>

 <script>

/*
$('#targetDate').on('change', displayWeekNumber );	

function displayWeekNumber( event ){
	var targetDate = $(event.target).val(); 	
	var d = new Date(targetDate);
	var wknum = d.getWeekNumber();
	if( !isNaN(wknum) ){
		$('#weeknum').html( ' wk#:' + wknum );
	}
}	
*/

  function editOrderDetails( orderId ){
	
	var orderDate = $('#orderDate').val();
	var targetDate = $('#targetDate').val();
	var targetLocked = ( $('#targetLocked').is(':checked') ) ? "1" : "0";
	var reconciled = ( $('#reconciled').is(':checked') ) ? "1" : "0";
	var statusId = $('#status').val(); 
	var loc = $('#location').val(); 
	var shippingLocation = $('#shippingLocation').val(); 
	var promoCode = $('#promoCode').val(); 
	var lead = $('#lead').val(); 
		 
	var parray = [
		{ name: "orderDate", value: orderDate },
		{ name: "targetLocked", value: targetLocked },
		{ name: "reconciled", value: reconciled },
		{ name: "targetDate", value: targetDate },
		{ name: "orderId", value: orderId, },
		{ name: "status", value: statusId },
		{ name: "shippingLocation", value: shippingLocation },
		{ name: "location", value: loc },
		{ name: "promoCode", value: promoCode },
		{ name: "lead", value: lead }
	];

	var parameters = $.param( parray, true );
	var url = "actions/editOrderDetails.php?" + parameters;

	$('#orderDetailsPanel').load( url );
  }	

  function enableOrderDetailEdit(){
  	  $('#orderDetailsPanel').load( "panels/order_details_panel.php?mode=EDIT&orderId=<?php echo $orderId ?>" );
  } 

  function cancelOrderDetailsEdit(){
  	  $('#orderDetailsPanel').load( "panels/order_details_panel.php?mode=VIEW&orderId=<?php echo $orderId ?>" );
  } 
 </script>  

