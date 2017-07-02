# Magnesium-PHP
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/floriangaerber/magnesium.svg?style=flat-square)](https://packagist.org/packages/floriangaerber/magnesium)
[![Dependency Status](https://www.versioneye.com/user/projects/59218fdf8dcc41003af21edd/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/59218fdf8dcc41003af21edd)
[![CircleCI](https://img.shields.io/circleci/project/github/floriangaerber/Magnesium-PHP.svg?style=flat-square)](https://circleci.com/gh/floriangaerber/Magnesium-PHP)

Wrapper for the [Mailgun](https://mailgun.com) API-client.

## Installation
Install the latest version
```bash
$ composer require floriangaerber/magnesium
```

## Usage
```php
$batchMessage = new Magnesium\Message\BatchMessage($mailgunKey, $mailgunDomain);
```

## Documentation
**You can find the documentation and usage examples [here](docs).**

You can also generate [PHPDocs](https://www.phpdoc.org), if needed.

## About
### Contributing
View [CONTRIBUTING.md](CONTRIBUTING.md) for information on creating issues, reporting bugs and requesting features.

### License
Magnesium is released under the MIT License, view the [LICENSE](LICENSE) file for details.
