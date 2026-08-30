#!/bin/sh
set -e

PORT="${PORT:-10000}"

# The persistent disk (if attached) mounts fresh on every boot with
# ownership the image can't predict — fix it before the app writes to it.
mkdir -p storage/app/public
chown -R www-data:www-data storage bootstrap/cache

# APP_KEY is required for sessions/encryption. It should be set once as a
# persistent env var on Render (so it survives redeploys) — but if it's
# genuinely missing, generate one so the app doesn't hard-crash on first
# boot, and print it loudly so it can be copied into Render's dashboard.
if [ -z "$APP_KEY" ]; then
    APP_KEY="$(php artisan key:generate --force --show)"
    export APP_KEY
    echo "=================================================================="
    echo "WARNING: APP_KEY was not set. Generated a temporary one for this boot:"
    echo "  APP_KEY=${APP_KEY}"
    echo "Copy it into Render's environment variables as APP_KEY so it stays"
    echo "the same across deploys — otherwise every redeploy invalidates"
    echo "existing sessions and any encrypted data."
    echo "=================================================================="
fi

# Safe to run on every boot — no-ops if already linked/migrated.
php artisan storage:link || true
php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting server on 0.0.0.0:${PORT}"
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
