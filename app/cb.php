<?php

include '../vars.php';

$domain = $AUTH0_DOMAIN;

$app = $_SERVER['HTTP_HOST'];
if ($app == 'master.com') {
    die('incorrect app: ' . $app);
}

if ($app == 'app1.com') {
    $client_id = $APP1_CLIENT_ID;
    $client_secret = $APP1_CLIENT_SECRET;
    $redirect_uri = $APP1_REDIRECT_URI;
} else {
    $client_id = $APP2_CLIENT_ID;
    $client_secret = $APP2_CLIENT_SECRET;
    $redirect_uri = $APP2_REDIRECT_URI;
}

$authorization_code = $_GET['code']; // $_POST['code'] if /authorize?response_mode=form_post

if (!$authorization_code) {
    die('no authorization_code!');
}

$url = 'https://' . $AUTH0_DOMAIN . '/oauth/token';


$data = array(
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
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

session_start();

$_SESSION['name'] = $name;
$_SESSION['picture'] = $picture;

header('Location: index.php');

exit();

