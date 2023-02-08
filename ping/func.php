<?php
function getAddress() {
	$address = $_SERVER['REMOTE_ADDR'];
	
	if($address == "85.214.225.28") {
		$address = "bns.devforce.de";
	} else if($address == "127.0.0.1") {
		$address = "bns.devforce.de";
	}
	
	return $address;
}
?>