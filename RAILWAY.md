# Deploying Folio to Railway with Supabase

The app uses Railway's Dockerfile builder. The Dockerfile builds the Vite assets,
installs PHP dependencies, applies database migrations, and starts Apache on
Railway's assigned `PORT`. Railway checks the Laravel `/up` endpoint.

## Supabase setup

1. Create a Supabase project and use its **Session pooler** connection details
   for Railway. Set the `DB_*` values from Supabase's connection parameters and
   use `DB_SSLMODE=require`.
2. Create a public Storage bucket named `portfolio-photos`.
3. Enable S3 access in Supabase Storage and create an access key. Note the
   project URL and S3 region as well as the access key ID and secret.

## Railway setup

Create a Railway project from this GitHub repository, select the Singapore
region when available, and generate a public domain. Set these variables on the
Laravel app service:

```text
APP_NAME=Folio
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:<generated Laravel key>
APP_URL=https://<your-domain>.up.railway.app
LOG_CHANNEL=stderr

DB_CONNECTION=pgsql
DB_HOST=<Supabase session-pooler host>
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.<your-project-ref>
DB_PASSWORD=<rotated Supabase database password>
DB_SSLMODE=require

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database

PHOTO_DISK=supabase
SUPABASE_URL=https://<your-project-ref>.supabase.co
SUPABASE_BUCKET=portfolio-photos
SUPABASE_S3_REGION=<Supabase Storage S3 region>
SUPABASE_S3_KEY=<Supabase S3 access key ID>
SUPABASE_S3_SECRET=<Supabase S3 secret access key>
```

Generate the app key locally with PHP 8.3 or newer:

```powershell
php artisan key:generate --show
```

Copy the output directly into Railway's `APP_KEY` variable. Keep it secret and
stable; do not commit `.env`, database passwords, or storage credentials.
Rotate any credential that has been exposed.

Deploy the service after setting the variables. The startup command runs
`php artisan migrate --force`; the migrations create the users, portfolios,
cache, queue, and session tables. Portfolio details are stored as JSON columns
on the `portfolios` table; this app does not use separate education, skills,
projects, experience, or links tables.

## Photo uploads

The portfolio form accepts a JPEG, PNG, GIF, or WebP image up to 5 MB, or an
HTTPS image URL. Uploaded image objects are stored in the configured photo
disk, and portfolio pages use their public URLs. With `PHOTO_DISK=supabase`,
the configured Supabase bucket must be public and the S3 access credentials
must have permission to upload and delete objects.

## Verify the deployment

1. Confirm the Railway deployment becomes **Active** and `/up` returns HTTP 200.
2. Register, create a portfolio, upload a photo, and verify the image in
   Supabase Storage and the portfolio row in the `portfolios` table.
3. Preview and share the portfolio, then test editing and deleting it.

For a startup or health-check failure, inspect Railway's deployment logs. Do
not enable `APP_DEBUG` on a public production deployment.
