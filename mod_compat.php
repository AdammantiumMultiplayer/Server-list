<!DOCTYPE html>
<html>
<?php
require("incl/database.php");

if(!empty($_POST["name"])) {
	session_start();
	include("incl/ratelimiter.php");

	// in this sample, we are using the originating IP, but you can modify to use API keys, or tokens or what-have-you.
	$rateLimiter = new RateLimiter($_SERVER["REMOTE_ADDR"]);

	$limit = 5; // number of connections to limit user to per $minutes
	$minutes = 3; // number of $minutes to check for.
	$seconds = floor($minutes * 60);	//	retry after $minutes in seconds.

	try {
		$rateLimiter->limitRequestsInMinutes($limit, $minutes);
	} catch (RateExceededException $e) {
		header("HTTP/1.1 429 Too Many Requests");
		header(sprintf("Retry-After: %d", $seconds));
		?>
		<head>
			
		</head>
		<body>
			<script>
				(function() {
					alert("You are submitting to fast, please slow down!\nRetry in <?php echo $minutes; ?> minutes.");
					window.location = window.location.href;
				})();
			</script>
		</body>
		</html>
		<?php
		die();
	}
	
	
	$statement = $conn->prepare("INSERT INTO mod_compat (name, url, compatibility, note) VALUES(?, ?, ?, ?);");
	$ok = $statement->execute([
			/* name          */ trim($_POST["name"]),
			/* url           */ trim($_POST["url"]),
			/* compatibility */ trim($_POST["state"]),
			/* note          */ trim($_POST["notes"])
							]);
	?>
	<head>
		
	</head>
	<body>
		<script>
			(function() {
				alert("Thank you for submitting a mod!");
				window.location = window.location.href;
			})();
		</script>
	</body>
	</html>
	<?php
	die();
}
?>

<head>
	<title>AMP Mod Compatibility</title>
	<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
	<link rel="manifest" href="/favicon/site.webmanifest">
	<link rel="stylesheet" type="text/css" href="/css/style.css?version=3">
	<meta name="description" content="Public Compatibility Chart of the AMP Mod for Blade & Sorcery VR.">
</head>
<body>
	<p class="title">AMP Mod Compatibility</p>
	<div class="subtitle">
		<div>
			Breaks Multiplayer: <div style='color: red; text-align: center; font-size: 30px; display: inline-block;' title='Breaks Multiplayer'>⚠</div>
		</div>
		<div>
			Not Syncing: <div style='color: red; text-align: center; display: inline-block;' title='Not Syncing'><?php echo stars(3, 1); ?></div>
		</div>
		<div>
			Works partially: <div style='color: orange; text-align: center; display: inline-block;' title='Works partially'><?php echo stars(3, 2); ?></div>
		</div>
		<div>
			Works: <div style='color: green; text-align: center; display: inline-block;' title='Works'><?php echo stars(3, 3); ?></div>
		</div>
	</div>
	<table class="serverlist" id="modlist" style="width: 100%;">
		<thead>
			<tr>
				<th>Mod Name</th>
				<th>Status</th>
				<th>AMP Version</th>
				<th>Note</th>
			</tr>
			<tr>
				<th><input type="text" class="search" id="search_name" /></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			function stars($count, $filled) {
				$filledstr = "<p class='stars filled'>".str_repeat("★", $filled)."</p>";
				$unfilledstr =  "<p class='stars unfilled'>"."".str_repeat("★", ($count - $filled))."</p>";
				
				return $filledstr.$unfilledstr;
			}
			
			function parseStatus($status) {
				switch($status){
					case -2:
						return "<div class='badge gray'>Untested</div>";
					case -1:
						return "<div style='color: red; text-align: center; font-size: 30px;' title='Breaks Multiplayer'>⚠</div>";
					case 0:
						return "<div style='color: red; text-align: center;' title='Not Syncing'>".stars(3, 1)."</div>";
					case 1:
						return "<div style='color: orange; text-align: center;' title='Works partially'>".stars(3, 2)."</div>";
					case 2:
						return "<div style='color: green; text-align: center;' title='Works'>".stars(3, 3)."</div>";
					default:
						return "";
				}
			}
			
			function parseAmpVersion($version) {
				if(empty($version)) return "-";
				return $version;
			}
			
			$result = executeQuery("select * from mod_compat where active = true order by name asc");
			foreach($result as $row) {
				echo "<tr>";
				echo "<td><a href='".$row["url"]."' target='_blank'>".$row["name"]."</a></td>";
				echo "<td>".parseStatus($row["compatibility"])."</td>";
				echo "<td>".parseAmpVersion($row["amp_version"])."</td>";
				echo "<td>".parseAmpVersion($row["note"])."</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	
	<div class="modstatus">
		<p class="message">Mod missing? Submit one you tested.</p>
		<button onclick='openSubmit()' class='btn green' style='display: block;'>Submit</button>
	</div>
	
	<form class="submitpage" style="display: none;" method="POST" action="">
		<h3>Name*</h3>
		<input name="name" type="text" required>
		<h3>Link*</h3>
		<input name="url" type="text" required>
		<h3>State*</h3>
		<select name="state" required>
			<option value="-1">Causes Issues</option>
			<option value="0">Doesn't work</option>
			<option value="1">Works partially</option>
			<option value="2">Works</option>
		</select>
		<h3>Notes</h3>
		<textarea name="notes"></textarea>
		<div style="height: 30px"></div>
		<button onclick='abortSubmit()' class='btn red' style='display: inline-block; float: left;'>Abort</button>
		<input type="submit" class='btn green' style='display: inline-block; float: right;' value='Submit'></input>
	</form>
	
	<script src="/js/jquery-3.7.1.min.js"></script>
	<script>
		$("#search_name").keyup(search);

		function search() {
			filter_name = $("#search_name").val().toLowerCase();
			
			$("#modlist tr").filter(function() {
				var columns = $(this).find("td");
				if(columns.length == 0) return;
				
				$(this).toggle( (filter_name.length == 0) || (filter_name.length > 0 && columns[0].innerText.toLowerCase().indexOf(filter_name) > -1) );
			});
		}
		
		function openSubmit() {
			$(".submitpage").show();
		}
		function abortSubmit() {
			$(".submitpage").hide();
		}
	</script>
</body>
</html>