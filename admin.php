<?php
require __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';

function quick_count($conn, $table) {
  $res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM `" . $table . "`");
  if ($res) { $row = mysqli_fetch_assoc($res); return (int)$row['c']; }
  return 0;
}

$counts = [
  'admin' => $DB_CONNECTED ? quick_count($conn, 'admin') : 0,
  'customers' => $DB_CONNECTED ? quick_count($conn, 'customers') : 0,
  'stock' => $DB_CONNECTED ? quick_count($conn, 'stock') : 0,
  'orders' => $DB_CONNECTED ? quick_count($conn, 'orders') : 0,
  'finance_agreements' => $DB_CONNECTED ? quick_count($conn, 'finance_agreements') : 0,
];
?>

<main class="container" style="padding: 28px 0 60px;">
  <section class="reveal admin-layout">
    <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>
    <div>
      <div class="section-header">
        <h2>Admin Dashboard</h2>
        <p class="muted" style="color:#9aa3af;">Quick overview of your dealership data.</p>
      </div>
    <?php if (!$DB_CONNECTED) { ?>
      <div class="card" style="padding:16px; margin-top:12px;">
        <div class="card__body">
          <h3>You must connect to the database</h3>
          <p>Enter the details in /includes/config.php</p>
        </div>
      </div>
    <?php } else { ?>
      <div class="cards" style="margin-top:12px;">
        <article class="card">
          <div class="card__body">
            <h3>Admins</h3>
            <p><span class="price"><?php echo $counts['admin']; ?></span> total</p>
          </div>
        </article>
        <article class="card">
          <div class="card__body">
            <h3>Customers</h3>
            <p><span class="price"><?php echo $counts['customers']; ?></span> total</p>
          </div>
        </article>
        <article class="card">
          <div class="card__body">
            <h3>Stock</h3>
            <p><span class="price"><?php echo $counts['stock']; ?></span> total</p>
          </div>
        </article>
        <article class="card">
          <div class="card__body">
            <h3>Orders</h3>
            <p><span class="price"><?php echo $counts['orders']; ?></span> total</p>
          </div>
        </article>
        <article class="card">
          <div class="card__body">
            <h3>Finance Agreements</h3>
            <p><span class="price"><?php echo $counts['finance_agreements']; ?></span> total</p>
          </div>
        </article>
      </div>

      <div class="card" style="padding:16px; margin-top:16px;">
        <div class="card__body">
          <h3>Quick Actions</h3>
          <div class="card__meta" style="margin-top:8px;">
            <a class="btn btn--primary" href="/carShop/seed.php">Run Seeder</a>
            <a class="btn btn--ghost" href="/carShop/reset.php">Reset (truncate tables)</a>
            <a class="btn btn--ghost" href="/carShop/">Go to Home</a>
          </div>
        </div>
      </div>
    <?php } ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>


