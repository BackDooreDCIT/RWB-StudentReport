<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= isset($title)?htmlspecialchars($title):'App' ?></title>
  <link rel="stylesheet" href="/static/navbar.css">
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
      <div class="navbar-menu">
        <?php if (!empty($_SESSION['user'])): ?>
          <a href="/?route=search" class="navbar-item <?= ($route ?? '') === 'search' ? 'active' : '' ?>">หน้าแรก</a>
          <a href="/?route=classroom" class="navbar-item <?= ($route ?? '') === 'classroom' ? 'active' : '' ?>">ห้องเรียน</a>
          <a href="/?route=log" class="navbar-item <?= ($route ?? '') === 'log' ? 'active' : '' ?>">บันทึกการหักคะแนน</a>
        <?php else: ?>
          <a href="/?route=home" class="navbar-item <?= ($route ?? '') === 'home' ? 'active' : '' ?>">หน้าแรก</a>
          <a href="/?route=classroom" class="navbar-item <?= ($route ?? '') === 'classroom' ? 'active' : '' ?>">ห้องเรียน</a>
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
