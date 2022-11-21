<?php

//require ( __DIR__.'/../configuration.php' );

function userList(){
	
	$users[0] = "Steven";
	
	return $users;
}

function deletePurchaseOrder( $purchaseOrderId ){

	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$statement = "DELETE from inv_purchase_orders where id = ".$purchaseOrderId;
	if (!($stmt = $mysqli->prepare($statement))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	
	
	$stmt->execute();

	$statement = "DELETE from inv_po_lines where po_id = ".$purchaseOrderId;
	if (!($stmt = $mysqli->prepare($statement))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}	

	$stmt->execute();

	$stmt->close();
	$mysqli->close();
}


class ItemMove
{
    public $date;
    public $qty;
}

function fetchStockIn($itemId){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$query = 
		"SELECT 
			qty, 
			UNIX_TIMESTAMP(date)
		FROM goods_in 
		WHERE item = ".$itemId."
		ORDER BY date DESC";

	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$stmt->bind_result($qty, $date );	

	$stmt->store_result();
	$itemsSize = $stmt->num_rows;

	$moves = array();
	while( $stmt->fetch() ){
		$move = new ItemMove;
		$move->date = $date;
		$move->qty = $qty;
		$moves[] = $move;
	}
	
	//print_r($moves);
	
 	return $moves;	
}

function fetchStockOut($itemId){
	
	$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

	if (mysqli_connect_errno()) {
	    echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$query = 
		"SELECT 
			qty, 
			UNIX_TIMESTAMP(date)
		FROM goods_out 
		WHERE item = ".$itemId."
		ORDER BY date DESC";

	if (!($stmt = $mysqli->prepare($query))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->execute()) {
	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$stmt->bind_result($qty, $date );	

	$stmt->store_result();
	$itemsSize = $stmt->num_rows;

	$moves = array();
	while( $stmt->fetch() ){
		$move = new ItemMove;
		$move->date = $date;
		$move->qty = $qty;
		$moves[] = $move;
	}
	
	//print_r($moves);
	
 	return $moves;	
}

function fetchInvLocations(){


$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
	"SELECT 
		id, 
		location
	FROM inv_location 
	ORDER BY 
		location";

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $location );	

$stmt->store_result();
$itemsSize = $stmt->num_rows;

$locations = array();
while( $stmt->fetch() ){
	$locations[$id] = $location;
}

//var_dump($locations);

return $locations;

}

function fetchAllCategories(){

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
	"SELECT 
		id, 
		name
	FROM 
		inv_categories 
	ORDER BY 
		name";

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $name );	

$stmt->store_result();
$itemsSize = $stmt->num_rows;

$categories = array();
while( $stmt->fetch() ){
	$categories[$id] = $name;
}


return $categories;
	
}

function fetchSupplierNames(){

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
	"SELECT 
		id, 
		name
	FROM 
		inv_suppliers";

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $name );	

$stmt->store_result();
$itemsSize = $stmt->num_rows;

$suppliers = array();
while( $stmt->fetch() ){
	$suppliers[$id] = $name;
}

//var_dump($suppliers);

return $suppliers;

}

function fetchStatuses(){

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
	"SELECT 
		id, 
		status
	FROM 
		inv_po_status";

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $status );	

$stmt->store_result();
$itemsSize = $stmt->num_rows;

$statuses = array();
while( $stmt->fetch() ){
	$statuses[$id] = $status;
}

//var_dump($suppliers);

return $statuses;

}

function fetchAllInventoryCategoryGroups(){
    $mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $query =
        "SELECT 
		id, 
		name
	FROM 
		inv_category_group";

    if (!($stmt = $mysqli->prepare($query))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    $stmt->bind_result($id, $name );

    $stmt->store_result();

    $groups = array();
    while( $stmt->fetch() ){
        $groups[$id] = $name;
    }

    return $groups;

}

function fetchAllPOStatuses(){

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
	"SELECT 
		id, 
		status
	FROM 
		inv_po_status";

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $status );

$stmt->store_result();
$itemsSize = $stmt->num_rows;

$statuses = [];
$index = 0;

$statuses = array();
while( $stmt->fetch() ){
    $statuses[$id] = $status;
}

//var_dump($suppliers);

return $statuses;

}
class Supplier{
    public $value;
    public $text;
    public $contact;
    public $email;
    public $country;
    public function __construct($value)
    {
        $this->value = $value;
    }
}

function fetchAllSuppliers($truncated){

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
	"SELECT 
		id, 
		name,
		contact_name,
		email_address,
		country 
	FROM 
		inv_suppliers 
	ORDER BY 
		name";

if( $truncated ){
$query = 
	"SELECT 
		id, 
		name
	FROM 
		inv_suppliers 
	ORDER BY 
		name";
}	
		
if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

if( $truncated ){
	$stmt->bind_result($id, $name );
}else{
	$stmt->bind_result($id, $name, $contact, $email, $country );	
}

$stmt->store_result();
$itemsSize = $stmt->num_rows;



$suppliers = array();
$index = 0;
while( $stmt->fetch() ){
    $supplier = new Supplier($id);
    $supplier->text = $name;

	if(!$truncated){
        $supplier->contact = $contact;
        $supplier->email = $email;
		$supplier->country = $country;
	}
    array_push($suppliers, $supplier );
	$index = $index+1;
}

return $suppliers;

}

class Category{
    public $value;
    public $text;
    public function __construct($value,$text)
    {
        $this->value = $value;
        $this->text = $text;
    }
}

function fetchCategories($fab){

$tableName = $fab ? "fab_categories" : "inv_categories";

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
	"SELECT 
		id, 
		name
	FROM 
		".$tableName." 
	ORDER BY 
		name";

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $name );

$stmt->store_result();
$itemsSize = $stmt->num_rows;

$categories = array();
$index = 0;
while( $stmt->fetch() ){
    $category = new Category($id,$name);
    array_push( $categories, $category );
	$index = $index+1;
}

return $categories;

}


function getItemSuppliers( $itemId ){

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = 
	"SELECT 
		supplier_id, 
		inv_suppliers.name 
	FROM 
		inv_item_supplier 
	LEFT JOIN 
		inv_suppliers
	ON 
		inv_item_supplier.supplier_id = inv_suppliers.id
	WHERE
		inv_item_supplier.item_id = ".$itemId;

if (!($stmt = $mysqli->prepare($query))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($id, $name);
$stmt->store_result();
$itemsSize = $stmt->num_rows;

$suppliers = array();
$index = 0;
while( $stmt->fetch() ){
	$suppliers[$index]->name = $name;
	$suppliers[$index]->id = $id;
	$index = $index+1;
}

//var_dump($suppliers);

return $suppliers;

}


?>