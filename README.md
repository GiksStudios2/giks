# Giks — Basic PHP Web Interface (prototype)

This is a small, self-contained PHP interface added as a starting point.

Files added
- index.php — single-entry router and bootstrap for pages
- pages/header.php, pages/footer.php — shared layout
- pages/dashboard.php — simple dashboard showing site info
- pages/settings.php — edit and persist settings to `data/settings.json`
- assets/style.css — tiny custom CSS
- data/settings.json — initial settings file

How to use
1. Copy or place the files into your repository's public web root.
2. Ensure the `data` directory is writable by the web server so settings can be saved:
   - On Linux: `chmod -R 755 data` and ensure correct owner (e.g., `www-data`).
3. Visit `http://your-site/path/index.php` (or configure your web server so index.php is served).
4. Use `?page=settings` to change the site title and admin email.

Notes and next steps
- This is intentionally minimal and meant as an editable starting point.
- If you'd like, I can:
  - Add authentication (login/password) before settings can be changed.
  - Add routing using pretty URLs (.htaccess / routing).
  - Add more admin pages (file manager, user management).
  - Integrate into your repository directly on a branch (I can push if you want).