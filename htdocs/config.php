<?php
// === EDIT THESE FOR YOUR DEPLOYMENT ===
// 1) Upload your Service Account JSON file to the project root (htdocs) and set its filename here.
// 2) Share BOTH Google Sheets with the service account's email (from the JSON), with Edit access.
// 3) Put the spreadsheet IDs and sheet names below.

return [
  'debug' => true,
  'session_secret' => 'change-me-in-production',
  'google' => [
    'service_account_json' => __DIR__ . '/rwb-sb-account-db-da8565c59ec7.json', // rename your JSON to this or update the path
    'scopes' => [
      'https://www.googleapis.com/auth/spreadsheets',
      'https://www.googleapis.com/auth/drive.readonly'
    ],
    'token_uri' => 'https://oauth2.googleapis.com/token',
    // Replace with your spreadsheet IDs (the long string in the sheet URL) and sheet tab names
    'account_spreadsheet_id' => '1uXSUwZOf1eBi3SohCvkzpJdTsSRsPC8ctxrtrvCWKd4',
    'account_sheet_name'     => 'Sheet1',
    'student_spreadsheet_id' => '10pLpTqzBGI7PNxiF4_V8avHQxG6bcwffid8spRyKiYs',
    'student_sheet_name'     => 'Sheet1',
    'log_spreadsheet_id' => '10pLpTqzBGI7PNxiF4_V8avHQxG6bcwffid8spRyKiYs',
    'log_sheet_name'     => 'Log', 
  ],
  // Expected headers in the student sheet (row 1). Must match exactly, lowercase recommended.
  'expected_headers' => ['no.', 'id', 'prefix', 'surname', 'lastname', 'grade', 'class', 'score'],
  // Reason codes → [description, points]
  'deduction_reasons' => [
    '101-123' => ['', 5],
    '201-211' => ['', 10],
    '301-309' => ['', 20],
    '401-412' => ['', 30],
    '501-509' => ['ประเภท ปค.2', 30],
    '510-512' => ['ประเภท ปค.2 (สิ่งเสพติด)', 30],
    'อื่นๆ'    => ['', 0],
  ],
];