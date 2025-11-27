<?php
require __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';

$q = $_GET['q'] ?? '';
$results = [];
$error = '';
if ($DB_CONNECTED && $q !== '') {
  // DELIBERATELY VULNERABLE: Direct SQL injection point for training
  $sql = "SELECT id, first_name, last_name, email, phone FROM customers WHERE first_name LIKE '%$q%' OR last_name LIKE '%$q%' OR email LIKE '%$q%' ORDER BY id DESC LIMIT 50";
  $res = @mysqli_query($conn, $sql);
  if ($res) {
    while ($row = mysqli_fetch_assoc($res)) { $results[] = $row; }
  } else {
    $error = mysqli_error($conn);
  }
}
?>

<main class="container" style="padding: 28px 0 60px;">
  <section class="reveal admin-layout">
    <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>
    <div>
      <div class="section-header">
        <h2>Search Customers</h2>
        <p class="muted" style="color:#9aa3af;">Type a name, email, or part of either.</p>
      </div>
      <?php if (!$DB_CONNECTED) { ?>
        <div class="card" style="padding:16px; margin-top:12px;">
          <div class="card__body">
            <h3>You must connect to the database</h3>
            <p>Enter the details in /includes/config.php</p>
          </div>
        </div>
      <?php } else { ?>
        <div class="card" style="padding:16px; margin-top:12px;">
          <div class="card__body">
            <form method="get" action="/carShop/search.php" style="display:flex; gap:10px; align-items:center;">
              <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search customers..." style="flex:1; padding:10px; background:#0f1216; border:1px solid #1f2430; border-radius:10px; color:#e5e7eb;">
              <button class="btn btn--primary" type="submit">Search</button>
            </form>
          </div>
        </div>

        <?php if ($q !== '') { ?>
          <?php if ($error) { ?>
            <div class="card" style="padding:16px; margin-top:16px; border:1px solid #f87171;">
              <div class="card__body">
                <h3 style="color:#f87171;">SQL Error</h3>
                <p style="color:#9aa3af; font-family:monospace;"><?php echo htmlspecialchars($error); ?></p>
              </div>
            </div>
          <?php } ?>
          <div class="card" style="padding:0; margin-top:16px; overflow:hidden;">
            <div class="card__body" style="padding:0;">
              <table style="width:100%; border-collapse:collapse;">
                <thead>
                  <tr style="background: rgba(255,255,255,.04); text-align:left;">
                    <th style="padding:12px; border-bottom:1px solid #1f2430;">ID</th>
                    <th style="padding:12px; border-bottom:1px solid #1f2430;">Name</th>
                    <th style="padding:12px; border-bottom:1px solid #1f2430;">Email</th>
                    <th style="padding:12px; border-bottom:1px solid #1f2430;">Phone</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!$results && !$error) { ?>
                    <tr><td colspan="4" style="padding:14px; color:#9aa3af;">No results</td></tr>
                  <?php } else { foreach ($results as $r) { ?>
                    <tr>
                      <td style="padding:12px; border-bottom:1px solid #1f2430;"><?php echo (int)$r['id']; ?></td>
                      <td style="padding:12px; border-bottom:1px solid #1f2430;"><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></td>
                      <td style="padding:12px; border-bottom:1px solid #1f2430;"><?php echo htmlspecialchars($r['email']); ?></td>
                      <td style="padding:12px; border-bottom:1px solid #1f2430;"><?php echo htmlspecialchars($r['phone']); ?></td>
                    </tr>
                  <?php }} ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>


