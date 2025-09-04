<?php
require("../incl/database.php");
require("func.php");

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bekomme den JSON Body der Anfrage
$data = json_decode(file_get_contents('php://input'), true);

if(isset($_GET['code'])) {
	$statement = $conn->prepare("SELECT address, port FROM join_code WHERE code = ?");
	$ok = $statement->execute([ trim(intval($_GET['code'] ?? '0')) ]);
	$resultArray = $statement->fetch();
	if(!$resultArray) {
		echo json_encode(["ok" => false]);
	}else{
		echo json_encode(["address" => $resultArray["address"], "port" => $resultArray["port"]]);
	}
}else if(isset($data['port'])) {
	$statement = $conn->prepare("SELECT CREATE_JOIN_CODE(?, ?, ?) as code;");
	$ok = $statement->execute([
			/* ADDRESS         */  trim(getAddress()),
			/* PORT            */  trim(intval($data['port'] ?? '0')),
			/* INFO            */  "",
		]);
	$resultArray = $statement->fetch();
	echo json_encode(["ok" => $resultArray["code"]]);
}else{
	echo json_encode(["ok" => false]);
}
?>
