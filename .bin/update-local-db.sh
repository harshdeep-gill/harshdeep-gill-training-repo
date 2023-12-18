##
#  Update local database from live database.
##

# Note: Make sure you have `pv` installed. Example: `brew install pv`
# Usage: sh update-local-db.sh /path/to/quark-expeditions_live_xxxx-xx-xxT19-00-00_UTC_database.sql

echo 'Importing database...'
pv $1 | mysql -uroot -proot -h 0.0.0.0 tcs

echo 'Replacing paths...'
#wp search-replace www.quarkexpeditions local.quarkexpeditions --all-tables
wp search-replace wp.quarkexpeditions local.quarkexpeditions --all-tables

echo 'Flushing cache...'
wp cache flush

echo 'Deactivating and activating plugins...'
wp plugin deactivate s3-uploads change-wp-admin-login two-factor stream pantheon-advanced-page-cache aws-ses-wp-mail
wp plugin activate query-monitor

echo 'Cleaning PII...'
mysql -u root -proot -h 0.0.0.0 tcs -e 'UPDATE wp_users SET user_email = CONCAT( "user", wp_users.ID, "@travelopia.com" )' # Reset all users' email address.
mysql -u root -proot -h 0.0.0.0 tcs -e 'UPDATE wp_users SET user_pass="$P$BlKV.RDIfk0iBJeyy0m1e5A.kIleIF."' # All users will have the password "password".
