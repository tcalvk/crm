# AD-456 Plan: Implement Phinx Migrations and Production DB Deployment

## Objective

Adopt Phinx as the source of truth for database schema changes in this repo, starting with a baseline migration that represents the current production/dev schema. After rollout, all future schema changes should be delivered through migrations and automatically applied to production as part of the post-merge deployment flow for `main`.

## Current Repo Observations

- The repo already has Composer enabled via `composer.json`.
- Database access is centralized in `model/database.php`, which is the best place to align application and migration configuration.
- GitHub Actions already deploys on `push` to `main` via `.github/workflows/remote-update.yml`, which calls `/var/www/update-crm.sh` on the server.
- The `plans/` directory exists and is currently empty.

## Scope

### In scope

- Add Phinx to the project and configure dev + production environments.
- Create an initial baseline migration from the existing schema.
- Define a repeatable developer workflow for future DB changes.
- Update the GitHub deployment flow so production migrations run after merges to `main`.
- Add verification, rollback, and operational safeguards for production DB changes.

### Out of scope

- Rebuilding historical database changes one-by-one.
- Syncing all mutable dev data across environments.
- Replacing the current app deployment mechanism beyond what is needed to run migrations safely.

## Recommended Implementation Strategy

### 1. Preflight checks and decisions

1. Verify PHP compatibility for both local XAMPP and production server runtimes (Phinx requires PHP 8.1+).
2. Decide how Phinx will be available in production:
   - **Recommended:** install Phinx as a normal Composer dependency if the production server will run `vendor/bin/phinx` from the deployed repo.
   - Alternative: install Phinx separately on the server, but this adds operational drift.
3. Confirm the canonical environment names to use in Phinx config:
   - `development`
   - `production`
4. Decide how DB credentials will be injected:
   - **Recommended:** move to environment-driven configuration for both the app and Phinx.
   - Do **not** store production DB credentials in the repo.

## Work Plan

### Phase 1 — Add Phinx scaffolding

Deliverables:

- `composer.json` updated with Phinx dependency
- `phinx.php` config file
- `db/migrations/`
- `db/seeds/`
- optional Composer scripts for common DB commands

Tasks:

1. Add Phinx via Composer.
2. Initialize Phinx in the repo.
3. Create a `phinx.php` config that reads environment variables and defines at least:
   - `development`
   - `production`
4. Point migration paths to `db/migrations` and seed paths to `db/seeds`.
5. Add developer-friendly Composer scripts if desired, for example:
   - `db:migrate`
   - `db:rollback`
   - `db:seed`
   - `db:status`

Notes:

- Prefer env-driven host/user/password/db name values.
- For local XAMPP, support whichever connection method is most reliable in CLI usage (typically TCP; socket support can be added if needed).

### Phase 2 — Baseline the existing schema

Deliverables:

- Structure-only export captured from phpMyAdmin for reference during authoring
- Initial migration representing the current schema
- Inventory of non-table DB objects that need special handling

Tasks:

1. Export the current database as **structure only** from phpMyAdmin.
2. Review the export and inventory the schema elements that must be represented in the baseline:
   - tables
   - columns and defaults
   - indexes
   - foreign keys
   - table engines/collations if relevant
3. Separately identify any objects that may require raw SQL or additional handling:
   - views
   - triggers
   - procedures/functions
   - events
4. Create a single baseline migration (for example `CreateInitialSchema`) that recreates the current schema in Phinx.
5. Prefer the Phinx table API where practical; use raw SQL only where needed.
6. If there is essential reference data required for the app to boot correctly, create targeted seeds for that data.

Important baseline rule:

- The baseline migration should represent the schema **as it exists now**.
- After it is merged, all future schema changes must be implemented as new incremental migrations.

### Phase 3 — Validate the baseline locally

Deliverables:

- Verified local migration flow
- Fresh database bootstrap test results

Tasks:

1. Create a fresh empty local database.
2. Run the baseline migration against the empty DB.
3. Compare the resulting schema against the structure-only export.
4. Fix mismatches until the migrated schema is functionally equivalent.
5. Test rollback behavior where safe and practical.
6. Confirm that the application can run against a DB built entirely from Phinx migrations (+ required seeds, if any).

Success criteria for this phase:

- A fresh database can be created from the repo alone.
- Another machine can pull the repo, configure DB credentials, run migrations, and get the correct schema.

### Phase 4 — Define the team/developer workflow

Deliverables:

- Short internal workflow documentation
- Rules for future schema changes

Tasks:

1. Document the required workflow for all future DB changes:
   - create migration
   - run locally
   - test application behavior
   - commit migration with app code
2. Document what belongs in seeds vs migrations:
   - migrations = schema changes and carefully controlled data transformations
   - seeds = baseline/dev/reference data only
3. Add a rule that manual phpMyAdmin schema edits are not considered complete until represented in a committed migration.
4. Encourage backward-compatible migration patterns for deploy safety.

Recommended deployment-safe rule:

- Use expand/contract style changes where possible.
- Example: add new nullable column first, deploy code that writes both formats, backfill if needed, then remove old column in a later release.

### Phase 5 — Integrate migrations into GitHub deployment

Deliverables:

- Updated GitHub Actions deployment workflow
- Updated server deploy script or equivalent migration step
- Production environment variables/secrets documented

Tasks:

1. Review the current deployment path in `.github/workflows/remote-update.yml` and `/var/www/update-crm.sh`.
2. Decide where the production migration should execute:
   - **Recommended:** on the production server, inside the existing deploy script after code update and Composer install.
3. Update the deployment flow so that after merge to `main`:
   - code is deployed/pulled
   - dependencies are installed
   - `vendor/bin/phinx migrate -e production` is executed
4. Ensure the workflow fails clearly if migrations fail.
5. Add concurrency protection so two production deploys cannot overlap.
6. Add logging/output capture so migration failures are diagnosable.

Recommended production order:

1. Back up production DB.
2. Pull/deploy latest code.
3. Install Composer dependencies needed for the release.
4. Run `vendor/bin/phinx migrate -e production`.
5. Run any post-deploy health check.

Implementation notes:

- If Phinx is only in `require-dev`, production installs with `--no-dev` will not have `vendor/bin/phinx` available.
- The workflow should use GitHub secrets and/or server environment variables for production DB access.
- Avoid injecting long-lived DB credentials directly into workflow files.

### Phase 6 — Production readiness and cutover

Deliverables:

- Deployment checklist
- Rollback guidance
- Initial production migration run plan

Tasks:

1. Define the first production rollout procedure for introducing Phinx.
2. Take a production backup before the first migration-managed deploy.
3. Run a no-surprises check before first cutover:
   - correct branch
   - correct DB target
   - correct credentials
   - baseline migration history table behavior understood (`phinxlog`)
4. Confirm who will respond if the deployment or migration fails.
5. Document rollback expectations:
   - application rollback
   - database restore procedure
   - when to use Phinx rollback vs full restore

## Risks and Mitigations

### Risk: baseline does not fully match the live schema

Mitigation:

- validate against a structure-only export
- test from a fresh empty database
- inventory special DB objects explicitly

### Risk: production deploy fails because Phinx is unavailable

Mitigation:

- decide dependency strategy up front
- verify `vendor/bin/phinx` exists in the deployed environment before cutover

### Risk: production code and schema become temporarily incompatible

Mitigation:

- prefer backward-compatible migrations
- run migrations as part of the deployment flow, not as a separate manual afterthought

### Risk: secrets/config drift between app and migration tool

Mitigation:

- centralize around environment-based configuration
- document required variables for dev and production

## Acceptance Criteria

- Phinx is installed and configured in this repo.
- A baseline migration can recreate the current schema from an empty database.
- Future DB changes can be added as incremental migrations.
- The production deployment path triggered by merges to `main` runs Phinx migrations automatically.
- Production DB credentials are handled outside the repo.
- The rollout includes backup, failure handling, and rollback guidance.

## Suggested Implementation Order

1. Preflight checks and config decisions
2. Add Phinx scaffolding
3. Author and validate the baseline migration
4. Document the ongoing developer workflow
5. Update GitHub Actions + server deploy script
6. Run first production rollout with backup and monitoring

## Definition of Done

This work is complete when a new developer machine or clean environment can:

1. clone the repo
2. configure DB credentials
3. run Phinx migrations
4. start the app against a correctly built schema

and when a merge to `main` can safely deploy code and apply production DB migrations without manual phpMyAdmin schema edits.
