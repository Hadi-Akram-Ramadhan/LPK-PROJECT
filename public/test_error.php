<?php
$logPath = __DIR__.'/../storage/logs/laravel.log';
if (!file_exists($logPath)) {
    die("Log file not found");
}

$lines = file($logPath);
$lastLines = array_slice($lines, -150);
echo implode("", $lastLines);
