<?php
	$driver = "mysql";
	$servername = "127.0.0.1";
	$username = "amp";
	$password = "fkQ6SxEBNcY2Xxxk";
	$dbname = "amp";
	
	$err_code = -1;
	
	if(isset($conn)) return;
	
	$conn = new PDO("$driver:host=$servername;dbname=$dbname", $username, $password)
	or $err_code = 1;
	
	if($err_code > -1){
		echo "Failed DB Connection.";
		die();
	}
	
	function executeQuery($query, ...$params) {
		global $conn;
		$stmt = $conn->prepare($query);
		if(sizeof($params) % 2 == 0){
			for($i = 0; $i < sizeof($params); $i += 2){
				$stmt->bindParam($params[$i], $params[$i + 1]);
			}
		}
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	function executeNonQuery($query, ...$params) {
		global $conn;
		$stmt = $conn->prepare($query);
		if(sizeof($params) % 2 == 0){
			for($i = 0; $i < sizeof($params); $i += 2){
				$stmt->bindParam($params[$i], $params[$i + 1]);
			}
		}
		$stmt->execute();
	}
	
	function getColumns($table) {
		global $conn;
		$stmt = $conn->prepare("DESCRIBE $table");
		$stmt->execute();
		$table_names = $stmt->fetchAll(PDO::FETCH_COLUMN);
		return $table_names;
	}
	
	function executeInsert($table, $form) {
		global $conn, $_POST;
		$table_fields = getColumns($table);
		
		$available_fields = array();
		$param_fields = array();
		
		foreach($table_fields as $column) {
			if(array_key_exists($column, $_POST)) {
				array_push($available_fields, $column);
				array_push($param_fields, ":".$column);
			}
		}
		
		$stmt = $conn->prepare("INSERT INTO $table (".implode(",", $available_fields).") VALUES (".implode(",", $param_fields).")");
		foreach($available_fields as $key) {
			$stmt->bindParam(":".$key, $_POST[$key]);
		}
		if($stmt->execute()) {
			return true;
		}
		return false;
	}
	
	function executeMerge($table, $form) {
		global $conn, $_POST;
		$table_fields = getColumns($table);
		
		$available_fields = array();
		$param_fields = array();
		
		foreach($table_fields as $column) {
			if(array_key_exists($column, $_POST)) {
				array_push($available_fields, $column);
				array_push($param_fields, ":".$column);
			}
		}
		
		$query = "INSERT INTO $table (".implode(",", $available_fields).") VALUES (".implode(",", $param_fields).")
					ON DUPLICATE KEY UPDATE ";
		
		$first = true;
		foreach($available_fields as $key) {
			if(!$first) $query = $query.",";
			if($first) $first = false;
			$query = $query.$key." = :".$key;
		}
		
		$stmt = $conn->prepare($query);
		foreach($available_fields as $key) {
			$stmt->bindParam(":".$key, $_POST[$key]);
		}
		if($stmt->execute()) {
			return true;
		}
		return false;
	}
	
	function printTable($table, ...$columntitles) {
		printQuery(executeQuery("SELECT * FROM $table"), ...$columntitles);
	}
	
	function printQuery($result, ...$columntitles) {
		echo "<table>";
		
		if(sizeof($columntitles) > 0) {
			echo "<tr>";
			foreach($columntitles as $title) {
				echo "<th>$title</th>";
			}
			echo "</tr>";
		}
		
		foreach($result as $row) {
			echo "<tr>";
			
			for($i = 0; $i < sizeof($row) / 2; $i += 1) {
				echo "<td>".$row[$i]."</td>";
			}
			
			echo "</tr>";
		}
		
		echo "</table>";
	}
?>