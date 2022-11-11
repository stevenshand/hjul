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
		location
FROM 	inv_location
ORDER BY location"; 

//echo ($itemListQuery);

if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($locationId, $location );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

include 'inc/header.php';
?>

<!-- MODALS  -->
<!-- new order modal -->
<div class="modal fade" id="newLocationModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	  
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4>Add New Location</h4>
			</div>
      
			<div class="modal-body">

				<form action="/actions/addInventoryLocation.php" class="form">
					<div class="container-fluid"><!--container-->
						<div class="row"><!--row1-->
						<div class="col-md-7">
							<div class="form-group">
								<label for="nameinput">Location Name</label>
								<input type="text" class="form-control" name="location" id="locationinput" placeholder="Location Name">
							</div>
							<div class="form-group text-right">
								<button type="submit" class="btn btn-primary">Add Location</button>
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
			<button title="add new location" data-toggle="modal" data-target="#newLocationModal" class="btn btn-default">Add New Location</button>
		</div>	
	</div>	


	<div class="row">
		<div class="col-md-4">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th>Location</th>
					</tr>
			
					<?php while ($stmt->fetch()) { ?>
						<tr>
							<td><a href="location.php?supplierId=<?php echo $locationId ?>"><?php echo $location ?></a></td>
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
