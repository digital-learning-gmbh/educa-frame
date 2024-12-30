Changelog
================

- Update support for Laravel 6 & phpunit 8
- Update composer.json
- Upgrade to PSR-4
- add parameter newline, delimiter, enclosure, and escape to export csv
- When converting a XML to an array, convert @attributes to _attribute_
- add parameter encoding and formated to export xml
- JSON parse fix (Instead of only converting the first level to array, use the associative array parameter with true, so all levels will be decoded to array structure)
- Add support for laravel 5
- add package discovery for laravel 5
- add support delimiter to a csv

Formatter Bundle
================

[![Build Status](https://travis-ci.org/SoapBox/laravel-formatter.svg?branch=master)](https://travis-ci.org/SoapBox/laravel-formatter)

A formatter package that will help you to easily convert between various formats such as XML, JSON, CSV, etc...

# Goals
The goals of this library are to allow the transfomation of data formats from one type to another.
See Parsers and Formats to see supported input / output formats.

# Installation

Through command line:

```bash
composer require soapbox/laravel-formatter
```

Through composer.json:

```json
{
  "require": {
    "soapbox/laravel-formatter": "2.x"
  }
}

```

## Parsers
All of the following are supported formats that the formatter can read from.
* Array
* CSV
* JSON
* XML
* YAML

## Formats
All of the following are formats that are supported for output.
* Array
* CSV
* JSON
* XML
* YAML

## General Usage

__Including The Formatter__

```php
use SoapBox\Formatter\Formatter;
```

__Supported Types__

```php
Formatter::JSON; //json
Formatter::CSV;  //csv
Formatter::XML;  //xml
Formatter::ARR;  //array
Formatter::YAML; //yaml
```

__Making Your First Formatter(s)__

```php
$formatter = Formatter::make($jsonString, Formatter::JSON);
$formatter = Formatter::make($yamlString, Formatter::YAML);
$formatter = Formatter::make($array, Formatter::ARR);
...
```

__Outputting From Your Formatter__

```php
$csv   = $formatter->toCsv();
$json  = $formatter->toJson();
$xml   = $formatter->toXml();
$array = $formatter->toArray();
$yaml  = $formatter->toYaml();
```

## Deprecated Functionality
The following have been deprecated from the library, however you can easily continue using them in your application

__Serialized Array__

```php
$serialized = serialize($formatter->toArray());
```

__PHP Export__

```php
$export = var_export($formatter->toArray());
```

