<?php
require_once __DIR__ . "/../../_init_.php";

session_start();                   //start the session

session_unset();                   // Unset all session variables

session_destroy();                 // Destroy the session

header("Location: " . APP_URL);    // Redirect to home or login page
exit;
