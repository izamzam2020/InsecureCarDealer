<?php
require __DIR__ . '/includes/config.php';

$file = isset($_GET['file']) ? $_GET['file'] : '';
$basePath = __DIR__ . '/documents';
$fullPath = $basePath . '/' . $file;

// Basic path traversal protection (but still vulnerable for training)
if (strpos($fullPath, $basePath) !== 0 || !is_file($fullPath)) {
  http_response_code(404);
  die('File not found');
}

$filename = basename($fullPath);
$size = filesize($fullPath);

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . $size);
header('Cache-Control: no-cache');

readfile($fullPath);
?>
