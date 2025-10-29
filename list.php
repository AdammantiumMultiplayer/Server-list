<?php
echo "[";

$cache_file = "cache/list.json";
if (time()-filemtime($cache_file) < 30) { // 30 seconds
	echo file_get_contents($cache_file);
//	echo "cached";
}else{
	require("incl/database.php");

	$result = executeQuery("select * from serverlist where last_update > NOW() - INTERVAL 5 MINUTE order by official desc, servername;");
	$first = true;
	$json = "";
	foreach($result as $row) {
		if($first) {
			$first = false;
		}else{
			$json = $json.",";
		}
		$data = $row;
		for($i = 0; $i < 20; $i++) {
			unset($data[$i]);
		}

		$json = $json.json_encode($data);
	}
	file_put_contents($cache_file, $json);
	echo $json;
//	echo "uncached";
}

echo "]";
?>
