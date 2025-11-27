<?php
// seed.php — deliberately basic/unsafe seeding script for training
// Run via browser or CLI after configuring DB in includes/config.php

require __DIR__ . '/includes/config.php';
if (!$DB_CONNECTED) {
  die('Database connection failed. Please configure /includes/config.php');
}

// Helpers
function table_exists($conn, $table) {
  $res = mysqli_query($conn, "SHOW TABLES LIKE '" . $table . "'");
  return $res && mysqli_num_rows($res) > 0;
}

function ensure_tables($conn) {
  $created = [];

  if (!table_exists($conn, 'admin')) {
    mysqli_query($conn, "CREATE TABLE admin (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(50),
      email VARCHAR(100),
      password VARCHAR(255),
      role VARCHAR(20),
      last_login DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $created[] = 'admin';
  }

  if (!table_exists($conn, 'customers')) {
    mysqli_query($conn, "CREATE TABLE customers (
      id INT AUTO_INCREMENT PRIMARY KEY,
      first_name VARCHAR(50),
      last_name VARCHAR(50),
      email VARCHAR(100),
      phone VARCHAR(20),
      address VARCHAR(200),
      city VARCHAR(100),
      state VARCHAR(50),
      zip VARCHAR(20),
      ssn VARCHAR(20),
      created_at DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $created[] = 'customers';
  }

  if (!table_exists($conn, 'stock')) {
    mysqli_query($conn, "CREATE TABLE stock (
      id INT AUTO_INCREMENT PRIMARY KEY,
      make VARCHAR(50),
      model VARCHAR(50),
      year INT,
      price DECIMAL(10,2),
      vin VARCHAR(20),
      color VARCHAR(30),
      mileage INT,
      in_stock TINYINT(1),
      created_at DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $created[] = 'stock';
  }

  if (!table_exists($conn, 'orders')) {
    mysqli_query($conn, "CREATE TABLE orders (
      id INT AUTO_INCREMENT PRIMARY KEY,
      customer_id INT,
      stock_id INT,
      order_date DATETIME,
      status VARCHAR(20),
      total DECIMAL(10,2),
      payment_method VARCHAR(20),
      card_last4 VARCHAR(4)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $created[] = 'orders';
  }

  if (!table_exists($conn, 'finance_agreements')) {
    mysqli_query($conn, "CREATE TABLE finance_agreements (
      id INT AUTO_INCREMENT PRIMARY KEY,
      order_id INT,
      lender VARCHAR(100),
      apr DECIMAL(5,2),
      term_months INT,
      monthly_payment DECIMAL(10,2),
      approved TINYINT(1),
      applicant_ssn VARCHAR(20),
      created_at DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $created[] = 'finance_agreements';
  }

  if (!table_exists($conn, 'reviews')) {
    mysqli_query($conn, "CREATE TABLE reviews (
      id INT AUTO_INCREMENT PRIMARY KEY,
      customer_name VARCHAR(100),
      rating TINYINT,
      title VARCHAR(150),
      body TEXT,
      image VARCHAR(255),
      created_at DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $created[] = 'reviews';
  }

  // Ensure image column exists for existing installs (unsafe/naive on purpose)
  $colRes = @mysqli_query($conn, "SHOW COLUMNS FROM `reviews` LIKE 'image'");
  if ($colRes && mysqli_num_rows($colRes) === 0) {
    @mysqli_query($conn, "ALTER TABLE `reviews` ADD COLUMN `image` VARCHAR(255)");
  }

  return $created;
}

function count_rows($conn, $table) {
  $res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM `" . $table . "`");
  if ($res) { $row = mysqli_fetch_assoc($res); return (int)$row['c']; }
  return 0;
}

// Very simple fake data generators (keep characters simple to avoid quote issues)
function pick($arr) { return $arr[array_rand($arr)]; }
function rand_num($min, $max) { return rand($min, $max); }
function rand_amount($min, $max) { return number_format(((rand($min*100, $max*100))/100), 2, '.', ''); }
function rand_phone() { return '+1-' . rand(200,999) . '-' . rand(200,999) . '-' . str_pad(rand(0,9999), 4, '0', STR_PAD_LEFT); }
function rand_zip() { return str_pad(strval(rand(10000, 99999)), 5, '0', STR_PAD_LEFT); }
function rand_date($daysBack = 365) { $t = time() - rand(0, 86400*$daysBack); return date('Y-m-d H:i:s', $t); }
function rand_ssn() { return str_pad(rand(100,999),3,'0',STR_PAD_LEFT) . '-' . str_pad(rand(10,99),2,'0',STR_PAD_LEFT) . '-' . str_pad(rand(1000,9999),4,'0',STR_PAD_LEFT); }
function rand_vin() {
  $chars = 'ABCDEFGHJKLMNPRSTUVWXYZ0123456789';
  $vin = '';
  for ($i=0; $i<17; $i++) { $vin .= $chars[rand(0, strlen($chars)-1)]; }
  return $vin;
}
function last4($num) { return substr($num, -4); }

$firstNames = ['James','Mary','Robert','Patricia','John','Jennifer','Michael','Linda','William','Elizabeth','David','Barbara','Richard','Susan','Joseph','Jessica','Thomas','Sarah','Charles','Karen'];
$lastNames  = ['Smith','Johnson','Williams','Brown','Jones','Garcia','Miller','Davis','Rodriguez','Martinez','Hernandez','Lopez','Gonzalez','Wilson','Anderson','Thomas','Taylor','Moore','Jackson','Martin'];
$cities     = ['Phoenix','Austin','Seattle','Miami','Boston','Denver','Atlanta','Dallas','San Diego','Orlando'];
$states     = ['AZ','TX','WA','FL','MA','CO','GA','TX','CA','FL'];
$makes      = ['Ford','Chevrolet','Toyota','Honda','BMW','Audi','Mercedes','Tesla','Nissan','Kia'];
$models     = ['Focus','Camaro','Camry','Civic','3-Series','A4','C-Class','Model 3','Altima','Sorento'];
$colors     = ['Black','White','Silver','Blue','Red','Gray','Green'];
$roles      = ['admin','editor','manager'];
$lenders    = ['FastFinance','AutoCredit Co.','Prime Lenders','Metro Bank','Drive Capital'];
$orderStatuses = ['pending','processing','completed','cancelled'];
$payMethods = ['card','cash','wire'];

$summary = [
  'admin' => 0,
  'customers' => 0,
  'stock' => 0,
  'orders' => 0,
  'finance_agreements' => 0,
  'reviews' => 0
];

// Ensure tables
$createdNow = ensure_tables($conn);

// Seed admin: exactly one admin user (admin/password123)
mysqli_query($conn, "DELETE FROM admin");
mysqli_query($conn, "INSERT INTO admin (username, email, password, role, last_login) VALUES ('admin', 'admin@example.test', 'password123', 'admin', '" . date('Y-m-d H:i:s') . "')");
if (mysqli_affected_rows($conn) > 0) { $summary['admin'] = 1; }

// Seed customers (random 40-100 total rows)
$existing = count_rows($conn, 'customers');
$desired = rand(40, 100);
$target = max(0, $desired - $existing);
for ($i=0; $i<$target; $i++) {
  $fn = pick($firstNames);
  $ln = pick($lastNames);
  $email = strtolower($fn . '.' . $ln . rand(1,99)) . '@mail.test';
  $phone = rand_phone();
  $addr = rand(100,9999) . ' ' . pick(['Oak','Pine','Maple','Cedar','Elm','Sunset','Hill','Ridge','Lake','Valley']) . ' St';
  $city = pick($cities);
  $state = pick($states);
  $zip = rand_zip();
  $ssn = rand_ssn();
  $createdAt = rand_date(365);
  mysqli_query($conn, "INSERT INTO customers (first_name, last_name, email, phone, address, city, state, zip, ssn, created_at) VALUES ('$fn', '$ln', '$email', '$phone', '$addr', '$city', '$state', '$zip', '$ssn', '$createdAt')");
  if (mysqli_affected_rows($conn) > 0) { $summary['customers']++; }
}

// Seed stock (random 40-100 total rows)
$existing = count_rows($conn, 'stock');
$desired = rand(40, 100);
$target = max(0, $desired - $existing);
for ($i=0; $i<$target; $i++) {
  $make = pick($makes);
  $model = pick($models);
  $year = rand_num(2015, 2024);
  $price = rand_amount(15000, 90000);
  $vin = rand_vin();
  $color = pick($colors);
  $mileage = rand_num(0, 120000);
  $inStock = rand(0,1);
  $createdAt = rand_date(365);
  mysqli_query($conn, "INSERT INTO stock (make, model, year, price, vin, color, mileage, in_stock, created_at) VALUES ('$make', '$model', $year, $price, '$vin', '$color', $mileage, $inStock, '$createdAt')");
  if (mysqli_affected_rows($conn) > 0) { $summary['stock']++; }
}

// Seed orders (random 40-100 total rows; link to existing customers and stock ids)
// Get id ranges
$custRes = mysqli_query($conn, "SELECT id FROM customers ORDER BY id");
$custIds = [];
if ($custRes) { while ($r = mysqli_fetch_assoc($custRes)) { $custIds[] = (int)$r['id']; } }
$stockRes = mysqli_query($conn, "SELECT id, price FROM stock ORDER BY id");
$stockRows = [];
if ($stockRes) { while ($r = mysqli_fetch_assoc($stockRes)) { $stockRows[] = ['id' => (int)$r['id'], 'price' => (float)$r['price']]; } }

$existing = count_rows($conn, 'orders');
$desired = rand(40, 100);
$target = max(0, $desired - $existing);
for ($i=0; $i<$target; $i++) {
  if (!$custIds || !$stockRows) { break; }
  $custId = $custIds[array_rand($custIds)];
  $stock = $stockRows[array_rand($stockRows)];
  $orderDate = rand_date(180);
  $status = pick($orderStatuses);
  $payment = pick($payMethods);
  $total = number_format($stock['price'] * (1 + rand(-5, 5)/100), 2, '.', '');
  $cardLast4 = str_pad(rand(0,9999), 4, '0', STR_PAD_LEFT);
  mysqli_query($conn, "INSERT INTO orders (customer_id, stock_id, order_date, status, total, payment_method, card_last4) VALUES ($custId, {$stock['id']}, '$orderDate', '$status', $total, '$payment', '$cardLast4')");
  if (mysqli_affected_rows($conn) > 0) { $summary['orders']++; }
}

// Seed finance agreements (random 40-100 total rows; link to orders)
$orderRes = mysqli_query($conn, "SELECT id, total FROM orders ORDER BY id");
$orderRows = [];
if ($orderRes) { while ($r = mysqli_fetch_assoc($orderRes)) { $orderRows[] = ['id' => (int)$r['id'], 'total' => (float)$r['total']]; } }

$existing = count_rows($conn, 'finance_agreements');
$desired = rand(40, 100);
$target = max(0, $desired - $existing);
for ($i=0; $i<$target; $i++) {
  if (!$orderRows) { break; }
  $o = $orderRows[array_rand($orderRows)];
  $lender = pick($lenders);
  $apr = number_format(rand(199, 239) / 100, 2); // 1.99 - 2.39
  $term = pick([36, 48, 60, 72]);
  $monthly = number_format($o['total'] * (1 + ($apr/100)) / $term, 2, '.', '');
  $approved = rand(0,1);
  $ssn = rand_ssn();
  $createdAt = rand_date(180);
  mysqli_query($conn, "INSERT INTO finance_agreements (order_id, lender, apr, term_months, monthly_payment, approved, applicant_ssn, created_at) VALUES ({$o['id']}, '$lender', $apr, $term, $monthly, $approved, '$ssn', '$createdAt')");
  if (mysqli_affected_rows($conn) > 0) { $summary['finance_agreements']++; }
}

// Seed reviews (exactly 4 fixed demo reviews)
mysqli_query($conn, "DELETE FROM reviews");
mysqli_query($conn, "INSERT INTO reviews (customer_name, rating, title, body, image, created_at) VALUES (
  'Alice Johnson', 5, 'Excellent service', 'Bought a 2021 Civic. Smooth process, great deal.', '" . base_url('images/car1.svg') . "', '" . rand_date(120) . "')");
if (mysqli_affected_rows($conn) > 0) { $summary['reviews']++; }
mysqli_query($conn, "INSERT INTO reviews (customer_name, rating, title, body, image, created_at) VALUES (
  'Marcus Lee', 4, 'Transparent and friendly', 'Staff were honest and helpful. Would buy again.', '" . base_url('images/car2.svg') . "', '" . rand_date(120) . "')");
if (mysqli_affected_rows($conn) > 0) { $summary['reviews']++; }
mysqli_query($conn, "INSERT INTO reviews (customer_name, rating, title, body, image, created_at) VALUES (
  'Sofia Ramirez', 5, 'Love my new SUV', 'Financing was quick and painless. Highly recommend.', '" . base_url('images/car3.svg') . "', '" . rand_date(120) . "')");
if (mysqli_affected_rows($conn) > 0) { $summary['reviews']++; }
mysqli_query($conn, "INSERT INTO reviews (customer_name, rating, title, body, image, created_at) VALUES (
  'Daniel Kim', 4, 'Great selection', 'Found exactly what I needed at a fair price.', '" . base_url('images/car4.svg') . "', '" . rand_date(120) . "')");
if (mysqli_affected_rows($conn) > 0) { $summary['reviews']++; }

// Output summary
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Seed Results</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo htmlspecialchars(base_url('style.css')); ?>">
  <style>
    body { background: #0b0d10; color: #e5e7eb; font-family: Inter, system-ui, sans-serif; }
    .container { width: min(900px, 92%); margin: 40px auto; }
    .panel { background: rgba(255,255,255,.03); border: 1px solid #1f2430; border-radius: 12px; padding: 18px; }
    h1 { margin: 0 0 12px; font-size: 22px; }
    ul { margin: 0; padding: 0 0 0 18px; }
    .ok { color: #22c55e; }
    .muted { color: #9aa3af; font-size: 13px; }
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  </head>
<body>
  <div class="container">
    <div class="panel">
      <h1>Seeding complete</h1>
      <ul>
        <li>admin: <strong class="ok"><?php echo (int)$summary['admin']; ?></strong> added</li>
        <li>customers: <strong class="ok"><?php echo (int)$summary['customers']; ?></strong> added</li>
        <li>stock: <strong class="ok"><?php echo (int)$summary['stock']; ?></strong> added</li>
        <li>orders: <strong class="ok"><?php echo (int)$summary['orders']; ?></strong> added</li>
        <li>finance_agreements: <strong class="ok"><?php echo (int)$summary['finance_agreements']; ?></strong> added</li>
        <li>reviews: <strong class="ok"><?php echo (int)$summary['reviews']; ?></strong> added</li>
      </ul>
    </div>
  </div>
</body>
</html>


