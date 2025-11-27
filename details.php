<?php
require __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';

$id = isset($_GET['id']) ? $_GET['id'] : null; // intentionally unsanitized for training
$car = null;
if ($DB_CONNECTED && $id !== null) {
  $res = mysqli_query($conn, "SELECT * FROM stock WHERE id = " . $id . " LIMIT 1");
  if ($res && mysqli_num_rows($res) === 1) {
    $car = mysqli_fetch_assoc($res);
  }
}
?>

<main class="container" style="padding: 28px 0 60px;">
  <?php if (!$DB_CONNECTED) { ?>
    <div class="card reveal" style="padding:16px;">
      <div class="card__body">
        <h3>You must connect to the database</h3>
        <p>Enter the details in /includes/config.php</p>
      </div>
    </div>
  <?php } elseif (!$id) { ?>
    <div class="card reveal" style="padding:16px;">
      <div class="card__body">
        <h3>Invalid request</h3>
        <p>Missing car id.</p>
      </div>
    </div>
  <?php } elseif (!$car) { ?>
    <div class="card reveal" style="padding:16px;">
      <div class="card__body">
        <h3>Car not found</h3>
        <p>The requested car does not exist.</p>
      </div>
    </div>
  <?php } else { ?>
    <?php $title = $car['year'] . ' ' . $car['make'] . ' ' . $car['model'];
          $imgIdx = ((int)$car['id'] % 8) + 1; ?>
    <section class="reveal">
      <div class="card" style="overflow:hidden;">
        <div class="card__media">
          <img src="images/car<?php echo $imgIdx; ?>.svg" alt="<?php echo htmlspecialchars($title); ?>">
        </div>
        <div class="card__body" style="padding:16px 16px 24px;">
          <h2 style="margin:0 0 6px; font-size:22px;"><?php echo htmlspecialchars($title); ?></h2>
          <p style="margin:0 0 14px; color:#9aa3af;">
            <?php echo htmlspecialchars($car['color']); ?> • <?php echo number_format((int)$car['mileage']); ?> miles • VIN: <?php echo htmlspecialchars($car['vin']); ?>
          </p>
          <div class="card__meta">
            <span class="price">$<?php echo number_format((float)$car['price'], 2); ?></span>
            <a class="btn btn--primary" href="<?php echo htmlspecialchars(base_url()); ?>">Back to Home</a>
          </div>
        </div>
      </div>
    </section>
  <?php } ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>


