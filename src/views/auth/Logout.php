<?php
require_once __DIR__."/../../_init_.php";

session_start();
session_unset();    // Unset all session variables
session_destroy();  // Destroy the session

// Redirect to home or login page
header("Location: " . APP_URL);
exit();
