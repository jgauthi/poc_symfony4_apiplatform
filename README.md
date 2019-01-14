POC Symfony 4 api platform
===========

## Prerequisites

* The PHP version must be greater than or equal to PHP 7.2
* The SQLite 3 extension must be enabled
* The JSON extension must be enabled
* The Ctype extension must be enabled
* The date.timezone parameter must be defined in php.ini

More information on [symfony website](https://symfony.com/doc/4.2/reference/requirements.html).

## Features developed




## Installation
Command lines:

```bash
git clone git@github.com:jgauthi/poc_symfony4_api_platform.git
cd poc_symfony4_api_platform

composer install

# (optional) Copy and edit configuration values ".env.local"

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate

# Optional
php bin/console doctrine:fixtures:load
```

For the asset symlink install, launch a terminal on administrator in windows environment.


## Installation with docker-compose

```bash
git clone git@github.com:jgauthi/poc_symfony3_fosrestbundle.git
cd poc_symfony3_fosrestbundle

docker-compose up -d
docker-compose exec php composer install

# Copy ".env.dist" to ".env"
# (optional) You can edit configurations values ".env" and "app/config/parameters.yml"

docker-compose exec php php bin/console assets:install --symlink
docker-compose exec php php bin/console doctrine:database:create --if-not-exists
docker-compose exec php php bin/console doctrine:migrations:migrate

# Optional
docker-compose exec php php bin/console doctrine:fixtures:load
```

## [Docker-compose] Application urls
This docker-compose use a reverse proxy: [Traefik](https://traefik.io/), url supported:

* [Plaform symfony](http://platform.docker)
* [phpMyAdmin](http://pma.docker)
* [mailDev](http://maildev.docker)



## Prepare deploy prod

* **Temporarily** edit the file web/app.php, change the 2e args to true: ``$kernel = new AppKernel('prod', true);`` and test the site on prod mode.
* Check prerequisites on prod server: [domain.com]/config.php (edit the file to edit/remove IP verification) OR command line: ``php bin/symfony_requirements``
* Configure apache symfony dir (virtual host on dev env) to **web/** folder.

## Deploy on prod

* Delete manualy "var/*/" content before send file (ftp)
* Chmod 755 recursive on prod, on folder: "var/"
* You can edit web/app_dev.php with personal IP to access dev environment on prod.
* If an 500 error occurs, check log on "var/logs/prod"















Symfony Demo Application
========================

The "Symfony Demo Application" is a reference application created to show how
to develop applications following the [Symfony Best Practices][1].

Requirements
------------

  * PHP 7.1.3 or higher;
  * PDO-SQLite PHP extension enabled;
  * and the [usual Symfony application requirements][2].

Installation
------------

```bash
composer install

# Private/Public Key (JWT)
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

Usage
-----

There's no need to configure anything to run the application. Just execute this
command to run the built-in web server and access the application in your
browser at <http://localhost:8000>:

```bash
$ cd symfony-folder/
$ php bin/console server:run
```

Alternatively, you can [configure a web server][3] like Nginx or Apache to run
the application.

Tests with phpunit
-----
Execute this command to run tests:

```bash
cd symfony-folder/
./bin/phpunit
```

Tests with Behat
-----
Execute this command to run tests:

```bash
cd symfony-folder/
APP_ENV=test php -d variables_order=EGPCS -S 127.0.0.1:8000 -t public/

# Open another console
./vendor/bin/behat
```


[1]: https://symfony.com/doc/current/best_practices/index.html
[2]: https://symfony.com/doc/current/reference/requirements.html
[3]: https://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html
