Installing
==========

Composer
--------

The easiest way to install Wise is by using [Composer][]:

    $ composer require cyon/wise=~2.0

You may then load it by requiring the Composer autoloader:

```php
require 'vendor/autoload.php';
```

PSR-4
-----

You may use any class loader that supports [PSR-4][].

```php
$loader = new SplClassLoader();
$loader->add('Herrera\\Wise', 'src/lib');
```

[Composer]: https://getcomposer.org/
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
