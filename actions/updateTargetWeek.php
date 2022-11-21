<?php
//TODO TARGET WEEK
require('../configuration.php');

$orderId = $_POST["orderId"];
$targetWeek = $_POST["targetWeek"];
$targetDate = date(INPUTFIELDDATEFORMAT,$targetWeek);

$mysqli = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$targetWeekUpdateStatement = "UPDATE orders set target_date = ? where id = ?";
if (!($stmt = $mysqli->prepare($targetWeekUpdateStatement))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->bind_param("si", $targetDate, $orderId )) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->execute();
$stmt->close();
$mysqli->close();

$form_data['success'] = true;
$form_data['orderId'] = $orderId;
$form_data['targetDate'] = $targetDate;
$form_data['msg'] = 'targetWeek updated';
echo json_encode($form_data);

?>