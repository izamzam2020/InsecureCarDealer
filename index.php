<?php // index.php
require __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main>
  <section class="hero reveal" style="--hero-img: url('images/hero.svg');">
    <div class="hero__overlay"></div>
    <div class="hero__content container">
      <h1>Welcome to <span class="accent">Speedy Motors</span> – Your Dream Car Awaits</h1>
      <p>Discover premium vehicles, flexible finance, and a seamless buying experience.</p>
      <a href="#featured" class="btn btn--primary">Browse Cars</a>
    </div>
  </section>

  <section id="featured" class="featured container">
    <div class="section-header reveal">
      <h2>Featured Cars</h2>
      <p>Hand-picked models you’ll love.</p>
    </div>

    <div class="cards">
      <?php if (!$DB_CONNECTED) { ?>
        <article class="card reveal">
          <div class="card__body">
            <h3>You must connect to the database</h3>
            <p>Enter the details in /includes/config.php</p>
          </div>
        </article>
      <?php } else { ?>
        <?php
          $res = mysqli_query($conn, "SELECT id, make, model, year, price, color, mileage FROM stock WHERE in_stock = 1 ORDER BY id DESC LIMIT 12");
          if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
              $id = (int)$row['id'];
              $title = $row['year'] . ' ' . $row['make'] . ' ' . $row['model'];
              $desc = $row['color'] . ' • ' . number_format((int)$row['mileage']) . ' miles';
              $price = number_format((float)$row['price'], 2);
              $imgIdx = ($id % 8) + 1;
        ?>
          <article class="card reveal">
            <div class="card__media">
              <img src="images/car<?php echo $imgIdx; ?>.svg" alt="<?php echo htmlspecialchars($title); ?>">
            </div>
            <div class="card__body">
              <h3><?php echo htmlspecialchars($title); ?></h3>
              <p><?php echo htmlspecialchars($desc); ?></p>
              <div class="card__meta">
                <span class="price">$<?php echo $price; ?></span>
                <a class="btn btn--ghost" href="details.php?id=<?php echo $id; ?>">View Details</a>
              </div>
            </div>
          </article>
        <?php
            }
          } else {
        ?>
          <article class="card reveal">
            <div class="card__body">
              <h3>No cars found</h3>
              <p>Try running the <a href="<?php echo htmlspecialchars(base_url('seed.php')); ?>">seed script</a> to populate sample data.</p>
            </div>
          </article>
        <?php } ?>
      <?php } ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>


