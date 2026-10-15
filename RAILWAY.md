# Deploying to Railway

This project is configured to deploy with its Dockerfile. The container builds
the Vite assets, runs database migrations, and starts Apache on Railway's
assigned `PORT`. Railway checks the Laravel `/up` health endpoint before
marking the deployment healthy.

## Create the Railway service

1. Push this project to GitHub and create a Railway project from that
   repository. Railway will use `railway.json` and `Dockerfile` automatically.
2. Add a PostgreSQL database to the Railway project, or use a separately
   managed PostgreSQL database.
3. Add these variables to the Laravel application service:

   ```text
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-public-domain
   APP_KEY=base64:<generated Laravel key>
   DB_CONNECTION=pgsql
   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=database
   ```

4. Connect the database using either `DB_URL` or the individual `DB_HOST`,
   `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` variables.
   For a Railway PostgreSQL service, reference its `DATABASE_URL` using
   Railway's variable-reference picker. For an external provider, use that
   provider's connection details and required SSL mode.
5. Generate `APP_KEY` locally with `php artisan key:generate --show` using PHP
   8.3 or newer, then save the output as the Railway `APP_KEY` variable. Do not
   commit the key or change it after deployment.
6. Deploy the service. The container runs `php artisan migrate --force` before
   starting Apache.

The database needs to be reachable by the app service during startup. Keep
`.env` local and enter production secrets only in Railway's Variables settings.
