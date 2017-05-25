# Basics
These features are shared across all message types.

### Setting `From`/`Reply-To`
By default, `BulkMessage` will set the `From`-address as `postmaster@<MAILGUN_DOMAIN>` (where `<MAILGUN_DOMAIN>` is the domain you used to instantiate your `BulkMessage`).
You can set a custom `From` and `Reply-To` address as follows:
```php
$message->setFrom('hello@example.org'); // From: hello@example.org

$message->setReplyTo('john.d@example.org', 'John Dee') // Reply-To: John Dee <hello@example.org>
```
`setFrom` and `setReplyTo` both accept an email as the first parameter and an optional name as the second parameter.

##### Getters
- `getFrom`: Associative array [email, name?:`null`]
- `getFromEmail`: `From` email adress
- `getFromName`: `From` name or `null`
- `getFromString`: Formatted `From` string, for example:
  - `John Dee <john.d@example.org>` (if name is set)
  - `john.d@example.org` (when name isn't set)
- `getReplyTo`: Associative array [email, name?:`null`]
- `getReplyToEmail`: `Reply-To` email adress
- `getReplyToName`: `Reply-To` name or `null`
- `getReplyToString`: Formatted `Reply-To` string, for example:
  - `John Dee <john.d@example.org>` (if name is set)
  - `john.d@example.org` (when name isn't set)

### Adding Content
As seen above, `setText` and `setHtml` accept strings. You can hard-code strings, or you can plug in your favourite template engine to create even more dynamic emails.
```php
$message->setHtml($myTemplateEngine->fetch('email.html', $config));
```

Also, don't forget to add a subject:
```php
$message->setSubject('Hello World!');
```

##### Getters
- `getText`
- `getHtml`
- `getSubject`

### Configuring Mailgun
> TODO: Documentation
