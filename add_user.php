<?php
require __DIR__ . '/includes/config.php';

// Check if user is logged in (but no role checking)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header('Location: /carShop/login.php');
  exit;
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';

$message = '';
$error = '';

// Handle form submission - NO CSRF protection (vulnerable by design)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $DB_CONNECTED) {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $role = $_POST['role'] ?? '';
  
  if ($username && $password && $role) {
    // Directly insert without any validation or sanitization
    $sql = "INSERT INTO admin (username, email, password, role, last_login) VALUES ('$username', '$username@example.test', '$password', '$role', '" . date('Y-m-d H:i:s') . "')";
    $result = @mysqli_query($conn, $sql);
    
    if ($result) {
      $message = "User '$username' created successfully with role '$role'";
    } else {
      $error = "Failed to create user: " . mysqli_error($conn);
    }
  } else {
    $error = "All fields are required";
  }
}
?>

<main class="container" style="padding: 28px 0 60px;">
  <section class="reveal admin-layout">
    <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>
    <div>
      <div class="section-header">
        <h2>Add New User</h2>
        <p class="muted" style="color:#9aa3af;">Create a new admin user account.</p>
      </div>
      
      <?php if (!$DB_CONNECTED) { ?>
        <div class="card" style="padding:16px; margin-top:12px;">
          <div class="card__body">
            <h3>You must connect to the database</h3>
            <p>Enter the details in /includes/config.php</p>
          </div>
        </div>
      <?php } else { ?>
        
        <?php if ($message) { ?>
          <div class="card" style="padding:16px; margin-top:16px; border:1px solid #22c55e;">
            <div class="card__body">
              <h3 style="color:#22c55e;">Success</h3>
              <p><?php echo htmlspecialchars($message); ?></p>
            </div>
          </div>
        <?php } ?>
        
        <?php if ($error) { ?>
          <div class="card" style="padding:16px; margin-top:16px; border:1px solid #f87171;">
            <div class="card__body">
              <h3 style="color:#f87171;">Error</h3>
              <p><?php echo htmlspecialchars($error); ?></p>
            </div>
          </div>
        <?php } ?>
        
        <div class="card" style="padding:16px; margin-top:16px;">
          <div class="card__body">
            <h3>User Details</h3>
            <form method="post" action="/carShop/add_user.php" style="display:grid; gap:16px; margin-top:12px; max-width:400px;">
              <label>
                <span style="display:block; font-size:13px; color:#9aa3af; margin-bottom:4px;">Username</span>
                <input name="username" type="text" required style="width:100%; padding:10px; background:#0f1216; border:1px solid #1f2430; border-radius:10px; color:#e5e7eb;">
              </label>
              
              <label>
                <span style="display:block; font-size:13px; color:#9aa3af; margin-bottom:4px;">Password</span>
                <input name="password" type="password" required style="width:100%; padding:10px; background:#0f1216; border:1px solid #1f2430; border-radius:10px; color:#e5e7eb;">
              </label>
              
              <label>
                <span style="display:block; font-size:13px; color:#9aa3af; margin-bottom:4px;">Role</span>
                <select name="role" required style="width:100%; padding:10px; background:#0f1216; border:1px solid #1f2430; border-radius:10px; color:#e5e7eb;">
                  <option value="">Select role...</option>
                  <option value="user">User</option>
                  <option value="admin">Admin</option>
                </select>
              </label>
              
              <button class="btn btn--primary" type="submit">Create User</button>
            </form>
          </div>
        </div>
        
      <?php } ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
