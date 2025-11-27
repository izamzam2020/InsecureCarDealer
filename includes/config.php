<?php
// Deliberately basic and unsafe config for training purposes
// NOTE: Do NOT use this in production.
 
$s = session_status();
if ($s === PHP_SESSION_NONE) { @session_start(); }

$DB_HOST = '';
$DB_USER = '';
$DB_PASS = '';
$DB_NAME = '';
 
$conn = @mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
$DB_CONNECTED = (bool)$conn;
$DB_ERROR = $DB_CONNECTED ? '' : mysqli_connect_error();