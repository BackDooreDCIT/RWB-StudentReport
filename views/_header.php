<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= isset($title)?htmlspecialchars($title):'App' ?></title>
  <link rel="stylesheet" href="/static/navbar.css?v=3">
  <!-- Minimal shell: no global CSS here to avoid overriding your page styles -->
</head>
<body class="has-navbar">
  <nav class="main-navbar">
    <div class="navbar-container">
      <div class="navbar-brand">
        <a href="/?route=home" class="navbar-logo">
          <img src="https://rwb.ac.th/wp-content/uploads/2021/09/logo11.png" alt="RWB Logo">
          <span>RWB Student Report</span>
        </a>
      </div>
      <button class="navbar-toggle" type="button" aria-expanded="false" aria-controls="navbar-menu" aria-label="Toggle navigation">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <div class="navbar-menu" id="navbar-menu">
        <?php if (!empty($_SESSION['user'])): ?>
          <a href="/?route=dashboard" class="navbar-item <?= ($route ?? '') === 'dashboard' ? 'active' : '' ?>">หน้าแรก</a>
          <a href="/?route=classroom" class="navbar-item <?= ($route ?? '') === 'classroom' ? 'active' : '' ?>">ห้องเรียน</a>
          <a href="/?route=handbook" class="navbar-item <?= ($route ?? '') === 'handbook' ? 'active' : '' ?>">คู่มือนักเรียน</a>
          <a href="/?route=log" class="navbar-item <?= ($route ?? '') === 'log' ? 'active' : '' ?>">บันทึกการหักคะแนน</a>
        <?php else: ?>
          <a href="/?route=home" class="navbar-item <?= ($route ?? '') === 'home' ? 'active' : '' ?>">หน้าแรก</a>
          <a href="/?route=classroom" class="navbar-item <?= ($route ?? '') === 'classroom' ? 'active' : '' ?>">ห้องเรียน</a>
          <a href="/?route=handbook" class="navbar-item <?= ($route ?? '') === 'handbook' ? 'active' : '' ?>">คู่มือนักเรียน</a>
        <?php endif; ?>
      </div>
      <div class="navbar-actions">
        <a href="https://github.com/BackDooreDCIT/RWB-StudentReport"
           class="navbar-github"
           target="_blank"
           rel="noopener noreferrer"
           title="GitHub Repository">
          <img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" alt="GitHub">
        </a>
        <?php if (!empty($_SESSION['user'])): ?>
          <span class="navbar-user">👤 <?= htmlspecialchars($_SESSION['user']) ?></span>
          <a href="/?route=logout" class="navbar-btn logout">ออกจากระบบ</a>
        <?php else: ?>
          <a href="/?route=login" class="navbar-btn login">เข้าสู่ระบบ</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  <div id="app">
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var toggle = document.querySelector('.navbar-toggle');
      var nav = document.querySelector('.main-navbar');
      if (!toggle || !nav) return;

      var mobileMQ = window.matchMedia('(max-width: 1024px)');

      function closeNav() {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }

      function toggleNav(e) {
        if (e) e.stopPropagation();
        var isOpen = nav.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      }

      toggle.addEventListener('click', toggleNav);

      // Close when clicking a menu item on mobile
      nav.addEventListener('click', function(e) {
        if (!mobileMQ.matches) return;
        var link = e.target.closest('a.navbar-item, a.navbar-btn');
        if (link) closeNav();
      });

      // Close when tapping outside on mobile
      document.addEventListener('click', function(e) {
        if (!mobileMQ.matches) return;
        if (nav.classList.contains('is-open') && !nav.contains(e.target)) {
          closeNav();
        }
      });

      // If the user resizes back to desktop, ensure it's closed
      window.addEventListener('resize', function() {
        if (!mobileMQ.matches) closeNav();
      });
    });
  </script>
