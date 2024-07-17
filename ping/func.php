<?php
function getAddress() {
	$address = $_SERVER['REMOTE_ADDR'];
	
	if($address == "85.214.225.28") {
		$address = "de-amp.adamite.de";
	} else if($address == "127.0.0.1") {
		$address = "de-amp.adamite.de";
	} else if($address == "193.203.238.183") {
		$address = "de-amp.adamite.de";
	} else if($address == "191.101.206.36") {
		$address = "us-amp.adamite.de";
	} else if($address == "85.215.226.233") {
		$address = "nexusrealms.de";
	} else if($address == "192.168.178.100") {
		$address = "dev.devforce.de";
	} else {
		// Just show the IP
		//$address = gethostbyaddr($address);
	}

	return $address;
}
?>