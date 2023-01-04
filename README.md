# Germania · i18n


[![Packagist](https://img.shields.io/packagist/v/germania-kg/i18n.svg?style=flat)](https://packagist.org/packages/germania-kg/i18n)
[![PHP version](https://img.shields.io/packagist/php-v/germania-kg/i18n.svg)](https://packagist.org/packages/germania-kg/i18n)
[![Tests](https://github.com/GermaniaKG/i18n/actions/workflows/tests.yml/badge.svg)](https://github.com/GermaniaKG/i18n/actions/workflows/tests.yml)

## Installation with Composer

```bash
$ composer require germania-kg/i18n
```



## BC breaks from v1

Class **DGettextRenderer:** 
This callable now expects returned messages to enclose placeholders with curly braces.

## Deprecated 

These classes have been used in **v1**, developers are encouraged to not use them any longer.

- `Germania\i18n\ServiceProvider`
- `Germania\i18n\AcceptedLanguageStringFactory`





## GettextMiddleware

```php
<?php
use Germania\i18n\GettextMiddleware;

$locale = "en_GB";
$domains = ["app", "project"];
$path = "./locales";

$middleware = new GettextMiddleware($locale, $domains, $path);
```



## LanguageNormalizer

```php
<?php
use Germania\i18n\LanguageNormalizer;

$norm = new LanguageNormalizer;
$norm("de-de"); // "de_DE"
```



## Translator

The constructor requires a *client* and and *default* language.

```php
<?php
use Germania\i18n\Translator;

$t = new Translator("de", "en");

echo $t("Just a string, nothing to translate");
// "Just a string, nothing to translate"

$var = array(
  "de" => "Deutsch: Nur eine String-Variable",
  "en" => "English: Just a string variable"
);

echo $t($var);
// "Deutsch: Nur eine String-Variable"

echo $t($var, "en");
// "English: Just a string variable"
```



## DGettextRenderer

Callable wrapper around *dgettext* for a given domain. Optionally expands at runtime variable placeholders given in second parameter:

```php
<?php
use Germania\i18n\DGettextRenderer;

$domain = "app";
$dgr = new DGettextRenderer($domain);

echo $dgr("MsgId");
// "Some translated {placeholder} messages"

echo $dgr("MsgId", [
  "placeholder" => "success"
]);
// "Some translated success messages"
```





## Issues

See [full issues list.][i0]

[i0]: https://github.com/GermaniaKG/i18n/issues


## Development

```bash
$ git clone https://github.com/GermaniaKG/i18n.git
$ cd i18n
$ composer install
```

## Unit tests

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. Run [PhpUnit](https://phpunit.de/) test or composer scripts like this:

```bash
$ composer test
# or
$ vendor/bin/phpunit
```

