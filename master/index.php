<?php
include '../vars.php';

$app = $_SERVER['HTTP_HOST'];

if ($app !== 'master.com') {
    die('incorrect app: ' . $app);
}

$authorize_url = "https://$AUTH0_DOMAIN/authorize?client_id=$CLIENT_ID&response_type=code&redirect_uri=$REDIRECT_URI&nonce=$app&scope=openid%20profile"; //&prompt=login

session_start();

if(!isset($_SESSION['name'])) {
    header('Location: ' . $authorize_url);
    exit();
}

echo "
<html>
    <head>
    <title>$app - Login</title>
    <head>
    <body>
";

$name = $_SESSION['name'];
$picture = $_SESSION['picture'];

echo "You're: ".$name;
echo "<img src=\"$picture\" alt=\"picture\" height=42 width=42/>";
echo "<a href=\"logout.php\">logout</a>";

echo "
    </body>
</html>
";
