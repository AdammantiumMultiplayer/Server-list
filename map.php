<!DOCTYPE html>
<html>
<?php
require("incl/database.php")
?>

<head>
	<title>Adammantium Multiplayer Server Map</title>
	<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
	<link rel="manifest" href="/favicon/site.webmanifest">
	<link rel="stylesheet" type="text/css" href="/css/style.css?version=5">
	<!-- Leaflet CSS -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
	<meta name="description" content="World map of Adammantium Multiplayer servers.">
</head>
<body>
	<div class="title-bar">
		<p class="title">Adammantium Multiplayer</p>
		<nav class="title-nav" aria-label="Main">
			<a href="index.php" class="title-link">Serverlist</a>
			<a href="mod_compat.php" class="title-link">Mod Compatibility</a>
			<a href="map.php" class="title-link active">Server Map</a>
		</nav>
	</div>

	<div id="map" style="width: 100%; height: calc(100vh - 60px);"></div>

	<!-- Leaflet JS -->
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
	<script src="/js/jquery-3.7.1.min.js"></script>
	<script>
		$(document).ready(function() {
			// Initialize map
			var map = L.map('map').setView([51.1657, 10.4515], 3); // Center on Germany

			// Add tile layer
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			}).addTo(map);

			// Get server data
			<?php
			// For demonstration, add some sample servers with locations
			$sampleServers = [
				[
					'servername' => '[US] Sample Server',
					'latitude' => 37.0902,
					'longitude' => -95.7129,
					'current_lobbies' => 5,
					'max_lobbies' => 10,
					'last_update' => date('Y-m-d H:i:s', strtotime('-1 minute'))
				],
				[
					'servername' => '[Canada] Sample Server',
					'latitude' => 56.1304,
					'longitude' => -106.3468,
					'current_lobbies' => 3,
					'max_lobbies' => 8,
					'last_update' => date('Y-m-d H:i:s', strtotime('-10 minutes'))
				],
				[
					'servername' => '[Germany] Sample Server',
					'latitude' => 51.1657,
					'longitude' => 10.4515,
					'current_lobbies' => 7,
					'max_lobbies' => 12,
					'last_update' => date('Y-m-d H:i:s', strtotime('-2 minutes'))
				]
			];

			// Get real servers from database
			$result = executeQuery("select name, latitude, longitude, current_lobbies, max_lobbies from hostservers where enabled = 1 AND latitude IS NOT NULL AND longitude IS NOT NULL");

			$allServers = $result; #array_merge($sampleServers, $result);
			?>

			var servers = <?php echo json_encode($allServers); ?>;

			// Add markers
			servers.forEach(function(server) {
				var lat = parseFloat(server.latitude);
				var lng = parseFloat(server.longitude);
				var name = server.name;
				var players = server.current_lobbies + '/' + server.max_lobbies;

				var isOnline = server.max_lobbies > 0;

				var color = isOnline ? 'green' : 'red';

				var marker = L.circleMarker([lat, lng], {
					color: color,
					fillColor: color,
					fillOpacity: 0.8,
					radius: 8
				}).addTo(map);

				// Show player count on hover
				marker.bindTooltip(name + '<br>Players: ' + players, {
					permanent: false,
					direction: 'top'
				});

				// Show server name and details on click
				marker.bindPopup('<b>' + name + '</b><br>Players: ' + players);
			});
		});
	</script>
</body>
</html>
