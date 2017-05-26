<?php

/**
 * This file is part of the floriangaerber/magnesium package.
 *
 * @copyright 2017 Florian Gärber
 * @license MIT
 * @license See "LICENSE" for details
 */

namespace Magnesium\Message;

use Mailgun\Mailgun;

/**
 * Base class for all message types.
 */
class Base
{
    /**
     * Mailgun API client.
     *
     * @property \Mailgun\Mailgun $mailgunClient
     */
    protected $mailgunClient;

    /**
     * Mailgun domain.
     *
     * @property string $domain
     */
    protected $domain;

    /**
     * From-address and optional name.
     *
     * @property array $from
     */
    protected $from = [];

    /**
     * Reply-To-address and optional name.
     *
     * @property array $replyTo
     */
    protected $replyTo = [];

    /**
     * Subject.
     *
     * @property string $subject
     */
    protected $subject = '';

    /**
     * HTML message body.
     *
     * @property string $htmlBody
     */
    protected $htmlBody;

    /**
     * Text message body.
     *
     * @property string $textBody
     */
    protected $textBody;

    /**
     * Whether to use testmode.
     *
     * @property bool $isTestmode
     */
    protected $isTestmode = false;

    /**
     * Whether TLS is required.
     *
     * @property bool $requireTls
     */
    protected $requireTls = true;

    /**
     * Is verification skipped.
     *
     * @property bool $skipVerification
     */
    protected $skipVerification = false;

    /**
     * Set delivery time.
     *
     * @property
     */
    protected $deliveryTime;

    /**
     * Set tags.
     *
     * @property
     */
    protected $tags;

    /**
     * Set custom headers.
     *
     * @property array $customHeaders
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
     * @return Base
     */
    public function setDomain(string $domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Gets the mailgun domain.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the "From" address.
     *
     * @param string $email Displayed sender address
     * @param string $name  Optional display name
     *
     * @return Base
     */
    public function setFrom(string $email, string $name = null)
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
    public function getFrom()
    {
        return [
            'email' => $this->getFromEmail(),
            'name' => $this->getFromName(),
        ];
    }

    /**
     * Get the sender email address.
     *
     * Defaults to postmaster@<MailgunDomain>
     *
     * @return string
     */
    public function getFromEmail()
    {
        return isset($this->from['email'])
        ? $this->from['email']
        : 'postmaster@'.$this->domain;
    }

    /**
     * Get the sender display name.
     *
     * @return string
     */
    public function getFromName()
    {
        return isset($this->from['name'])
        ? $this->from['name']
        : null;
    }

    /**
     * Gets the formatted email string.
     *
     * @return string
     */
    public function getFromString()
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
    protected function formatEmailString(string $email, string $name = null)
    {
        return $name
        ? sprintf(
            '%s <%s>',
            $this->removeToStringBreakingSymbols($name, false),
            $this->removeToStringBreakingSymbols($email, true))
        : $this->removeToStringBreakingSymbols($email, true);
    }

    /**
     *
     * Should a user have chosen a name like "no1@example.com, Not Okay <no2@example.com>, Sherbert",
     * Mailgun would accept the following to: "user@example.com, user2@example.com, no1@example.com, Not Okay <no2@example.com>, Sherbert <hello@example.com>"
     * and would send it accordingly, which is unwanted behavior.
     * Removing only "," breaks the To-string, sending the message to
     * "user@example.com, user2@example.com, no1@example.com Not Okay <no2@example.com> Sherbert" hello@example.com,
     * revealing email addresses of other users.
     * Removing only either of "<,>" or "@," breaks the string the same way.
     * Only removing "<>@," from the string prevents breaking (as far as I know).
     *
     * Also use an input validation library like Respect/Validation or find
     * another way to prevent emails and names from containing "<>,"!
     *
     * @param string $string
     * @param bool   $isEmail
     *
     * @return string
     */
    protected function removeToStringBreakingSymbols(string $string, bool $isEmail)
    {
        return str_replace($isEmail ? ['>', '<', ','] : ['>', '<', ',', '@'], '', $string);
    }

    /**
     * Sets the "Reply-To" address.
     *
     * @param string $email
     * @param string $name  Optional name to be used
     *
     * @return Base
     */
    public function setReplyTo(string $email, string $name = null)
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
    public function getReplyTo()
    {
        return [
            'email' => $this->getReplyToEmail(),
            'name' => $this->getReplyToName(),
        ];
    }

    /**
     * Get the "Reply-To" email address.
     *
     * @return string
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
     * @return string
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
     * @return string
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
     * @param string $html
     *
     * @return Base
     */
    public function setHtml(string $html)
    {
        $this->htmlBody = $html;

        return $this;
    }

    /**
     * Gets the HTML-body.
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->htmlBody ?: null;
    }

    /**
     * Sets the text-body.
     *
     * @param string $txt
     *
     * @return Base
     */
    public function setText(string $txt)
    {
        $this->textBody = $txt;

        return $this;
    }

    /**
     * Gets the text-body.
     *
     * @return string
     */
    public function getText()
    {
        return $this->textBody ?: null;
    }

    /**
     * Set Mailgun Testmode.
     *
     * Mailgun will accept, but not send messages sent in testmode.
     *
     * @param bool $bool
     *
     * @return Base
     */
    public function setTestmode(bool $bool)
    {
        $this->isTestmode = $bool;

        return $this;
    }

    /**
     * Shows whether or not testmode is enabled.
     *
     * @return bool
     */
    public function isTestmode()
    {
        return $this->isTestmode;
    }

    /**
     * Whether to require TLS or not.
     *
     * @param bool $bool
     *
     * @return Base
     */
    public function setRequireTls(bool $bool)
    {
        $this->requireTls = $bool;

        return $this;
    }

    /**
     * Whether TLS is currently required or not.
     *
     * @return bool
     */
    public function isRequiringTls()
    {
        return $this->requireTls;
    }

    /**
     * Whether to skip verification or not.
     *
     * @param bool $bool
     *
     * @return Bulk
     */
    public function setSkipVerification(bool $bool)
    {
        $this->skipVerification = $bool;

        return $this;
    }

    /**
     * Whether skipping verification or not.
     *
     * @return bool
     */
    public function isSkippingVerification()
    {
        return $this->skipVerification;
    }

    /**
     * Delivery timestamp. Must be parseable by \DateTime.
     *
     * @param $timestamp
     *
     * @return Base
     */
    public function setDeliveryTime($timestamp = null)
    {
        $this->deliveryTime = $timestamp;

        return $this;
    }

    /**
     * Returns the delivery time as RFC2822.
     *
     * Magnesium parses the given timestamp into RFC2822 using \DateTime
     *
     * @return string RFC2822-Timestamp
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
     * @return Base
     */
    public function setTags(array $tags = null)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get the tags you set.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add a custom header.
     *
     * @param string $name
     * @param string $value
     *
     * @return Base
     */
    public function addCustomHeader(string $name, string $value)
    {
        $this->customHeaders[$name] = $value;

        return $this;
    }

    /**
     * Remove a custom header by name.
     *
     * @param string $name
     *
     * @return Base
     */
    public function removeCustomHeader(string $name)
    {
        unset($this->customHeaders[$name]);

        return $this;
    }

    /**
     * Remove all custom headers.
     *
     * @return Base
     */
    public function removeCustomHeaders()
    {
        $this->customHeaders = [];

        return $this;
    }

    /**
     * Get custom header by name.
     *
     * @param string $name
     *
     * @return string
     */
    public function getCustomHeader(string $name)
    {
        return isset($this->customHeaders[$name]) ? $this->customHeaders[$name] : null;
    }

    /**
     * Get all custom headers.
     *
     * @return array
     */
    public function getCustomHeaders()
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
    protected function addCustomHeadersToConfig(array $config)
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
    protected function addOptionsToConfig(array $config)
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
    protected function addFSRtToConfig(array $config)
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
     * @param array  $config
     * @param string $html   Custom HTML body
     * @param string $text   Custom Text body
     *
     * @return array
     */
    protected function addMessageBodyToConfig(array $config, string $html = null, string $text = null)
    {
        $config['html'] = $html ?: $this->getHtml();
        $config['text'] = $text ?: $this->getText();

        return $config;
    }

    /**
     * Sends the message via the mailgun client.
     *
     * @param array $config Mailgun send options
     *
     * @return array Mailgun Response
     */
    protected function sendMessage(array $config)
    {
        return $this->mailgunClient->sendMessage($this->getDomain(), $config);
    }
}
