# AliveOnRecord.com

The initial public source for **Alive On Record**, a personal safety, intent,
continuity, and verification record.

The site is intentionally small and auditable: one PHP homepage, one standalone
personal pledge document, one optional Apache configuration file, no database,
no external libraries, and no build step. It targets PHP 8 or newer on ordinary
shared hosting.

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

Upload `index.php`, `pledge.html`, and, on compatible Apache hosting,
`.htaccess`. The
`documents`, `evidence`, and `media` directories are reserved for future files;
the homepage does not require them.
