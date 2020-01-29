<?php
include '../vars.php';

$app = $_SERVER['HTTP_HOST'];

if ($app == 'master.com') {
    die('incorrect app: ' . $app);
}

if ($app == 'app1.com') {
    $client_id = $APP1_CLIENT_ID;
    $redirect_uri = $APP1_REDIRECT_URI;
} else {
    $client_id = $APP2_CLIENT_ID;
    $redirect_uri = $APP2_REDIRECT_URI;
}

session_start();

if(isset($_SESSION['sub']) && !isset($_SESSION['name'])) { // light session, get full from AZ
    $authorize_url = "https://$AUTH0_DOMAIN/authorize?client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&nonce=$app&scope=openid%20profile";
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

if(!isset($_SESSION['name'])) {
    echo "You're anonymous";
} else {
    $name = $_SESSION['name'];
    $picture = $_SESSION['picture'];
    echo "You're: ".$name;
    echo "<img src=\"$picture\" alt=\"picture\" height=42 width=42/>";
    echo "<a href=\"logout.php\">logout</a>";
}

echo "
    </body>
</html>
";
