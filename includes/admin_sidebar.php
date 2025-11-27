<?php
// admin_sidebar.php - Admin navigation sidebar
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="card sidebar" style="padding:0; overflow:hidden;">
  <div class="card__body">
    <nav class="sidebar-nav">
      <a href="/carShop/admin.php" class="<?php echo $current_page === 'admin' ? 'active' : ''; ?>">Dashboard</a>
      <a href="/carShop/search.php" class="<?php echo $current_page === 'search' ? 'active' : ''; ?>">Search</a>
      <a href="/carShop/resources.php" class="<?php echo $current_page === 'resources' ? 'active' : ''; ?>">Resources</a>
      <a href="/carShop/add_user.php" class="<?php echo $current_page === 'add_user' ? 'active' : ''; ?>">Add Users</a>
    </nav>
  </div>
</aside>
