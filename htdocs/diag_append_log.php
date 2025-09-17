<?php
// diag_append_log.php — append a test row to the log sheet and print the API response
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cfg = require __DIR__ . '/config.php';
require_once __DIR__ . '/lib/GoogleSheets.php';

$gs = new GoogleSheets($cfg['google']);
$sheetId = $cfg['google']['log_spreadsheet_id'];
$sheetName = $cfg['google']['log_sheet_name'];

$ts = date('Y-m-d H:i:s');
$row = [
  $ts,
  $_GET['id'] ?? 'TEST_ID',
  $_GET['name'] ?? 'Test Name',
  $_GET['class'] ?? 'ม.0/0',
  $_GET['from'] ?? '10',
  $_GET['change'] ?? '-1',
  $_GET['to'] ?? '9',
  $_GET['reason'] ?? 'diag append test',
  $_GET['teacher'] ?? 'diag'
];

header('Content-Type: text/plain; charset=utf-8');
echo "Appending to sheet '{$sheetName}'...\n";
try {
  $out = $gs->appendRow($sheetId, $sheetName, $row);
  echo "OK\nResponse:\n$out\n";
} catch (Exception $e) {
  echo "ERROR: " . $e->getMessage() . "\n";
  echo "Check that the service account has EDIT access to this spreadsheet and the sheet/tab name matches exactly.\n";
}