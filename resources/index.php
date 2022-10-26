<?php

/**
 *
 */

if (!__NAMESPACE__) throw new Exception("Define an PSR-4 namespace");

require_once __DIR__ . "/../../config/config.php";

defined("ROOT") OR exit;

session_start();

error_reporting(!DEBUG ? 0 : DEBUG_LEVEL);

require_once __DIR__ . "/../../vendor/autoload.php";

new \Marado\Framework\MVC(__NAMESPACE__);

