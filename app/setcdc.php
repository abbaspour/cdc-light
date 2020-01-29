<?php
include '../vars.php';

$encryption = $_GET['jwe'];
$ts = $_GET['ts'];
$nonce = $_GET['nonce'];

if (!$encryption || !$ts || !$nonce) {
    die('params missing');
}

$now = time();
$delta = $now - $ts;

if($delta > $LEEWAY) {
    die('ts expired by margin of: ' . $delta);
}

session_start();

if(!isset($_SESSION['used_nonce'])) {
    $_SESSION['used_nonce'] = [];
}

if(in_array($nonce, $_SESSION['used_nonce'])) {
    die('nonce used');
}

array_push($_SESSION['used_nonce'], $nonce);

$option = 0;
$json_string=openssl_decrypt (base64_decode(urldecode($encryption)), $ciphering,
		$encryption_key, $options, $encryption_iv);

$user = json_decode($json_string, true);

if($user['nonce'] != $nonce) {
    die('nonce mismatch');
}

if($user['nonce'] != $nonce) {
    die('nonce mismatch');
}

$jwe_delta = $now - $user['iat'];
if($jwe_delta > $LEEWAY) {
    die('jwt.iat expired by margin of: ' . $delta);
}

echo "looks good. setting session...";

$_SESSION['sub'] = $user['sub'];

exit();

