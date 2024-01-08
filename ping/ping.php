<?php
require("../incl/database.php");
require("func.php");

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bekomme den JSON Body der Anfrage
$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['port'])) {
	$statement = $conn->prepare("CALL PING_SERVER(?, ?, ?, ?, ?);");
	$ok = $statement->execute([
			/* ADDRESS         */  trim(getAddress()),
			/* PORT            */  trim(intval($data['port'] ?? '0')),
			/* CURRENT_PLAYERS */  trim(intval($data['players'] ?? '0')),
			/* CURRENT_MAP     */  trim($data['map'] ?? '?'),
			/* CURRENT_MODE    */  trim($data['mode'] ?? '?'),
							]);
	echo json_encode(["ok" => $ok]);
}else{
	echo json_encode(["ok" => false]);
}
?>