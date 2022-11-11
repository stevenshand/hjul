<?php

require __DIR__.'/../configuration.php';

$id = $_GET["po_id"];	

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$itemListQuery = 
"SELECT inv_po_lines.id,
		po_id,
		item_id,
		inv_po_lines.qty,
		inv_items.short_name,
		inv_items.description,
		inv_items.cost,
		inv_items.supplier_code,
		inv_items.qty
FROM 	inv_po_lines
LEFT JOIN inv_items
ON inv_po_lines.item_id = inv_items.id
WHERE po_id = ".$id;

if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($lineId, $poId, $itemId, $qty, $shortName, $description, $cost, $supplierCode, $stockQty );
$stmt->store_result();
$resultsSize = $stmt->num_rows;
	
?>

				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th>Stock</th><th>QTY Ordered</th><th class="rec">Rec</th><th>SKU</th><th>Supplier Code</th><th>Item</th><th>Unit Cost</th><th>Total</th><th></th>
					</tr>
			
			
					<?php
						$total = 0;
						while ($stmt->fetch()) { ?>
						<tr id="<?php echo $lineId ?>_line">
							<td><?php echo $stockQty ?></td>
							<td> 
								<a 
									class="editable"
									data-title="Quantity" 
									data-name="qty" 
									data-pk="<?php echo $lineId ?>" 
									data-type="text" 
									data-value="<?php echo $qty ?>">
									<?php echo $qty ?>
								</a>
							</td>
							<td class="rec">
								<input type="hidden" name="itemId" value="<?php echo $itemId ?>">
								<input type="hidden" name="orderQty" value="<?php echo $qty ?>">
								<input type="text" value="<?php echo $qty ?>" width="3" size="3">
								<button class="lineItem" id="<?php echo $lineId ?>_linerec" data-line="<?php echo $lineId ?>">receive</button>
							</td>
							<td ><?php echo $itemId ?></td>
							<td ><?php echo $supplierCode ?></td>
							<td data-toggle="tooltip" data-html="true" data-container="body" data-placement="bottom" title="<?php echo $description ?>">
								<?php echo $shortName ?>
							</td>
							<td><?php echo $cost ?></td>
							<td><?php echo ( $cost * $qty ) ?></td>
							<td class="center"><a target="_blank" href="actions/removeItemFromPO.php?po_id=<?php echo($id) ?>&amp;lineId=<?php echo $lineId ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>	
						</tr>
						<?php $total = $total + ($cost*$qty); }?>
						<tr id="newItemLine">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>
								<input type="text" name="item" id="item">
								&nbsp;&nbsp;&nbsp;<button title="Add New Item" data-toggle="modal" data-target="#newItemModal" class="btn btn-default">Quick Add Item</button>
								
							</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr id="receivePOLine"><td colspan="7">Rec</td></td></tr>	
						<tr>
							<td class="text-right" colspan="6">Total</td>
							<td><?php echo $total ?></td>
							<td></td>
						</tr>	
					</tbody>			
				</table>
				
<script>
	
	$('.rec').hide();
	$('#receivePOLine').hide();	
		
	$("#receive").click( function(){
		$('#newItemLine').hide();
		$('.rec').show();
	} );
	
	$(".lineItem").click( function(event){
		
		var lineId = $(event.target).data("line");
		var input = $(event.target).prev("input");
		var recQty = input.val();
		var ordQty = input.prev("input").val();
		var itemId = input.prev("input").prev("input").val();
		
		//console.log( 'lineId : [' + lineId + '], rec qty:[' + recQty + '], ord qty:[' + ordQty + ']' ); 
	
  	  	var itemData = {
  	  		lineId : lineId,
			receivedQuantity : recQty,
			orderedQuantity : ordQty,
			itemId : itemId,
			poId : <?php echo $id ?>  
  	  	};	
		
		//console.log( itemData );
		
  	  $.post( "/actions/receiveItems.php", itemData, function(data){ 
    		  var responseData = $.parseJSON(data);
    		  if( responseData.err ){
    			  alert( 'error :' + responseData.err );
    		  }else{
				  reloadTable(true);
				  if( responseData.reload ){
					  window.location.href = "/purchaseorders.php";	
				  }
				  
				  //alert( responseData.lineId );
    		  }
    	   } );		
	});	  
	
	
	
	
	
	$( "#item" ).autocomplete({
		minLength:3,
		source:"/data/itemSearch.php",
		response: function( event, ui ) {
			//console.log( ui );
			},
		select: function( event, ui ) {
			addItem(ui.item.id);
		}
	});
		
	function addItem( itemId ){
		this.location = "actions/addItemToPO.php?itemId=" + itemId + "&po_id=" + <?php echo $id ?>;
	}	
		
	$(function () {
		$('[data-toggle="tooltip"]').tooltip();
	})
	
	$( document ).ready(function() {
	  $( "#item" ).focus();

		$('a.editable').editable({
			url: 'actions/updatePoQty.php',
			showbuttons: false,
			success: function(response, newValue) {
			    if(!response.success){
					reloadTable();		
			    } 
			}
		});
	});
		
	function reloadTable(enableReceiveMode){
		$( "#potable" ).load( "inc/purchaseordertable.php?po_id=<?php echo $id ?>", function(){
			// alert(enableReceiveMode);
			if(enableReceiveMode){
				$('#newItemLine').hide();
				$('.rec').show()	
			}
		} );
	}	
		
	$( "#newItemForm" ).submit(function( event ) {
 
		event.preventDefault();
 
	  var form = $( this );
	  var itemData = form.serializeArray();
 	 
	  $.post( "/actions/addItem.php", itemData, function(data){ 
		  console.log(data);	  
		  var reponseData = $.parseJSON(data);
	      console.log(reponseData);
		  var itemId = reponseData.itemId;		
		  if( reponseData.err ){
			  alert( 'error :' + reponseData.err );
		  }else{
			  addItem( itemId );
		  }
	   } );
	});	
		
		
</script>