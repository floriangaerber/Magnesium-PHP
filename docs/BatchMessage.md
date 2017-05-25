# BatchMessage
This is a minimal example for sending Batch messages:
```php
$message = new BatchMessage(MAILGUN_KEY, MAILGUN_DOMAIN);

foreach ($recipients as $recipient) {
    $message->addRecipient($recipient['email'], [
        'name' => $recipient['name'],
        'my_var' => $recipient['id'],
        // et cetera...
    ]);
}

$message->setText(
    "Hello %recipient.name%,\n\n".
    "your email address is %recipient.email%."
);

$mailgunResponse = $message->send();
```
Every message sent with the `BatchMessage` class requires you to at least add 1 recipient and set at least one of the message body types.

## Use cases
`BatchMessage` was designed for sending personalized messages to a group of users of varying size.
Assume you want to notify users in a forum conversation about new comments.
You may have users with disabled notifications, users that have manually subscribed the thread, users that have commented but have manually disabled notifications about this thread, et cetera.
This may leave you with a group of users to notify of `n >= 0`. Now if your user count is 0, you won't have to spin up a message. If your user count is 2 or larger, you could use Mailgun's recipient variables. But if you had only 1 user to notify, Mailgun would not use recipient variables and require you to modify your email body yourself.
`BatchMessage` was made to fix this nuisance: It allows you to use only one set of code and templates for recipient groups of all sizes (larger than zero, of course).

## Configuration
### Basics
Don't forget to check out the [basics](Basics.md).

### Adding recipients
You can easily add a recipient like this:
```php
$message->addRecipient('el+007@example.com', [
    'my_var' => 123,
    'my_var2' => 'Hello World',
    'name' => 'Elizabeth',
    'email' => 'elizabeth@example.uk'
]);
```
If you set a `name` in the recipient variables, it will automatically be used in the `To` field of your message (`Elizabeth <el+007@example.com` in this case).
If you do not set `email` in the recipient variables, it will be set to the recipients email by default. You can overwrite it if you want.

##### Functions
- `addRecipient($email, $vars)` Add a recipient
- `removeRecipient($email)` Remove a recipient by email
- `removeRecipients()` Remove all recipients and start over
- `getRecipient($email)` Gets recipient variables for recipient by email
- `getRecipients()` Gets array of recipients, where keys are emails and values recipient variables.
- `getRecipientCount()` Returns the count of recipients

### HTML-escaping
`BatchMessage` automatically escapes HTML in recipient variables by default, which you can adjust using like this:
```php
$message->setEscapeHtmlInRecipientVariables(false);
```

### Custom Config
You can get the configuration that'd be used the send the message using
```php
$config = $message->getConfig();
```
if you want to modify it yourself before sending.

You can send using your own instance of the Mailgun-client or you can send a message with custom config using
```php
$mailgunResponse = $message->send($config);
```

Keep in mind that the `$config` you pass will not be merged with the configuration you have made before using the classes' setters.
