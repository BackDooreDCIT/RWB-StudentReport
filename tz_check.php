<?php
// tz_check.php — quick sanity test to confirm timezone
require __DIR__ . '/tz_bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');
echo "PHP default timezone: " . date_default_timezone_get() . "\n";
echo "Local time now: " . date('Y-m-d H:i:s') . "\n";