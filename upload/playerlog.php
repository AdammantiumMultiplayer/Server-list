<?php
session_start();
require_once __DIR__ . '/../incl/ratelimiter.php';

header('Content-Type: text/plain; charset=utf-8');

function uploadError($status, $message) {
	http_response_code($status);
	echo $message;
	exit;
}

$rateLimiter = new RateLimiter($_SERVER['REMOTE_ADDR'] ?? '');
try {
	$rateLimiter->limitRequestsInMinutes(5, 3);
} catch (RateExceededException $exception) {
	header('Retry-After: 180');
	uploadError(429, 'Too many uploads. Please wait 3 minutes before trying again.');
}

$username = $_GET['username'] ?? null;
if (!is_string($username) || trim($username) === '') {
	uploadError(400, 'Missing or invalid username.');
}
if (strlen($username) > 100) {
	uploadError(400, 'Username must be 100 characters or fewer.');
}

$file = $_FILES['file'] ?? null;
if (!is_array($file) && count($_FILES) === 1) {
	$file = reset($_FILES);
}
if (!is_array($file)) {
	uploadError(400, 'No uploaded file received. Select a file in a multipart/form-data body.');
}
if (
	!isset($file['name'], $file['error'], $file['tmp_name'], $file['size']) ||
	!is_string($file['name']) ||
	!is_int($file['error']) ||
	!is_string($file['tmp_name']) ||
	!is_int($file['size'])
) {
	uploadError(400, 'Invalid uploaded file data.');
}

$uploadErrors = [
	UPLOAD_ERR_INI_SIZE => 'File exceeds the server upload size limit.',
	UPLOAD_ERR_FORM_SIZE => 'File exceeds the upload size limit.',
	UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
	UPLOAD_ERR_NO_FILE => 'No file was selected.',
	UPLOAD_ERR_NO_TMP_DIR => 'Server upload directory is missing.',
	UPLOAD_ERR_CANT_WRITE => 'Server could not write the uploaded file.',
	UPLOAD_ERR_EXTENSION => 'A server extension stopped the upload.'
];
if ($file['error'] !== UPLOAD_ERR_OK) {
	uploadError(400, $uploadErrors[$file['error']] ?? 'File upload failed.');
}

$filename = basename(str_replace('\\', '/', $file['name']));
if (!in_array($filename, ['Player.log', 'Player-prev.log'], true)) {
	uploadError(400, 'File must be named Player.log or Player-prev.log.');
}
if (!is_uploaded_file($file['tmp_name'])) {
	uploadError(400, 'Uploaded file could not be verified.');
}
$actualSize = filesize($file['tmp_name']);
if ($actualSize === false) {
	uploadError(500, 'Server could not read the uploaded file size.');
}
if ($file['size'] > 10 * 1024 * 1024 || $actualSize > 10 * 1024 * 1024) {
	uploadError(400, 'File must be 10 MB or smaller.');
}

$storagePath = __DIR__ . '/logs';
if (!is_dir($storagePath) && !mkdir($storagePath, 0750, true) && !is_dir($storagePath)) {
	uploadError(500, 'Server could not create the log storage directory.');
}

$recordPath = false;
$id = null;
for ($attempt = 0; $attempt < 20; $attempt++) {
	$candidate = (string) random_int(100000, 999999);
	$candidatePath = $storagePath . '/' . $candidate;
	if (@mkdir($candidatePath, 0750)) {
		$id = $candidate;
		$recordPath = $candidatePath;
		break;
	}
}

if ($recordPath === false) {
	uploadError(500, 'Server could not allocate a unique log number. Please try again.');
}

$savedLog = $recordPath . '.txt';
$logContents = file_get_contents($file['tmp_name']);
if ($logContents === false) {
	rmdir($recordPath);
	uploadError(500, 'Server could not read the uploaded log.');
}
if (file_put_contents($savedLog, trim($username) . PHP_EOL . $logContents, LOCK_EX) === false) {
	if (is_file($savedLog)) {
		unlink($savedLog);
	}
	rmdir($recordPath);
	uploadError(500, 'Server could not save the log.');
}

if (!rmdir($recordPath)) {
	unlink($savedLog);
	uploadError(500, 'Server could not finish saving the log.');
}

echo $id;
?>
