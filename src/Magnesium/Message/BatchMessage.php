<?php

namespace Magnesium\Message;

use Magnesium\Recipient;

/**
 * This message class allows sending batch messages to 1 or more users via
 * recipient-variables.
 */
class BatchMessage extends AbstractMessage
{
    /**
     * Recipients.
     *
     * @var array[Recipient]
     */
    protected $recipients = [];

    /**
     * Add a recipient by email, optionally with recipient variables.
     *
     * @param string $email
     * @param array  $vars
     *
     * @return self
     */
    public function addNewRecipient($email, array $vars = []): self
    {
        $this->recipients[$email] = new Recipient($email, $vars);

        return $this;
    }

    /**
     * Add an existing Recipient object.
     *
     * @param Recipient $recipient
     *
     * @return self
     */
    public function addRecipient(Recipient $recipient): self
    {
        $this->recipients[$recipient->email] = $recipient;

        return $this;
    }

    /**
     * Remove a recipient by email.
     *
     * @param string $email
     *
     * @return self
     */
    public function removeRecipient(string $email): self
    {
        unset($this->recipients[$email]);

        return $this;
    }

    /**
     * Remove all recipients.
     *
     * @return self
     */
    public function removeRecipients(): self
    {
        $this->recipients = [];

        return $this;
    }

    /**
     * Get recipient by email.
     *
     * @param string $email
     *
     * @return Recipient
     */
    public function getRecipient(string $email): Recipient
    {
        return $this->recipients[$email];
    }

    /**
     * Get all recipients.
     *
     * @return array[Recipients]
     */
    public function getRecipients(): array
    {
        return array_values($this->recipients);
    }

    /**
     * Returns the count of recipients.
     *
     * @return int
     */
    public function getRecipientCount(): int
    {
        return count($this->recipients);
    }

    /**
     * Add Recipients to the To-string.
     *
     * @param array $config
     *
     * @return array
     */
    protected function addRecipientsToConfig(array $config): array
    {
        $to = [];

        foreach ($this->getRecipients() as $recipient) {
            $to[] = $this->formatEmailString(
                $recipient->email,
                $recipient->name
            );
        }

        $config['to'] = implode(', ', $to);

        return $config;
    }

    /**
     * Add recipient variables to the config.
     *
     * @param array $config
     *
     * @return array
     */
    protected function addRecipientVariablesToConfig(array $config): array
    {
        $vars = [];

        foreach ($this->getRecipients() as $recipient) {
            $vars[$recipient->email] = $recipient->getVariables();
        }

        $config['recipient-variables'] = json_encode($vars);

        return $config;
    }

    protected function addMessageBodyToConfig(array $config = []): array
    {
        // If there is only 1 recipient: Replace recipient variables in message body
        if (1 === $this->getRecipientCount()) {
            $recipient = $this->getRecipients()[0];

            $html = $this->hasHtml()
                ? $this->replaceRecipientVariables($this->getHtml(), $recipient->getVariables())
                : null;
            // Here we can allow unescaped variables
            $text = $this->hasText()
                ? $this->replaceRecipientVariables($this->getText(), $recipient->getUnescapedVariables())
                : null;

            $config['html'] = $html;
            $config['text'] = $text;
        } else {
            $config['html'] = $this->getHtml();
            $config['text'] = $text->getText();
            $config = $this->addRecipientVariablesToConfig($config);
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(array $config = []): array
    {
        $config = parent::getConfig();
        $config = $this->addRecipientsToConfig($config);

        return $config;
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
            if (is_array($value)) {
                throw new \InvalidArgumentException('Recipient variables must not be nested');
            }
            $string = str_replace('%recipient.'.$key.'%', $value, $string);
        }

        return $string;
    }
}
