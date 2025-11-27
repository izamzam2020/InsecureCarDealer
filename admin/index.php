<?php
// Soft 404 page for /admin that returns HTTP 200
http_response_code(200);
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Page not found</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 40px; background: #fafafa; color: #222; }
    .wrap { max-width: 720px; margin: 0 auto; background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    h1 { margin: 0 0 12px; font-size: 28px; }
    p { margin: 8px 0; line-height: 1.5; }
    a { color: #0b5ed7; text-decoration: none; }
    a:hover { text-decoration: underline; }
  </style>
  <meta name="robots" content="noindex, nofollow">
</head>
<body>
  <div class="wrap">
    <h1>Page not found</h1>
    <p>Sorry, the page you requested does not exist here.</p>
    <p>This is a custom not found page served with a 200 status code.</p>
    <p><a href="/carShop/">Return to home</a></p>
  </div>
</body>
</html>


