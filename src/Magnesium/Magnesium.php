<?php

/**
 * This file is part of the floriangaerber/magnesium package.
 *
 * @copyright 2017 Florian Gärber
 * @license MIT
 * @license See "LICENSE" for details
 */

namespace Magnesium;

use Mailgun\Mailgun;

/**
 * The main Magnesium class.
 */
class Magnesium
{
    /**
     * Your Mailgun API key.
     *
     * @property string $mgKey
     */
    protected $mgKey;

    /**
     * Instantiate Magnesium with your API-key.
     *
     * @param string $mgKey Mailgun Private Key
     */
    public function __construct(string $mgKey)
    {
        $this->mgKey = $mgKey;
    }

    /**
     * Validates a Webhook.
     *
     * @param string|int $timestamp
     * @param string     $token
     * @param string     $signature
     *
     * @source https://github.com/mailgun/mailgun-php/blob/ce484ecbc823ababb98e1402a98109a500a69e3a/src/Mailgun/Api/Webhook.php
     *
     * @return bool
     */
    public function validateWebhook($timestamp, string $token, string $signature): bool
    {
        if (empty($timestamp) || empty($token) || empty($signature)) {
            return false;
        }

        $hmac = hash_hmac('sha256', $timestamp.$token, $this->mgKey);

        return hash_equals($hmac, $signature);
    }

    /**
     * Create a new BatchMessage.
     *
     * @param string $domain Mailgun domain to send from
     *
     * @return BatchMessage
     */
    public function createBatchMessage(string $domain)
    {
        return new BatchMessage($this->mgKey, $domain);
    }

    /**
     * This removes "To" breaking symbols (For messages with multiple To).
     *
     * Known breaking symbols are: `<`, `>`, `,` (and `@` for names).
     *
     * Example for a breaking name: "x@example, Bill <y@example>, Joe"
     * In To: "Allen <a@example>, x@example, Bill <y@example>, Joe <b@example>"
     * (With recipients Allen <a@exmaple> and the breaking name <b@example>).
     *
     * @param string $string  Name or email
     * @param bool   $isEmail
     *
     * @return string
     */
    public static function removeToStringBreakingSymbols(string $string, bool $isEmail): string
    {
        return str_replace(
            ['>', '<', ','],
            '',
            $isEmail ? $string : str_replace('@', '', $string)
        );
    }
}
