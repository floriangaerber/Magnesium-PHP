# Magnesium-PHP
Wrapper for the [Mailgun](https://mailgun.com) API-client.

## Installation
Install the latest version
```bash
$ composer require floriangaerber/magnesium
```

## Documentation
You can find the documentation [here](http://magnesium-php.floriangaer.be).
You can also generate [PHPDocs](https://www.phpdoc.org), if needed.

## Usage
Example: Sending individual bulk notifications.
```php
<?php

$message = new Magnesium\Messages\Bulk('key-xxxxxx', 'mg.example.org');

// Assuming array of users you want to notify
foreach ($usersToNotify as $user) {
    $message->addRecipient($user['email'], [
        'name' => $user['name'],
        'uid' => $user['id'],
    ]);
}

$message->setHtml('Hello %recipient.name%,<br/><br/>Your email is "%recipient.email%", and your user ID is <b>%recipient.uid%</b>')
    ->setText('Hello %recipient.name%,\n\nYour email is "%recipient.email%", and your user ID is %recipient.uid%')
    ->setSubject('User Notification Example')
    ->setFrom('Notification <notification@mg.example.org')
    ->setReplyTo('support@example.org')
    ->setTrackOpens(true)
    ->setDeliveryTime(time() + 60)
    ->setTags(['testing'])
    ->addRecipient('debug@example.org', [
        'name' => 'Debug',
        'uid' => 0
    ]);

try {
    $response = $message->send();
} catch (\Error $e) {
    var_dump($e);
}

echo 'Mailgun-Response: '.$response['message'];
```
You can set HTML/Text-body using hard-coded strings, or you can use your favorite template engine to dynamically generate email bodies with per-message values (For example event names, ids, etc.).

In the bodies given to Magnesium, you will be able to use `%recipient.variables%`, where `variables` is a key for each recipient's variables, set with `addRecipient()`.

This even supports just sending to one user, something that isn't possible with the vanilla Mailgun-client. Mailgun ignores `Recipient-Variables` if there is only one `To` recipient. Magnesium automatically replaces `%recipient.variables%` in the email bodies, if there is only one recipient.

Mailgun also does not escape HTML in `%recipient.variables%`. Magnesium automatically escapes HTML (although this can easily be disabled by setting `$message->setEscapeHtmlInCustomVariables(false)`). HTML-escaping is enabled by default, to prevent you from accidentally letting user-generated content be rendered as HTML (similar to Reacts `dangerouslySetInnerHTML`).

## About
### Contributing
View [CONTRIBUTING.md](CONTRIBUTING.md) for information on creating issues, reporting bugs and requesting features.

### License
Magnesium is released under the MIT License, view the [LICENSE](LICENSE) file for details.
