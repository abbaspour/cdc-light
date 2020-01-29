<?php

include '../vars.php';
session_start();
session_destroy();

$auth0_logout = 'https://' . $AUTH0_DOMAIN . '/v2/logout?client_id=' . $CLIENT_ID . "&returnTo=" . urlencode($MASTER_BASE_URL);

echo "
<html>
    <head>
    <title>Master Callback</title>
    <body onload='window.location=\"$auth0_logout\";'>
        Please wait while logging out from all...
        <img src=\"$APP1_SLO_URL\" width=1 height=1 style=\"border:0px\"/>
        <img src=\"$APP2_SLO_URL\" width=1 height=1 style=\"border:0px\"/>
    <body>
</html>
";

exit();
