<?php
// Settings page: simple POST to update settings.json
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF-like token (session)
    if (!isset($_POST['_token']) || !hash_equals($_SESSION['_token'] ?? '', $_POST['_token'])) {
        $errors[] = 'Invalid form submission.';
    } else {
        $site_title = trim($_POST['site_title'] ?? '');
        $admin_email = trim($_POST['admin_email'] ?? '');

        if ($site_title === '') {
            $errors[] = 'Site title cannot be empty.';
        }
        if ($admin_email === '' || !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Provide a valid admin email.';
        }

        if (empty($errors)) {
            $new = [
                'site_title' => $site_title,
                'admin_email' => $admin_email
            ];
            $written = file_put_contents(SETTINGS_FILE, json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            if ($written === false) {
                $errors[] = 'Failed to save settings. Check file permissions.';
            } else {
                $success = true;
                // update in-memory settings for immediate feedback
                $settings = array_merge($settings, $new);
            }
        }
    }
}

// create (or rotate) CSRF token
if (empty($_SESSION['_token'])) {
    $_SESSION['_token'] = bin2hex(random_bytes(16));
}
?>
<div class="card">
  <div class="card-body">
    <h3 class="card-title">Settings</h3>

    <?php if ($success): ?>
        <div class="alert alert-success">Settings saved.</div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="row g-3">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($_SESSION['_token']); ?>">
      <div class="col-md-6">
        <label class="form-label">Site title</label>
        <input class="form-control" name="site_title" value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Admin email</label>
        <input class="form-control" name="admin_email" type="email" value="<?php echo htmlspecialchars($settings['admin_email'] ?? ''); ?>" required>
      </div>

      <div class="col-12">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="?page=dashboard" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
