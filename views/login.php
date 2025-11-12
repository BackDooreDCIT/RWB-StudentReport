<?php /* views/login.php */ ?>

<!-- If you want this page's CSS specifically, add this to _header.php or keep here -->
<link rel="stylesheet" href="static/style3.css">
<link rel="stylesheet" href="/static/mobile.css?v=1">

<style>
  /* Remove scrollbar and fix size to viewport */
  html, body {
    margin: 0 !important;
    padding: 0 !important;
    height: 100vh !important;
    max-height: 100vh !important;
    overflow: hidden !important;
    display: flex !important;
    flex-direction: column !important;
  }
  
  /* Center login container with navbar - exact viewport height */
  #app {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100vw !important;
    height: calc(100vh - 70px) !important;
    max-height: calc(100vh - 70px) !important;
    margin-top: 70px !important;
    overflow: hidden !important;
    padding: 0 !important;
  }
  
  /* Force login container to be smaller and override any conflicting styles */
  .login-container {
    background-color: #fff !important;
    padding: 30px 40px !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1) !important;
    width: 100% !important;
    max-width: 380px !important;
    box-sizing: border-box !important;
    margin: 20px !important;
  }
  
  /* Override any search-box or other conflicting styles */
  .search-box {
    width: auto !important;
    max-width: 100% !important;
  }
</style>

<div class="login-container">
  <h2>Teacher's Login</h2>

  <?php foreach (\Flash::getAll() as $f): ?>
    <p class="<?= htmlspecialchars($f['type']) ?>"><?= $f['msg'] ?></p>
  <?php endforeach; ?>

  <form action="/?route=login" method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>

  <div class="extra-links">
    <p><a href="/?route=home">Back</a></p>
  </div>
</div>
