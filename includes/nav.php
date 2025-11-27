<?php // nav.php ?>
<?php
$currentPath = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
$base = $currentPath !== '' ? basename($currentPath) : '';
$isHome = ($base === '' || $base === 'index.php');
$isReviews = ($base === 'reviews.php');
$isAdmin = ($base === 'admin.php');
$isLogin = ($base === 'login.php');
?>
<header class="site-header">
  <div class="container nav-container">
    <a href="<?php echo htmlspecialchars(base_url()); ?>" class="brand">Speedy<span>Motors</span></a>
    <nav class="top-nav" id="topNav">
      <a href="<?php echo htmlspecialchars(base_url()); ?>"<?php echo $isHome ? ' class="active"' : ''; ?>>Home</a>
      <a href="<?php echo htmlspecialchars(base_url('reviews.php')); ?>"<?php echo $isReviews ? ' class="active"' : ''; ?>>Reviews</a>
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) { ?>
        <a class="<?php echo $isAdmin ? 'cta active' : 'cta'; ?>" href="<?php echo htmlspecialchars(base_url('admin.php')); ?>">Admin</a>
        <a href="<?php echo htmlspecialchars(base_url('login.php?action=logout')); ?>">Logout</a>
      <?php } else { ?>
        <a class="<?php echo $isLogin ? 'cta active' : 'cta'; ?>" href="<?php echo htmlspecialchars(base_url('login.php')); ?>">Login</a>
      <?php } ?>
    </nav>
    <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="nav-underline"></div>
</header>


