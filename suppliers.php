<?php
require 'configuration.php';
require 'functions/harefn.php';
require 'functions/invfn.php';
	
$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$itemListQuery = 
"SELECT id,
		name,
		contact_name,
		email_address,
		country
FROM 	inv_suppliers
ORDER BY name"; 

//echo ($itemListQuery);

if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($supplierId, $name, $contact, $emailAddress, $countryId );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

$countries = fetchCountriesArray();

include 'inc/header.php';
?>

<!-- MODALS  -->
<!-- new order modal -->
<div class="modal fade" id="newSupplierModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	  
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4>Add New Supplier</h4>
			</div>
      
			<div class="modal-body">

				<form action="/actions/addSupplier.php" class="form">
					<div class="container-fluid"><!--container-->
						<div class="row"><!--row1-->
						<div class="col-md-7">
							<div class="form-group">
								<label for="nameinput">Supplier Name</label>
								<input type="text" class="form-control" name="name" id="nameinput" placeholder="Supplier Name">
							</div>
							<div class="form-group">
								<label for="countryinput">Country</label>
								<select name="country" id="countryinput">
								  	<?php foreach ($countries as $key => $value) {
								  		if( !$country ){$country = 230;}
								  	?>
									<option <?php echo ( $key==$country ? "selected" : "") ?> value="<?php echo $key ?>">
										<?php echo $value ?>
									</option>
  									<?php }?>
  								</select>
							</div>
						</div>		
						
						<div class="col-md-5">
							<div class="form-group">
								<label for="contactinput">Contact Name</label>
								<input type="text" class="form-control" name="contact" id="contactinput" placeholder="Contact Name">
							</div>
							<div class="form-group">
								<label for="contactemail">Contact Email</label>
								<input type="text" class="form-control" name="email" id="contactemailinput" placeholder="Contact Email">
							</div>
							<div class="form-group">
								<div class="form-group text-right">
									<button type="submit" class="btn btn-primary">Add Suppplier</button>
								</div>
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

<!-- MODALS -->


<div class="container">
	
	<div class="row">
		<div class="col-md-12">
			<button title="View Items" class="btn btn-default"><a href="/inventory.php">View Items</a></button>
			<button title="add new supplier" data-toggle="modal" data-target="#newSupplierModal" class="btn btn-default">Add New Supplier</button>
		</div>	
	</div>	


	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th>Supplier</th><th>Contact</th><th>Email</th><th>Country</th>
					</tr>
			
					<?php while ($stmt->fetch()) { ?>
						<tr>
							<td><a href="supplier.php?supplierId=<?php echo $supplierId ?>"><?php echo $name ?></a></td>
							<td><?php echo $contact  ?></td>
							<td><a href="mailto:<?php echo $emailAddress  ?>"><?php echo $emailAddress  ?><a/></td>
							<td><?php echo ($countries[$countryId]) ?></td>
								 
						</tr>
					<?php }?>
				</tbody>			
				</table>	
			</div>	
		</div>	
	</div>	
</div>	


<?php $stmt->close(); ?>

<?php include 'inc/footer.php'; ?>
