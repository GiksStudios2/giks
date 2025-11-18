<?php
// Dashboard page
?>
<div class="card">
  <div class="card-body">
    <h3 class="card-title">Dashboard</h3>
    <p class="card-text">Welcome to <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>.</p>
    <dl class="row">
      <dt class="col-sm-3">Site title</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($settings['site_title']); ?></dd>

      <dt class="col-sm-3">Admin email</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($settings['admin_email']); ?></dd>
    </dl>
    <a href="?page=settings" class="btn btn-primary">Edit settings</a>
  </div>
</div>
