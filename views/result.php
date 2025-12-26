<?php /* views/result.php — full functional version with styling */ ?>

<link rel="stylesheet" href="/static/style2.css?v=recov1">
<link rel="stylesheet" href="/static/log.css?v=recov5">
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
        <h2 class="subtitle">ปรับคะแนน</h2>

        <form class="form" method="POST" action="/?route=search" id="scoreForm">
          <input type="hidden" name="route" value="search">
          <input type="hidden" name="studentID" value="<?= htmlspecialchars($student_id ?? '') ?>">
          <input type="hidden" name="mode" id="scoreMode" value="deduct">

          <div class="segmented" role="tablist" aria-label="โหมดการปรับคะแนน">
            <button class="seg-btn is-active" type="button" data-mode="deduct">หักคะแนน</button>
            <button class="seg-btn" type="button" data-mode="add">เพิ่มคะแนน</button>
          </div>

          <!-- Deduct -->
          <div id="deductFields">
            <label class="label">เหตุผลที่หักคะแนน</label>
            <div class="picker-row">
              <input class="input" id="reasonDisplay" type="text" placeholder="กดปุ่ม “เลือก” เพื่อค้นหาเหตุผล…" readonly>
              <button class="btn ghost" type="button" id="openReason">เลือก</button>
            </div>

            <!-- Mobile-friendly preview (wraps to multiple lines) -->
            <div class="chosen-reason" id="chosenReasonChip" style="display:none"></div>

            <input type="hidden" name="reason_code" id="reasonCode">
            <input type="hidden" name="custom_reason_detail" id="customReasonHidden">
            <input type="hidden" name="custom_points" id="customPointsHidden" value="">
            <div class="muted" id="reasonHint">เลือกเหตุผลก่อนกดบันทึก</div>
          </div>

          <!-- Add -->
          <div id="addFields" style="display:none;">
            <label class="label">จำนวนคะแนนที่เพิ่ม</label>
            <input class="input" type="number" name="add_points" min="1" max="100" value="5">

            <label class="label">เหตุผลที่เพิ่มคะแนน</label>
            <input class="input" type="text" name="add_reason" maxlength="100" placeholder="เช่น ช่วยงานโรงเรียน / จิตอาสา">
            <div class="muted">ระบบจะบันทึกเป็น “เพิ่มคะแนน: …” ใน Log</div>
          </div>

          <button class="btn" type="submit" id="submitBtn">บันทึก</button>
        </form>
      </section>

      <!-- Reason modal -->
      <div class="modal" id="reasonModal" aria-hidden="true">
        <div class="modal-backdrop" data-close></div>
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="reasonTitle">
          <div class="modal-head">
            <h3 id="reasonTitle">เลือกเหตุผลหักคะแนน</h3>
            <button class="icon-btn" type="button" data-close aria-label="ปิด">×</button>
          </div>

          <input class="input modal-search" id="reasonSearch" type="text" placeholder="ค้นหา… (พิมพ์เลข/คำ เช่น 109, ทรงผม, มาสาย)">

          <div class="modal-list" id="reasonList">
            <?php foreach (($deduction_reasons ?? []) as $code => $info): ?>
              <?php if ($code === 'อื่นๆ') continue; ?>
              <?php list($desc, $pts) = $info; ?>
              <button class="reason-item" type="button"
                data-code="<?= htmlspecialchars($code) ?>"
                data-pts="<?= htmlspecialchars(intval($pts)) ?>"
                data-desc="<?= htmlspecialchars($desc) ?>">
                <span class="reason-code"><?= htmlspecialchars($code) ?></span>
                <span class="reason-desc"><?= htmlspecialchars($desc) ?></span>
                <span class="reason-pts">-<?= htmlspecialchars(intval($pts)) ?></span>
              </button>
            <?php endforeach; ?>

            <button class="reason-item other" type="button" data-code="อื่นๆ" data-pts="0" data-desc="อื่นๆ">
              <span class="reason-code">อื่นๆ</span>
              <span class="reason-desc">ระบุเหตุผลเอง…</span>
              <span class="reason-pts">—</span>
            </button>
          </div>

          <div class="modal-foot" id="customReasonBox" style="display:none;">
            <label class="label">เหตุผลเพิ่มเติม (อื่นๆ)</label>
            <input class="input" id="customReasonInput" type="text" maxlength="100" placeholder="พิมพ์เหตุผล…">
            <label class="label" style="margin-top:10px;">คะแนนที่หัก</label>
            <input class="input" id="customPointsInput" type="number" min="1" max="100" value="5">
            <div class="modal-foot-actions">
              <button class="btn ghost" type="button" id="cancelCustom">ยกเลิก</button>
              <button class="btn" type="button" id="useCustomReason">ใช้เหตุผลนี้</button>
            </div>
          </div>
        </div>
      </div>

      <script>
        document.addEventListener("DOMContentLoaded", () => {
          // Mode toggle (deduct/add)
          const modeInput = document.getElementById("scoreMode");
          const deductFields = document.getElementById("deductFields");
          const addFields = document.getElementById("addFields");
          const submitBtn = document.getElementById("submitBtn");
          const segBtns = document.querySelectorAll(".seg-btn");

          function setMode(mode){
            modeInput.value = mode;
            segBtns.forEach(b => b.classList.toggle("is-active", b.dataset.mode === mode));
            const isDeduct = mode === "deduct";
            deductFields.style.display = isDeduct ? "" : "none";
            addFields.style.display = isDeduct ? "none" : "";
            submitBtn.textContent = isDeduct ? "บันทึกการหักคะแนน" : "บันทึกการเพิ่มคะแนน";
          }
          segBtns.forEach(b => b.addEventListener("click", () => setMode(b.dataset.mode)));
          setMode("deduct");

          // Modal open/close
          const modal = document.getElementById("reasonModal");
          const openBtn = document.getElementById("openReason");
          const search = document.getElementById("reasonSearch");
          const list = document.getElementById("reasonList");
          const reasonCode = document.getElementById("reasonCode");
          const reasonDisplay = document.getElementById("reasonDisplay");
          const reasonHint = document.getElementById("reasonHint");
          const chosenChip = document.getElementById("chosenReasonChip");
          const customBox = document.getElementById("customReasonBox");
          const customInput = document.getElementById("customReasonInput");
          const customHidden = document.getElementById("customReasonHidden");
          const customPtsHidden = document.getElementById("customPointsHidden");
          const customPtsInput = document.getElementById("customPointsInput");
          const useCustom = document.getElementById("useCustomReason");
          const cancelCustom = document.getElementById("cancelCustom");

          function openModal(){
            modal.classList.add("is-open");
            modal.setAttribute("aria-hidden","false");
            search.value = "";
            filterList("");
            customBox.style.display = "none";
            customInput.value = "";
            if (customPtsInput) customPtsInput.value = 5;
            setTimeout(() => search.focus(), 50);
          }
          function closeModal(){
            modal.classList.remove("is-open");
            modal.setAttribute("aria-hidden","true");
          }
          modal.addEventListener("click", (e) => {
            if (e.target.matches("[data-close]")) closeModal();
          });
          document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && modal.classList.contains("is-open")) closeModal();
          });
          openBtn.addEventListener("click", openModal);

          // Filter
          function norm(s){ return (s || "").toString().toLowerCase().trim(); }
          function filterList(q){
            const query = norm(q);
            [...list.querySelectorAll(".reason-item")].forEach(btn => {
              const code = norm(btn.dataset.code);
              const desc = norm(btn.dataset.desc);
              const ok = !query || code.includes(query) || desc.includes(query);
              btn.style.display = ok ? "" : "none";
            });
          }
          search.addEventListener("input", () => filterList(search.value));

          // Pick reason
          list.addEventListener("click", (e) => {
            const btn = e.target.closest(".reason-item");
            if (!btn) return;

            const code = btn.dataset.code;
            const pts = parseInt(btn.dataset.pts || "0", 10);
            const desc = btn.dataset.desc || "";

            if (code === "อื่นๆ") {
              customBox.style.display = "";
              setTimeout(() => customInput.focus(), 50);
              return;
            }

            reasonCode.value = code;
            customHidden.value = "";
            customPtsHidden.value = "";
            // Keep the input short-ish (ellipsis handled by CSS), and show full text in the chip (better on mobile)
            reasonDisplay.value = `${code} - ${desc} (-${pts})`;
            if (chosenChip) {
              chosenChip.style.display = "";
              chosenChip.textContent = `${code} • ${desc}  (-${pts})`;
            }
            reasonHint.textContent = `จะหัก ${pts} คะแนน`;
            closeModal();
          });

          function useCustomReason(){
            const v = (customInput.value || "").trim();
            if (!v) { customInput.focus(); return; }
            reasonCode.value = "อื่นๆ";
            customHidden.value = v;
            const p = Math.max(1, Math.abs(parseInt((customPtsInput && customPtsInput.value) || "0", 10) || 0));
            customPtsHidden.value = String(p);
            reasonDisplay.value = `อื่นๆ: ${v} (-${p})`;
            if (chosenChip) {
              chosenChip.style.display = "";
              chosenChip.textContent = `อื่นๆ • ${v}  (-${p})`;
            }
            reasonHint.textContent = `จะหัก ${p} คะแนน`;
            closeModal();
          }
          useCustom.addEventListener("click", useCustomReason);
          cancelCustom.addEventListener("click", () => {
            customBox.style.display = "none";
            customInput.value = "";
            if (customPtsInput) customPtsInput.value = 5;
            search.focus();
          });

          // Safety: block submit in deduct mode if no reason chosen
          document.getElementById("scoreForm").addEventListener("submit", (e) => {
            if (modeInput.value === "deduct" && !reasonCode.value) {
              e.preventDefault();
              openModal();
            }
          });
        });
      </script>
    <?php else: ?>
      <p class="muted center">เข้าสู่ระบบเพื่อหักคะแนน</p>
    <?php endif; ?>

    <section class="card table-card">
      <div class="table-head">
        <h2 class="subtitle">ประวัติการปรับคะแนนล่าสุด</h2>
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
