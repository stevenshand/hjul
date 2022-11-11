<?php
	
class FeatureCollection implements JsonSerializable{
	
	public function setFeatures($features) {
	        $this->features = $features;
	    }
	
	function getFeatures(){
		return $this->features;
	}		
	
	function getType(){
		return "FeatureCollection";
	} 		

	public function jsonSerialize()
	{
		return
		[
		'type' => $this->getType(),
		'features' => $this->getFeatures()
		];
	}

	public $features;
} 

class Geo implements JsonSerializable{

	public function __construct( $postcode, $longitude, $latitude ) {
		$this->postcode = $postcode;
	    $this->longitude = $longitude;
	    $this->latitude = $latitude;
	  }

	  public function getLatitude(){
		  return $this->latitude;
	  }
	  
	  public function getLongitude(){
		  return $this->longitude;
	  }
	  
	  public function getCoordinates(){
		  return array($this->getLatitude(),$this->getLongitude());
	  }

	  public function getType(){
		  return "Point";
	  }

	  public function jsonSerialize()
      {
          return
          [
              'type' => $this->getType(),
			  'coordinates' => $this->getCoordinates()
          ];
      }

	  public $postcode;
	  public $latitude;
	  public $longitude;
}

class Feature implements JsonSerializable{
	
	public function __construct($weight, $id, $postcode, $longitude, $latitude) {
	    $this->weight = $weight;
	    $this->id = $id;
		$this->postcode = $postcode;
	    $this->longitude = $longitude;
	    $this->latitude = $latitude;
		$this->geometry = new Geo($postcode, $longitude, $latitude);
	}	
	  
	  public function getType(){
		  return "Feature";
	  }

	  public function getGeometry(){
		  return $this->geometry;
	  }

	  public function getProps(){
		  $props = array( 'weight' => $this->weight );
		  return $props;
	  }
	  
	  public function jsonSerialize()
      {
          return 
          [		
              'type' => $this->getType(),
			  'geometry' => $this->getGeometry(),
			  'id' => $this->id,
			  'properties' => $this->getProps()
		  ];
      }
	  
	  public $id;
	  public $weight;
	  public $geometry;
	  public $postcode;
	  public $latitude;
	  public $longitude;
	
}	
?>