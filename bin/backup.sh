#!/usr/bin/env bash
#
# Daily backup: gzipped database dump + uploads archive, keeping the
# newest $KEEP of each. Meant for cron on the server, e.g.:
#
#   15 3 * * * /home/ubuntu/LuKaSports/bin/backup.sh >> /home/ubuntu/backups/backup.log 2>&1
#
# Restore the database with:
#   gunzip -c db-YYYYmmdd-HHMM.sql.gz | docker compose exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"'

set -euo pipefail
cd "$(dirname "$0")/.."

BACKUP_DIR="${BACKUP_DIR:-$HOME/backups}"
KEEP="${KEEP:-7}"
stamp=$(date +%Y%m%d-%H%M)
mkdir -p "$BACKUP_DIR"

# Credentials are read inside the db container from its own environment,
# so they never appear in this host's process list.
docker compose exec -T db sh -c 'exec mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --single-transaction --routines --no-tablespaces "$MYSQL_DATABASE"' 2>/dev/null \
	| gzip > "$BACKUP_DIR/db-$stamp.sql.gz"

docker compose exec -T wordpress tar -C /var/www/html/wp-content -czf - uploads \
	> "$BACKUP_DIR/uploads-$stamp.tar.gz"

for prefix in db uploads; do
	ls -1t "$BACKUP_DIR/$prefix-"* 2>/dev/null | tail -n +"$((KEEP + 1))" | xargs -r rm -f
done

echo "$(date '+%F %T') backup ok: db-$stamp.sql.gz uploads-$stamp.tar.gz"
