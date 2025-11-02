<?php
echo "[";

$cache_file = "../cache/hostserver.json";
if (time()-filemtime($cache_file) < 120) { // 60 seconds
        echo file_get_contents($cache_file);
//      echo "cached";
}else{
	require("../incl/database.php");
	
	ini_set('default_socket_timeout', 5); // 900

        $result = executeQuery("select * from v_hostservers;");
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

		$usage = file_get_contents($data["address"]."/status");
		if(strlen($usage) > 10) {
			$usage = explode(":", $usage)[1];
			$parts = explode("/", $usage);
			$data["current_lobbies"] = intval(trim($parts[0]));
			$data["max_lobbies"] = intval(trim($parts[1]));
			$data["utilization"] = round($data["current_lobbies"] / $data["max_lobbies"] * 100);
		}else{
			$data["current_lobbies"] = 0;
			$data["max_lobbies"] = 0;
			$data["utilization"] = 0;
		}

                $json = $json.json_encode($data);
        }
        file_put_contents($cache_file, $json);
        echo $json;
}

echo "]";
?>
