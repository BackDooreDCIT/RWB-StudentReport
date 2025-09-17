<?php /* views/result.php — full functional version with styling */ ?>

<link rel="stylesheet" href="/static/style2.css?v=recov1">
<link rel="stylesheet" href="/static/log.css?v=recov1">
<link rel="stylesheet" href="/static/mobile.css?v=1">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">

<div class="page-wrap">
  <?php $flashes = \Flash::getAll(); if ($flashes): ?>
    <div class="flash-stack">
      <?php foreach ($flashes as $f): ?>
        <div class="flash <?= htmlspecialchars($f['type']) ?>"><?= $f['msg'] ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($student)): ?>
    <section class="card result-card">
      <h1 class="title">ผลการค้นหา</h1>
      <div class="student-grid">
        <div class="row"><span class="k">ชื่อ-สกุล</span><span class="v"><?= htmlspecialchars(($student['prefix'] ?? '').' '.($student['surname'] ?? '').' '.($student['lastname'] ?? '')) ?></span></div>
        <div class="row"><span class="k">ระดับชั้น</span><span class="v">ม.<?= htmlspecialchars($student['grade'] ?? '') ?>/<?= htmlspecialchars($student['class'] ?? '') ?></span></div>
        <div class="row"><span class="k">เลขที่</span><span class="v"><?= htmlspecialchars($student['no.'] ?? '') ?></span></div>
        <div class="row"><span class="k">รหัสประจำตัว</span><span class="v"><?= htmlspecialchars($student['id'] ?? ($student['student_id'] ?? '')) ?></span></div>
        <div class="row"><span class="k">คะแนนพฤติกรรม</span><span class="v score"><?= htmlspecialchars($student['score'] ?? ($student['คะแนน'] ?? ($student['คะแนนพฤติกรรม'] ?? ''))) ?></span></div>
      </div>
    </section>

    <?php if (!empty($_SESSION['user'])): ?>
      <section class="card action-card">
        <h2 class="subtitle">หักคะแนน</h2>
        <!-- IMPORTANT: keep action="/?route=search" and hidden fields -->
        <form class="form" method="POST" action="/?route=search">
          <input type="hidden" name="route" value="search">
          <input type="hidden" name="studentID" value="<?= htmlspecialchars($student_id ?? '') ?>">

          <label class="label">เลือกเหตุผลที่หักคะแนน</label>
          <select class="input" name="reason_code" required>
            <?php foreach (($deduction_reasons ?? []) as $code => $info): list($desc, $pts) = $info; ?>
              <option value="<?= htmlspecialchars($code) ?>">
                <?= htmlspecialchars($code) ?> - <?= htmlspecialchars($desc) ?> (ตัด <?= htmlspecialchars($pts) ?> คะแนน)
              </option>
            <?php endforeach; ?>
          </select>

          <div id="custom_reason" class="custom-reason" style="display:none;">
            <label class="label">กรุณาระบุเหตุผลเพิ่มเติม</label>
            <input class="input" type="text" name="custom_reason_detail" maxlength="100">
          </div>

          <button class="btn" type="submit">ยืนยันการหักคะแนน</button>
        </form>
      </section>

      <script>
        document.addEventListener("DOMContentLoaded", function () {
          const selector = document.querySelector('select[name="reason_code"]');
          const customInput = document.getElementById("custom_reason");
          if (!selector) return;
          function toggle() { customInput.style.display = selector.value === "อื่นๆ" ? "block" : "none"; }
          selector.addEventListener("change", toggle); toggle();
        });
      </script>
    <?php else: ?>
      <p class="muted center">เข้าสู่ระบบเพื่อหักคะแนน</p>
    <?php endif; ?>

    <section class="card table-card">
      <div class="table-head">
        <h2 class="subtitle">ประวัติการหักคะแนนล่าสุด</h2>
      </div>
      <?php if (!empty($student_log)): ?>
        <div class="table-wrap">
          <table class="nice-table">
            <thead>
              <tr>
                <th class="nowrap">เวลา</th>
                <th class="nowrap">จาก</th>
                <th class="nowrap">การเปลี่ยนแปลง</th>
                <th class="nowrap">เป็น</th>
                <th>เหตุผล</th>
                <th class="nowrap">ผู้ทำรายการ</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($student_log as $row): ?>
              <tr>
                <td class="nowrap"><?= htmlspecialchars($row['timestamp'] ?? ($row['time'] ?? $row['col1'] ?? '')) ?></td>
                <td class="nowrap"><?= htmlspecialchars($row['from'] ?? $row['col5'] ?? '') ?></td>
                <td class="nowrap"><?= htmlspecialchars($row['change'] ?? $row['col6'] ?? '') ?></td>
                <td class="nowrap"><?= htmlspecialchars($row['to'] ?? $row['col7'] ?? '') ?></td>
                <td class="reason"><?= htmlspecialchars($row['reason'] ?? $row['col8'] ?? '') ?></td>
                <td class="nowrap"><?= htmlspecialchars($row['teacher'] ?? $row['col9'] ?? '') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="muted">ยังไม่มีบันทึกการหักคะแนนสำหรับนักเรียนคนนี้</p>
      <?php endif; ?>
    </section>

  <?php else: ?>
    <section class="card not-found">
      <h1>ไม่พบนักเรียนที่มีรหัสดังกล่าว</h1>
    </section>
  <?php endif; ?>

  <div class="nav-links">
    <?php if (!empty($_SESSION['user'])): ?>
      <a class="link" href="/?route=log">ดูบันทึกทั้งหมด (สำหรับครู)</a>
    <?php endif; ?>
    <a class="link" href="/?route=home">← กลับหน้าหลัก</a>
  </div>
</div>
