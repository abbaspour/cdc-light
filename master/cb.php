<?php

include '../vars.php';

$domain = $AUTH0_DOMAIN;

$app = $_SERVER['HTTP_HOST'];
if ($app !== 'master.com') {
    die('incorrect app: ' . $app);
}

$authorization_code = $_GET['code']; // $_POST['code'] if /authorize?response_mode=form_post

if (!$authorization_code) {
    die('no authorization_code!');
}

$url = 'https://' . $AUTH0_DOMAIN . '/oauth/token';

$data = array(
    'client_id' => $CLIENT_ID,
    'client_secret' => $CLIENT_SECRET,
    'redirect_uri' => $REDIRECT_URI,
    'code' => $authorization_code,
    'grant_type' => 'authorization_code'
);

$options = array(
    'http' => array(
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    )
);

$context = stream_context_create($options);
$result_string = file_get_contents($url, false, $context);

if ($result_string === FALSE) {
    die('unable to exchange');
}

$result = json_decode($result_string, true);

$access_token = $result['access_token'];

// TODO: proper id_token validation by SDK
list($header, $jwt_b64, $signature) = explode(".", $result['id_token']);
$jwt_payload = base64_decode($jwt_b64);
$id_token = json_decode($jwt_payload, true);

$picture = $id_token['picture'];
$name = $id_token['name'];
$sub = $id_token['sub'];

session_start();

$_SESSION['name'] = $name;
$_SESSION['picture'] = $picture;

$nonce = rand();

$user_array = array('sub' => $sub, /* 'picture' => $picture, */ 'nonce' => $nonce, /* 'iss' => 'http://master.com', */ 'iat' => time());
$user_json = json_encode($user_array);

$iv_length = openssl_cipher_iv_length($ciphering);
$options = 0;

$encryption = base64_encode(openssl_encrypt($user_json, $ciphering,
			$encryption_key, $options, $encryption_iv));

$cdcArgs = "?jwe=" . urlencode($encryption) . "&nonce=". $nonce . "&ts=" . time();

echo "
<html>
    <head>
    <title>Master Callback</title>
    <body onload='window.location=\"index.php\";'>
        Please wait while distributing sessions....
        <iframe src=\"$APP1_CDC_URL$cdcArgs\" width=1 height=1 style=\"border:0px\"></iframe>
        <iframe src=\"$APP2_CDC_URL$cdcArgs\" width=1 height=1 style=\"border:0px\"></iframe>
    <body>
</html>
";

exit();

