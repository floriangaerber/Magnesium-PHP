<?php

namespace Magnesium\Message;

use Magnesium\Magnesium;
use Mailgun\Mailgun;

/**
 * Base class for all message types.
 */
abstract class AbstractMessage
{
    /**
     * Mailgun API client.
     *
     * @var Mailgun
     */
    protected $mailgunClient;

    /**
     * Mailgun domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * From-address and optional name.
     *
     * @var array
     */
    protected $from = [];

    /**
     * Reply-To-address and optional name.
     *
     * @var array
     */
    protected $replyTo = [];

    /**
     * Subject.
     *
     * @var string
     */
    protected $subject = '';

    /**
     * HTML message body.
     *
     * @var string|null
     */
    protected $htmlBody;

    /**
     * Text message body.
     *
     * @var string|null
     */
    protected $textBody;

    /**
     * Whether to use testmode.
     *
     * @var bool
     */
    protected $isTestmode = false;

    /**
     * Whether TLS is required.
     *
     * @var bool
     */
    protected $requireTls = true;

    /**
     * Is verification skipped.
     *
     * @var bool
     */
    protected $skipVerification = false;

    /**
     * Set delivery time.
     *
     * @var
     */
    protected $deliveryTime;

    /**
     * Set tags.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Set custom headers.
     *
     * @var array
     */
    protected $customHeaders = [];

    /**
     * Instantiate message with your API-key and domain.
     *
     * @param string $key    Your Mailgun API-key
     * @param string $domain Your Mailgun domain
     */
    public function __construct(string $key, string $domain)
    {
        $this->setDomain($domain);
        $this->mailgunClient = new Mailgun($key);
    }

    /**
     * Sets the domain to send from.
     *
     * @param string $domain
     *
     * @return self
     */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Gets the mailgun domain.
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Sets the "From" address.
     *
     * @param string $email Displayed sender address
     * @param string $name  Optional display name
     *
     * @return self
     */
    public function setFrom(string $email, string $name = null): self
    {
        $this->from = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * Gets the "From" data.
     *
     * @return array
     */
    public function getFrom(): array
    {
        return [
            'email' => $this->getFromEmail(),
            'name' => $this->getFromName(),
        ];
    }

    /**
     * Get the sender email address.
     *
     * Defaults to postmaster@<Domain>
     *
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->from['email'] ?? 'postmaster@'.$this->getDomain();
    }

    /**
     * Get the sender display name.
     *
     * @return string
     */
    public function getFromName(): string
    {
        return $this->from['name'] ?? '';
    }

    /**
     * Gets the formatted From string.
     *
     * @return string
     */
    public function getFromString(): string
    {
        return $this->formatEmailString($this->getFromEmail(), $this->getFromName());
    }

    /**
     * Formats email and name into email string for To/From/Reply-To fields.
     *
     * The address can be either of:
     * - "Sender <sender@example.com>"
     * - "sender@example.com"
     * Depending on whether a name was set.
     *
     * @param string $email
     * @param string $name  (Optional)
     *
     * @return string
     */
    protected function formatEmailString(string $email, string $name = null): string
    {
        return $name
            ? sprintf(
                '%s <%s>',
                Magnesium::removeToStringBreakingSymbols($name, false),
                Magnesium::removeToStringBreakingSymbols($email, true))
            : Magnesium::removeToStringBreakingSymbols($email, true);
    }

    /**
     * Sets the "Reply-To" address.
     *
     * @param string $email
     * @param string $name  Optional name to be used
     *
     * @return self
     */
    public function setReplyTo(string $email, string $name = null): self
    {
        $this->replyTo = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * Gets the "Reply-To" data.
     *
     * @return array
     */
    public function getReplyTo(): string
    {
        return [
            'email' => $this->getReplyToEmail(),
            'name' => $this->getReplyToName(),
        ];
    }

    /**
     * Get the "Reply-To" email address.
     *
     * @return string|null
     */
    public function getReplyToEmail()
    {
        return isset($this->replyTo['email'])
            ? $this->replyTo['email']
            : null;
    }

    /**
     * Get the "Reply-To" display name.
     *
     * @return string|null
     */
    public function getReplyToName()
    {
        return isset($this->replyTo['name'])
            ? $this->replyTo['name']
            : null;
    }

    /**
     * Gets the formatted "Reply-To" string.
     *
     * @return string|null
     */
    public function getReplyToString()
    {
        return $this->getReplyToEmail()
            ? $this->formatEmailString($this->getReplyToEmail(), $this->getReplyToName())
            : null;
    }

    /**
     * Sets the subject.
     *
     * @param string $subject
     *
     * @return Base
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Gets the subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the HTML-body.
     *
     * @param string|null $html
     *
     * @return self
     */
    public function setHtml(string $html = null): self
    {
        $this->htmlBody = $html;

        return $this;
    }

    /**
     * Gets the HTML-body.
     *
     * @return string|null
     */
    public function getHtml()
    {
        return $this->htmlBody ?: null;
    }

    /**
     * Whether message has a HTML body.
     *
     * @return bool
     */
    public function hasHtml(): bool
    {
        return null !== $this->htmlBody;
    }

    /**
     * Sets the text-body.
     *
     * @param string|null $text
     *
     * @return self
     */
    public function setText(string $text = null): self
    {
        $this->textBody = $text;

        return $this;
    }

    /**
     * Gets the text-body.
     *
     * @return string|null
     */
    public function getText()
    {
        return $this->textBody ?: null;
    }

    /**
     * Whether message has a text body.
     *
     * @return bool
     */
    public function hasText(): bool
    {
        return null !== $this->textBody;
    }

    /**
     * Set Mailgun Testmode.
     *
     * Mailgun will accept, but not send messages sent in testmode.
     *
     * @param bool $bool
     *
     * @return self
     */
    public function setTestmode(bool $bool): self
    {
        $this->isTestmode = $bool;

        return $this;
    }

    /**
     * Shows whether or not testmode is enabled.
     *
     * @return bool
     */
    public function isTestmode(): bool
    {
        return $this->isTestmode;
    }

    /**
     * Whether to require TLS or not.
     *
     * @param bool $bool
     *
     * @return self
     */
    public function setRequireTls(bool $bool): self
    {
        $this->requireTls = $bool;

        return $this;
    }

    /**
     * Whether TLS is currently required or not.
     *
     * @return bool
     */
    public function isRequiringTls(): bool
    {
        return $this->requireTls;
    }

    /**
     * Whether to skip verification or not.
     *
     * @param bool $bool
     *
     * @return self
     */
    public function setSkipVerification(bool $bool): self
    {
        $this->skipVerification = $bool;

        return $this;
    }

    /**
     * Whether skipping verification or not.
     *
     * @return bool
     */
    public function isSkippingVerification(): bool
    {
        return $this->skipVerification;
    }

    /**
     * Delivery timestamp. Must be parseable by \DateTime.
     *
     * @param $timestamp
     *
     * @return self
     */
    public function setDeliveryTime($timestamp = null): self
    {
        $this->deliveryTime = $timestamp;

        return $this;
    }

    /**
     * Returns the delivery time as RFC2822.
     *
     * Magnesium parses the given timestamp into RFC2822 using \DateTime
     *
     * @return string|null RFC2822-Timestamp
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime
            ? (new \DateTime($this->deliveryTime))->format(\DateTime::RFC2822)
            : null;
    }

    /**
     * Array of tags that apply to this message.
     *
     * @param array $tags
     *
     * @return self
     */
    public function setTags(array $tags = null): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get the tags you set.
     *
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Add a custom header.
     *
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public function addCustomHeader(string $name, string $value): self
    {
        $this->customHeaders[$name] = $value;

        return $this;
    }

    /**
     * Remove a custom header by name.
     *
     * @param string $name
     *
     * @return self
     */
    public function removeCustomHeader(string $name): self
    {
        unset($this->customHeaders[$name]);

        return $this;
    }

    /**
     * Remove all custom headers.
     *
     * @return self
     */
    public function removeCustomHeaders(): self
    {
        $this->customHeaders = [];

        return $this;
    }

    /**
     * Get custom header by name.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getCustomHeader(string $name)
    {
        return $this->customHeaders[$name] ?? null;
    }

    /**
     * Get all custom headers.
     *
     * @return array
     */
    public function getCustomHeaders(): array
    {
        return $this->customHeaders;
    }

    /**
     * Adds custom headers as h:$name to array.
     *
     * @param array $config
     *
     * @return array
     */
    protected function addCustomHeadersToConfig(array $config = []): array
    {
        foreach ($this->getCustomHeaders() as $key => $value) {
            $config["h:$name"] = $value;
        }

        return $config;
    }

    // TODO Custom Variables

    /**
     * Adds options o:... to array.
     *
     * @param array $config
     *
     * @return array
     */
    protected function addOptionsToConfig(array $config = []): array
    {
        $config['o:testmode'] = $this->isTestmode();
        $config['o:require-tls'] = $this->isRequiringTls();
        $config['o:skip-verification'] = $this->isSkippingVerification();
        if ($this->getDeliveryTime()) {
            $config['o:deliverytime'] = $this->getDeliveryTime();
        }
        // TODO Tracking options
        if ($this->getTags()) {
            $config['o:tag'] = $this->getTags();
        }

        return $config;
    }

    /**
     * Adds From, Subject, ReplyTo to config.
     *
     * @param array $config
     *
     * @return array
     */
    protected function addFSRtToConfig(array $config = [])
    {
        $config['subject'] = $this->getSubject();
        $config['from'] = $this->getFromString();

        if ($this->getReplyToString()) {
            $config['h:Reply-To'] = $this->getReplyToString();
        }

        return $config;
    }

    /**
     * Adds HTML and Text message bodies to config.
     *
     * Override set HTMl and Text through arguments.
     *
     * @param array $config
     *
     * @return array
     */
    protected function addMessageBodyToConfig(array $config = []): array
    {
        $config['html'] = $this->getHtml();
        $config['text'] = $this->getText();

        return $config;
    }

    /**
     * Get sending configuration.
     *
     * @param array $config
     *
     * @return array
     */
    public function getConfig(array $config = []): array
    {
        $config = $this->addCustomHeadersToConfig($config);
        $config = $this->addOptionsToConfig($config);
        $config = $this->addFSRtToConfig($config);
        $config = $this->addMessageBodyToConfig($config);

        return $config;
    }

    /**
     * Sends the message via the mailgun client.
     *
     * @param array $config Mailgun send options
     *
     * @return array Mailgun Response
     */
    public function send(array $config = null): array
    {
        return $this->mailgunClient->sendMessage(
            $this->getDomain(),
            $config ?: $this->getConfig()
        );
    }
}
