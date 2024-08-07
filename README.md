
![GitHub Actions](https://github.com/Travelopia/quarkexpeditions/workflows/Coding%20Standards%20and%20Tests/badge.svg)

# Quark Expeditions

Custom WordPress website for Quark Expeditions.

## Local Setup

Make sure you have PHP 8.2, Docker, NodeJS 18 and Composer 2 or greater installed. You should preferably name your project directory as `quarkexpeditions`.

1. Clone this repository
2. Get the `.env` file from one of your colleague and add that to the project.
3. Run `npm run start` to install NodeJS and Composer packages. This also automatically installs the PHP coding standards. You will be prompted for your password to trust the SSL certificate
5. Start your local environment (see below) by running `npm run local-environment:start`, it will create all the required docker images.
6. Connect to MySQL: Host: `127.0.0.1` User: `root` Password: `root` Port: `3306`
7. Create a database `quark` and import the database into it: `echo "CREATE DATABASE quark;" | mysql -uroot -proot -h 0.0.0.0`
8. Get the database from one of your team-mates and import it.
8. If you don't use a Mac, go to `.docker/ssl` and trust the self-signed certificate in there. If you use a Mac, this should be done automatically
9. Add `127.0.0.1 local.quarkexpeditions.com` to your hosts file
11. Visit https://local.quarkexpeditions.com in your browser.
13. Create a user for yourself if you don't already have one: `composer wp user create bob bob@example.com --role=travelopia_super_user --user_pass=password` . If you already have a user, set yourself as a super admin: `composer wp user set-role bob travelopia_super_user`
14. To access WP Admin, visit https://local.quarkexpeditions.com/wp-admin/
15. To access MailHog, visit http://0.0.0.0:8025
16. To access Solr, visit http://localhost:8983/sites/self/environments/lando/index/admin/

### Starting and stopping the Docker environment

To start your local Docker environment, run: `npm run local-environment:start`

To re-start your local Docker environment that will remove previous docker files and re-created them,
run: `npm run local-environment:restart`

To stop your local Docker environment, run: `npm run local-environment:stop`

### Coding Standards

This project uses WordPress' Coding Standards: https://make.wordpress.org/core/handbook/best-practices/coding-standards/ . When you push changes to GitHub, they are checked against these coding stanards.

You can test this locally by running `npm run lint`

### PHPUnit Tests Setup

When you push changes to GitHub, it runs an automated suite of tests. To set this up locally, follow these steps:

1. Create the tests database ```echo "CREATE DATABASE \`quark-tests\`;" | mysql -uroot -proot -h 0.0.0.0```
1. Run `npm run test:php` to run the tests

### Best Practices

1. Run `npm run lint-test` before committing code to check if all automated tests and linters pass.
