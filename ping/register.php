<?php
require("../incl/database.php");

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bekomme den JSON Body der Anfrage
$data = json_decode(file_get_contents('php://input'), true);

function base64_to_jpeg($base64_string, $output_file) {
    // open the output file for writing
    $ifp = fopen( $output_file, 'wb' ); 

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    fclose( $ifp ); 

    return $output_file; 
}

if(isset($data['port'])) {
	$icon = "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";
	
	if(isset($data['icon']) && strlen($data['icon']) > 0) {
		print_r(base64_to_jpeg($data['icon']));
	}
	
	$statement = $conn->prepare("CALL REGISTER_SERVER(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
	$ok = $statement->execute([
			/* ADDRESS     */  trim($_SERVER['REMOTE_ADDR']),
			/* PORT        */  trim(intval($data['port'] ?? 0)),
			/* SERVERNAME  */  trim($data['name'] ?? 'No name'),
			/* DESCRIPTION */  trim($data['description'] ?? 'No description'),
			/* SERVERICON  */  trim($icon),
			/* PLAYERS_MAX */  trim(intval($data['max_players'] ?? '10')),
			/* MAP         */  trim($data['map'] ?? '?'),
			/* MODE        */  trim($data['mode'] ?? '?'),
			/* VERSION     */  trim($data['version'] ?? '?'),
			/* PVP         */  trim(intval($data['pvp_enabled'] ?? '1')),
			/* STATIC_MAP  */  trim(intval($data['static_map'] ?? '1')),
							]);
	echo json_encode(["ok" => $ok]);
}else{
	echo json_encode(["ok" => false]);
}
?>