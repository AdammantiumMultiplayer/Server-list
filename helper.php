<!DOCTYPE html>
<html>

<head>
	<title>AMP Issue Diagnose</title>
	<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
	<link rel="manifest" href="/favicon/site.webmanifest">
	<link rel="stylesheet" type="text/css" href="/css/style.css?version=4">
	<meta name="description" content="Public Serverlist of the AMP Mod for Blade & Sorcery VR.">
</head>
<body>
	<p class="title">AMP Issue Diagnose</p>
	
	<div>
		<div class="question">
			
		</div>
		<div class="answers">
			
		</div>
	</div>
	
	<script src="/js/jquery-3.7.1.min.js"></script>
	<script>
	var questions = {
		"start": {
			question: "Welcome to the AMP Mod Issue Questionnaire!<br/><br/>If you experience issues, this might help you fix it.<br/><br/>Please do this before creating a ticket.",
			answers: {
				"Let's go": "select_issue"
			}
		},
		
		"select_issue": {
			question: "What is your core issue with the mod?",
			answers: {
				"The mod isn't working": "loaded_in_level",
				"Website says 'Could not connect to mod'": "adblock",
				"Players are just standing around": "compatible_mods"
			}
		},
		
		"loaded_in_level": {
			question: "Did you load into a level? The mod won't work in the character selection, make sure you at least loaded into the Home.",
			answers: {
				"I didn't load into a level, that fixed it": "fixed",
				"I'm loaded into a level": "correct_version"
			}
		},
		
		"correct_version": {
			question: "Did you get the correct mod version for your game version?<br/><br/><img src='https://www.adamite.de/assets/img/amp/Versions.jpg' style='width: 60%;'></img>",
			answers: {
				"Yes": "book_mods",
				"No": "latest_version"
			}
		},
		
		"book_mods": {
			question: "Is the mod listed inside the book's \"Mods\" category?<br/><br/><img src='/img/helper/book_mods.png' style='max-width: 100%;'></img>",
			answers: {
				"Yes": "book_errors",
				"No": "install_dir"
			}
		},
		
		"book_errors": {
			question: "Does it show any error messages when you select the Mod inside the book?<br/><br/><img src='/img/helper/book_error.png' style='max-width: 100%;'></img>",
			answers: {
				"Yes": "install_dir",
				"No": "report"
			}
		},
		
		"install_dir": {
			question: "Please check your game's \"Mods\" folder, make sure it looks like this:<br/><br/><img src='/img/helper/mod_folder.png' style='max-width: 100%;'></img>",
			answers: {
				"It looks like this": "report",
				"That fixed it": "fixed"
			}
		},
		
		"latest_version": {
			question: "You can download all mod versions here:<br/><br/><a href='https://www.nexusmods.com/bladeandsorcery/mods/6888' target='_blank'>NexusMods</a><br/><a href='https://mod.io/g/blade-and-sorcery/m/amp' target='_blank'>Mod.io</a>",
			answers: {
				"Restart": "start"
			}
		},
		
		"compatible_mods": {
			question: "Do you have mods installed?<br/>Please check that they are compatible with the multiplayer mod.<br/>You can find a compatibility list <a href='/mod_compat.php' target='_blank'>here</a>",
			answers: {
				"I have no mods installed": "report",
				"I have no incompatible mods installed": "report",
				"That fixed it": "fixed",
			}
		},
		
		"adblock": {
			question: "Do you have an Adblock running? Try disabling it.<br>Do you have an agressive AntiVirus? I don't recommend disabling it, you can still use the Ingame Menu to join servers.",
			answers: {
				"It was the AdBlock!": "fixed",
				"I don't have an aggressive AntiVirus": "loaded_in_level",
				"That's not it": "loaded_in_level",
			}
		},
		
		"report": {
			question: "Please report the issue on discord: <a href='https://discord.com/channels/995964594508025866/1138149866971861082'>Click to report</a>",
		},
		
		"fixed": {
			question: "Nice! Have fun with the mod!",
			answers: {
				"Restart": "start"
			}
		}
	};
	
	function showQuestion(question) {
		if(question in questions) {
			$(".question").html(questions[question].question);
			$(".answers").html("");
			Object.keys(questions[question].answers).forEach(function(key, i) {
				var btn = $("<button class='btn green' style='display: inline-block; margin-right: 20px;'>" + key + "</button>");
				btn.on("click", function() {
					showQuestion(questions[question].answers[key]);
				});
				$(".answers").append(btn);
			});
		}
	}
	showQuestion("start");
	</script>
</body>
</html>