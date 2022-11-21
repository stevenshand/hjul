<?php
require 'configuration.php';
require 'functions/harefn.php';

$showShipped = $_GET && $_GET["showShipped"];
$showInternal = $_GET && $_GET["showInternal"];

$showInternalQuery = $showInternal ? "" : " AND internal = 0";

$sortOrder = "ASC";
if( $_GET && $_GET["sortOrder"] && ( $_GET["sortOrder"] == "DESC" ) ){
	$sortOrder = "DESC";
};

$sortField = "orders.status";

if( $_GET && $_GET("sortField") ){
    $sortField = $_GET["sortField"];
}

$concatString = "";
$searchField = "";
if( $_GET && $_GET["search"] ){
    $searchField = ($_GET["search"] );
}
$searchString =" WHERE ";
if( !empty(trim($searchField) ) ){
	$searchField = trim($searchField); 
	$concatString = " AND ";
	$searchString =" WHERE orders.sname LIKE '%".$searchField."%' OR orders.fname LIKE '%".$searchField."%'";
}	

	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$orderListQuery = 
"SELECT orders.id as orderId,
		UNIX_TIMESTAMP(order_date) as dt,
		UNIX_TIMESTAMP(target_date),
		target_locked,
		sname,
		fname,
		models.name,
		models.brand,
		sizes.size as framesize,
		status.status,
		frame_only,
		internal,
		uuid,
		payment_pending,
		invoicable,
		orders.status,
		count(shipping.id),
		count(comms.id),
		shipping.shipping_date,
		vat_exempt,
		size_confirmed,
		paint_confirmed,
		cancelled
FROM 	orders 
LEFT JOIN
		comms
ON orders.id = comms.order_id
LEFT JOIN
		shipping
ON orders.id = shipping.order_id
LEFT JOIN 
		status
ON orders.status = status.id		
LEFT JOIN 
		models
ON orders.model = models.id		
LEFT JOIN 
		sizes
ON orders.size = sizes.id ".$searchString.$concatString.($showShipped ? " orders.status > 0 " : " orders.status != 8 ").$showInternalQuery." GROUP BY orders.id ORDER BY " . $sortField . " ".$sortOrder.", order_date ASC";

// echo($orderListQuery );

if (!($stmt = $mysqli->prepare($orderListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($orderId, $orderDate, $targetDate, $targetLocked, $sname, $fname, $modelName, $brandId, $frameSize, $orderStatus, $frameOnly, $internal, $uuid, $paymentPending, $invoicable, $statusId, $shippingEntries, $commsEntries, $shippingDate, $vatExempt, $sizeConfirmed, $paintConfirmed, $cancelled );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

$statuses = fetchStatusArray();
$models = fetchCurrentModelsArray();
$sizes = fetchSizesArray();
$brands = fetchBrandsArray();

$rollingStockCount = fetchInitialRollingStockCount();
//print_r($initialStockCount);

$orderBookValue = 0;

include 'inc/header.php';
?>

<script src="js/jquery.tabletoCSV.js"></script>


<!-- comments modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div id="commentModalContainer" class="modal-content">
		</div>
	</div>
</div>
<!-- comments modal -->

<!-- payments modal -->
<div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div id="paymentsModalContainer" class="modal-content">
		</div>
	</div>
</div>
<!-- payments modal -->

<!-- shipping modal -->
<div class="modal fade" id="shippingModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div id="shippingModalContainer" class="modal-content">
		</div>
	</div>
</div>
<!-- shipping modal -->


<!-- new order modal -->
<div class="modal fade" id="newOrderModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	  
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 style="cursor:pointer;" class="modal-title" id="newOrderModalLabel" onclick="$('#audiox').get(0).play();">New Order</h4>
				<audio controls id="audiox" style="display:none;">
				  <source src="audio/Blue_Monday.ogg" type="audio/ogg">
				Your browser does not support the audio element.
				</audio>
			</div>
      
			<div class="modal-body">

				<form action="/actions/addOrder.php" class="form">
					<div class="container-fluid"><!--container-->
						<div class="row"><!--row1-->
						<div class="col-md-6">
							<div class="form-group">
								<label for="fnameinput">First Name</label>
								<input type="text" class="form-control" name="fname" id="fnameinput" placeholder="First Name">
							</div>
							<div class="form-group">
								<label for="snameinput">Second Name</label>
								<input type="text" class="form-control" name="sname" id="snameinput" placeholder="Second Name">
							</div>
							<div class="form-group">
								<label for="emailinput">Email Address</label>
								<input type="text" class="form-control" name="email" id="emailinput" placeholder="Email Address">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="modelinput">Model</label><br>
								<select id="modelinput" name="modelId" >
								<?php foreach ($models as $key => $value) {?>
									<option value="<?php echo $key ?>"><?php echo $value ?></option>
								<?php }?>
								</select>
							</div>
							<div class="form-group">
								<label for="sizeinput">Size</label><br>
								<select id="sizeinput" name="sizeId" >
								<?php foreach ($sizes as $key => $value) {?>
									<option value="<?php echo $key ?>"><?php echo $value ?></option>
								<?php }?>
								</select>
							</div>
							<div class="form-group">
								<label for="frameonly">Frame only? </label>
								&nbsp;<input type="checkbox" name="frameOnly" id="frameonly">
							</div>
							<div class="form-group text-right">
								<button type="submit" class="btn btn-primary">Add Order</button>
							</div>						
						</div>						
						
									
					  </div><!--row1-->
				  </div><!--container-->
				  
			  </form>
		  </div>
		</div>
	</div>
</div>
<!-- modal -->



<div class="container">

	<div class="row">
		<div class="col-md-11">
			<!-- <div class="table-controls"> -->
			<div class="table-controls">
					<div class="row">
						
						<div class="col-md-11">
						
				<form id="orderForm" class="form-inline" action="/" method="get">
							<label for="showShippedCheckbox">show shipped ? </label> <input id="showShippedCheckbox" <?php echo $showShipped ? 'checked' : '' ?> name="showShipped" type="checkbox">			

							 &nbsp;&nbsp;|&nbsp;&nbsp;<label for="sortByDropdown">sort by </label>
							 	<select name="sortField" id="sortByDropdown">
									<option <?php echo ($sortField =='order_date' ? 'selected' : '' ) ?> value="order_date">Order date</option>
									<option <?php echo ($sortField =='target_week' ? 'selected' : '' ) ?> value="target_week">Target week</option>
									<option <?php echo ($sortField =='orders.status' ? 'selected' : '' ) ?> value="orders.status">Status</option>
									<option <?php echo ($sortField =='model' ? 'selected' : '' ) ?> value="model">Model</option>
									<option <?php echo ($sortField =='orders.size' ? 'selected' : '' ) ?> value="orders.size">Size</option>
									<option <?php echo ($sortField =='sname' ? 'selected' : '' ) ?> value="sname">Surname</option>
									<option <?php echo ($sortField =='shipping_date' ? 'selected' : '' ) ?> value="shipping_date">Shipping Date</option>
								</select>
							 &nbsp;&nbsp;|&nbsp;&nbsp;<label for="sortOrderDropdown">sort order </label>
							 	<select name="sortOrder" id="sortOrderDropdown">
									<option <?php echo ($sortOrder =='ASC' ? 'selected' : '' ) ?> value="ASC">ASC</option>
									<option <?php echo ($sortOrder =='DESC' ? 'selected' : '' ) ?> value="DESC">DESC</option>
								</select>

							&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<input id="search" name="search" type="search" value="<?php echo $searchField ?>" placeholder="Search...">&nbsp;&nbsp;
							&nbsp;&nbsp;|&nbsp;&nbsp;<button type="submit" class="glyphicon glyphicon-refresh"></button>			
								
				</form>
						
						
						</div>
						<div class="col-md-1">
							<button onclick="toggleOptions();" id="optionsToggle" class="glyphicon glyphicon-menu-right"></button>&nbsp;&nbsp;	
						</div>
					</div>
					<div class="row" id="options" style="display:none;">
						<div class="col-md-12">
<div class="btn-group">
		  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Bulk Actions<span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu">
		    <li><a id="ba_stocksummary" href="">Generate Stock Summary</a></li>
		  </ul>
		</div>
	</div>
	</div>	
			</div>	
		</div>	
		<div class="col-md-1 text-right">
			<button title="add new order" data-toggle="modal" data-target="#newOrderModal" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span></button><br>
			<small><?php echo $resultsSize ?> results</small>			
		</div>	
	</div>
	<div class="row">
		<div class="col-md-6">
			<?php include 'panels/cashflow_summary.php' ?>
		</div>	
		<div class="col-md-6 text-right"><button id="export" data-export="export">export</button></div>	
	</div>	
	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table id="orderTable" class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th><input type="checkbox" id="select_all"></th>
						<th>Brand</th><th>Order Date</th><th>Target</th><th>Name</th><th>Model</th><th>Size</th><th>Paint</th><th>Status</th>
						<th colspan="4">Actions</th>
					</tr>
			
					<?php while ($stmt->fetch()) {
						
//							$stockStatus = fetchStockStatus($orderId, $rollingStockCount );
							
							$totalPrice = fetchTotalPrice($orderId);
							
							if( $vatExempt == 1 ){
								$orderBookValue = $orderBookValue+$totalPrice;
							}else{
								$orderBookValue =  $orderBookValue+($totalPrice/1.2);
							}
							
							$totalPayments = fetchTotalPayments($orderId);
							$paymentClass = "";
							if( (float)$totalPayments >= (float)$totalPrice ){
								$paymentClass = " bg-success";
							}
							if( (float)$totalPayments <= 0 ){
								$paymentClass = " bg-danger";
							}
							if( $paymentPending ){	 	
								$paymentClass = " bg-warning";
							}
							if( $invoicable ){	 	
								$paymentClass = " bg-info";
							}
							if( $internal ){	 	
								$paymentClass = " bg-primary";
							}
							
							$paintConfirmedClass = "";
							if( $paintConfirmed < 1 ){
								$paintConfirmedClass = " bg-danger";
							}

							$sizeConfirmedClass = "";
							if( $sizeConfirmed < 1 ){
								$sizeConfirmedClass = " bg-danger";
							}

							$lateClass = "onschedule";
							if( $statusId < 8 && isLate($orderDate) ){
								$lateClass = "isLate";
							}

                            $shippedClass = ( $statusId == 8 ? "bg-success" : "" );
							if( $cancelled ){
								$shippedClass = "bg-danger text-muted";
							}
							
						 ?>
						<tr id="orderRow_<?php echo $orderId  ?>" class="<?php echo $shippedClass ?>">
							<td><input id="order_select_<?php echo $orderId  ?>" name="order_select_<?php echo $orderId  ?>" class="order_select" data-orderid="<?php echo $orderId  ?>" type="checkbox"></td>
							<td><?php echo $brands[$brandId] ?></td>
							<td><?php echo date( DATEFORMAT, $orderDate ) ?></td>
							<td class="<?php echo $lateClass ?>">
                            <?php
                                $target = "n/a";
                                if( isset($targetDate) ){
                                    $target = "week ".wkNum($targetDate).", '".date('y', $targetDate );
                                }
                            ?>

                			<a href="board.php#row<?php echo  wkNum($targetDate)  ?>"><?php echo $target ?></a>
								<?php if ($targetLocked){  ?>
								<span class="glyphicon glyphicon-exclamation-sign"></span>	
								<?php }?>
							</td>
							<td><a href="/editorder.php?orderId=<?php echo $orderId ?>"><?php echo ( $cancelled ? '[CANCELLED] ' : '' )?><?php echo $sname ?>, <?php echo $fname ?></a></td>
							<td><a href="/editorder.php?orderId=<?php echo $orderId ?>"><?php echo $modelName ?><?php echo( $frameOnly ? " (f)" : "" ) ?></a></td>
							<td class="<?php echo $sizeConfirmedClass ?>"><a href="/editorder.php?orderId=<?php echo $orderId ?>"><?php echo $frameSize ?></a></td>
							<td class="<?php echo $paintConfirmedClass ?>"></td>
							<td>
								<?php if($statusId < 9 ){ ?>
								<select data-orderid="<?php echo $orderId ?>" class="statusModifier" name="selected_status" >
									<?php foreach ($statuses as $key => $value) {?>
										<?php if ( $key < 9 ){ ?>
											<option <?php echo ( $value==$orderStatus ? "selected" : "") ?> value="<?php echo $key ?>"><?php echo $value ?></option>
										<?php }?>
									<?php }?>
								</select>
								<?php } ?>
								<!-- <?php if($statusId == 7 ){ ?>
									shipped
								<?php } ?> -->
							</td>							
							<?php 
    							$title = "Total : £".$totalPrice."<br>Payments : £".$totalPayments."<br>Balance Due : £".($totalPrice-$totalPayments).($paymentPending ? "<br><b>PAYMENT PENDING</b>" : "");
							?>

							<td data-toggle="tooltip" 
								data-html="true" 
								data-container="body" 
								data-placement="left" 
								title="<?php echo $title ?>" 
								class="center<?php echo $paymentClass ?>">
								<a href=""data-toggle="modal" 
									data-orderid="<?php echo $orderId ?>" 
									data-target="#paymentsModal">
										<span class="glyphicon glyphicon-gbp" aria-hidden="true"></span>
								</a>
							</td>	
							
							<td class="center" title="<?php echo $shippingDate ?>"><a href="" data-toggle="modal" data-orderid="<?php echo $orderId ?>" data-target="#shippingModal"><span class="glyphicon glyphicon-plane" aria-hidden="true"></span><sup style="color:red"><?php echo( $shippingEntries > 0 ? $shippingEntries : "" ) ?></sup></a></td>	
							
							<td class="center"><a href="" data-toggle="modal" data-orderid="<?php echo $orderId ?>" data-target="#commentModal"><span class="glyphicon glyphicon-comment" aria-hidden="true"></span><sup style="color:red"><?php echo( $commsEntries > 0 ? $commsEntries : "" ) ?></sup></a></td>	

					<?php }?>
				</tbody>			
				</table>	
			</div>
		</div>
	</div>	
</div>	

<form id="stockForecastSummaryAction" action="stockForecastSummary.php" method="POST">

<script>


$("#export").click(function(){
  $("#orderTable").tableToCSV(false);
});

$("#ba_stocksummary").click(function(event){
	
	event.preventDefault();
	
	var orderIds = [];
	
	$('.order_select:checked').each( function(index,chckbx){
		orderIds.push(chckbx.getAttribute('data-orderid'));
		// console.log(chckbx.getAttribute('data-orderid'));
	})
	
	// console.log(orderIds);
	var qs = '?orderIds='+orderIds
	// console.log(qs);
	window.location = "stockForecastSummary.php"+qs;
	
	// var parray = [
	// 	{ name: "orderIds", value: orderIds }
	// ];
	//
	// var parameters = $.param( parray, true );
	// var url = "stockForecastSummary.php?" + parameters;
	//
	// $("#commentModalContainer").load( url );
});


function toggleOptions(){
	$('#options').toggle();
	$('#optionsToggle').toggleClass( "glyphicon-menu-right").toggleClass( "glyphicon-menu-down" );
}

$(function () {
	$('[data-toggle="tooltip"]').tooltip();
})

$(function () {
  $('[data-toggle="popover"]').popover()
})

$( ".statusModifier" ).change( modifyStatus );

function deletePayment(orderId, paymentId){
	$("#paymentsModalContainer").load( "actions/deletepayment.php?orderId="+ orderId + "&paymentId=" + paymentId  );
}

function deleteShipping(orderId, shippingId){
	$("#shippingModalContainer").load( "actions/deleteshipping.php?orderId="+ orderId + "&shippingId=" + shippingId  );
}

function deleteComment(orderId, commentId){
	$("#commentModalContainer").load( "actions/deletecomment.php?orderId="+ orderId + "&commentId=" + commentId  );
}


function addComment(orderId){
	
	var newComment = $('#newComment').val();
		
	if( !newComment || newComment.trim().length < 1 ){
		alert( "Comment cannot be empty" );
		return false;
	}
		
	var parray = [
		{ name: "comment", value: newComment },
		{ name: "orderId", value: orderId }
	];		
	
	var parameters = $.param( parray, true );			
	var url = "actions/addcomment.php?" + parameters;
		
	$("#commentModalContainer").load( url );

}


function addShipping(orderId){
	
	var shippingMethod = $('#newShippingMethod').val();
	var shippingDate = $('#newShippingDate').val(); 
	var trackingNumber = $('#tracking').val(); 
	
	if( !shippingMethod && !shippingDate ){
		alert( "Date and method are mandatory fields" );
		return false;
	}
	if( !shippingDate ){
		alert( "Date is a mandatory field" );
		return false;
	}
	
	if( !shippingMethod ){
		alert( "Method amount is a mandatory field" );
		return false;
	}
		
	var parray = [
		{ name: "tracking", value: trackingNumber },
		{ name: "orderId", value: orderId },
  	  	{ name: "shippingDate", value: shippingDate },
  	  	{ name: "shippingMethod", value: shippingMethod }
	];		
	
	var parameters = $.param( parray, true );			
	var url = "actions/addshipping.php?" + parameters;
		
	$("#shippingModalContainer").load( url );

}



function addPayment(orderId){
	
	var paymentAmount = $('#newPaymentAmount').val();
	var paymentDate = $('#newPaymentDate').val(); 

	if( !paymentAmount && !paymentDate ){
		alert( "Date and amount are mandatory fields" );
		return false;
	}
	if( !paymentDate ){
		alert( "Date is a mandatory field" );
		return false;
	}
	
	if( !paymentAmount ){
		alert( "Payment amount is a mandatory field" );
		return false;
	}else{
		if ( !(/^\d{0,4}(\.\d{0,2})?$/.test(paymentAmount)) ){
			alert( "Payment amount should be in format XXXn(.XX)")
			return false;
		}	
	}
			
	$("#paymentsModalContainer").load( "actions/addpayment.php?orderId="+ orderId + "&paymentAmount=" + paymentAmount + "&paymentDate=" + paymentDate, function(){
		$("#paymentsModalContainer").modal('hide');
	} );
}

$('#select_all').on( 'change', function(e){
	$('.order_select').each( function(index,chckbx){
		chckbx.checked=e.target.checked;
	});
} )

$('#paymentsModal').on('show.bs.modal', function (e) {
	var paymentsOrderId = e.relatedTarget.getAttribute('data-orderId');
	$("#paymentsModalContainer").load( "payments.php?orderId="+ paymentsOrderId );
})

$('#commentModal').on('show.bs.modal', function (e) {
	var commentsOrderId = e.relatedTarget.getAttribute('data-orderId');
	$("#commentModalContainer").load( "comments.php?orderId="+ commentsOrderId );
})

$('#shippingModal').on('show.bs.modal', function (e) {
	var shippingOrderId = e.relatedTarget.getAttribute('data-orderId');
	$("#shippingModalContainer").load( "shipping.php?orderId="+ shippingOrderId );
})

$('#shippingModal').on('hide.bs.modal', function (e) {
	location.reload(true);
})

$('#paymentsModal').on('hide.bs.modal', function (e) {
	location.reload(true);
})

$( document ).ready( setOrderBookValue );

function setOrderBookValue(){
	$('#orderBookTotal').html( '<?php curry( $orderBookValue ) ?>' );
}

</script>
<?php
$stmt->close();
?>

<?php include 'inc/footer.php'; ?>
