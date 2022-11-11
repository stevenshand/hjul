<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

require 'configuration.php';
require 'functions/harefn.php';
require 'functions/snapshotfn.php';

$snapshotDate = $_GET['date'];
$refresh = $_GET['refresh'];

if( !isset($snapshotDate) ){
	$snapshotDate = fetchWeekStartDate( date("d-m-Y") );
}


$snapshot = fetchSnapShot($snapshotDate);

if( !$snapshot ){
	addNewSnapshot($snapshotDate);
	$snapshot = fetchSnapShot($snapshotDate);
}

if( $refresh ){
	$snapshot = refreshSnapShot($snapshot);
}

$previousDates = fetchPreviousSnapshotDates();

include 'inc/header.php';

?>

<div class="container">

<form action="/actions/savesnapshot.php" method="POST">
<input type="hidden" name="id" value="<?php echo $id ?>">

<div class="row">
	<div class="col-md-6">
		<h3>Willow Bike weekly snapshot</h3>
		<h4>wk beginning : <?php echo $snapshot->week ?></h4>	
	</div>
	<div class="col-md-6 text-right">
		<label for="dateSelector">Select Date : </label>
		<select id="dateSelector" name="week">
			<?php  
			foreach( $previousDates as $week ){ ?>
			<option value="<?php echo $week ?>" <?php echo ( $week == $snapshotDate ? "selected" : "" ) ?>><?php echo $week ?></option>
			<?php } ?>
		</select>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
<table class="table table-responsive table-bordered table-condensed table-striped snapshot<?php echo ( $snapshot->locked ? ' snapshot-locked' : '' )?>">
	<tbody>
	<tr>
		<th class="col-md-2"></th>
		<th colspan="3">Last Week</th>
		<th colspan="3">MTD</th>
		<th colspan="3">Forecast</th>
		<th colspan="3">Budget</th>
	</tr>	
	<tr>
		<th></th>
		<th>Frames</th>
		<th>Bikes</th>
		<th>Total</th>
		<th>Frames</th>
		<th>Bikes</th>
		<th>Total</th>
		<th>Frames</th>
		<th>Bikes</th>
		<th>Total</th>
		<th>Total</th>
	</tr>
	<tr>
		<th>New Orders (£)</th>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->frame_orders_value ) ?>" 
					name="db_frame_orders_value"		
					id="frame_orders_value"		
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->frame_orders_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->bike_orders_value ) ?>" 	 
					name="db_bike_orders_value" 		
					id="bike_orders_value" 		
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->bike_orders_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo $snapshot->frame_orders_value + $snapshot->bike_orders_value ?>"
					name="total_orders_value"
					style="width:100%;">
			<?php } else{ ?>
				<?php curry(  $snapshot->frame_orders_value + $snapshot->bike_orders_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mtd_frame_orders_value ) ?>" 
					name="db_mtd_frame_orders_value"	
					id="mtd_frame_orders_value" 
					style="width:100%;">
			<?php } else{ ?>
				<?php curry(  $snapshot->mtd_frame_orders_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mtd_bike_orders_value ) ?>" 
					name="db_mtd_bike_orders_value" 	
					id="mtd_bike_orders_value" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mtd_bike_orders_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo $snapshot->mtd_frame_orders_value + $snapshot->mtd_bike_orders_value ?>"
					name="total_mtd_orders_value" 							
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mtd_frame_orders_value + $snapshot->mtd_bike_orders_value ) ?>
				<?php } ?>
		</td>
		<td class="manualEntry">
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mf_frame_orders_value ) ?>" 
					name="db_mf_frame_orders_value" 	
					id="mf_frame_orders_value" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mf_frame_orders_value ) ?>
				<?php } ?>
		</td>
		<td class="manualEntry">
			<?php if( !$snapshot->locked) {?>
			<input 	type="text"
					value="<?php echo( $snapshot->mf_bike_orders_value ) ?>"  
					name="db_mf_bike_orders_value" 		
					id="mf_bike_orders_value" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mf_bike_orders_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo $snapshot->mf_frame_orders_value + $snapshot->mf_bike_orders_value ?>" 						 
					name="total_mf_orders_value" 							
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mf_frame_orders_value + $snapshot->mf_bike_orders_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mb_total_orders_value ) ?>" 
					name="db_mb_total_orders_value" 	
					id="mb_total_orders_value" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php curry(  $snapshot->mb_total_orders_value ) ?>
				<?php } ?>
		</td>
	</tr>	                              
	<tr>                                  
		<th>New Orders (units)</th>       
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->frame_orders_units ) ?>"   
					name="db_frame_orders_units"  		
					id="frame_orders_units" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->frame_orders_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->bike_orders_units ) ?>" 	  
					name="db_bike_orders_units" 	 	
					id="bike_orders_units" 		
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->bike_orders_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo $snapshot->frame_orders_units + $snapshot->bike_orders_units ?>"				 	 
					name="total_orders_units" 					 		
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->frame_orders_units + $snapshot->bike_orders_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mtd_frame_orders_units ) ?>" 
					name="db_mtd_frame_orders_units" 	
					id="mtd_frame_orders_units" 
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mtd_frame_orders_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mtd_bike_orders_units ) ?>"  
					name="db_mtd_bike_orders_units"  	
					id="mtd_bike_orders_units" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mtd_bike_orders_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo $snapshot->mtd_frame_orders_units + $snapshot->mtd_bike_orders_units ?>" 						 
					name="total_mtd_order_units" 					 		
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mtd_frame_orders_units + $snapshot->mtd_bike_orders_units ) ?>
				<?php } ?>
		</td>
		<td class="manualEntry">
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mf_frame_orders_units ) ?>"  
					name="db_mf_frame_orders_units"  	
					id="mf_frame_orders_units" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mf_frame_orders_units ) ?>
				<?php } ?>
		</td>
		<td class="manualEntry">
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mf_bike_orders_units ) ?>"   
					name="db_mf_bike_orders_units"  	
					id="mf_bike_orders_units" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mf_bike_orders_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mf_frame_orders_units+$snapshot->mf_bike_orders_units ) ?>"				 	 
					name="mf_total_orders_units" 					 		
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mf_frame_orders_units+$snapshot->mf_bike_orders_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mb_total_orders_units ) ?>"  
					name="db_mb_total_orders_units"  	
					id="mb_total_orders_units" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mb_total_orders_units ) ?>
				<?php } ?>
		</td>
	</tr>	                              
	<tr>                                  
		<th>Sales (£)</th>                
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->frame_sales_value ) ?>" 	  
					name="db_frame_sales_value" 	 	
					id="frame_sales_value" 		
					style="width:100%;">
			<?php } else{ ?>
				<?php curry(  $snapshot->frame_sales_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->bike_sales_value ) ?>" 	  
					name="db_bike_sales_value" 	 		
					id="bike_sales_value" 		
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->bike_sales_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo($snapshot->frame_sales_value+$snapshot->bike_sales_value ) ?>"					 	 
					name="total_sales_value" 					 		
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->frame_sales_value+$snapshot->bike_sales_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mtd_frame_sales_value ) ?>" 
					name="db_mtd_frame_sales_value" 	
					id="mtd_frame_sales_value" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mtd_frame_sales_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mtd_bike_sales_value ) ?>"  
					name="db_mtd_bike_sales_value" 	
					id="mtd_bikes_sales_value" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mtd_bike_sales_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mtd_bike_sales_value+$snapshot->mtd_frame_sales_value ) ?>"
					name="mtd_total_sales_value"
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mtd_bike_sales_value+$snapshot->mtd_frame_sales_value ) ?>
				<?php } ?>
		</td>
		<td class="manualEntry">
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mf_frame_sales_value ) ?>"  
					name="db_mf_frame_sales_value"
					id="mf_frame_sales_value" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mf_frame_sales_value ) ?>
				<?php } ?>
		</td>
		<td class="manualEntry">
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mf_bike_sales_value ) ?>"   
					name="db_mf_bike_sales_value"  	
					id="mf_bike_sales_value" 	
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mf_bike_sales_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mf_frame_sales_value + $snapshot->mf_bike_sales_value ) ?>"
					name="mf_total_sales_value"
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mf_frame_sales_value + $snapshot->mf_bike_sales_value ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text"
					value="<?php echo( $snapshot->mb_total_sales_value ) ?>"
					name="db_mb_total_sales_value"
					id="mb_total_sales_value"
					style="width:100%;">
			<?php } else{ ?>
				<?php curry( $snapshot->mb_total_sales_value ) ?>
				<?php } ?>
		</td>
	</tr>	                             
	<tr>                                 
		<th>Sales (units)</th>           
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text"
					value="<?php echo( $snapshot->frame_sales_units ) ?>"
					name="db_frame_sales_units"
					id="frame_sales_units"
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->frame_sales_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text"
					value="<?php echo( $snapshot->bike_sales_units ) ?>"
					name="db_bike_sales_units"
					id="bike_sales_units"
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->bike_sales_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text"
					value="<?php echo( $snapshot->frame_sales_units+$snapshot->bike_sales_units ) ?>"
					name="total_sales_units"
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->frame_sales_units+$snapshot->bike_sales_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mtd_frame_sales_units ) ?>"
					name="db_mtd_frame_sales_units"
					id="mtd_frame_sales_units"
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mtd_frame_sales_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text"
					value="<?php echo( $snapshot->mtd_bike_sales_units ) ?>"
					name="db_mtd_bike_sales_units"
					id="mtd_bike_sales_units"
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mtd_bike_sales_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mtd_frame_sales_units+$snapshot->mtd_bike_sales_units ) ?>"
					name="mtd_total_sales_units"
					id=""
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mtd_frame_sales_units+$snapshot->mtd_bike_sales_units ) ?>
				<?php } ?>
		</td>
		<td class="manualEntry">
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mf_frame_sales_units ) ?>"
					name="db_mf_frame_sales_units"
					id="mf_frame_sales_units"
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mf_frame_sales_units ) ?>
				<?php } ?>
		</td>
		<td class="manualEntry">
			<?php if( !$snapshot->locked) {?>
			<input 	type="text"
					value="<?php echo( $snapshot->mf_bike_sales_units ) ?>"
					name="db_mf_bike_sales_units"
					id="mf_bike_sales_units"
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mf_bike_sales_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text"
					value="<?php echo( $snapshot->mf_frame_sales_units + $snapshot->mf_bike_sales_units ) ?>"
					name="mf_total_sales_units"
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mf_frame_sales_units + $snapshot->mf_bike_sales_units ) ?>
				<?php } ?>
		</td>
		<td>
			<?php if( !$snapshot->locked) {?>
			<input 	type="text" 
					value="<?php echo( $snapshot->mb_total_sales_units ) ?>"
					name="db_mb_total_sales_units"
					id="mb_total_sales_units"
					style="width:100%;">
			<?php } else{ ?>
				<?php echo( $snapshot->mb_total_sales_units ) ?>
				<?php } ?>
		</td>
	</tr>	
</tbody>	
</table>
</div>
</div>

<div class="row">
	<div class="col-md-4">
		<table class="table table-responsive table-bordered table-condensed <?php echo ( $snapshot->locked ? ' snapshot-locked' : '' )?>">
			<tbody>
				<tr>
					<th class="col-xs-6">Orderbook (£)</th>
					<td class="col-md-10">
					<?php if( !$snapshot->locked) {?>
						<input 	type="text"
								value="<?php echo( $snapshot->order_book_value ) ?>"
								name="db_order_book_value"
								id="order_book_value"
								style="width:100%;">
					<?php } else{ ?>
						<?php curry( $snapshot->order_book_value ) ?>
						<?php } ?>
					</td>
				</tr>	
				<tr>
					<th class="col-xs-6">Orderbook (units)</th>
						<td class="col-md-10">
						<?php if( !$snapshot->locked) {?>
							<input 	type="text"
									value="<?php echo( $snapshot->order_book_units ) ?>"
									name="db_order_book_units"
									id="order_book_units"
									style="width:100%;">
						<?php } else{ ?>
							<?php echo( $snapshot->order_book_units ) ?>
							<?php } ?>
					</td>
				</tr>
				<tr>
					<th class="col-xs-6">Enquiries</th>
						<td class="col-md-10 manualEntry">
						<?php if( !$snapshot->locked) {?>
							<input 	type="text"
									value="<?php echo $snapshot->enquiries ?>"
									name="db_enquiries"
									id="enquiries"
									style="width:100%;">
						<?php } else{ ?>
							<?php echo( $snapshot->enquiries ) ?>
							<?php } ?>
					</td>
				</tr>	
				<tr>
					<th class="col-xs-6">Web Sessions</th>
						<td class="col-md-10 manualEntry">
						<?php if( !$snapshot->locked) {?>
							<input 	type="text"
									value="<?php echo $snapshot->web_sessions ?>"
									name="db_web_sessions"
									id="web_sessions"
									style="width:100%;">
						<?php } else{ ?>
							<?php echo( $snapshot->web_sessions ) ?>
							<?php } ?>
					</td>
				</tr>	
				<tr>
					<th class="col-xs-6">Cash In Bank</th>
						<td class="col-md-10 manualEntry">
						<?php if( !$snapshot->locked) {?>
							<input 	type="text"
									value="<?php echo $snapshot->cash_in_bank ?>"
									name="db_cash_in_bank"
									id="cash_in_bank"
									style="width:100%;">
						<?php } else{ ?>
							<?php curry( $snapshot->cash_in_bank ) ?>
							<?php } ?>
					</td>
				</tr>	
			</tbody>
		</table>	

		<small>snapshot id : <?php echo $snapshot->id ?></small>

	</div>
	<div class="col-md-8">
		<h5>Notes</h5>
		<div id="notecontainer" style="padding:10px;border:1px solid #c0c0c0;">
		<?php if( !$snapshot->locked) {?>
		<textarea id="notes" style="width:100%" rows="10" name=db_notes><?php echo $snapshot->notes ?></textarea>
			<?php } else{ ?>
				<div>
					<?php echo( $snapshot->notes ) ?>
				</div>
				<?php } ?>
		</div>
		
		
		<div class="text-right">
			<button id="refresh" type="button" class="btn btn-default" name="refresh" value="refresh" style="margin-top:10px;">refresh</button>
			<input type="submit" class="btn btn-default" name="submit" value="save" style="margin-top:10px;"/>
		</div>	
	</div>	
</div>
</form>
</div>

<script>
	
	$( '#refresh' ).click( function(){
		window.location.href = "snapshot.php?refresh=true&date=" + $('#dateSelector').val(); 
	});
	
	$('#dateSelector').change( function(){
		window.location.href = "snapshot.php?date=" + $('#dateSelector').val(); 
	});
		
</script>	

<?php include 'inc/footer.php'; ?>
