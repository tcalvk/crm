# AGENTS.md

## Repo shape
- Root is a direct PHP/XAMPP app, not a framework: `index.php` handles unauthenticated login/signup/landing routing; authenticated feature folders (`customer/`, `contract/`, `files/`, etc.) use `index.php?action=...` controllers and include local view files.
- Shared DB access lives in `model/*_db.php`; every model expects `model/database.php` to have been required first.
- `react_frontend/` is only the Vite/React landing page. Its build output is `../landing/`, which is the PHP app's public landing route.
- Ignore `vendor/` and `react_frontend/node_modules/` when inferring project style.

## Commands
- PHP deps/metadata: `composer install`; `composer validate --no-check-publish`.
- There are no Composer test/lint scripts and no root PHPUnit config. For focused PHP verification, run `php -l path/to/file.php` on touched PHP files.
- Composer's generated platform check requires PHP `>= 8.4.1`; use a CLI PHP that satisfies it before running scripts that load `vendor/autoload.php`.
- DB migrations use Phinx: `vendor/bin/phinx migrate` (or `vendor/bin/phinx rollback`). `phinx.php` defaults to `APP_ENV=development`, DB `crm_dev` on `127.0.0.1`, user `root`; the live app DB connection is separately hard-coded in `model/database.php` to `crm54` via XAMPP socket with TCP fallback.
- Landing page commands run from `react_frontend/`: `npm install`, `npm run dev` (Vite on port 3000), `npm run build` (empties and rewrites tracked `landing/`).

## Runtime/config gotchas
- `.gitignore` excludes `config/`, `credentials/`, `.env.local`, `.env.production`, `statements/`, and frontend build scratch dirs; do not add new secrets or generated statement PDFs.
- Stripe code expects `config/stripe_config.php` or `config/stripe_dev.php` with `stripe_secret_key`; webhook/automation paths currently require `config/stripe_dev.php` and `webhook_secret` directly.
- GCS uploads in `files/index.php` look for `gcs_upload_signer.json` in `.credentials/`, `credentials/`, then `/etc/gcp/`.
- Statement scripts (`run_statements_1.php`, `run_statements_15.php`, `statementautoreceive.php`, overdue scripts) generate PDFs, send email, and mutate DB. The `*_test.php` variants still send email and write DB rows; they are not harmless unit tests.
- Auto-payments: `php automation/run_auto_payments.php --run_mode=dev|prod`; `dev` selects test statements but still creates Stripe PaymentIntents using local Stripe config.

## Workflow conventions
- New PHP feature work should follow the existing pattern: feature-folder `index.php` action switch, `model/*_db.php` PDO queries, and included view files.
- Preserve access checks before loading records: session `logged_in`, superuser checks, and owner checks (`userId`) with `view/record_access_error.php` on denial.
- Frontend Vite config uses `base: './'` so `landing/` works from a subdirectory, and has Figma-export-style aliases like `react-hook-form@7.55.0`; do not remove these aliases unless imports are updated.
- GitHub Actions do not run tests: push to `main` SSHes to the server and runs `/var/www/update-crm.sh`; `test-ssh.yml` is manual only.

## SKILLS
- project_ideate_skill: When the user asks for you to come up with or ideate on some new projects, instruct the agent to follow the instructions in this skill EXACTLY. 
