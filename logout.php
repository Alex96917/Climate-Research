<?php
session_start();

$_SESSION = [];                       // clear all session data

if (ini_get("session.use_cookies")) { // remove the session cookie
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
}

session_destroy();                    // end the session on the server

header("Location: login.php?loggedout=1");
exit;