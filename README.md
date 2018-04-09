# Magnesium-PHP

[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/floriangaerber/magnesium.svg?style=flat-square)](https://packagist.org/packages/floriangaerber/magnesium)
[![Dependency Status](https://www.versioneye.com/user/projects/59218fdf8dcc41003af21edd/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/59218fdf8dcc41003af21edd)
[![CircleCI](https://img.shields.io/circleci/project/github/floriangaerber/Magnesium-PHP.svg?style=flat-square)](https://circleci.com/gh/floriangaerber/Magnesium-PHP)

Wrapper for the [Mailgun](https://mailgun.com) API-client.

## Installation

Install the latest version

```bash
composer require floriangaerber/magnesium
```

## Usage

### Common Settings

#### Set your message contents

```php
$message
    ->setFrom('magnesium@example.com', 'Magnesium Messages')
    ->setReplyTo('support@example.com', 'Example Support Email')
    ->setSubject('Magnesium Batch Messages')
    ->setText($textBody)
    ->setHtml($htmlBody);
```

#### Change Mailgun settings

```php
$message
    ->setTestmode(true) // Default: false
    ->setRequireTls(false) // Default: true
    ->setSkipVerification(true) // Default: false
    ->setDeliveryTime(time()) // Default: null; must be parseable by \DateTime
    ->setTags(['transactional', 'account-notice', 'tag-3']);
```

#### Sending your message

```php
try {
    $mailgunResponse = $message->send();
} catch (\Mailgun\Exception $ex) {
    // Handle Mailgun Exceptions
}
```

You can also get the options array that would be passed to the Mailgun client:

```php
$mailgunOptions = $message->getOptions();

// ... change options

try {
    $mailgunResponse = $mailgun->sendMessage($mailgunDomain, $mailgunOptions);
    // or: $mailgunResponse = $message->send($mailgunOptions);
} catch (\Mailgun\Exception $ex) {
    // Handle Mailgun Exceptions
}
```

### Message Types

#### Simple Messages

**`SimpleMessage` has not been implemented yet!**
A `SimpleMessage` class will be added later.

```php
$simpleMessage = new Magnesium\Message\SimpleMessage('key-xyz', 'example.com');
// or: $message = $magnesium->createBatchMessage('example.com');

$simpleMessage->setNewRecipient($recipient->email, [
    'id' => $recipient->id,
    'name' => $recipient->name,
]);
// or:
// $simpleMessage->setRecipient(new Magnesium\Recipient($recipient->email, [
//     'id' => $recipient->id,
//     'name' => $recipient->name,
// ]));

// ... Configure message

$simpleMessage->send();
```

#### Batch Messages

```php
$batchMessage = new Magnesium\Message\BatchMessage('key-xyz', 'example.com');
// or: $message = $magnesium->createBatchMessage('example.com');

// Get some recipients
$recipients = UserModel::all();

foreach ($recipients as $recipient) {
    $batchMessage->addNewRecipient($recipient->email, [
        'id' => $recipient->id,
        'name' => $recipient->name,
    ]);
    // or:
    // $batchMessage->addRecipient(new Magnesium\Recipient($recipient->email, [
    //     'id' => $recipient->id,
    //     'name' => $recipient->name,
    // ]));
}

// ... Configure message

$batchMessage->send();
```

## Documentation

You can also generate [PHPDocs](https://www.phpdoc.org), if needed.

## About

### License

Magnesium is released under the MIT License, view the [LICENSE](LICENSE) file for details.
