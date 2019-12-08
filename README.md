# POC Symfony 4 api platform

## Prerequisites

* The PHP version must be greater than or equal to PHP 7.2
* The SQLite 3 extension must be enabled
* The JSON extension must be enabled
* The Ctype extension must be enabled
* The date.timezone parameter must be defined in php.ini

More information on [symfony website](https://symfony.com/doc/4.2/reference/requirements.html).

## Features developed

**BackEnd Application:**
The development started in Symfony 4.1, and migrate successively to 4.2, 4.3 and finally 4.4.

* Rest Api
    * API Platform
    * Authentication with JWT (user sign-up, sign-in, including account confirmation through e-mail)
    * File uploads with Vich Uploader Bundle
* Controllers / routing
* Database tables as objects in Doctrine / Doctrine model to an API Resource
* Data validation and serialization/deserialization
* Paginate, filter and sort your collections
* Authorization (User roles, privileges, restricting access)
* Log errors with Monolog
* Send e-mail
* Entity data administration with EasyAdminBundle
* Tests
    * Unit testing (PHPUnit)
    * Functional testing (Behat)
    * Data fixture with Faker

Additional commands lines:
* `php bin/console blogpost:list --number=10 --order=DESC`: List BlogPost Items.
* `php bin/console blogpost:view ID`: View BlogPost Items.

**Front End Application (ReactJS / Redux) in other git project:**
https://github.com/jgauthi/poc_reactjs_redux


## Installation
Command lines:

```bash
git clone git@github.com:jgauthi/poc_symfony4_apiplatform.git
cd poc_symfony4_apiplatform

composer install

# (optional) Copy and edit configuration values ".env.local"

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate

# Private/Public Key (JWT)
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# Optional
php bin/console doctrine:fixtures:load
```

For the asset symlink install, launch a terminal on administrator in windows environment.

## Usage
There's no need to configure anything to run the application. Just execute this
command to run the built-in web server and access the application in your
browser at <http://localhost:8000>:

```bash
# Dev env
php bin/console server:run

# Test env
APP_ENV=test php -d variables_order=EGPCS -S 127.0.0.1:8000 -t public/
```

Alternatively, you can [configure a web server](https://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html) like Nginx or Apache to run
the application.

## Tests
Execute this command to run tests with phpunit _(with test env)_:

```bash
./bin/phpunit
```

Execute this command to run tests with Behat _(with test env)_:

```bash
./vendor/bin/behat
```
