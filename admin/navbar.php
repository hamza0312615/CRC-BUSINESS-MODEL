<?php
// admin/navbar.php
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="../dashboard.php">Admin Panel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="../drivers/index.php">Drivers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../cars/index.php">Cars</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../maintenance/index.php">Maintenance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../wages/index.php">Wages</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../../logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>