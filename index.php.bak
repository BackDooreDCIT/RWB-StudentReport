<?php
// Index.php without Reasons Sheet: uses only config.php['deduction_reasons']
session_start();

// (Optional) enable while debugging
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once __DIR__ . '/tz_bootstrap.php';
$cfg = require __DIR__ . '/config.php';
require_once __DIR__ . '/lib/Response.php';
require_once __DIR__ . '/lib/Flash.php';
require_once __DIR__ . '/lib/GoogleSheets.php';

if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(16));

$gs = new GoogleSheets($cfg['google']);

// Helpers
function current_user() { return $_SESSION['user'] ?? null; }
function require_login() { if (!current_user()) { Flash::add("โปรดเข้าสู่ระบบ", "error"); Response::redirect('/?route=login'); } }
function is_post() { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
function param($k, $d='') { return isset($_POST[$k]) ? trim($_POST[$k]) : (isset($_GET[$k]) ? trim($_GET[$k]) : $d); }
function detect_score_col($headers) {
  $map = array_flip($headers);
  if (isset($map['score'])) return $map['score'] + 1;
  foreach ($headers as $i=>$h) {
    if (mb_stripos($h,'score')!==false || mb_stripos($h,'คะแนน')!==false) return $i+1;
  }
  return count($headers);
}
function parse_date($s) {
  if (!$s) return null;
  $ts = strtotime($s.' 00:00:00');
  return $ts ? date('Y-m-d', $ts) : null;
}

$route = $_GET['route'] ?? 'home';

// Routes
if ($route === 'home') {
  if (current_user()) Response::redirect('/?route=dashboard');
  Response::render('index', ['title'=>'Home', 'route'=>$route]);
  exit;
}

if ($route === 'login') {
  if (current_user()) Response::redirect('/?route=dashboard');
  if (is_post()) {
    $username = param('username');
    $password = param('password');
    try {
      $acc = $gs->getAllRecords($cfg['google']['account_spreadsheet_id'], $cfg['google']['account_sheet_name'], []);
      $user = null;
      foreach ($acc['rows'] as $r) {
        if (strcasecmp($r['username'] ?? '', $username) === 0) { $user = $r; break; }
      }
      if ($user) {
        $hash = $user['password_hash'] ?? '';
        if ($hash && password_verify($password, $hash)) {
          $_SESSION['user'] = $username;
          Response::redirect('/?route=dashboard');
        }
      }
      Flash::add("Incorrect Username or Password", "error");
    } catch (Exception $e) {
      Flash::add("Auth error: " . $e->getMessage(), "error");
    }
  }
  Response::render('login', ['title'=>'Login', 'route'=>$route]);
  exit;
}

if ($route === 'dashboard') {
  require_login();
  Response::render('dashboard', ['title'=>'Dashboard', 'user'=>current_user(), 'route'=>$route]);
  exit;
}

if ($route === 'logout') {
  $_SESSION = [];
  session_destroy();
  Response::redirect('/?route=login');
  exit;
}

// Teacher log with pagination + date filter
if ($route === 'log') {
  require_login();

  $q     = param('studentID','');
  $class = param('class','');              // keep ONE source of truth
  $start = parse_date(param('start',''));  // YYYY-MM-DD
  $end   = parse_date(param('end',''));    // YYYY-MM-DD
  $page  = max(1, intval(param('page', 1)));
  $per   = max(10, min(200, intval(param('per', 50))));

  $all = [];
  try {
    $ldata = $gs->getAllRecords($cfg['google']['log_spreadsheet_id'], $cfg['google']['log_sheet_name'], []);
    foreach ($ldata['rows'] as $r) {
      // student ID filter (exact, as you had)
      if ($q && strval($r['id'] ?? '') !== strval($q)) continue;

      // date range filter (as you had)
      $ts = trim($r['timestamp'] ?? ($r['time'] ?? ''));
      $day = $ts ? substr($ts,0,10) : '';
      if ($start && $day && $day < $start) continue;
      if ($end   && $day && $day > $end)   continue;

      $all[] = $r;
    }
  } catch (Exception $e) {
    Flash::add("โหลดบันทึกไม่สำเร็จ: " . $e->getMessage(), "error");
  }

  /* ------- NEW: CLASS FILTER goes here, on $all ------- */
  if (!empty($class) && is_array($all) && !empty($all)) {
    // normalize teacher input (allow "ม.5/4", "5/4", "5", or "4")
    $needle = preg_replace('/\s+/', '', trim($class));
    $needle = preg_replace('/^ม\./u', '', $needle); // strip "ม."
    $needleLC = mb_strtolower($needle, 'UTF-8');

    $all = array_values(array_filter($all, function ($row) use ($needleLC) {
      // possible sources in your log sheet
      $grade = (string)($row['grade'] ?? $row['Grade'] ?? $row['ชั้น'] ?? '');
      $room  = (string)($row['class'] ?? $row['Class'] ?? $row['room'] ?? $row['ห้อง'] ?? '');
      $combined = (string)($row['ชั้น/ห้อง'] ?? $row['grade_class'] ?? $row['class_str'] ?? $row['class'] ?? '');

      // build "G/R" if both present
      $gc = ($grade !== '' && $room !== '') ? ($grade . '/' . $room) : '';

      // normalize combined like "ม.5/4" -> "5/4" (and coerce "5-4" or "5 ห้อง 4" to "5/4")
      $combinedNorm = preg_replace('/\s+/', '', $combined);
      $combinedNorm = preg_replace('/^ม\./u', '', $combinedNorm);
      if ($combinedNorm !== '' && strpos($combinedNorm, '/') === false) {
        if (preg_match_all('/\d+/', $combinedNorm, $m) && count($m[0]) >= 2) {
          $combinedNorm = $m[0][0] . '/' . $m[0][1];
        }
      }

      // candidates to match against
      $cand = [
        mb_strtolower($gc, 'UTF-8'),
        mb_strtolower($combinedNorm, 'UTF-8'),
        mb_strtolower((string)$grade, 'UTF-8'),
        mb_strtolower((string)$room,  'UTF-8'),
      ];

      foreach ($cand as $c) {
        if ($c !== '' && strpos($c, $needleLC) !== false) return true;
      }

      // if teacher typed only digits, also allow exact grade/room matches
      if (ctype_digit($needleLC)) {
        if ($needleLC === mb_strtolower((string)$grade, 'UTF-8')) return true;
        if ($needleLC === mb_strtolower((string)$room,  'UTF-8')) return true;
      }
      return false;
    }));
  }
  /* ------- end class filter ------- */

  // sort & paginate (unchanged)
  usort($all, function($a,$b){
    $ta = $a['timestamp'] ?? ($a['time'] ?? '');
    $tb = $b['timestamp'] ?? ($b['time'] ?? '');
    return strcmp($tb, $ta);
  });

  $total  = count($all);
  $pages  = max(1, intval(ceil($total / $per)));
  if ($page > $pages) $page = $pages;
  $offset = ($page - 1) * $per;
  $slice  = array_slice($all, $offset, $per);

  Response::render('log', [
    'title' => 'บันทึกการหักคะแนน',
    'log'   => $slice,
    'q'     => $q,
    'class' => $class,      // keep the input filled and preserve in links
    'start' => $start,
    'end'   => $end,
    'page'  => $page,
    'pages' => $pages,
    'per'   => $per,
    'total' => $total,
    'csrf'  => $_SESSION['_csrf'],
    'route' => $route,
  ]);
  exit;
}


// CSV export (teachers only)
if ($route === 'log_export') {
  require_login();
  $q = param('studentID','');
  $start = parse_date(param('start',''));
  $end   = parse_date(param('end',''));
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="behavior_log.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['timestamp','id','name','class','from','change','to','reason','teacher']);

  try {
    $ldata = $gs->getAllRecords($cfg['google']['log_spreadsheet_id'], $cfg['google']['log_sheet_name'], []);
    foreach ($ldata['rows'] as $r) {
      if ($q && strval($r['id'] ?? '') !== strval($q)) continue;
      $ts = trim($r['timestamp'] ?? ($r['time'] ?? ''));
      $day = $ts ? substr($ts,0,10) : '';
      if ($start && $day && $day < $start) continue;
      if ($end && $day && $day > $end) continue;
      fputcsv($out, [
        $ts,
        $r['id'] ?? '',
        $r['name'] ?? '',
        $r['class'] ?? '',
        $r['from'] ?? '',
        $r['change'] ?? '',
        $r['to'] ?? '',
        $r['reason'] ?? '',
        $r['teacher'] ?? '',
      ]);
    }
  } catch (Exception $e) {
    fputcsv($out, ['ERROR', $e->getMessage()]);
  }
  fclose($out);
  exit;
}

// Undo / Restore (teachers only, POST)
if ($route === 'undo') {
  require_login();
  if (!is_post() || !hash_equals($_SESSION['_csrf'], param('_csrf'))) {
    Flash::add("Invalid request.", "error");
    Response::redirect('/?route=log');
  }
  $student_id = param('studentID');
  $from = intval(param('from'));
  $to   = intval(param('to'));
  $reason = param('reason','');
  $class = param('class','');
  $name  = param('name','');

  try {
    $data = $gs->getAllRecords($cfg['google']['student_spreadsheet_id'], $cfg['google']['student_sheet_name'], []);
    $score_col = detect_score_col($data['headers']);
    $row_index = null;
    foreach ($data['rows'] as $i=>$r) {
      if (strval($r['id'] ?? '') === strval($student_id)) { $row_index = $i + 2; break; }
    }
    if (!$row_index) { Flash::add("ไม่พบนักเรียน: " . htmlspecialchars($student_id), "error"); Response::redirect('/?route=log'); }

    $gs->updateCell($cfg['google']['student_spreadsheet_id'], $cfg['google']['student_sheet_name'], $row_index, $score_col, $from);

    $teacher = current_user() ?: 'anonymous';
    $ts = date('Y-m-d H:i:s');
    $rows = [
      $ts,
      $student_id,
      $name,
      $class,
      $to,
      ($from - $to),
      $from,
      "UNDO: " . $reason,
      $teacher
    ];
    $gs->appendRow($cfg['google']['log_spreadsheet_id'], $cfg['google']['log_sheet_name'], $rows);
    Flash::add("ยกเลิกรายการสำเร็จ และคืนค่าเป็น {$from}", "success");
  } catch (Exception $e) {
    Flash::add("ยกเลิกไม่สำเร็จ: " . $e->getMessage(), "error");
  }
  Response::redirect('/?route=log');
  exit;
}

// Student Handbook
if ($route === 'handbook') {
  Response::render('handbook', [
    'title' => 'คู่มือนักเรียน',
    'route' => $route,
  ]);
  exit;
}

// Classroom — Display all students in selected class
if ($route === 'classroom') {
  $selectedGrade = param('grade', '');
  $selectedRoom = param('room', '');
  $page = max(1, intval(param('page', 1)));
  $per = max(10, min(200, intval(param('per', 50))));
  
  $grades = [];
  $rooms = [];
  $students = [];
  $all_students = [];
  
  try {
    $data = $gs->getAllRecords($cfg['google']['student_spreadsheet_id'], $cfg['google']['student_sheet_name'], []);
    
    // Build list of unique grades
    $gradeSet = [];
    foreach ($data['rows'] as $r) {
      $grade = trim($r['grade'] ?? '');
      if ($grade) {
        $gradeSet[$grade] = true;
      }
    }
    $grades = array_keys($gradeSet);
    sort($grades, SORT_NATURAL);
    
    // Build list of rooms for selected grade
    if ($selectedGrade) {
      $roomSet = [];
      foreach ($data['rows'] as $r) {
        $grade = trim($r['grade'] ?? '');
        $room = trim($r['class'] ?? '');
        if ($grade === $selectedGrade && $room) {
          $roomSet[$room] = true;
        }
      }
      $rooms = array_keys($roomSet);
      sort($rooms, SORT_NATURAL);
    }
    
    // Filter students if both grade and room are selected
    if ($selectedGrade && $selectedRoom) {
      foreach ($data['rows'] as $r) {
        $grade = trim($r['grade'] ?? '');
        $room = trim($r['class'] ?? '');
        if ($grade === $selectedGrade && $room === $selectedRoom) {
          $all_students[] = $r;
        }
      }
    }
  } catch (Exception $e) {
    Flash::add("โหลดข้อมูลไม่สำเร็จ: " . $e->getMessage(), "error");
  }
  
  // Pagination
  $total = count($all_students);
  $pages = max(1, intval(ceil($total / $per)));
  if ($page > $pages) $page = $pages;
  $offset = ($page - 1) * $per;
  $students = array_slice($all_students, $offset, $per);
  
  Response::render('classroom', [
    'title' => 'ห้องเรียน',
    'grades' => $grades,
    'rooms' => $rooms,
    'selectedGrade' => $selectedGrade,
    'selectedRoom' => $selectedRoom,
    'students' => $students,
    'page' => $page,
    'pages' => $pages,
    'per' => $per,
    'total' => $total,
    'route' => $route,
  ]);
  exit;
}

// Search + Deduct (public search; deduct requires login)
if ($route === 'search') {
  $student_id = param('studentID','');
  $student = null;
  $row_index = null;
  $headers = [];
  $score_col = null;

  try {
    $data = $gs->getAllRecords($cfg['google']['student_spreadsheet_id'], $cfg['google']['student_sheet_name'], []);
    $headers = $data['headers'];
    $score_col = detect_score_col($headers);
    foreach ($data['rows'] as $i=>$r) {
      $sid = $r['id'] ?? ($r['student_id'] ?? '');
      if (strval($sid) === strval($student_id)) { $student = $r; $row_index = $i + 2; break; }
    }

    if (is_post()) {
      if (!current_user()) { Flash::add("โปรดเข้าสู่ระบบก่อนหักคะแนน", "error"); Response::redirect('/?route=login'); }
      if (!$row_index) { Flash::add("ไม่พบแถวนักเรียนที่ต้องการอัปเดต", "error"); Response::redirect('/?route=search&studentID=' . urlencode($student_id)); }

      // Reasons from config only
      $REASONS = $cfg['deduction_reasons'] ?? [];

      // mode: deduct (default) or add
      $mode = param('mode', 'deduct');
      $mode = ($mode === 'add') ? 'add' : 'deduct';

      $delta = 0;
      $reason_desc = '';

      if ($mode === 'add') {
        $add_points = intval(param('add_points', 0));
        $add_reason = trim(param('add_reason', ''));

        if ($add_points <= 0) {
          Flash::add("กรุณาระบุจำนวนคะแนนที่เพิ่ม", "error");
          Response::redirect('/?route=search&studentID=' . urlencode($student_id));
        }
        if ($add_reason === '') {
          Flash::add("กรุณาระบุเหตุผลที่เพิ่มคะแนน", "error");
          Response::redirect('/?route=search&studentID=' . urlencode($student_id));
        }

        $delta = abs($add_points);
        $reason_desc = "เพิ่มคะแนน: " . $add_reason;
      } else {
        // NEW: allow multi-select reasons from the modal (JSON array)
        $reasons_json_raw = param('reasons_json', '');

        if ($reasons_json_raw !== '') {
          $items = json_decode($reasons_json_raw, true);
          if (!is_array($items) || count($items) === 0) {
            Flash::add("กรุณาเลือกเหตุผลอย่างน้อย 1 ข้อ", "error");
            Response::redirect('/?route=search&studentID=' . urlencode($student_id));
          }

          $total = 0;
          $desc_list = [];

          foreach ($items as $it) {
            if (!is_array($it)) continue;
            $code = trim((string)($it['code'] ?? ''));
            if ($code === '') continue;

            if ($code === 'อื่นๆ') {
              $custom_detail = trim((string)($it['custom_detail'] ?? $it['detail'] ?? ''));
              if ($custom_detail === '') {
                // allow fallback from legacy hidden input
                $custom_detail = trim(param('custom_reason_detail', ''));
              }
              if ($custom_detail === '') {
                Flash::add("กรุณากรอกรายละเอียดเหตุผล (อื่นๆ)", "error");
                Response::redirect('/?route=search&studentID=' . urlencode($student_id));
              }

              $pts = intval($it['pts'] ?? 0);
              if ($pts <= 0) {
                $legacy_pts = intval(param('custom_points', 0));
                $pts = $legacy_pts > 0 ? $legacy_pts : 5;
              }
              $pts = abs($pts);

              $total += $pts;
              $desc_list[] = "อื่นๆ: " . $custom_detail . " (-" . $pts . ")";
            } else {
              if (!array_key_exists($code, $REASONS)) {
                Flash::add("รหัสเหตุผลไม่ถูกต้อง: " . htmlspecialchars($code), "error");
                Response::redirect('/?route=search&studentID=' . urlencode($student_id));
              }
              list($desc, $pts) = $REASONS[$code];
              $pts = abs(intval($pts));

              $total += $pts;
              $desc_list[] = $code . ": " . $desc . " (-" . $pts . ")";
            }
          }

          if ($total <= 0 || count($desc_list) === 0) {
            Flash::add("กรุณาเลือกเหตุผลอย่างน้อย 1 ข้อ", "error");
            Response::redirect('/?route=search&studentID=' . urlencode($student_id));
          }

          $delta = -abs($total);
          $reason_desc = implode(' / ', $desc_list);

        } else {
          // Legacy single-select
          $reason_code = param('reason_code');
          $custom_reason = trim(param('custom_reason_detail', ''));

          if (!array_key_exists($reason_code, $REASONS)) {
            Flash::add("รหัสเหตุผลไม่ถูกต้อง", "error");
            Response::redirect('/?route=search&studentID=' . urlencode($student_id));
          }

          list($desc, $pts) = $REASONS[$reason_code];
          $pts = intval($pts);

          if ($reason_code === 'อื่นๆ') {
            if ($custom_reason === '') {
              Flash::add("กรุณากรอกรายละเอียดเหตุผล", "error");
              Response::redirect('/?route=search&studentID=' . urlencode($student_id));
            }
            $desc = "อื่นๆ: " . $custom_reason;
            $custom_pts = intval(param('custom_points', 0));
            if ($custom_pts <= 0) { $custom_pts = 5; }
            $pts = abs($custom_pts);
          }

          $delta = -abs($pts);
          $reason_desc = ($desc !== '' ? $desc : $reason_code);
        }
      }

$current_score = intval($student['score'] ?? 0);        $new_score = $current_score + $delta; // negatives allowed

        // Update score
        $gs->updateCell($cfg['google']['student_spreadsheet_id'], $cfg['google']['student_sheet_name'], $row_index, $score_col, $new_score);

        // Append log
        $teacher = current_user() ?: 'anonymous';
        $ts = date('Y-m-d H:i:s');
        $row = [
          $ts,
          $student['id'] ?? '',
          ($student['prefix'] ?? '') . ' ' . ($student['surname'] ?? '') . ' ' . ($student['lastname'] ?? ''),
          'ม.' . ($student['grade'] ?? '') . '/' . ($student['class'] ?? ''),
          $current_score,
          $delta,
          $new_score,
          $reason_desc,
          $teacher
        ];
        $gs->appendRow($cfg['google']['log_spreadsheet_id'], $cfg['google']['log_sheet_name'], $row);

        if ($mode === 'add') {
          Flash::add('เพิ่มคะแนนสำเร็จ (+' . $delta . ' คะแนน) จาก ' . $current_score . ' เป็น ' . $new_score, 'success');
        } else {
          Flash::add('หักคะแนนสำเร็จ (' . abs($delta) . ' คะแนน) จาก ' . $current_score . ' เป็น ' . $new_score, 'success');
        }
        Response::redirect('/?route=search&studentID=' . urlencode($student_id));
	      }
	    }
	  catch (Exception $e) {
    Flash::add("เกิดข้อผิดพลาด: " . $e->getMessage(), "error");
  }

  // Per-student recent log (public)
  $student_log = [];
  try {
    $ldata = $gs->getAllRecords($cfg['google']['log_spreadsheet_id'], $cfg['google']['log_sheet_name'], []);
    foreach (array_reverse($ldata['rows']) as $r) {
      if (strval($r['id'] ?? '') === strval($student_id)) {
        $student_log[] = $r;
        if (count($student_log) >= 10) break;
      }
    }
  } catch (Exception $e) { /* ignore */ }

  // Reasons for the select
  $REASONS = $cfg['deduction_reasons'] ?? [];

  Response::render('result', [
    'title'=>'ผลการค้นหา',
    'student'=>$student,
    'student_id'=>$student_id,
    'deduction_reasons'=>$REASONS,
    'student_log'=>$student_log,
    'route'=>$route
  ]);
  exit;
}

// 404
http_response_code(404);
Response::render('index', ['title'=>'Not Found', 'route'=>'home']);