<?php
require("../incl/database.php");

ini_set('default_socket_timeout', 5); // 900


$statement = $conn->prepare("SELECT 1 FROM moderator WHERE unique_id = ?");
$ok = $statement->execute([ trim(intval($_GET['id'] ?? '0')) ]);
$resultArray = $statement->fetch();
if(!$resultArray) {
        echo json_encode(["moderator" => false]);
}else{
        echo json_encode(["moderator" => true]);
}

?>
