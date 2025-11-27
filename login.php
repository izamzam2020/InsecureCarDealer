<?php
require __DIR__ . '/includes/config.php';

$error = '';

// Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
  }
  @session_destroy();
  header('Location: /carShop/login.php');
  exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = isset($_POST['username']) ? $_POST['username'] : '';
  $password = isset($_POST['password']) ? $_POST['password'] : '';

  if ($username === 'admin' && $password === 'password123') {
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = 'admin';
    header('Location: /carShop/admin.php');
    exit;
  } else {
    $error = 'Invalid username or password';
  }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main class="container" style="padding: 28px 0 60px;">
  <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
    <div class="card reveal" style="padding:16px;">
      <div class="card__body">
        <h3>You are logged in</h3>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>.</p>
        <div class="card__meta">
          <a class="btn btn--primary" href="/carShop/">Go to Home</a>
          <a class="btn btn--ghost" href="/carShop/login.php?action=logout">Logout</a>
        </div>
      </div>
    </div>
  <?php } else { ?>
    <section class="reveal">
      <div class="card" style="max-width: 460px; margin: 0 auto; overflow:hidden;">
        <div class="card__body" style="padding:20px 18px 24px;">
          <h2 style="margin:0 0 10px;">Login</h2>
          <?php if ($error) { ?>
            <p style="color:#f87171; margin:0 0 10px;"><?php echo htmlspecialchars($error); ?></p>
          <?php } ?>
          <form method="post" action="/carShop/login.php" style="display:grid; gap:12px;">
            <label>
              <span style="display:block; font-size:13px; color:#9aa3af;">Username</span>
              <input name="username" type="text" value="" style="width:100%; padding:10px; background:#0f1216; border:1px solid #1f2430; border-radius:10px; color:#e5e7eb;">
            </label>
            <label>
              <span style="display:block; font-size:13px; color:#9aa3af;">Password</span>
              <input name="password" type="password" value="" style="width:100%; padding:10px; background:#0f1216; border:1px solid #1f2430; border-radius:10px; color:#e5e7eb;">
            </label>
            <div style="display:flex; gap:10px; align-items:center;">
              <button class="btn btn--primary" type="submit">Login</button>
            </div>
          </form>
        </div>
      </div>
    </section>
  <?php } ?>

  <!-- TODO: George, Remove the creds when deployed! -->
  <!-- admin:cGFzc3dvcmQxMjM= -->
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>


