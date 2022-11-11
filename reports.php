<?php
require 'configuration.php';
require 'functions/harefn.php';

include 'inc/header.php';
?>

<script src="js/jquery.tabletoCSV.js"></script>

<div class="container">
	<div class="row">
		<div class="col-md-12">
		<h1>Reports</h1>
		<div>
	</div>
	
	<div class="row">
		
		<!-- Single button -->
		<div class="btn-group">
		  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Shipped By Month  <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu">

			  <li><a href="rep_shippedbymonth.php?month=12&year=2022">December 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=11&year=2022">November 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=10&year=2022">October 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=9&year=2022">September 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=8&year=2022">August 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=7&year=2022">July 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=6&year=2022">June 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=5&year=2022">May 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=4&year=2022">April 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=3&year=2022">March 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=2&year=2022">February 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=1&year=2022">January 22</a></li>
			  <li><a href="rep_shippedbymonth.php?month=12&year=2021">December 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=11&year=2021">November 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=10&year=2021">October 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=9&year=2021">September 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=8&year=2021">August 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=7&year=2021">July 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=6&year=2021">June 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=5&year=2021">May 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=4&year=2021">April 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=3&year=2021">March 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=2&year=2021">February 21</a></li>
			  <li><a href="rep_shippedbymonth.php?month=1&year=2021">January 21</a></li>
		    <li><a href="rep_shippedbymonth.php?month=12&year=2020">December 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=11&year=2020">November 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=10&year=2020">October 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=9&year=2020">September 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=8&year=2020">August 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=7&year=2020">July 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=6&year=2020">June 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=5&year=2020">May 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=4&year=2020">April 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=3&year=2020">March 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=2&year=2020">February 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=1&year=2020">January 20</a></li>
		    <li><a href="rep_shippedbymonth.php?month=12&year=2019">December 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=11&year=2019">November 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=10&year=2019">October 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=9&year=2019">September 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=8&year=2019">August 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=7&year=2019">July 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=6&year=2019">June 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=5&year=2019">May 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=4&year=2019">April 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=3&year=2019">March 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=2&year=2019">February 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=1&year=2019">January 19</a></li>
		    <li><a href="rep_shippedbymonth.php?month=12&year=2018">December 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=11&year=2018">November 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=10&year=2018">October 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=9&year=2018">September 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=8&year=2018">August 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=7&year=2018">July 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=6&year=2018">June 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=5&year=2018">May 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=4&year=2018">April 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=3&year=2018">March 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=2&year=2018">February 18</a></li>
		    <li><a href="rep_shippedbymonth.php?month=1&year=2018">January 18</a></li>
		  </ul>
		</div>
	</div>			

	<div class="row">
		<div class="btn-group">
		  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Customer Data By Month <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu">
	 		<?php  foreach( fetchCustomerMonths() as $entry ){ ?>	 		
			<li><a href="rep_customerdatabymonth.php?month=<?php echo ($entry->mt )?>&year=<?php echo ($entry->yr )?>">
					
					<?php 	$dateObj   = DateTime::createFromFormat('!m', $entry->mt);
							$monthName = $dateObj->format('F'); 
							echo ($monthName )?> <?php echo ($entry->yr )
					?>
				</a></li>			
			<?php } ?>
			  
		    
		  </ul>
		</div>		
	</div>			

	<div class="row">
		<!-- Single button -->
		<div class="btn-group">
		  <a href="rep_incomeinadvance.php" class="btn btn-default">Income in Advance</a>
		</div>
	</div>
	
	<hr>
	
	<style>
	    .mapboxgl-popup {
	        max-width: 400px;
	        font: 12px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
	    }
	</style>
	
	<div><!-- map container -->
					
				<div class="row">
					<div class="md-col-6">
						<h3>2018 Market Survey</h3>
						<h4>Distribution of survey completions</h4>
					</div>
					<div class="md-col-6">
						<button class="btn btn-default" style="font-weight:bold;color:rgb(72,61,139);" id="customerToggle">Toggle Customer Data</button>
						<button class="btn btn-default" style="font-weight:bold;color:rgb(171,72,33);" id="surveyToggle">Toggle Survey Data</button>
					</div>
				</div>
		<div id="worldcontainer" style="padding-top:20px;padding-bottom:20px;">
			<div id='map' style='width:100%;height:600px'>
			</div>
		</div>

	</div>	
	
	
	</div>			

</div>		


<script>

	mapboxgl.accessToken = 'pk.eyJ1Ijoic3RldmVuc2hhbmQiLCJhIjoiY2psNng1bTN2MzJiNDNycXR0cWIwOXV4MSJ9.Mv9VdGJ4slAROiikC5v3Mw';
	var map = new mapboxgl.Map({
		container: 'map',
		center: [-3.496368, 55.904904],
		zoom:5,
		style: 'mapbox://styles/mapbox/streets-v10'
	});
	
	map.on('load', function () {
		
		submissionsDataSource = {
			"type": "geojson",
			"data": "/data/mapping/survey_data.php?nocache=" + (new Date()).getTime()
		};
		
		customerDataSource = {
			"type": "geojson",
			"data": "/data/mapping/customer_data.php?nocache=" + (new Date()).getTime()
		};
		
		// Survey Layer Object
		submissionsLayer = {
			        "id": "submissions",
			        "type": "circle",
			        "source": submissionsDataSource,
			paint: {
				'circle-radius':5,
			      'circle-opacity': 0.6,
			      'circle-color': 'rgb(171,72,33)'
			    }
		};

		// Customer Layer Object
		customerLayer = {
			        "id": "customers",
			        "type": "circle",
			        "source": customerDataSource,
					paint: {
						'circle-radius':5,
			      	  	'circle-opacity': 0.6,
			      	  	'circle-color': 'rgb(72,61,139)'
			    	},
					'layout': {
						'visibility': 'visible',
					},
			    };

	    map.addLayer(customerLayer);
	    map.addLayer(submissionsLayer);
	});
	
	$( function(){
		$('#customerToggle').click( function(){
			if( map.getLayoutProperty('customers', 'visibility' ) == "visible" ){
				map.setLayoutProperty('customers', 'visibility', 'none' );
			}else{
				map.setLayoutProperty('customers', 'visibility', 'visible' );
			}
		});
	});
	
	
	$( function(){
		$('#surveyToggle').click( function(){
			if( map.getLayoutProperty('submissions', 'visibility' ) == "visible" ){
				map.setLayoutProperty('submissions', 'visibility', 'none' );
			}else{
				map.setLayoutProperty('submissions', 'visibility', 'visible' );
			}
		});
	});

</script>





<?php include 'inc/footer.php'; ?>
