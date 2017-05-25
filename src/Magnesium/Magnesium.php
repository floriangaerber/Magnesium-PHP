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
     * Validates a webhook.
     *
     * @param array $webhook Recieved webhook as array
     *
     * @return bool
     */
    public function isValidWebhook(array $webhook)
    {
        if (!isset($webhook['timestamp'])
        || !isset($webhook['token'])
        || !isset($webhook['signature'])) {
            return false;
        }

        $ts = (int) $webhook['timestamp'];

        if (abs(time() - $ts) > 15) {
            return false;
        }

        return hash_hmac(
            'sha256',
            $ts.$webhook['token'],
            $this->mgKey
        ) === $webhook['signature'];
    }

    /**
     * Create a new BulkMessage.
     *
     * @param string $mgDomain your mailgun domain to send from
     *
     * @return Message\BulkMessage
     */
    public function newBulkMessage(string $mgDomain)
    {
        return new Message\BulkMessage($this->mgKey, $mgDomain);
    }
}
