<?php
// diag_update.php — quick manual test to update one cell
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cfg = require __DIR__ . '/config.php';
require_once __DIR__ . '/lib/GoogleSheets.php';

$gs = new GoogleSheets($cfg['google']);
$sheetId = $cfg['google']['student_spreadsheet_id'];
$sheetName = $cfg['google']['student_sheet_name'];

$row = isset($_GET['row']) ? intval($_GET['row']) : 2;
$col = isset($_GET['col']) ? intval($_GET['col']) : 7;
$val = isset($_GET['val']) ? $_GET['val'] : 'TEST';

header('Content-Type: text/plain; charset=utf-8');
echo "Updating {$sheetName}! row={$row}, col={$col} to '{$val}'\n";
try {
  $out = $gs->updateCell($sheetId, $sheetName, $row, $col, $val);
  echo "OK\nResponse:\n$out\n";
} catch (Exception $e) {
  echo "ERROR: " . $e->getMessage() . "\n";
}