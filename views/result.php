<?php /* views/result.php — full functional version with styling */ ?>

<link rel="stylesheet" href="/static/style2.css?v=recov1">
<link rel="stylesheet" href="/static/log.css?v=recov16">
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

            <!-- NEW: Multi-select payload (JSON array) -->
            <input type="hidden" name="reasons_json" id="reasonsJson" value="">

            <!-- Backward-compatible single fields (kept to avoid breaking older pages) -->
            <input type="hidden" name="reason_code" id="reasonCode" value="">
            <input type="hidden" name="custom_reason_detail" id="customReasonHidden" value="">
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
            <div class="modal-title">
              <div class="modal-title-row">
                <span class="modal-badge" aria-hidden="true">−</span>
                <h3 id="reasonTitle">เลือกเหตุผลหักคะแนน</h3>
              </div>
              <div class="modal-sub">เลือกได้หลายข้อ • กดซ้ำเพื่อยกเลิก</div>
            </div>
            <button class="icon-btn" type="button" data-close aria-label="ปิด">×</button>
          </div>

          <div class="modal-search-wrap">
            <span class="search-ico" aria-hidden="true">🔎</span>
            <input class="input modal-search" id="reasonSearch" type="text" placeholder="ค้นหา… (พิมพ์เลข/คำ เช่น 109, ทรงผม, มาสาย)">
          </div>

          <!-- Selected summary (sticky on mobile) -->
          <div class="modal-selected" id="modalSelected" style="display:none">
            <div class="modal-selected-row">
              <div class="modal-selected-left">
                <div class="modal-selected-meta" id="modalSelectedMeta">เลือกแล้ว 0 รายการ • รวม -0</div>
                <button class="btn ghost selected-toggle" type="button" id="toggleSelected" aria-expanded="false">ดูรายการ</button>
              </div>
              <div class="modal-selected-actions">
                <button class="btn ghost" type="button" id="clearSelected">ล้างทั้งหมด</button>
                <button class="btn" type="button" id="doneSelected">เสร็จสิ้น</button>
              </div>
            </div>
            <div class="modal-selected-chips" id="modalSelectedChips"></div>
          </div>

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
              <button class="btn" type="button" id="useCustomReason">เพิ่มเข้าไป</button>
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
          const reasonsJson = document.getElementById("reasonsJson");
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

          const modalSelected = document.getElementById("modalSelected");
          const modalSelectedMeta = document.getElementById("modalSelectedMeta");
          const modalSelectedChips = document.getElementById("modalSelectedChips");
          const toggleSelectedBtn = document.getElementById("toggleSelected");
          const clearSelectedBtn = document.getElementById("clearSelected");
          const doneSelectedBtn = document.getElementById("doneSelected");

          // Selected chips preview (show a few chips + “+N more”, expandable)
          const previewMQ = window.matchMedia("(max-width: 520px)");
          let selectedExpanded = false;
          let chosenExpanded = false;

          function getPreviewLimit(){
            return previewMQ.matches ? 2 : 3;
          }

          function setSelectedExpanded(v){
            selectedExpanded = !!v;
            if (modalSelected) modalSelected.classList.toggle("is-expanded", selectedExpanded);
            if (modalSelected) modalSelected.classList.remove("is-collapsed"); // legacy class safety
          }

          function setChosenExpanded(v){
            chosenExpanded = !!v;
            if (chosenChip){
              chosenChip.classList.toggle("is-expanded", chosenExpanded);
              chosenChip.classList.toggle("is-collapsed", !chosenExpanded);
            }
          }

          function updateSelectedToggleUI(){
            const n = selected.length;
            const limit = getPreviewLimit();
            const hasMore = n > limit;

            // Keep the old toggle button as a backup control (mobile only)
            if (toggleSelectedBtn){
              const show = previewMQ.matches && hasMore;
              toggleSelectedBtn.style.display = show ? "" : "none";
              toggleSelectedBtn.textContent = selectedExpanded ? "ซ่อนรายการ" : `ดูรายการ (${n})`;
              toggleSelectedBtn.setAttribute("aria-expanded", String(selectedExpanded));
            }
          }

          // Toggle expand/collapse via the (optional) button / meta click
          if (toggleSelectedBtn) {
            toggleSelectedBtn.addEventListener("click", () => {
              setSelectedExpanded(!selectedExpanded);
              renderChosen();
            });
          }
          if (modalSelectedMeta) {
            modalSelectedMeta.addEventListener("click", () => {
              const hasMore = selected.length > getPreviewLimit();
              if (!hasMore) return;
              // Only treat meta as toggle on mobile
              if (!previewMQ.matches) return;
              setSelectedExpanded(!selectedExpanded);
              renderChosen();
            });
          }

          // Reset expansion when switching breakpoints
          const onPreviewChange = () => {
            setSelectedExpanded(false);
            setChosenExpanded(false);
            renderChosen();
          };
          if (previewMQ.addEventListener) previewMQ.addEventListener("change", onPreviewChange);
          else if (previewMQ.addListener) previewMQ.addListener(onPreviewChange);
// Selected reasons (multi)
          /** @type {{code:string, desc:string, pts:number, custom_detail?:string}[]} */
          let selected = [];

          function totalPts(){
            return selected.reduce((a, r) => a + Math.abs(parseInt(r.pts || 0, 10) || 0), 0);
          }

          function syncHidden(){
            // Server trusts config; we still send full list for UX/preview
            reasonsJson.value = JSON.stringify(selected);
            // Back-compat: keep a single code (first) so older handlers don't explode
            if (selected.length) {
              reasonCode.value = selected[0].code;
              if (selected[0].code === 'อื่นๆ') {
                customHidden.value = selected[0].custom_detail || '';
                customPtsHidden.value = String(Math.abs(parseInt(selected[0].pts || 0, 10) || 0));
              } else {
                customHidden.value = '';
                customPtsHidden.value = '';
              }
            } else {
              reasonCode.value = '';
              customHidden.value = '';
              customPtsHidden.value = '';
            }
          }

          function renderChosen(){
            const n = selected.length;
            const t = totalPts();

            if (!n) {
              reasonDisplay.value = '';
              reasonHint.textContent = 'เลือกเหตุผลก่อนกดบันทึก';
              if (chosenChip) chosenChip.style.display = 'none';
              if (modalSelected) modalSelected.style.display = 'none';
              setSelectedExpanded(false);
              setChosenExpanded(false);
              updateSelectedToggleUI();
              syncHidden();
              return;
            }

            // Short summary in the input (keeps UI clean)
            reasonDisplay.value = `เลือกแล้ว ${n} รายการ (รวม -${t})`;
            reasonHint.textContent = `จะหักรวม ${t} คะแนน (${n} รายการ)`;

            // Chips preview on the page (mobile-friendly)
            if (chosenChip) {
              chosenChip.style.display = '';
              const limit = getPreviewLimit();
              const hasMore = selected.length > limit;
              const shown = chosenExpanded ? selected : selected.slice(0, limit);

              const chips = shown.map(r => {
                const label = `${r.code} • ${r.desc} (-${Math.abs(parseInt(r.pts||0,10)||0)})`;
                return `<span class="chip" title="${escapeHtml(label)}">${escapeHtml(r.code)} <span class="chip-sub">${escapeHtml(r.desc)}</span> <span class="chip-pts">-${Math.abs(parseInt(r.pts||0,10)||0)}</span></span>`;
              }).join('');
              const more = hasMore
                ? (chosenExpanded
                    ? `<button type="button" class="chip chip-more" data-action="collapse" title="ย่อรายการ">ย่อรายการ</button>`
                    : `<button type="button" class="chip chip-more" data-action="expand" title="ดูเพิ่ม">+${selected.length - limit} เพิ่มเติม</button>`)
                : '';

              setChosenExpanded(chosenExpanded);
              chosenChip.innerHTML = `<div class="chips">${chips}${more}</div><div class="meta">รวม -${t} คะแนน • แตะเพื่อแก้ไข</div>`;
            }

            // Modal selected summary
            if (modalSelected && modalSelectedMeta && modalSelectedChips) {
              modalSelected.style.display = '';
              modalSelectedMeta.textContent = `เลือกแล้ว ${n} รายการ • รวม -${t}`;
              const limit = getPreviewLimit();
              const hasMore = selected.length > limit;
              const shown = selectedExpanded ? selected : selected.slice(0, limit);

              const chipsHtml = shown.map(r => {
                const label = `${r.code} ${r.desc} (-${Math.abs(parseInt(r.pts||0,10)||0)})`;
                return `<button type="button" class="mchip" data-code="${escapeAttr(r.code)}" data-desc="${escapeAttr(r.desc)}" title="ลบ: ${escapeAttr(label)}">
                          <span class="mchip-code">${escapeHtml(r.code)}</span>
                          <span class="mchip-desc">${escapeHtml(r.desc)}</span>
                          <span class="mchip-pts">-${Math.abs(parseInt(r.pts||0,10)||0)}</span>
                          <span class="mchip-x" aria-hidden="true">×</span>
                        </button>`;
              }).join('');
              let moreHtml = "";
              if (hasMore) {
                moreHtml = selectedExpanded
                  ? `<button type="button" class="mchip mchip-more" data-action="collapse" title="ย่อรายการ">ย่อรายการ</button>`
                  : `<button type="button" class="mchip mchip-more" data-action="expand" title="ดูเพิ่ม">+${selected.length - limit} เพิ่มเติม</button>`;
              }
              modalSelectedChips.innerHTML = chipsHtml + moreHtml;
              updateSelectedToggleUI();
            }

            syncHidden();
          }

          function escapeHtml(s){
            return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
          }
          function escapeAttr(s){
            return escapeHtml(s).replace(/\s+/g,' ').trim();
          }

          function isSelected(code, desc){
            return selected.some(r => r.code === code && r.desc === (desc||r.desc));
          }

          function toggleSelected(code, desc, pts, extra){
            // Remove if exists
            const idx = selected.findIndex(r => r.code === code && r.desc === desc);
            if (idx >= 0) {
              selected.splice(idx, 1);
            } else {
              selected.push({ code, desc, pts: Math.abs(parseInt(pts||0,10)||0), ...(extra||{}) });
            }
            markSelected();
            renderChosen();
          }

          function clearSelectedMarks(){
            [...list.querySelectorAll(".reason-item")].forEach(b => b.classList.remove("is-selected"));
          }

          function markSelected(){
            clearSelectedMarks();
            if (!selected.length) return;
            // Mark by code (for config reasons) + keep "อื่นๆ" highlighted if any custom exists
            selected.forEach(r => {
              const btn = list.querySelector(`.reason-item[data-code="${CSS.escape(r.code)}"]`);
              if (btn) btn.classList.add('is-selected');
            });
          }

          function openModal(){
            modal.classList.add("is-open");
            modal.setAttribute("aria-hidden","false");
            // Start collapsed on mobile so the list isn't blocked when many items are selected
            setSelectedExpanded(false);
            search.value = "";
            filterList("");
            customBox.style.display = "none";
            customInput.value = "";
            if (customPtsInput) customPtsInput.value = 5;
            // Highlight previously chosen reasons
            markSelected();
            renderChosen();
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

          // Page preview chips: collapse to a few + “+N more”. Click to expand/collapse or open the modal.
          if (chosenChip) {
            chosenChip.addEventListener("click", (e) => {
              const act = e.target.closest("[data-action]");
              if (act) {
                e.preventDefault();
                setChosenExpanded(act.dataset.action === "expand");
                renderChosen();
                return;
              }
              openModal();
            });
          }

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

          // Pick reason (toggle; stays open)
          list.addEventListener("click", (e) => {
            const btn = e.target.closest(".reason-item");
            if (!btn) return;

            const code = btn.dataset.code;
            const pts = parseInt(btn.dataset.pts || "0", 10);
            const desc = btn.dataset.desc || "";

            if (code === "อื่นๆ") {
              markSelected();
              customBox.style.display = "";
              setTimeout(() => customInput.focus(), 50);
              return;
            }

            // Toggle selection for normal reasons
            toggleSelected(code, desc, pts);
          });

          function useCustomReason(){
            const v = (customInput.value || "").trim();
            if (!v) { customInput.focus(); return; }
            const p = Math.max(1, Math.abs(parseInt((customPtsInput && customPtsInput.value) || "0", 10) || 0));
            // Add as an entry (allow more than 1 custom if they want)
            toggleSelected('อื่นๆ', `อื่นๆ: ${v}`, p, { custom_detail: v });
            customBox.style.display = "none";
            customInput.value = "";
            if (customPtsInput) customPtsInput.value = 5;
            search.focus();
          }
          useCustom.addEventListener("click", useCustomReason);
          cancelCustom.addEventListener("click", () => {
            customBox.style.display = "none";
            customInput.value = "";
            if (customPtsInput) customPtsInput.value = 5;
            search.focus();
          });

          // Remove selected chip inside modal
          if (modalSelectedChips) {
            modalSelectedChips.addEventListener('click', (e) => {
              const b = e.target.closest('.mchip');
              if (!b) return;

              // “+N more” / “collapse” chip
              if (b.classList.contains('mchip-more')) {
                const act = b.dataset.action || '';
                if (act === 'expand') setSelectedExpanded(true);
                if (act === 'collapse') setSelectedExpanded(false);
                renderChosen();
                return;
              }

              const c = b.dataset.code || '';
              const d = b.dataset.desc || '';
              // Find exact match
              const idx = selected.findIndex(r => r.code === c && r.desc === d);
              if (idx >= 0) selected.splice(idx, 1);
              markSelected();
              renderChosen();
            });
          }

          // Clear all
          if (clearSelectedBtn) {
            clearSelectedBtn.addEventListener('click', () => {
              selected = [];
              markSelected();
              renderChosen();
              customBox.style.display = 'none';
              customInput.value = '';
              if (customPtsInput) customPtsInput.value = 5;
              search.focus();
            });
          }

          // Done
          if (doneSelectedBtn) {
            doneSelectedBtn.addEventListener('click', () => {
              // Close only if at least 1 reason selected
              if (selected.length) closeModal();
            });
          }

          // Safety: block submit in deduct mode if no reason chosen
          document.getElementById("scoreForm").addEventListener("submit", (e) => {
            if (modeInput.value === "deduct" && selected.length === 0) {
              e.preventDefault();
              openModal();
            }
          });

          // Init
          renderChosen();
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
