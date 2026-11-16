<?php
require("../incl/database.php");

header('Content-Type: application/json');

if (!isset($_GET['name'], $_GET['vote_type'], $_GET['username'])) {
	echo json_encode(["ok" => false]);
	exit;
}

$statement = $conn->prepare("CALL RECORD_MOD_COMPAT_VOTE(?, ?, ?, ?)");
$ok = $statement->execute([
	trim($_SERVER['REMOTE_ADDR'] ?? ''),
	trim($_GET['name']),
	trim($_GET['vote_type']),
	trim($_GET['username'])
]);

echo json_encode(["ok" => $ok]);
?>
