<?php
// tz_bootstrap.php — set PHP timezone to Asia/Bangkok for consistent logging
// Include this at the very top of index.php (after session_start is fine).
date_default_timezone_set('Asia/Bangkok');

// Optional: if your host supports it, also set TZ env (usually not required)
// putenv('TZ=Asia/Bangkok');