##
#  Update local database from live database.
##

# Note: Make sure you have `pv` installed. Example: `brew install pv`
# Usage: sh update-local-db.sh live|dev|qa

ENV=${1:-live}
SITE="quark-expeditions"
BACKUP_FILE="backup.sql.gz"
OUTPUT_FILE="import.sql"
DATABASE_NAME="quark"

echo 'Getting download URL...'
URL=$(terminus backup:get "$SITE"."$ENV" --element=database)

if [ -z "$URL" ]; then
    echo "Failed to get backup URL!"
    exit 1
fi

echo 'Downloading backup...'
curl -o "$BACKUP_FILE" "$URL"

if [ ! -f "$BACKUP_FILE" ]; then
    echo "Download failed!"
    exit 1
fi

echo 'Extracting backup...'
gunzip -c "$BACKUP_FILE" > "$OUTPUT_FILE"

if [ $? -ne 0 ]; then
    echo "Extraction failed!"
    exit 1
fi

echo 'Cleaning up...'
rm "$BACKUP_FILE"

echo "Backup successfully downloaded and extracted as $OUTPUT_FILE"

echo 'Importing database...'
pv $OUTPUT_FILE | mysql -uroot -proot -h 0.0.0.0 quark

echo 'Replacing paths...'
wp search-replace www.quarkexpeditions local.quarkexpeditions --all-tables

echo 'Flushing cache...'
wp cache flush

echo 'Deactivating and activating plugins...'
wp plugin deactivate s3-uploads change-wp-admin-login two-factor stream pantheon-advanced-page-cache aws-ses-wp-mail
wp plugin activate query-monitor

echo 'Cleaning PII...'
mysql -u root -proot -h 0.0.0.0 $DATABASE_NAME -e 'UPDATE wp_users SET user_email = CONCAT( "user", wp_users.ID, "@travelopia.com" )' # Reset all users' email address.
mysql -u root -proot -h 0.0.0.0 $DATABASE_NAME -e 'UPDATE wp_users SET user_pass="$P$BlKV.RDIfk0iBJeyy0m1e5A.kIleIF."' # All users will have the password "password".

echo 'Backing up local database...'
mysqldump --column-statistics=0 -u root -proot -h 0.0.0.0 $DATABASE_NAME > local.latest.dump

echo 'Cleaning up...'
rm "$OUTPUT_FILE"

echo 'Done!'
