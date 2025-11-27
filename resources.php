<?php
require __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';

$path = isset($_GET['path']) ? $_GET['path'] : '';
$searchFile = isset($_POST['search']) ? $_POST['search'] : '';
$basePath = __DIR__ . '/documents';
$fullPath = $basePath . '/' . $path;

// Basic path traversal protection (but still vulnerable for training)
if (strpos($fullPath, $basePath) !== 0) {
  $fullPath = $basePath;
  $path = '';
}

// File/directory search - DELIBERATELY VULNERABLE to path traversal
$searchResult = '';
$searchError = '';
if ($searchFile !== '') {
  // Remove leading slash and resolve relative to app root
  $cleanPath = ltrim($searchFile, '/\\');
  $targetPath = __DIR__ . '/' . $cleanPath;
  
  if (is_file($targetPath)) {
    // Display file contents
    $searchResult = @file_get_contents($targetPath);
    if ($searchResult === false) {
      $searchError = "Could not read file: $targetPath";
    }
  } elseif (is_dir($targetPath)) {
    // List directory contents
    $files = @scandir($targetPath);
    if ($files === false) {
      $searchError = "Could not read directory: $targetPath";
    } else {
      $listing = [];
      foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $fullItemPath = $targetPath . '/' . $file;
        $type = is_dir($fullItemPath) ? '[DIR]' : '[FILE]';
        $listing[] = "$type $file";
      }
      $searchResult = implode("\n", $listing);
      if (empty($listing)) {
        $searchResult = "Directory is empty";
      }
    }
  } else {
    $searchError = "Path not found: $targetPath";
  }
}

$items = [];
$error = '';
if (is_dir($fullPath)) {
  $files = scandir($fullPath);
  foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    $itemPath = $fullPath . '/' . $file;
    $relativePath = $path ? $path . '/' . $file : $file;
    $items[] = [
      'name' => $file,
      'path' => $relativePath,
      'is_dir' => is_dir($itemPath),
      'size' => is_file($itemPath) ? filesize($itemPath) : 0,
      'modified' => filemtime($itemPath)
    ];
  }
} else {
  $error = 'Directory not found';
}

function formatSize($bytes) {
  if ($bytes < 1024) return $bytes . ' B';
  if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
  return round($bytes / 1048576, 1) . ' MB';
}
?>

<main class="container" style="padding: 28px 0 60px;">
  <section class="reveal admin-layout">
    <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>
    <div>
      <div class="section-header">
        <h2>Document Resources</h2>
        <p class="muted" style="color:#9aa3af;">Browse company documents and files.</p>
      </div>
      
      <!-- File Search Form -->
      <div class="card" style="padding:16px; margin-top:12px;">
        <div class="card__body">
          <h3>File Search</h3>
          <form method="post" action="/carShop/resources.php<?php echo $path ? '?path=' . urlencode($path) : ''; ?>" style="display:flex; gap:10px; align-items:center; margin-top:8px;">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchFile); ?>" placeholder="Enter path (e.g., documents found in /documents)" style="flex:1; padding:10px; background:#0f1216; border:1px solid #1f2430; border-radius:10px; color:#e5e7eb;">
            <button class="btn btn--primary" type="submit">Find File</button>
          </form>
        </div>
      </div>

      <!-- Search Results -->
      <?php if ($searchFile !== '') { ?>
        <div class="card" style="padding:16px; margin-top:16px; <?php echo $searchError ? 'border:1px solid #f87171;' : 'border:1px solid #22c55e;'; ?>">
          <div class="card__body">
            <h3 style="color:<?php echo $searchError ? '#f87171' : '#22c55e'; ?>;">
              <?php echo $searchError ? 'Error' : 'File Contents'; ?>
            </h3>
            <?php if ($searchError) { ?>
              <p style="color:#9aa3af;"><?php echo $searchError; ?></p>
            <?php } else { ?>
              <p style="color:#9aa3af; margin-bottom:8px;">File: <?php echo htmlspecialchars($searchFile); ?></p>
              <pre style="background:#0a0c0f; padding:12px; border-radius:8px; color:#e5e7eb; font-family:monospace; font-size:13px; line-height:1.4; max-height:400px; overflow-y:auto; white-space:pre-wrap;"><?php echo htmlspecialchars($searchResult); ?></pre>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
      
      <?php if ($path) { ?>
        <div class="card" style="padding:12px; margin-top:12px;">
          <div class="card__body" style="display:flex; align-items:center; gap:8px;">
            <a href="/carShop/resources.php" style="color:#06b6d4; text-decoration:none;">📁 documents</a>
            <?php 
            $pathParts = explode('/', $path);
            $currentPath = '';
            foreach ($pathParts as $part) {
              $currentPath .= ($currentPath ? '/' : '') . $part;
              echo ' / <a href="/carShop/resources.php?path=' . urlencode($currentPath) . '" style="color:#06b6d4; text-decoration:none;">' . htmlspecialchars($part) . '</a>';
            }
            ?>
          </div>
        </div>
      <?php } ?>

      <?php if ($error) { ?>
        <div class="card" style="padding:16px; margin-top:16px; border:1px solid #f87171;">
          <div class="card__body">
            <h3 style="color:#f87171;">Error</h3>
            <p><?php echo htmlspecialchars($error); ?></p>
          </div>
        </div>
      <?php } else { ?>
        <div class="card" style="padding:0; margin-top:16px; overflow:hidden;">
          <div class="card__body" style="padding:0;">
            <table style="width:100%; border-collapse:collapse;">
              <thead>
                <tr style="background: rgba(255,255,255,.04); text-align:left;">
                  <th style="padding:12px; border-bottom:1px solid #1f2430;">Name</th>
                  <th style="padding:12px; border-bottom:1px solid #1f2430;">Size</th>
                  <th style="padding:12px; border-bottom:1px solid #1f2430;">Modified</th>
                  <th style="padding:12px; border-bottom:1px solid #1f2430;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!$items) { ?>
                  <tr><td colspan="4" style="padding:14px; color:#9aa3af;">No files found</td></tr>
                <?php } else { foreach ($items as $item) { ?>
                  <tr>
                    <td style="padding:12px; border-bottom:1px solid #1f2430;">
                      <?php if ($item['is_dir']) { ?>
                        <a href="/carShop/resources.php?path=<?php echo urlencode($item['path']); ?>" style="color:#06b6d4; text-decoration:none;">
                          📁 <?php echo htmlspecialchars($item['name']); ?>
                        </a>
                      <?php } else { ?>
                        📄 <?php echo htmlspecialchars($item['name']); ?>
                      <?php } ?>
                    </td>
                    <td style="padding:12px; border-bottom:1px solid #1f2430; color:#9aa3af;">
                      <?php echo $item['is_dir'] ? '-' : formatSize($item['size']); ?>
                    </td>
                    <td style="padding:12px; border-bottom:1px solid #1f2430; color:#9aa3af;">
                      <?php echo date('Y-m-d H:i', $item['modified']); ?>
                    </td>
                    <td style="padding:12px; border-bottom:1px solid #1f2430;">
                      <?php if (!$item['is_dir']) { ?>
                        <a href="/carShop/download.php?file=<?php echo urlencode($item['path']); ?>" class="btn btn--ghost" style="padding:4px 8px; font-size:12px;">Download</a>
                      <?php } ?>
                    </td>
                  </tr>
                <?php }} ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php } ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
