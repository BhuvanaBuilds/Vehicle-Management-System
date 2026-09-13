<?php
session_start();

/* Destroy the login session */
session_unset();
session_destroy();

/* Go back to login page */
header("Location: login.php");
exit();
?>