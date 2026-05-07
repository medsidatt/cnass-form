# CNASS — Fiche de Situation Familiale

Web application for collecting employee family-situation files (national IDs and
photographs) for CNASS (Caisse Nationale d'Assurance Maladie) enrollment in
Mauritania. Submitters identify themselves by WhatsApp OTP; an admin dashboard
allows back-office review and Excel export.

Built on Laravel 13 (PHP ≥ 8.3).

---

## Features

- **WhatsApp OTP authentication** (Twilio Messages API).
- **Multi-step submission form** with employee, parents, spouse, siblings, and
  descendants — including ID/photo file uploads.
- **Resubmission flow**: a verified phone re-opens its existing record for
  edits.
- **Admin dashboard** at `/admin` (password-gated): list, view, per-record
  Excel download, and bulk Excel export.
- **Authenticated file serving** — uploads are not exposed at predictable
  public paths; access is checked per-request.

---

## Requirements

- PHP **≥ 8.3** with extensions: `mbstring`, `openssl`, `pdo`, `tokenizer`,
  `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd` (or `imagick`), `zip`.
- **Composer 2.x**.
- **Node.js 18+** (only if you customize the frontend build).
- A SQL database (SQLite OK for low traffic, MySQL/PostgreSQL recommended for
  production).
- A **Twilio account** with WhatsApp messaging enabled.

---

## Local development

```bash
git clone <repo> cnass-form
cd cnass-form
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite       # if using SQLite
php artisan migrate
php artisan serve
```

Without Twilio credentials the OTP step runs in **dev mode** — any phone is
accepted and the verification code is hard-coded to `123456`.

To exercise the real WhatsApp flow locally, set `TWILIO_SID`,
`TWILIO_AUTH_TOKEN` and `TWILIO_WHATSAPP_FROM` (the Twilio sandbox sender
`whatsapp:+14155238886` works once you join the sandbox from the test handset).

---

## Production deployment

### 1. Provision the server

Recommended target: a Linux VM with nginx + PHP-FPM 8.3, behind HTTPS (Let's
Encrypt or another TLS source). The application document root must be the
`public/` directory.

### 2. Install code & dependencies

```bash
git clone <repo> /var/www/cnass
cd /var/www/cnass
composer install --no-dev --optimize-autoloader
```

### 3. Environment file

```bash
cp .env.production.example .env
# edit .env and fill in every __REPLACE_ME__ value:
#   APP_KEY        → run `php artisan key:generate --force`
#   APP_URL        → https://your-domain
#   DB_*           → production database credentials
#   TWILIO_*       → live Twilio credentials (NOT the sandbox)
#   ADMIN_PASSWORD → long random string (e.g. `openssl rand -base64 32`)
```

`SESSION_SECURE_COOKIE=true` is mandatory in production (the template already
sets it).

### 4. Initialize the database

```bash
php artisan migrate --force
```

Migrations create the `submissions`, `sessions`, `cache`, and `jobs` tables.

### 5. Cache configuration & routes

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Re-run these after every deployment.

### 6. File permissions

The web server user (e.g. `www-data`) must own:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 7. Do NOT run `php artisan storage:link`

Uploaded ID/photo files live in `storage/app/public/submissions/`. In production
they are intentionally **not** symlinked into `public/`. All access goes through
authenticated routes (`/files/{submission}/{key}`) so files are never reachable
by a guessable URL.

### 8. nginx configuration sketch

```nginx
server {
    server_name your-domain;
    listen 443 ssl http2;

    root /var/www/cnass/public;
    index index.php;

    client_max_body_size 12M;        # uploads are capped at 10 MB per file

    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "DENY" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Block direct access to the storage symlink even if someone creates it.
    location ^~ /storage/ { return 404; }
}
```

### 9. Health check

The application exposes `GET /up`, configured in `bootstrap/app.php`. Point
your uptime monitor at it; a 200 response means the framework booted and the
config cache is valid.

---

## Operations

### Admin access

Visit `/admin`. The password is whatever you set in `ADMIN_PASSWORD`. Use the
**Déconnexion** button on the dashboard to end the session.

### Twilio: sandbox vs. production

- **Sandbox** (`whatsapp:+14155238886`): only delivers to phones that have
  joined your sandbox by sending the join code. Fine for QA, **not** for real
  users.
- **Production**: register and verify a WhatsApp Business sender in the Twilio
  Console, then set `TWILIO_WHATSAPP_FROM=whatsapp:+222XXXXXXXX`.

If `TWILIO_SID` or `TWILIO_AUTH_TOKEN` is empty the application falls back to
dev mode (`123456` accepted as the OTP) — never deploy with empty credentials.

### Rate limits

Configured in `app/Providers/AppServiceProvider.php`:

| Endpoint              | Limit                                                                |
|-----------------------|----------------------------------------------------------------------|
| `POST /verify/send`   | 3/min, 20/hr, 60/day per phone · 30/min, 200/hr per IP               |
| `POST /verify/check`  | 8/min, 40/hr per phone                                               |
| `POST /submit`        | 6/min per verified phone                                             |

OTP codes are valid for **15 minutes** (`OTP_TTL_MINUTES`), and per-session
attempts are capped at **5** (`MAX_ATTEMPTS`) before the code is invalidated.

### Backups

At minimum, schedule daily backups of:

- The application database (sessions table can be excluded).
- `storage/app/public/submissions/` — the uploaded ID & photo files.

### Logs

Configured to `daily` rotating files under `storage/logs/` in
`.env.production.example`. Submission saves and Twilio failures are logged at
`info` and `warning` levels.

### Rotating Twilio credentials

If credentials leak, rotate them in the Twilio Console then update the
production `.env` and run `php artisan config:cache`.

---

## Security checklist before go-live

- [ ] `APP_DEBUG=false` and `APP_ENV=production` in `.env`.
- [ ] HTTPS enforced; `SESSION_SECURE_COOKIE=true`.
- [ ] `APP_KEY` generated (`php artisan key:generate --force`).
- [ ] `ADMIN_PASSWORD` set to a strong random string.
- [ ] Real Twilio credentials and a non-sandbox `TWILIO_WHATSAPP_FROM`.
- [ ] No `public/storage` symlink in production.
- [ ] Daily database + uploads backup scheduled.
- [ ] Server hardened (firewall, automatic security updates).
- [ ] `php artisan config:cache route:cache view:cache` run after deploy.
- [ ] `/up` health check wired to your monitoring.

---

## License

Proprietary — internal use for CNASS enrollment.
