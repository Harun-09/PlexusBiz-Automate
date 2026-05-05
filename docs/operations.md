# Operations Runbook

## Queue Workers

Use a real queue driver outside local development.

```bash
php artisan queue:table
php artisan migrate
```

Recommended production environment:

```env
QUEUE_CONNECTION=database
```

Run workers under Supervisor, systemd, or the platform process manager:

```bash
php artisan queue:work --queue=default --sleep=3 --tries=3 --backoff=30 --max-time=3600
```

Restart workers after deployment:

```bash
php artisan queue:restart
```

## Scheduler

Marketing, social, cart, and workflow maintenance automation use Laravel's scheduler through `app/Console/Kernel.php`.

For a long-running server daemon, prefer `schedule:work` under Supervisor, systemd, or your platform process manager:

```bash
php artisan schedule:work
```

Cron entry:

```cron
* * * * * cd /path/to/PlexusBiz-Automate && php artisan schedule:run >> /dev/null 2>&1
```

Local/manual check:

```bash
php artisan schedule:list
php artisan schedule:work
php artisan schedule:run
php artisan campaigns:send-scheduled
php artisan carts:check-abandoned
php artisan social-posts:publish-due
php artisan workflow:close-stale-runs
```

## Deployment Checklist

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan down
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan queue:restart
php artisan up
```

Verify after deployment:

```bash
php artisan about
php artisan route:list --path=api/v1 --except-vendor
php artisan test --testsuite=Feature
```

## Render Docker Deployment

This repo can deploy to Render as a Docker web service. Render builds the `Dockerfile`, serves Apache/PHP from the container, and expects the app to bind to the `PORT` environment variable.

Repository files:

```text
Dockerfile
render.yaml
docker/apache-vhost.conf
docker/entrypoint.sh
docker/php.ini
```

Render setup:

```bash
php artisan key:generate --show
```

Set these Render environment variables:

```env
APP_KEY=base64:...
APP_URL=https://your-service.onrender.com
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
DATABASE_URL=<Render Postgres internal connection string>
LOG_CHANNEL=stderr
RUN_MIGRATIONS=true
```

The Docker entrypoint caches config, routes, views, and events at container start. It also runs migrations only when `RUN_MIGRATIONS=true`.

Render Blueprint notes:

- Use `render.yaml` from the repo root.
- The web service uses `runtime: docker`.
- The health check path is `/healthz`.
- The bundled database is named `plexusbiz-db`.
- Uploaded public media is mounted at `/var/www/html/storage/app/public` through the `plexusbiz-storage` persistent disk.
- After the first deploy, update `APP_URL` to the assigned Render URL.

## Backups

MySQL dump:

```bash
mysqldump -u "$DB_USERNAME" -p "$DB_DATABASE" > backups/plexusbiz-$(date +%Y%m%d-%H%M%S).sql
```

Storage archive:

```bash
tar -czf backups/storage-$(date +%Y%m%d-%H%M%S).tar.gz storage/app
```

Restore database:

```bash
mysql -u "$DB_USERNAME" -p "$DB_DATABASE" < backups/plexusbiz-YYYYMMDD-HHMMSS.sql
```

## Integration Credentials

Keep real provider credentials in `.env` only. The application defaults to mock providers for email, SMS, Facebook, and Instagram so tests and local development do not call external services.
