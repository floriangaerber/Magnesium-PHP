<?php

/**
 * This file is part of the Magnesium package.
 */

namespace Magnesium;

use Mailgun\Mailgun;

/**
 * The main Magnesium class.
 */
class Magnesium
{
    protected $mgKey;

    /**
     * @param string $mgKey    Mailgun Private Key
     * @param string $mgDomain (Optional) Mailgun Domain
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
     * Gets email string from email and name.
     *
     * Possible usage formats:
     * - setFrom('hello@floriangaer.be')
     * - setFrom('Florian <hello@floriangaer.be>')
     * - setFrom('hello@floriangaer.be', 'Florian')
     *
     * @param string $email Email address
     * @param string $name  (Optional) Name
     */
    public function getEmailString(string $email, string $name = null)
    {
        return $name ? sprintf('%s <%s>', $name, $email) : $email;
    }

    /**
     * @param string $mgDomain Your mailgun domain to send from.
     *
     * @return Messages\Bulk
     */
    public function newBulkMessage(string $mgDomain)
    {
        return new Messages\Bulk($this->mgKey, $mgDomain);
    }
}
