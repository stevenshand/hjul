<?php
require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';

include 'inc/header.php';
?>

<div class="container">
	

	<div class="row">
		<div class="col-md-12">
			
			<h3>Stock Transfer Form</h3>
			
			<div class="col-md-4">
					<div class="form-group">
					<label for="search">Search</label>
					<input class="form-control" type="search" id="search">
					</div>

					<div class="form-group">
					<label for="item">Inventory Item</label>
					<input class="form-control" type="text" id="item">
					</div>

					<div class="form-group">
					<label for="sku">SKU</label>
					<input class="form-control" type="text" id="sku" name="sku">
					</div>
				
					<div class="form-group">
					<label for="qty">Quantity</label>
					<input size="5" style="width:100px" class="form-control" type="number" id="qty" name="qty">
					</div>

					<div class="form-group">
					<label for="direction">Direction</label>
					<select class="form-control"  name="direction" id="direction">
						<option value="n">Other > Moat Hall</option>	
						<option value="s">Moat Hall > Other</option>	
					</select>	
					</div>
				
					<div class="form-group">
					<label for="reason">Reason</label>
					<textarea class="form-control"  id="reason" name="reason"></textarea>
					</div>

					<input type="submit" id="stockTransferButton">
			</div>	
			
			<div class="col-md-4">
				<div id="results_panel">
				</div>	
			</div>	
		</div>	
	</div>	
</div>	


<script>
	
	$('#stockTransferButton').click( submitStockTransfer );
	
	
	$( "#search" ).autocomplete({
		minLength:3,
		source:"https://hjul.willowbike.com/data/itemSearch.php",
		response: function( event, ui ) {
			//console.log( ui );
			},
		select: function( event, ui ) {
			console.log( ui.item );
			addItemToMove(ui.item);
		}
	});
		
	function addItemToMove( item ){
		$('#item').val(item.item);
		$('#sku').val(item.id);
	}	
	
		
	function submitStockTransfer(){
		
		var itemId = $('#sku').val();
		var qty = $('#qty').val();
		var direction = $('#direction').val();
		var reason = $('#reason').val();
		
		var parray = [
			{ name: "itemId", value: itemId },
			{ name: "direction", value: direction },
			{ name: "reason", value: reason },
			{ name: "qty", value: qty }
		];
		
		var parameters = $.param( parray, true );
		var url = 'actions/addStockMovement.php?' + parameters;
		
	    $('#results_panel').load( url, function( response, status, xhr ){
	    				
			if( status == 'success' ){
				$('#sku').val('');
				$('#qty').val('');
				$('#item').val('');
				$('#search').val('');
				$('#results_panel').html(response);
			}else{
				$('#results_panel').html(response);
			}
		} );
	}	
		
		
		
</script>
	
<?php include 'inc/footer.php'; ?>
