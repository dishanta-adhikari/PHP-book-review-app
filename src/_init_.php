<?php
require_once __DIR__ . '/config/Session.php';        // start the session

require_once __DIR__ . '/../vendor/autoload.php';    // composer autoload

require_once __DIR__ . '/config/env.php';            // load .env

require_once __DIR__ . '/config/constants.php';      // defined constants - APP_URL,AUTH_URL

require_once __DIR__ . '/config/Database.php';       // returns db $conn