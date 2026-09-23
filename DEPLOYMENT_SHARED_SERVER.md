# Shared Server Deployment

This project is a Laravel 10 application. These steps assume Apache-based shared hosting.

## 1. Upload layout

Preferred layout:

- Put the full project outside `public_html` if your host allows it.
- Point the domain or subdomain document root to the project's `public/` directory.

Fallback layout for hosts that force the document root to the project root:

- Keep the root-level `.htaccess` and `index.php` files in this repository.
- They forward all web requests into `public/` so Laravel still boots correctly.

## 2. Required server settings

- PHP 8.2 or newer
- Apache `mod_rewrite` enabled
- `storage/` and `bootstrap/cache/` must be writable by the web server user

## 3. Production environment

Use production-safe values in `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.example
MAIL_FROM_NAME="${APP_NAME}"

SENDGRID_ENABLED=false
GEMINI_ENABLED=false
QUEUE_EMAILS=false
```

Notes:

- Set `FILESYSTEM_DISK=public` because uploaded archive documents are returned through `/storage/...` URLs.
- Enable `SENDGRID_ENABLED` or `GEMINI_ENABLED` only when the corresponding credentials are valid on the server.

## 4. Install and optimize

Run these commands after upload:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If the app key already exists in `.env`, skip `php artisan key:generate --force`.

## 5. Frontend assets

This project currently loads Tailwind from CDN and serves local static assets from `public/`, so no Node build step is required for the current UI.

## 6. Final checks

- Verify login pages load without exposing `/public` in the URL.
- Verify file uploads create files under `storage/app/public` and open through `/storage/...` URLs.
- Verify outgoing mail with the configured SMTP provider.
- Confirm `storage/logs/laravel.log` is writable.
