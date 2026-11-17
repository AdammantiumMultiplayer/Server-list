<?php
$id = $_GET['id'] ?? '';
$error = '';

if (isset($_GET['id'])) {
	if (!is_string($id) || !preg_match('/\A[0-9]{6}\z/', $id)) {
		$error = 'Enter a valid six-digit log number.';
	} elseif (!is_file(__DIR__ . '/logs/' . $id . '.txt')) {
		$error = 'No log was found for that number.';
	} else {
		header('Location: logs/' . $id . '.txt');
		exit;
	}
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Check player log</title>
</head>
<body>
	<form method="get">
		<label for="id">Log number</label>
		<input id="id" name="id" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required>
		<button type="submit">Open log</button>
	</form>
	<?php if ($error !== ''): ?>
		<p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
	<?php endif; ?>
</body>
</html>
