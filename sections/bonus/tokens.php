<?php

enforce_login();
authorize();

if (!preg_match('/^(token|other)-[1-4]$/', $Label, $match)) {
    error(403);
}

if ($match[1] === 'token') {
    try {
        $Bonus->purchaseToken($LoggedUser['ID'], $Label);
    }
    catch (\Exception $e) {
        $message = $e->getMessage();
        error("Purchase not concluded ($message).");
    }
}
else {
    if (empty($_GET['user'])) {
        error('You have to enter a username to give tokens to.');
    }
    $ID = Users::ID_from_username(urldecode($_GET['user']));
    if (is_null($ID)) {
        error('Nobody with that name found at ' . SITE_NAME . '. Are you certain the spelling is right?');
    }
    elseif ($ID == G::$LoggedUser['ID']) {
        error('You cannot give yourself tokens. (Nice try :)');
    }
    if (!$Bonus->purchaseTokenOther(G::$LoggedUser['ID'], $ID, $Label)) {
        error('Purchase for other not concluded. Either you lacked funds or they have chosen to decline FL tokens.');
    }
}

header('Location: bonus.php?complete=' . urlencode($Label));
