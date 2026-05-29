# TruthLens

Web application for checking articles against published fact-check databases and collecting community credibility votes.

## Requirements

- PHP 8.2+
- Composer
- MySQL (or MariaDB)
- Google Fact Check Tools API key (for automated scoring)

## Installation

1. Copy `.env.example` to `.env` and configure database credentials.
2. Set `GOOGLE_FACT_CHECK_API_KEY` in `.env`.
3. Run:

```bash
composer install
php artisan key:generate
php artisan migrate --seed
```

The seed step creates roles, credibility badges, and two local test accounts:

| Role  | Email                   | Password   |
|-------|-------------------------|------------|
| Admin | admin@truthlens.local   | `password` |
| User  | user@truthlens.local    | `password` |

Change or remove these accounts before production deployment.

4. Point the web server document root at `public/`.

For local development with Laravel’s built-in server:

```bash
php artisan serve
```

Use `QUEUE_CONNECTION=sync` in `.env` for immediate analysis, or run `php artisan queue:work` when using the database queue driver.

## License

Proprietary — all rights reserved unless otherwise agreed with the client.
