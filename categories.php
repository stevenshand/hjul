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
		category_group
FROM 	inv_categories";

if (!($stmt = $mysqli->prepare($itemListQuery))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $category, $group );
$stmt->store_result();
$resultsSize = $stmt->num_rows;

include 'inc/header.php';
?>

<!-- MODALS  -->
<!-- new category group modal -->
<div class="modal fade" id="newCategoryGroupModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	  
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4>Add New Category Group</h4>
			</div>
      
			<div class="modal-body">

				<form action="/actions/addCategoryGroup.php" class="form">
					<div class="container-fluid"><!--container-->
						<div class="row"><!--row1-->
						<div class="col-md-7">
							<div class="form-group">
								<label for="nameinput">Category Group Name</label>
								<input type="text" class="form-control" name="name" id="groupnameinput" placeholder="Group Name">
							</div>
							<div class="form-group text-right">
								<button type="submit" class="btn btn-primary">Add Group</button>
							</div>
						</div>		
						
					  </div><!--row1-->
				  </div><!--container-->
				  
			  </form>
		  </div>
		</div>
	</div>
</div>

<!-- new category modal -->
<div class="modal fade" id="newCategoryModal" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4>Add New Category</h4>
			</div>

			<div class="modal-body">

				<form action="/actions/addCategory.php" class="form">
					<div class="container-fluid"><!--container-->
						<div class="row"><!--row1-->
						<div class="col-md-7">
							<div class="form-group">
								<label class="form-label"  for="nameinput">Category Name</label>
								<input type="text" class="form-control" name="name" id="nameinput" placeholder="Category Name">
							</div>
                            <div class="form-group">
                                <label class="form-label" for="groupinput">Category Group</label><br>
                                <select name="group" id="groupinput">
                                    <option value="1">Components</option>
                                    <option value="2">Fabrication</option>
                                </select>
                            </div>

                            <div class="form-group text-right">
								<button type="submit" class="btn btn-primary">Add Category</button>
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
			<button title="add new category" data-toggle="modal" data-target="#newCategoryModal" class="btn btn-default">Add New Category</button>
			<button title="add new category group" data-toggle="modal" data-target="#newCategoryGroupModal" class="btn btn-default">Add New Category Group</button>
		</div>
	</div>	


	<div class="row">
		<div class="col-md-3">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<th>Category</th>
						<th>Group</th>
					</tr>
			
					<?php while ($stmt->fetch()) { ?>
						<tr>
							<td><?php echo $category  ?></td>
							<td><?php echo ( $group == 1 ? "Components" : "Fabrication" ) ?></td>
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
