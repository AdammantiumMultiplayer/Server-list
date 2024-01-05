<html>
<?php
require("incl/database.php")
?>

<head>
	<script src="/js/jquery-3.7.1.min.js"></script>
	<title>Adammantium Multiplayer Serverlist</title>
	<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
	<link rel="manifest" href="/favicon/site.webmanifest">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
	<p class="title">Adammantium Multiplayer</p>
	<p class="title">Serverlist</p>

	<div class="modstatus">
		<p class="message"></p>
		<div class="session-details" style="display: none;">
			<div style="float: left; vertical-align: middle; padding-right: 5px; height: 64px;">
				<span></span>
			</div>
			<div style="float: right;">
				<object class='map-preview' data='' type='image/png'>
					<img src='/img/AMP.jpg' alt=''>
				</object>
			</div>
		</div>
		<button onclick='connectToMod()' class='btn red'>Retry</button>
	</div>

	<table class="serverlist" id="serverlist">
		<thead>
			<tr>
				<th style="width: 64px;"></th>
				<th>Server name</th>
				<th>Info</th>
				<th>Map</th>
				<th>Players</th>
				<th>Address</th>
				<th style="width: 90px;">actions</th>
			</tr>
			<tr>
				<th></th>
				<th><input type="text" class="search" id="search_name" /></th>
				<th></th>
				<th><input type="text" class="search" id="search_map" /></th>
				<th></th>
				<th><input type="text" class="search" id="search_ip" /></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			function decode_boolean($str) {
				if($str) {
					return "<span style='color: green; font-weight: bold;'>&#9745;</span>";
				}else{
					return "<span style='color: red; font-weight: bold;'>&#9746;</span>";
				}
			}
			
			$result = executeQuery("select * from serverlist where last_update > NOW() - INTERVAL 5 MINUTE order by official desc, servername;");
			foreach($result as $row) {
				echo "<tr class='server'>";
				
				echo "<td><img src='data:image/png;base64,".htmlspecialchars_decode($row["servericon"])."'></img></td>";
				
				$servername = "<span>".htmlspecialchars_decode($row["servername"])."</span>";
				if($row["official"]) {
					$servername .= "<span title='This is a offical server' class='tick'>✓</span>";
				}
				$servername .= "<span class='description'>".htmlspecialchars_decode($row["description"])."</span>";
				
				echo "<td>".$servername."</td>";
				
				echo "<td>
						".htmlspecialchars_decode($row["version"])."<br>
						PvP: ".decode_boolean($row["pvp"])."<br>
						Map changable: ".decode_boolean($row["static_map"])."<br>
					 </td>";
				
				echo "<td>
						<div class='map-image'>
							<object class='map-preview' data='/img/maps/".strtolower($row["map"]).".jpg' type='image/jpeg'>
								<img src='/img/AMP.jpg' alt='{$row["map"]}'>
							</object>
							<object class='gamemode-preview' data='/img/mode/".strtolower($row["modus"]).".png' type='image/png'>
								<img src='/img/AMP.jpg' alt='{$row["modus"]}'>
							</object>
						</div>
						
						{$row["modus"]} @ {$row["map"]}
						</td>";
				
				echo "<td>{$row["players_connected"]} / {$row["players_max"]}</td>";
				//echo "<td>? / {$row["players_max"]}</td>";
				echo "<td>".htmlspecialchars_decode($row["address"]).":{$row["port"]}</td>";
				echo "<td>
						<details style='display: none;'>
							<description>".htmlspecialchars_decode($row["description"])."</description>
						</details>
						<!--<button onclick='infoRow(this)' class='btn blue'>Info</button>--!>
						<button onclick='joinRow(this)' class='btn green'>Join</button>
					  </td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	
	<p>Big thanks to <b>flex hd</b>!</p>
	
	<div class="panel" id="details" style="display: none;">
		<div class="close" onclick="closeDetails();">&#9746;</div>
		
		<center><h1>Join server</h1></center>
		
		<table class="data">
			<tr>
				<td>Servername:</td>
				<td id="join_name"></td>
			</tr>
			<tr>
				<td>Address:</td>
				<td id="join_address"></td>
			</tr>
			<tr>
				<td>Port:</td>
				<td id="join_port"></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td id="join_password">
					<input type="password" style="font-size: 20px;" autocomplete="nope" />
				</td>
			</tr>
			<tr id="map_info">
				<td colspan=2 style="text-align: center;">
					<object class='map-preview-lg' data='' type='image/png'>
						<img src='/img/AMP.jpg' alt=''>
					</object>
					<br>
					<span></span>
				</td>
			</tr>
			<tr>
				<td colspan=2 style="text-align: center;">
					<button id="join-invite" class='btn green'>Join Server</button>
				</td>
			</tr>
		</table>
	</div>

	<script src="js/base.js"></script>
	<script src="js/mod-communication.js"></script>
</body>
</html>