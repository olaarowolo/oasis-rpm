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

## 4. First-time server setup

Run the full setup only once on a new server or new app install:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Notes:

- Do not run `php artisan key:generate --force` on an existing live install. Only generate an app key once, before the app is in use.
- This project does not currently require a Node build step for deployment.

## 5. Routine deploy for small code changes

Use this path for normal updates where dependencies did not change and no new migration was added.

```bash
git pull origin main
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

This is the fastest safe deploy path for commits like:

- `84c48e8` Add student portal and super admin workflow updates
- `2d891ee` Add OTP mail delivery diagnostics

## 6. Deploy when database schema changed

If the pulled commit includes files under `database/migrations/`, run the routine deploy commands above and add:

```bash
php artisan migrate --force
```

Run `composer install --no-dev --optimize-autoloader` only when `composer.json` or `composer.lock` changed.

## 7. Frontend assets

This project currently loads Tailwind from CDN and serves local static assets from `public/`, so no Node build step is required for the current UI.

## 8. Final checks

- Verify login pages load without exposing `/public` in the URL.
- Verify file uploads create files under `storage/app/public` and open through `/storage/...` URLs.
- Verify outgoing mail with the configured SMTP provider.
- Confirm `storage/logs/laravel.log` is writable.

## 9. Quick shared-hosting deploy checklist

Use this order to avoid long delays during routine production updates:

1. Pull the latest code.
2. Run `composer install --no-dev --optimize-autoloader` only if PHP dependencies changed.
3. Run `php artisan migrate --force` only if new migrations were deployed.
4. Clear and rebuild Laravel caches.
5. Test one login and one email-triggering flow.
6. If email is part of the release, inspect `storage/logs/laravel.log` immediately after the test.
