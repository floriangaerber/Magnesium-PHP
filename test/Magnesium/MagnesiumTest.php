<?php

use Magnesium\Magnesium;
use PHPUnit\Framework\TestCase;

class MagnesiumTest extends TestCase
{
    /**
     * The keys used here are taken from mailgun/mailgun-php's tests.
     *
     * @see https://github.com/mailgun/mailgun-php/blob/ce484ecbc823ababb98e1402a98109a500a69e3a/tests/MailgunTest.php
     */
    public function testIsValidWebhookPasses()
    {
        $mg = new Magnesium('key-3ax6xnjp29jd6fds4gc373sgvjxteol0');

        $this->assertTrue($mg->validateWebhook(
            '1403645220',
            '5egbgr1vjgqxtrnp65xfznchgdccwh5d6i09vijqi3whgowmn6',
            '9cfc5c41582e51246e73c88d34db3af0a3a2692a76fbab81492842f000256d33'
        ));
    }

    /**
     * The keys used here are taken from mailgun/mailgun-php's tests.
     *
     * @see https://github.com/mailgun/mailgun-php/blob/ce484ecbc823ababb98e1402a98109a500a69e3a/tests/MailgunTest.php
     */
    public function testIsValidWebhookFails()
    {
        $mg = new Magnesium('key-3ax6xnjp29jd6fds4gc373sgvjxteol0');

        $this->assertFalse($mg->validateWebhook(
            '1403645220',
            'owyldpe6nxhmrn78epljl6bj0orrki1u3d2v5e6cnlmmuox8jr',
            '9cfc5c41582e51246e73c88d34db3af0a3a2692a76fbab81492842f000256d33'
        ));
    }
}
