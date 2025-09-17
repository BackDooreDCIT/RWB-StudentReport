<?php /* views/dashboard.php */ ?>

<link rel="stylesheet" href="static/style4.css">
<link rel="stylesheet" href="/static/mobile.css?v=1">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="top-right">
  <div class="logout">
    <a href="/?route=logout" class="logout-button"><strong>Logout</strong></a><br>
  </div>
  <div class="log">
    <a class="btn" href="/?route=log" style="margin-top:16px; display:inline-block; color:black;">ดูบันทึกการหักคะแนน</a>
  </div>
</div>



<div>
  <a href="https://github.com/teety5354/RWB_student_report"
     class="github-icon-wrapper"
     target="_blank"
     rel="noopener noreferrer">
    <img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" alt="GitHub">
  </a>
</div>

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
