<?php

require_once("functions.php");

if (session_id() == "")
{
  ini_set("session.cookie_httponly", True);
  session_start();
}
setcookie("PHPSESSID", "", time() - 6400);
unset($_SESSION['username']);
unset($_SESSION['user_id']);
unset($_SESSION['privileges']);
unset($_SESSION['access']);
session_destroy();
przekierowanie("./index.php");
?>
