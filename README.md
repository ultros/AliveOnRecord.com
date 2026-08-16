# AliveOnRecord.com

The public source for **Alive On Record**, a community life-and-safety pledge
platform with suicide-prevention information and crisis-support pathways.

The site is intentionally small and auditable: a PHP community-pledge platform,
a public pledge wall backed by SQLite, focused suicide-prevention resource
pages, and one optional Apache configuration file. It uses no external
libraries and has no build step. It targets PHP 8 or newer on ordinary shared
hosting.

## Before publishing updates

Review health and crisis wording against the current official sources linked on
each prevention page. A pledge must never be represented as proof of safety, a
clinical risk assessment, a no-suicide contract, treatment, or a replacement
for an individualized safety plan.

The pledge wording and consent version are configured in the application code.
Increase both versions when the meaning of the accepted pledge or publication
consent changes.

## Local preview

With PHP 8+ installed, run PHP's local development server from this directory
and open the local address it prints. The production server should point its
document root at this directory.

## Deployment

Upload the repository contents and, on compatible Apache hosting, retain
`.htaccess`. Keep `media/og.png` available at the published path because it is
used by Open Graph and social-sharing metadata.

The public pledge flow requires the PHP `PDO` and `pdo_sqlite` extensions. By
default, its database is created at `../aliveonrecord-data/pledges.sqlite`,
outside the public web root. The hosting account must be able to create and
write that directory. To use a different non-public absolute path, set the
`AOR_DB_PATH` environment variable.

The audited schema is in `schema.sql`; the application creates the same schema
on first use. Participant email addresses and IP addresses are not stored in
the pledge table. Public records contain only a display name, random pledge ID,
pledge/consent versions, and submission time. A one-time removal code is shown
after submission; only its SHA-256 hash is stored privately in SQLite.

Removal requests are directed to `jesse.shelley@aliveonrecord.com`. After
verifying the requested pledge ID, run the CLI-only removal tool:

```text
php bin/remove-pledge.php AOR-PLEDGE-XXXXXXXXXXXX AOR-REMOVE-XXXXXXXXXXXXXXXXXXXXXXXX
```

The removal tool permanently deletes the matching pledge record from the
database and public wall.

To confirm database integrity, columns, indexes, and the public-list query plan:

```text
php bin/check-database.php
```
