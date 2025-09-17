<?php
// diag_lookup.php — find a student row by ID and show detected headers/row/score-col
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cfg = require __DIR__ . '/config.php';
require_once __DIR__ . '/lib/GoogleSheets.php';

function detect_score_col($headers) {
  $map = array_flip($headers); // zero-based
  if (isset($map['score'])) return $map['score'] + 1;
  $needles = ['score', 'คะแนน', 'behaviour', 'behavior'];
  foreach ($headers as $i => $h) {
    foreach ($needles as $n) {
      if (mb_stripos($h, $n) !== false) return $i + 1;
    }
  }
  return count($headers);
}

$gs = new GoogleSheets($cfg['google']);
$id = $_GET['id'] ?? '';
if ($id === '') { echo "Usage: diag_lookup.php?id=STUDENT_ID\n"; exit; }

$data = $gs->getAllRecords($cfg['google']['student_spreadsheet_id'], $cfg['google']['student_sheet_name'], []);
$headers = $data['headers'];
echo "Headers (lowercased):\n"; print_r($headers);

$score_col = detect_score_col($headers);
echo "Detected score_col (1-based): $score_col\n";

$found = false;
foreach ($data['rows'] as $i=>$r) {
  $sid = $r['id'] ?? ($r['student_id'] ?? '');
  if (strval($sid) === strval($id)) {
    $row_index = $i + 2; // +1 for array idx +1 for header
    echo "Found ID {$id} at row {$row_index}\n";
    echo "Row values:\n"; print_r($r);
    $score = $r['score'] ?? ($r['คะแนน'] ?? ($r['คะแนนพฤติกรรม'] ?? ''));
    echo "Current score seen by app: " . var_export($score, true) . "\n";
    $found = true;
    break;
  }
}
if (!$found) echo "Not found.\n";