<?php /* views/login.php */ ?>

<!-- If you want this page’s CSS specifically, add this to _header.php or keep here -->
<link rel="stylesheet" href="static/style3.css">
<link rel="stylesheet" href="/static/mobile.css?v=1">

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
