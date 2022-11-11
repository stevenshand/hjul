<table class="table">
	<tr>
		<th>SKU</th>
		<th>Item</th>
		<th>Supplier</th>
		<th>Cost</th>
		<th>Amount</th>
		<th></th>
	</tr>
	
<?php while ($stmt->fetch()) { ?>
		<tr>
			<td><?php echo $id ?></td>
			<td><?php echo $shortName ?></td>
			<td><?php echo $supplier ?></td>
			<td><?php echo $cost ?></td>
			<td><input id="<?php echo $id ?>_qty" name="qty" style="width:3em;" type="number" min="1" value="1"></td>
			<td><span id="<?php echo $id ?>_add" data-itemId="<?php echo $id ?>" class="addBOMItemButton pull-right glyphicon glyphicon-plus-sign" aria-hidden="true"></td>
		</tr>	
<?php } ?>	

</table>		
	
	