<?php
// setup.php — create all DB tables without inserting demo data.
require __DIR__ . '/includes/config.php';

header('Content-Type: text/html; charset=utf-8');

function run_sql($conn, $sql) {
  return mysqli_query($conn, $sql);
}

$tables = [
  'admin' => "CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255),
    role VARCHAR(20),
    last_login DATETIME NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  'customers' => "CREATE TABLE IF NOT EXISTS customers (
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
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  'stock' => "CREATE TABLE IF NOT EXISTS stock (
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
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  'orders' => "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    stock_id INT,
    order_date DATETIME,
    status VARCHAR(20),
    total DECIMAL(10,2),
    payment_method VARCHAR(20),
    card_last4 VARCHAR(4)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  'finance_agreements' => "CREATE TABLE IF NOT EXISTS finance_agreements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    lender VARCHAR(100),
    apr DECIMAL(5,2),
    term_months INT,
    monthly_payment DECIMAL(10,2),
    approved TINYINT(1),
    applicant_ssn VARCHAR(20),
    created_at DATETIME
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  'reviews' => "CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100),
    rating TINYINT,
    title VARCHAR(150),
    body TEXT,
    image VARCHAR(255),
    created_at DATETIME
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Setup</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo htmlspecialchars(base_url('style.css')); ?>">
  <style>
    body { background:#0b0d10; color:#e5e7eb; font-family: Inter, system-ui, sans-serif; }
    .container { width:min(900px, 92%); margin:40px auto; }
    .panel { background:rgba(255,255,255,.03); border:1px solid #1f2430; border-radius:12px; padding:20px; }
    h1 { margin:0 0 14px; }
    ul { margin:0; padding-left:20px; }
    .ok { color:#22c55e; }
    .err { color:#f87171; }
    .muted { color:#9aa3af; font-size:13px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="panel">
      <?php if (!$DB_CONNECTED) { ?>
        <h1>Database connection error</h1>
        <p class="err"><?php echo htmlspecialchars($DB_ERROR ?: 'Unable to connect with the config provided.'); ?></p>
        <p class="muted">Update <code>includes/config.php</code> with working credentials and rerun this setup script.</p>
      <?php } else { ?>
        <h1>Database connected</h1>
        <p class="ok">Connection established. Creating tables if they do not exist.</p>
        <ul>
          <?php foreach ($tables as $name => $sql): ?>
            <?php
              $result = run_sql($conn, $sql);
              if ($result) {
                $message = "Table '{$name}' ready.";
                $class = 'ok';
              } else {
                $message = "Failed to create '{$name}': " . mysqli_error($conn);
                $class = 'err';
              }
            ?>
            <li class="<?php echo $class; ?>"><?php echo htmlspecialchars($message); ?></li>
          <?php endforeach; ?>
        </ul>
        <p class="muted">Once this succeeds you can optionally run <a href="<?php echo htmlspecialchars(base_url('seed.php')); ?>">seed.php</a> to populate demo data.</p>
      <?php } ?>
    </div>
  </div>
</body>
</html>


