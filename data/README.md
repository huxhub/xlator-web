# Data storage

The PHP application creates `xlator.sqlite` here on first request using PDO SQLite.
It imports existing records from `events.json`, `applications.json`, and `leads.csv`
once, then uses only the SQLite database for reads and writes. Keep the legacy files
as migration backups until you have verified the deployed database. Back up
`xlator.sqlite` regularly. PHP must have `pdo_sqlite` enabled, and this directory
must be writable by PHP. Apache access to this directory is denied by `.htaccess`.
