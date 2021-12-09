<?php
session_start();

//Delete session array
$_SESSION = array();

//Get session parameter
$param = session_get_cookie_params();

//Delete cookie
setcookie(session_name(),'',time() - 42000,
    $param['path'],
    $param['domain'],
    $param['secure'],
    $param['httponly']);

//Destroy session
session_destroy();

header("Refresh:0,login.php")
?>