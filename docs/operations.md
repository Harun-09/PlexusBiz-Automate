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

## Server Deployment Steps

If you are deploying to a production server, keep the app in Docker so the invoice preview and download PDFs are generated with the same A4 layout everywhere.

1. Prepare the production environment file.

```bash
cp .env.example .env
php artisan key:generate --show
```

2. Set production values before you deploy.

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_CONNECTION=pgsql
DATABASE_URL=<your-production-database-connection-string>
LOG_CHANNEL=stderr
RUN_MIGRATIONS=true
```

3. Build the Docker image from the repository root.

```bash
docker build -t plexusbiz-automate .
```

4. Run the container on the server and expose port `10000`.

```bash
docker run -d --name plexusbiz-automate -p 80:10000 --env-file .env plexusbiz-automate
```

5. Confirm the application is healthy after startup.

```bash
curl https://your-domain.com/healthz
```

6. Verify that invoice preview and download both open the same PDF.

```text
/invoices
/invoices/{invoiceId}/preview
/invoices/{invoiceId}/download
```

7. After first boot, keep queue workers and scheduler running separately if your server uses background jobs.

```bash
php artisan queue:work --queue=default --sleep=3 --tries=3 --backoff=30 --max-time=3600
php artisan schedule:work
```

## Docker Compose Run

Use `docker-compose.yml` when you want a simple local Docker stack with the app and MySQL in one command.

1. Copy the environment file and generate an app key if you do not already have one.

```bash
cp .env.example .env
php artisan key:generate --show
```

2. Make sure the application key and database credentials are available in `.env`.

```env
APP_KEY=base64:...
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=plexus_biz_automate
DB_USERNAME=plexusbiz
DB_PASSWORD=plexusbizpassword
```

3. Start the stack from the repository root.

```bash
docker compose up -d --build
```

4. Open the app in your browser.

```text
http://localhost:8000
```

5. If you need to run a one-off command inside the container, use the app service.

```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan tinker
```

6. Stop the stack when you are done.

```bash
docker compose down
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
