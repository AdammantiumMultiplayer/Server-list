<?php
require("incl/database.php");

echo "[";

$result = executeQuery("select * from serverlist where last_update > NOW() - INTERVAL 5 MINUTE order by official desc, servername;");
$first = true;
foreach($result as $row) {
	if($first) {
		$first = false;
	}else{
		echo ",";
	}
	$data = $row;
	for($i = 0; $i < 20; $i++) {
		unset($data[$i]);
	}
	
	echo json_encode($data);
}
echo "]";
?>