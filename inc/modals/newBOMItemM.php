<?php
//
//$searchField;
//
//?>


<div class="modal fade" id="newBOMItemModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	  
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4>Add BOM Item</h4>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
								<input id="search" name="search" type="search" value="" placeholder="Search...">
								&nbsp;&nbsp;&nbsp;
								<button id="BOMItemSearch" class="glyphicon glyphicon-refresh"></button>  
						</div>
				  	</div>
			  	</div>
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div id="BOMItemSearchResults"></div>
						</div>
					</div>		
				</div>	
	  	  </div>
		</div>
	</div>
</div>
<!-- new BOMItem Modal -->

<script>
	
	$('#newBOMItemModal').on('hidden.bs.modal', function (e) {
		location.reload(true);
	})
	
	
	$( "#BOMItemSearch" ).click(function( event ) {

		event.preventDefault();
		var searchString = $('#search').val();
	    $("#BOMItemSearchResults").load( "/actions/bomItemSearch.php?search="+searchString, function(){
	    	
			$('.addBOMItemButton').click( function(e){
				var itemId = $(e.currentTarget).attr('data-itemId');
				var qty = $( '#'+itemId+'_qty' ).val();
				$.ajax({
				  method: "POST",
				  url: "actions/addItemToBOM.php",
				  data: { 	orderId: <?php echo $orderId ?>, 
					  		itemId: itemId,
					  		model: <?php echo $model ?>,
					  		qty: qty
				  	  	}
				})
				  .done(function( msg ) {
					$('#'+itemId+'_add').toggleClass( "glyphicon-plus-sign" );
					$('#'+itemId+'_add').toggleClass( "glyphicon-ok" );
				  });
			})	
	    } );
	});	
	
	
	
	
</script>
