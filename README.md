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
