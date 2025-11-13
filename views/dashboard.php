<?php /* views/dashboard.php */ ?>

<link rel="stylesheet" href="static/style4.css">
<link rel="stylesheet" href="/static/mobile.css?v=1">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  /* Remove scrollbar and fix size to viewport - dashboard page */
  html, body {
    margin: 0 !important;
    padding: 0 !important;
    height: 100vh !important;
    max-height: 100vh !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
  }
  
  #app {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100vw !important;
    height: calc(100vh - 70px) !important;
    max-height: calc(100vh - 70px) !important;
    margin-top: 70px !important;
    overflow: hidden !important;
    padding: 0 20px !important;
    box-sizing: border-box !important;
  }
</style>

<h1 class="welcomemessage">Welcome, <span class="welcomeuser"><?= htmlspecialchars($user ?? ($_SESSION['user'] ?? '')) ?></span>!</h1>

<div class="top-left">
  <a href="https://rwb.ac.th/" target="_blank" class="logolink">
    <img src="https://rwb.ac.th/wp-content/uploads/2021/09/logo11.png" alt="RWB Logo" class="logo">
  </a>
  <h1 class="topheader"><span class="rwb">RWB</span> Student Report</h1>
</div>

<div class="search-box">
  <form method="get" action="/">
    <input type="hidden" name="route" value="search">
    <input type="search" name="studentID" placeholder="Enter Student ID" required>
  </form>
</div>

<div class="credits">
  <footer>
    <p>จัดทำโดยงานกิจการนักเรียนและเครื่อข่ายการศึกษา กลุ่มบริหารทั่วไป</p>
  </footer>
</div>
