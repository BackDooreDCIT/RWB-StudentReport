<?php /* views/log.php — full functional version with Class filter */ ?>

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
      <h1 class="title">บันทึกการหักคะแนน (สำหรับครู)</h1>
      <a class="btn ghost" href="/?route=dashboard">แดชบอร์ด</a>
    </div>

    <div class="toolbar-fw">
      <!-- Filter form: stays GET + route=log -->
      <form id="logFilters" class="filters-fw" method="get" action="/">
        <input type="hidden" name="route" value="log">

        <div class="field">
          <label>Student ID</label>
          <input class="input short" type="text" name="studentID" value="<?= htmlspecialchars($q ?? '') ?>">
        </div>

        <div class="field">
          <label>ชั้น/ห้อง</label>
          <input class="input short" type="text" name="class" value="<?= htmlspecialchars($class ?? '') ?>" placeholder="เช่น 5/2">
        </div>

        <div class="field">
          <label>ตั้งแต่วันที่</label>
          <input class="input date" type="date" name="start" value="<?= htmlspecialchars($start ?? '') ?>">
        </div>

        <div class="field">
          <label>ถึงวันที่</label>
          <input class="input date" type="date" name="end" value="<?= htmlspecialchars($end ?? '') ?>">
        </div>

        <div class="field">
          <label>ต่อหน้า</label>
          <input class="input tiny" type="number" min="10" max="200" name="per" value="<?= htmlspecialchars($per ?? 50) ?>">
        </div>
      </form>

      <div class="filters-actions">
        <button class="btn" type="submit" form="logFilters">กรอง</button>
        <a class="btn ghost" href="/?route=log">ล้างตัวกรอง</a>
        <a class="btn ghost"
           href="/?route=log_export&studentID=<?= urlencode($q ?? '') ?>&class=<?= urlencode($class ?? '') ?>&start=<?= urlencode($start ?? '') ?>&end=<?= urlencode($end ?? '') ?>">
          ดาวน์โหลด CSV
        </a>
      </div>
    </div>
  </section>

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

    <?php if (!empty($log)): ?>
      <div class="table-wrap">
        <table class="nice-table">
          <thead>
            <tr>
              <th class="nowrap">เวลา</th>
              <th class="nowrap">Student ID</th>
              <th>ชื่อ-สกุล</th>
              <th class="nowrap">ชั้น/ห้อง</th>
              <th class="nowrap">จาก</th>
              <th class="nowrap">การเปลี่ยนแปลง</th>
              <th class="nowrap">เป็น</th>
              <th>เหตุผล</th>
              <th class="nowrap">ผู้ทำรายการ</th>
              <th class="nowrap">ยกเลิก</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($log as $row): ?>
              <tr>
                <td class="nowrap"><?= htmlspecialchars($row['timestamp'] ?? ($row['time'] ?? '')) ?></td>
                <td class="nowrap"><?= htmlspecialchars($row['id'] ?? '') ?></td>
                <td class="name"><?= htmlspecialchars($row['name'] ?? '') ?></td>
                <td class="class"><?= htmlspecialchars($row['class'] ?? '') ?></td>
                <td class="nowrap"><?= htmlspecialchars($row['from'] ?? '') ?></td>
                <td class="nowrap"><?= htmlspecialchars($row['change'] ?? '') ?></td>
                <td class="nowrap"><?= htmlspecialchars($row['to'] ?? '') ?></td>
                <td class="reason"><?= htmlspecialchars($row['reason'] ?? '') ?></td>
                <td class="nowrap"><?= htmlspecialchars($row['teacher'] ?? '') ?></td>
                <td class="action">
                  <!-- UNDO must be POST and include CSRF -->
                  <form method="post" action="/?route=undo" onsubmit="return confirm('ยืนยันการยกเลิกรายการนี้และคืนคะแนน?');">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf ?? '') ?>">
                    <input type="hidden" name="studentID" value="<?= htmlspecialchars($row['id'] ?? '') ?>">
                    <input type="hidden" name="from" value="<?= htmlspecialchars($row['from'] ?? '') ?>">
                    <input type="hidden" name="to" value="<?= htmlspecialchars($row['to'] ?? '') ?>">
                    <input type="hidden" name="reason" value="<?= htmlspecialchars($row['reason'] ?? '') ?>">
                    <input type="hidden" name="class" value="<?= htmlspecialchars($row['class'] ?? '') ?>">
                    <input type="hidden" name="name" value="<?= htmlspecialchars($row['name'] ?? '') ?>">
                    <button class="btn small" type="submit">UNDO</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="pager">
        <?php if (($page ?? 1) > 1): ?>
          <a class="btn ghost"
             href="/?route=log&studentID=<?= urlencode($q ?? '') ?>&class=<?= urlencode($class ?? '') ?>&start=<?= urlencode($start ?? '') ?>&end=<?= urlencode($end ?? '') ?>&per=<?= intval($per ?? 50) ?>&page=<?= intval(($page ?? 1)-1) ?>">
            ← ก่อนหน้า
          </a>
        <?php endif; ?>
        <div class="spacer"></div>
        <?php if (($page ?? 1) < ($pages ?? 1)): ?>
          <a class="btn ghost"
             href="/?route=log&studentID=<?= urlencode($q ?? '') ?>&class=<?= urlencode($class ?? '') ?>&start=<?= urlencode($start ?? '') ?>&end=<?= urlencode($end ?? '') ?>&per=<?= intval($per ?? 50) ?>&page=<?= intval(($page ?? 1)+1) ?>">
            ถัดไป →
          </a>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <p class="muted center">ยังไม่มีรายการหรือไม่ตรงตามตัวกรอง</p>
    <?php endif; ?>
  </section>
</div>
