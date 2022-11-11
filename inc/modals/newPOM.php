
<!-- newItemModal --> 
<div class="modal fade" id="newPOModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	  
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4>Add New Purchase Order</h4>
			</div>
			<div class="modal-body">

				<form action="/actions/addPO.php" class="form" onsubmit="return addNewPO()">
					<div class="container-fluid">
						<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="supplier">Supplier</label><br>
								<select name="supplier" is="supplier">
								<?php foreach ( $allSuppliers as $key=>$value ){ ?>
									<option value="<?php echo $key ?>"><?php echo $value ?></option>
								<?php } ?>
								</select>
							</div>		
							<div class="form-group">
								<label for="reference">Reference</label><br>
								<input type="text" class="form-control" name="reference" id="reference" placeholder="Reference">
							</div>		
							<div class="form-group">
								<label for="location">Location</label><br>
								<select name="location" id="location">
									<option value="2">CHOOSE LOCATION</option>
									<option value="1">Moat Hall</option>
								</select>
							</div>		
							<div class="form-group">
								<label for="user">User</label><br>
								<select name="user" id="user">
								<?php foreach ( $userList as $key=>$value ){ ?>
									<option value="<?php echo $value ?>"><?php echo $value ?></option>
								<?php } ?>
								</select>
							</div>		
							<div class="form-group text-right">
								<button type="submit" class="btn btn-primary">Add PO</button>
							</div>
						</div>
														
					  </div>
				  </div>
				  
			  </form>
		  </div>
		</div>
	</div>
</div>

<script>
	function addNewPO(){
		var location = $('#location').val();
		if( location > 1 ){
			alert( 'Please set a location' );
			return false;
		}else{
			return true;
		}
	}
</script>

<!-- new Item Modal -->