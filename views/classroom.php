<?php /* views/classroom.php — Display all students in selected classroom */ ?>

<link rel="stylesheet" href="/static/style2.css?v=recov1">
<link rel="stylesheet" href="/static/log.css?v=recov1">
<link rel="stylesheet" href="/static/mobile.css?v=1">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">

<style>
  /* Remove outer scrollbar and fix size to viewport */
  html, body {
    margin: 0 !important;
    padding: 0 !important;
    height: 100vh !important;
    max-height: 100vh !important;
    overflow: hidden !important;
  }
  
  #app {
    width: 100vw !important;
    height: calc(100vh - 70px) !important;
    max-height: calc(100vh - 70px) !important;
    margin-top: 70px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    box-sizing: border-box !important;
  }
  
  .page-wrap {
    min-height: 100% !important;
    box-sizing: border-box !important;
  }
</style>

<div class="page-wrap">
  <section class="card">
    <div class="header-row">
      <h1 class="title">รายชื่อนักเรียนในห้องเรียน</h1>
      <?php if (!empty($_SESSION['user'])): ?>
        <a class="btn ghost" href="/?route=dashboard">แดชบอร์ด</a>
      <?php endif; ?>
    </div>

    <div class="toolbar-fw">
      <!-- Filter form -->
      <form id="classroomFilters" class="filters-fw" method="get" action="/">
        <input type="hidden" name="route" value="classroom">

        <div class="field">
          <label>เลือกชั้น</label>
          <select class="input" name="grade" id="gradeSelect" required onchange="this.form.submit()">
            <option value="">-- เลือกชั้น --</option>
            <?php foreach ($grades as $grade): ?>
              <option value="<?= htmlspecialchars($grade) ?>" <?= ($selectedGrade === $grade) ? 'selected' : '' ?>>
                ม.<?= htmlspecialchars($grade) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label>เลือกห้อง</label>
          <select class="input" name="room" id="roomSelect" <?= empty($selectedGrade) ? 'disabled' : '' ?> required>
            <option value="">-- เลือกห้อง --</option>
            <?php foreach ($rooms as $room): ?>
              <option value="<?= htmlspecialchars($room) ?>" <?= ($selectedRoom === $room) ? 'selected' : '' ?>>
                ห้อง <?= htmlspecialchars($room) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label>ต่อหน้า</label>
          <input class="input tiny" type="number" min="10" max="200" name="per" value="<?= htmlspecialchars($per ?? 50) ?>">
        </div>
      </form>

      <div class="filters-actions">
        <button class="btn" type="submit" form="classroomFilters">แสดงรายชื่อ</button>
        <a class="btn ghost" href="/?route=classroom">ล้างตัวกรอง</a>
      </div>
    </div>
  </section>

  <?php if ($selectedGrade && $selectedRoom && !empty($students)): ?>
    <section class="card table-card">
      <div class="table-head">
        <div>
          ผลลัพธ์ทั้งหมด: <?= intval($total ?? 0) ?>
          • หน้า <?= intval($page ?? 1) ?> / <?= intval($pages ?? 1) ?>
        </div>
      </div>

      <?php $flashes = \Flash::getAll(); if ($flashes): ?>
        <div class="flash-stack">
          <?php foreach ($flashes as $f): ?>
            <div class="flash <?= htmlspecialchars($f['type']) ?>"><?= $f['msg'] ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="table-wrap">
        <table class="main-table">
          <thead>
            <tr>
              <th>ลำดับ</th>
              <th>รหัสนักเรียน</th>
              <th>คำนำหน้า</th>
              <th>ชื่อ</th>
              <th>นามสกุล</th>
              <th>ชั้น</th>
              <th>ห้อง</th>
              <th>คะแนนปัจจุบัน</th>
              <th>ดำเนินการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $student): ?>
              <tr>
                <td><?= htmlspecialchars($student['no'] ?? '') ?></td>
                <td><?= htmlspecialchars($student['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($student['prefix'] ?? '') ?></td>
                <td><?= htmlspecialchars($student['surname'] ?? '') ?></td>
                <td><?= htmlspecialchars($student['lastname'] ?? '') ?></td>
                <td><?= htmlspecialchars($student['grade'] ?? '') ?></td>
                <td><?= htmlspecialchars($student['class'] ?? '') ?></td>
                <td><strong><?= htmlspecialchars($student['score'] ?? '100') ?></strong></td>
                <td>
                  <a class="btn-sm" href="/?route=search&q=<?= urlencode($student['id'] ?? '') ?>">ดูรายละเอียด</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($pages > 1): ?>
        <div class="pagination">
          <?php for ($p = 1; $p <= $pages; $p++): ?>
            <?php
              $url = "/?route=classroom&grade=" . urlencode($selectedGrade) . "&room=" . urlencode($selectedRoom) . "&per=" . urlencode($per) . "&page=$p";
              $cls = ($p === $page) ? 'page-link active' : 'page-link';
            ?>
            <a href="<?= $url ?>" class="<?= $cls ?>"><?= $p ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </section>
  <?php elseif ($selectedGrade && $selectedRoom && empty($students)): ?>
    <section class="card">
      <p>ไม่พบนักเรียนในห้อง ม.<?= htmlspecialchars($selectedGrade) ?>/<?= htmlspecialchars($selectedRoom) ?></p>
    </section>
  <?php endif; ?>
</div>
