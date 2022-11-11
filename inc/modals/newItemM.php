<script>
	var supplierId = "<?php echo $supplier ?>";	
</script>	


<!-- newItemModal --> 
<div class="modal fade" id="newItemModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	  
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4>Add Item</h4>
			</div>
			<div class="modal-body">

				<form id="newItemForm" action="/" class="form">
					<div class="container-fluid">
						<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="supplier_code">Supplier Code</label>
								<input type="text" class="form-control" name="supplier_code" id="supplier_code" placeholder="Supplier Code">
							</div>
							<div class="form-group">
								<label for="iteminput">Item</label>
								<input type="text" class="form-control" name="item" id="iteminput" placeholder="Item Short Name">
							</div>
							<div class="form-group">
								<label for="descriptioninput">Description</label><br>
								<textarea name="description" id="descriptioninput" cols="65" rows="5"></textarea>
							</div>		
							<div class="form-group">
								<label for="costinput">Cost</label><br>
								<input type="number" class="form-control" name="cost" id="costinput" placeholder="Item Unit Cost" step="0.01">
							</div>		
							
							<div class="form-group">
								<label for="variationinput">Variation</label>
								<input type="text" class="form-control" name="variation" id="variationinput" placeholder="Variation (eg:colour)">
							</div>
							
							<div class="form-group">
								<label for="location">Location</label><br>
								<select name="location">
								<?php foreach ( $invLocations as $locId => $locName ){ ?>
									<option <?php echo ( $locId == $location ? "selected" : "" ) ?> value="<?php echo $locId ?>"><?php echo( $locName ) ?></option>
								<?php } ?>
								</select>
							</div>		

							<div class="form-group">
								<label for="category">Category</label><br>
								<select name="category">
									<option value="39">UNFILED</option>
								<?php foreach ( $allCategories as $catId => $catName ){ ?>
									<option <?php echo ( ( ( isset($categoryChoice) && $catId == $categoryChoice ) ) ? "selected" : "" ) ?> value="<?php echo $catId ?>"><?php echo( $catName ) ?></option>
								<?php } ?>
								</select>
							</div>		
							<div class="form-group">
								<label for="primarysupplier">Primary Supplier</label><br>
								<select name="supplier">
								<?php foreach ( $allSuppliers as $sup ){ ?>
									<option <?php echo ( $sup->value == $supplier ? "selected" : "" ) ?> value="<?php echo $sup->value ?>"><?php echo( $sup->text ) ?></option>
								<?php } ?>
								</select>
							</div>		
							<div class="form-group text-right">
								<button type="submit" class="btn btn-primary">Add Item</button>
							</div>
						</div>
														
					  </div>
				  </div>
				  
			  </form>
		  </div>
		</div>
	</div>
</div>
<!-- new Item Modal -->