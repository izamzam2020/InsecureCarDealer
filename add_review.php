<?php
require __DIR__ . '/includes/config.php';
header('Content-Type: application/json; charset=utf-8');

if (!$DB_CONNECTED) {
	echo json_encode(['success' => false, 'error' => 'db']);
	exit;
}

// UNSAFE on purpose for testing: no sanitization, no CSRF, no auth
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
if ($rating < 1) $rating = 1;
if ($rating > 5) $rating = 5;
$title = isset($_POST['title']) ? $_POST['title'] : '';
$body = isset($_POST['body']) ? $_POST['body'] : '';

// UNSAFE upload handling: no checks, move to images/uploads with original name
$imagePath = '';
if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'] !== '') {
	$uploadDir = __DIR__ . '/images/uploads';
	@mkdir($uploadDir, 0777, true);
	$destName = basename($_FILES['image']['name']);
	$destPath = $uploadDir . '/' . $destName;
	@move_uploaded_file($_FILES['image']['tmp_name'], $destPath);
	$imagePath = base_url('images/uploads/' . $destName);
}

// Fixed customer_name for demo; could be a free text as well (unsanitized)
$customerName = 'Guest';
$createdAt = date('Y-m-d H:i:s');

$sql = "INSERT INTO reviews (customer_name, rating, title, body, image, created_at) VALUES ('$customerName', $rating, '$title', '$body', '$imagePath', '$createdAt')";
$ok = mysqli_query($conn, $sql);

if ($ok) {
	echo json_encode(['success' => true]);
} else {
	echo json_encode(['success' => false, 'error' => 'insert']);
}
