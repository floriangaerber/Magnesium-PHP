<?php

use Magnesium\Message\BatchMessage;
use Magnesium\Recipient;
use PHPUnit\Framework\TestCase;

class BatchMessageTest extends TestCase
{
    public function testConstruct()
    {
        $m = new BatchMessage('MAILGUN_KEY', 'MAILGUN_DOMAIN');

        $this->assertEquals('MAILGUN_DOMAIN', $m->getDomain());
    }

    public function testSetDomain()
    {
        $m = new BatchMessage('MAILGUN_KEY', 'MAILGUN_DOMAIN');

        $m->setDomain('example.com');

        $this->assertEquals('example.com', $m->getDomain());
    }

    public function testSetFrom()
    {
        $m = new BatchMessage('MAILGUN_KEY', 'MAILGUN_DOMAIN');

        $this->assertEquals('postmaster@'.'MAILGUN_DOMAIN', $m->getFromEmail());
        $this->assertEquals(null, $m->getFromName());

        $m->setFrom('hello@example.com');
        $this->assertEquals('hello@example.com', $m->getFromEmail());
        $this->assertEquals(null, $m->getFromName());
        $this->assertEquals('hello@example.com', $m->getFromString());

        $m->setFrom('john.dee@example.com', 'John Dee');
        $this->assertEquals('john.dee@example.com', $m->getFromEmail());
        $this->assertEquals('John Dee', $m->getFromName());
        $this->assertEquals('John Dee <john.dee@example.com>', $m->getFromString());

        $m->setFrom('elizabeth@example.uk');
        $this->assertEquals('elizabeth@example.uk', $m->getFromEmail());
        $this->assertEquals(null, $m->getFromName());
        $this->assertEquals('elizabeth@example.uk', $m->getFromString());
    }

    public function testReplyTo()
    {
        $m = new BatchMessage('MAILGUN_KEY', 'MAILGUN_DOMAIN');

        $this->assertEquals(null, $m->getReplyToEmail());
        $this->assertEquals(null, $m->getReplyToName());
        $this->assertEquals(null, $m->getReplyToString());

        $m->setReplyTo('hello@example.com');
        $this->assertEquals('hello@example.com', $m->getReplyToEmail());
        $this->assertEquals(null, $m->getReplyToName());
        $this->assertEquals('hello@example.com', $m->getReplyToString());

        $m->setReplyTo('john.dee@example.com', 'John Dee');
        $this->assertEquals('john.dee@example.com', $m->getReplyToEmail());
        $this->assertEquals('John Dee', $m->getReplyToName());
        $this->assertEquals('John Dee <john.dee@example.com>', $m->getReplyToString());

        $m->setReplyTo('elizabeth@example.uk');
        $this->assertEquals('elizabeth@example.uk', $m->getReplyToEmail());
        $this->assertEquals(null, $m->getReplyToName());
        $this->assertEquals('elizabeth@example.uk', $m->getReplyToString());
    }

    public function testSetSubject()
    {
        $m = new BatchMessage('MAILGUN_KEY', 'MAILGUN_DOMAIN');

        $this->assertEquals('', $m->getSubject());

        $m->setSubject('Top Secret Information');
        $this->assertEquals('Top Secret Information', $m->getSubject());
    }

    public function testSetMessageBody()
    {
        $m = new BatchMessage('MAILGUN_KEY', 'MAILGUN_DOMAIN');

        $this->assertEquals(null, $m->getHtml());
        $this->assertEquals(null, $m->getText());

        $m->setHtml('Email Body');
        $this->assertEquals('Email Body', $m->getHtml());
        $this->assertEquals(null, $m->getText());

        $m->setText('Email Text Body');
        $this->assertEquals('Email Body', $m->getHtml());
        $this->assertEquals('Email Text Body', $m->getText());

        $m->setHtml();
        $this->assertEquals(null, $m->getHtml());
        $this->assertEquals('Email Text Body', $m->getText());

        $m->setText();
        $this->assertEquals(null, $m->getHtml());
        $this->assertEquals(null, $m->getText());
    }

    public function testMailgunOptions()
    {
        $m = new BatchMessage('MAILGUN_KEY', 'MAILGUN_DOMAIN');

        $this->assertEquals(false, $m->isTestmode());
        $this->assertEquals(true, $m->isRequiringTls());
        $this->assertEquals(false, $m->isSkippingVerification());
        $this->assertEquals([], $m->getTags());

        $m->setSkipVerification(true);
        $this->assertEquals(true, $m->isSkippingVerification());
        $m->setRequireTls(false);
        $this->assertEquals(false, $m->isRequiringTls());
        $m->setTestmode(true);
        $this->assertEquals(true, $m->isTestmode());
        $m->setTags(['really', 'awesome', 'test']);
        $this->assertEquals(['really', 'awesome', 'test'], $m->getTags());
    }

    public function testDeliveryTime()
    {
        $m = new BatchMessage('MAILGUN_KEY', 'MAILGUN_DOMAIN');

        $this->assertEquals(null, $m->getDeliveryTime());

        // TODO More elaborate

        $m->setDeliveryTime('now');
        $this->assertEquals((new \DateTime('now'))->format(\DateTime::RFC2822), $m->getDeliveryTime());
    }

    public function testCustomHeaders()
    {
        $m = new BatchMessage('MAILGUN_KEY', 'MAILGUN_DOMAIN');

        $this->assertEquals([], $m->getCustomHeaders());
        $this->assertEquals(null, $m->getCustomHeader('No-Header'));

        $m->addCustomHeader('My-Header', 'My-Value');
        $this->assertEquals(['My-Header' => 'My-Value'], $m->getCustomHeaders());
        $this->assertEquals('My-Value', $m->getCustomHeader('My-Header'));
        $m->addCustomHeader('Other-Header', 'New-Value');
        $m->removeCustomHeader('My-Header');
        $this->assertEquals(['Other-Header' => 'New-Value'], $m->getCustomHeaders());
        $this->assertEquals('New-Value', $m->getCustomHeader('Other-Header'));
        $this->assertEquals(null, $m->getCustomHeader('My-Header'));
        $m->removeCustomHeaders();
        $this->assertEquals([], $m->getCustomHeaders());
    }

    public function testAddingRecipients()
    {
        $m = new BatchMessage('MAILGUN_KEY', 'MAILGUN_DOMAIN');

        $this->assertEquals([], $m->getRecipients());
        $this->assertEquals(0, $m->getRecipientCount());

        $m->addNewRecipient('john.dee@example.com', [
            'id' => 123,
            'name' => 'John Dee',
        ]);

        $recipient = new Recipient('john.dee@example.com', [
            'id' => 123,
            'name' => 'John Dee',
        ]);

        $this->assertEquals([$recipient], $m->getRecipients());
        $this->assertEquals($recipient, $m->getRecipient('john.dee@example.com'));
        $this->assertEquals(1, $m->getRecipientCount());

        // This belongs into testAddRecipientsToConfig
        // $this->assertEquals(
        //     sprintf('%s <%s>', $recipient->name, $recipient->email),
        //     $m->getConfig()['to']
        // );

        // TODO ...
    }
}
