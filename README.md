# AliveOnRecord.com

The initial public source for **Alive On Record**, a personal safety, intent,
continuity, and verification record.

The site is intentionally small and auditable: a PHP homepage, a standalone
personal pledge document, a public community pledge flow backed by SQLite, and
one optional Apache configuration file. It uses no external libraries and has
no build step. It targets PHP 8 or newer on ordinary shared hosting.

## Before publishing updates

Edit the clearly marked configuration block at the top of `index.php`. In
particular, `lastPersonalVerification` must only be changed after a real,
manual personal verification. The server-generated `Page Generated` value is
separate and does not prove personal verification.

Timeline and evidence records are maintained in PHP arrays near the top of the
same file. Do not publish invented evidence, hashes, timestamps, or allegations
presented as fact.

The dates and identifying fields in `pledge.html` are also deliberately static.
Update them manually when the pledge itself is revised.

## Local preview

With PHP 8+ installed, run PHP's local development server from this directory
and open the local address it prints. The production server should point its
document root at this directory.

## Deployment

Upload the repository contents and, on compatible Apache hosting, retain
`.htaccess`. The
`documents`, `evidence`, and `media` directories are reserved for future files;
the homepage does not require them.

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
