<?php
require("../incl/database.php");

ini_set('default_socket_timeout', 5); // 900


$statement = $conn->prepare("SELECT vote_willbreakmod, vote_notsynced, vote_partiallysynced, vote_fullysynced, voteable FROM ingame_mod_compat WHERE mod_name = ?");
$ok = $statement->execute([ trim($_GET['name'] ?? '---') ]);
$resultArray = $statement->fetch();
if(!$resultArray) {
        echo json_encode([
        	"vote_willbreakmod" => 0,
			"vote_notsynced" => 0,
			"vote_partiallysynced" => 0,
			"vote_fullysynced" => 0,
			"voteable" => true
        ]);
}else{
        echo json_encode([
			"vote_willbreakmod" => $resultArray["vote_willbreakmod"],
			"vote_notsynced" => $resultArray["vote_notsynced"],
			"vote_partiallysynced" => $resultArray["vote_partiallysynced"],
			"vote_fullysynced" => $resultArray["vote_fullysynced"],
			"votable" => $resultArray["voteable"]
        ]);
}

?>
