<?php

/**
 * This file is part of the floriangaerber/magnesium package.
 *
 * @copyright 2017 Florian Gärber
 * @license MIT
 * @license See "LICENSE" for details
 */

namespace Magnesium\Message;

use Magnesium\Error;

/**
 * BatchMessage class.
 *
 * Send to a group of users of varying size
 */
class BatchMessage extends Base
{
    /**
     * Whether HTML is escaped or not.
     *
     * @property bool $escapeHtml
     */
    protected $escapeHtml = true;

    /**
     * Recipients.
     *
     * @property array $recipients
     */
    protected $recipients = [];

    /**
     * Enable or disable escaping HTML in recipient variables.
     *
     * @param bool $bool Whether to escape HTML in recipient Variables
     *
     * @return BatchMessage
     */
    public function setEscapeHtmlInRecipientVariables(bool $bool)
    {
        $this->escapeHtml = $bool;

        return $this;
    }

    /**
     * Whether or not currently escaping HTML in recipient variables.
     *
     * @return bool
     */
    public function isEscapingHtmlInRecipientVariables()
    {
        return $this->escapeHtml;
    }

    /**
     * Add a recipient by email, optionally with recipient variables.
     *
     * @param string $email
     * @param array  $vars
     *
     * @return BatchMessage
     */
    public function addRecipient(string $email, array $vars = [])
    {
        if (!isset($vars['email'])) {
            $vars['email'] = $email;
        }

        $this->recipients[$email] = $vars;

        return $this;
    }

    /**
     * Remove a recipient by email.
     *
     * @param string $email
     *
     * @return BatchMessage
     */
    public function removeRecipient(string $email)
    {
        unset($this->recipients[$email]);

        return $this;
    }

    /**
     * Remove all recipients.
     *
     * @return BatchMessage
     */
    public function removeRecipients()
    {
        $this->recipients = [];

        return $this;
    }

    /**
     * Get recipient by email.
     *
     * @param string $email
     *
     * @return array
     */
    public function getRecipient(string $email)
    {
        return $this->recipients[$email];
    }

    /**
     * Get all recipients.
     *
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Returns the count of recipients.
     *
     * @return int
     */
    public function getRecipientCount()
    {
        return count($this->recipients);
    }

    /**
     * Makes the configuration for the Mailgun client.
     *
     * @return array Configuration
     */
    public function getConfig()
    {
        $CONFIG = [];
        $count = $this->getRecipientCount();

        $html = $this->getHtml();
        $text = $this->getText();
        if (!$html && !$text) {
            throw new Error\Base('No email body set', 1);
        }

        $recipients = $this->isEscapingHtmlInRecipientVariables()
            ? $recipients = $this->getEscapedRecipients()
            : $this->getRecipients();

        if ($count < 1) {
            throw new Error\Base('No recipients specified', 1);
        } elseif ($count === 1) {
            $recipient = array_values($recipients)[0];
            if ($html) {
                $html = $this->replaceRecipientVariables($html, $recipient);
            }
            if ($text) {
                $text = $this->replaceRecipientVariables($text, $recipient);
            }
        } else {
            $CONFIG = $this->addRecipientVariablesToConfig($CONFIG, $recipients);
        }

        $CONFIG = $this->addMessageBodyToConfig($CONFIG, $html, $text);

        $CONFIG = $this->addCustomHeadersToConfig($CONFIG);
        $CONFIG = $this->addOptionsToConfig($CONFIG);
        $CONFIG = $this->addFSRtToConfig($CONFIG);
        $CONFIG = $this->addRecipientsToConfig($CONFIG);

        return $CONFIG;
    }

    /**
    * Adds recipient variables to config
    * @param array $config
    * @param array $vars
    * @return array
    */
    protected function addRecipientVariablesToConfig(array $config, array $vars = null)
    {
        $config['recipient-variables'] = json_encode($vars ?: $this->getRecipients());
        return $config;
    }

    /**
    * Adds To string to config
    * @param array $config
    * @return array
    */
    protected function addRecipientsToConfig(array $config)
    {
        $i = 0;
        $to = [];
        foreach ($recipients as $recipientEmail => $recipientVariables) {
            $to[$i] = $this->formatEmailString(
                $recipientEmail,
                isset($recipientVariables['name']) ? $recipientVariables['name'] : null
            );
            $i++;
        }

        $config['to'] = implode(', ', $to);

        return $config;
    }

    /**
     * Send message.
     *
     * @param array $config optional config override
     *
     * @return \stdClass Mailgun Response
     */
    public function send(array $config = null)
    {
        return $this->sendMessage($config ?: $this->getConfig());
    }

    /**
     * Get recipients with HTML-escaped variables.
     *
     * @return array
     */
    protected function getEscapedRecipients()
    {
        $recipients = $this->getRecipients();

        array_walk_recursive($recipients, function (&$value) {
            $value = htmlspecialchars($value, ENT_QUOTES);
        });

        return $recipients;
    }

    /**
     * Replaces %recipient.$key% with values from vars.
     *
     * @param string $string Email template
     * @param array  $vars   Recipient's variables
     *
     * @return string Email template with replaced placeholders
     */
    protected function replaceRecipientVariables($string, $vars)
    {
        foreach ($vars as $key => $value) {
            $string = str_replace('%recipient.'.$key.'%', $value, $string);
        }

        return $string;
    }
}
