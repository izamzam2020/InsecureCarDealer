<?php
// reset.php — truncate all data tables (unsafe on purpose)
require __DIR__ . '/includes/config.php';
if (!$DB_CONNECTED) { die('Database connection failed. Please configure /includes/config.php'); }

// Disable FK checks for truncation safety
@mysqli_query($conn, 'SET FOREIGN_KEY_CHECKS=0');

$tables = ['finance_agreements', 'orders', 'stock', 'customers', 'admin'];
$results = [];
foreach ($tables as $t) {
  $ok = @mysqli_query($conn, "TRUNCATE TABLE `" . $t . "`");
  $results[$t] = $ok ? 'ok' : 'error';
}

@mysqli_query($conn, 'SET FOREIGN_KEY_CHECKS=1');

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reset Results</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo htmlspecialchars(base_url('style.css')); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { background: #0b0d10; color: #e5e7eb; font-family: Inter, system-ui, sans-serif; }
    .container { width: min(900px, 92%); margin: 40px auto; }
    .panel { background: rgba(255,255,255,.03); border: 1px solid #1f2430; border-radius: 12px; padding: 18px; }
    h1 { margin: 0 0 12px; font-size: 22px; }
    ul { margin: 0; padding: 0 0 0 18px; }
    .ok { color: #22c55e; }
    .error { color: #f87171; }
  </style>
</head>
<body>
  <div class="container">
    <div class="panel">
      <h1>Reset complete</h1>
      <ul>
        <?php foreach ($results as $table => $status) { ?>
          <li><?php echo $table; ?>: <strong class="<?php echo $status; ?>"><?php echo $status; ?></strong></li>
        <?php } ?>
      </ul>
      <div style="margin-top:10px;">
        <a class="btn btn--primary" href="<?php echo htmlspecialchars(base_url('seed.php')); ?>">Run Seeder</a>
        <a class="btn btn--ghost" href="<?php echo htmlspecialchars(base_url()); ?>">Back to Home</a>
      </div>
    </div>
  </div>
</body>
</html>


