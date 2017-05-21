<?php

namespace Magnesium\Messages;

use Mailgun\Mailgun;

/**
 * Class for mass-messages and group-transactional email.
 *
 * This doenst support CC and BCC, because we always use recipvars
 * (or are sending only to one recipient)
 */
class Bulk
{
    protected $mgConfig;

    protected $mgDomain;

    protected $mgKey;

    protected $recipients = [];

    protected $customHeaders = [];

    protected $customVariables = [];

    protected $config = [];

    /**
     * Instantiate a bulk message with your Mailgun API-key and optionally.
     *
     * @param string $mgKey    Your Mailgun API-key.
     * @param string $mgDomain Your Mailgun domain.
     */
    public function __construct(string $mgKey, string $mgDomain)
    {
        $this->setDomain($mgDomain);
        $this->mgKey = $mgKey;
    }

    /**
     * Sets the domain to send message(s) from.
     *
     * @param string $domain
     *
     * @return Bulk
     */
    public function setDomain(string $domain)
    {
        $this->mgDomain = $domain;

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
     * @param string $email
     * @param string $name  Optional name to be used
     *
     * @return Bulk
     */
    public function setFrom(string $email, string $name = null)
    {
        $this->mgConfig['from'] = $name ? sprintf('%s <%s>', $name, $email) : $email;

        return $this;
    }

    /**
     * Gets the "From" address.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->mgConfig['from'];
    }

    /**
     * Sets the "Reply-To" address.
     *
     * @param string $email
     * @param string $name  Optional name to be used
     *
     * @return Bulk
     */
    public function setReplyTo(string $email, string $name = null)
    {
        $this->mgConfig['h:Reply-To'] = $name ? sprintf('%s <%s>', $name, $email) : $email;

        return $this;
    }

    /**
     * Gets the "Reply-To" address.
     *
     * @return string
     */
    public function getReplyTo()
    {
        return $this->mgConfig['h:Reply-To'];
    }

    /**
     * Sets the subject.
     *
     * @param string $subject
     *
     * @return Bulk
     */
    public function setSubject(string $subject)
    {
        $this->mgConfig['subject'] = $subject;

        return $this;
    }

    /**
     * Gets the subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->mgConfig['subject'];
    }

    /**
     * Sets the HTML-body.
     *
     * @param string $html
     *
     * @return Bulk
     */
    public function setHtml(string $html)
    {
        $this->mgConfig['html'] = $html;

        return $this;
    }

    /**
     * Gets the HTML-body.
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->mgConfig['html'];
    }

    /**
     * Sets the text-body.
     *
     * @param string $html
     *
     * @return Bulk
     */
    public function setText($txt)
    {
        $this->mgConfig['text'] = $txt;

        return $this;
    }

    /**
     * Gets the text-body.
     *
     * @return string
     */
    public function getText()
    {
        return $this->mgConfig['text'];
    }

    // public function setCampaign($id)
    // {
    //     // TODO
    //     return $this;
    // }
    //
    // public function getCampaign()
    // {
    //     // TODO
    //     return null;
    // }

    // public function enableDkim($bool = true)
    // {
    //     $value = $bool ? 'yes' : 'no';
    //     // TODO
    //     return $this;
    // }

    // public function isDkimEnabled()
    // {
    //     // TODO
    //     return (bool) true;
    // }

    /**
     * Set Mailgun Testmode.
     *
     * Mailgun will accept, but not send messages sent in testmode.
     *
     * @param bool $bool
     *
     * @return Bulk
     */
    public function setTestmode(bool $bool)
    {
        $this->mgConfig['o:testmode'] = $bool;

        return $this;
    }

    /**
     * Shows whether or not testmode is enabled.
     *
     * @return bool
     */
    public function isTestmode()
    {
        return (bool) $this->mgConfig['o:testmode'];
    }

    /**
     * @param bool $bool
     *
     * @return Bulk
     */
    public function setRequireTls(bool $bool)
    {
        $this->mgConfig['o:require-tls'] = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequiringTls()
    {
        return (bool) $this->mgConfig['o:require-tls'];
    }

    /**
     * @param bool $bool
     *
     * @return Bulk
     */
    public function setSkipVerification(bool $bool)
    {
        $this->mgConfig['o:skip-verification'] = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSkippingVerification()
    {
        return (bool) $this->mgConfig['o:skip-verification'];
    }

    /**
     * @param bool $bool
     *
     * @return Bulk
     */
    public function setTrackOpens($bool = true)
    {
        $this->mgConfig['o:tracking'] = $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTrackingOpens()
    {
        return (bool) $this->mgConfig['o:tracking'];
    }

    // public function enableTrackingClicks($bool = true)
    // {
    //     // TODO
    //     return $this;
    // }
    //
    // public function isTrackingClicks()
    // {
    //     // TODO
    //     return (bool) false;
    // }

    // public function enableTrackingOpens($bool = true)
    // {
    //     // TODO
    //     return $this;
    // }
    //
    // public function isTrackingOpens()
    // {
    //     // TODO
    //     return (bool) false;
    // }

    /**
     * Timestamp must be either RFC2822 or Unix epoch.
     *
     * @param $timestamp
     *
     * @return Bulk
     */
    public function setDeliveryTime($timestamp = null)
    {
        if (!$timestamp) {
            unset($this->mgConfig['o:deliverytime']);
        } else {
            $this->mgConfig['o:deliverytime'] = $timestamp;
        }

        return $this;
    }

    /**
     * Returns the delivery time as it was set.
     *
     * Magnesium doesn't normalize the set delivery time into a certain format.
     *
     * @return
     */
    public function getDeliveryTime()
    {
        return $this->mgConfig['o:deliverytime'];
    }

    /**
     * @param array $tags
     *
     * @return Bulk
     */
    public function setTags(array $tags = [])
    {
        $this->mgConfig['o:tag'] = $tags;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->mgConfig['o:tag'];
    }

    /**
     * @param bool $bool
     *
     * @return Bulk
     */
    public function setEscapeHtmlInRecipientVariables(bool $bool)
    {
        $this->config['escapeHtml'] = (bool) $bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEscapingHtmlInRecipientVariables()
    {
        return (bool) $this->config['escapeHtml'];
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return Bulk
     */
    public function addCustomHeader(string $name, string $value)
    {
        $this->customHeaders[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Bulk
     */
    public function removeCustomHeader(string $name)
    {
        unset($this->customHeaders[$name]);

        return $this;
    }

    /**
     * @return Bulk
     */
    public function removeAllCustomHeaders()
    {
        $this->customHeaders = [];

        return $this;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getCustomHeader(string $name)
    {
        return $this->customHeaders[$name];
    }

    // public function addCustomVariable($key, $value)
    // {
    //     $this->customVariables[$key] = $value;
    //     // if $value is %recipient.$key%, Mailgun will use recipvar
    //     // Custom vars show up on Webhook events and as Header in the email
    //
    //     return $this;
    // }
    //
    // public function removeCustomVariable($key)
    // {
    //     unset($this->customVariables[$key]);
    //
    //     return $this;
    // }
    //
    // public function removeAllCustomVariables()
    // {
    //     $this->customVariables = [];
    //
    //     return $this;
    // }
    //
    // public function getCustomVariable($key)
    // {
    //     return $this->customVariables[$key];
    // }

    /**
     * @param string $email
     * @param array  $vars
     *
     * @return Bulk
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
     * @param string $email
     *
     * @return Bulk
     */
    public function removeRecipient(string $email)
    {
        unset($this->recipients[$email]);

        return $this;
    }

    /**
     * @return Bulk
     */
    public function removeAllRecipients()
    {
        $this->recipients = [];

        return $this;
    }

    /**
     * @param string $email
     *
     * @return array
     */
    public function getRecipient(string $email)
    {
        return $this->recipients[$email];
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
     * Sends the message.
     *
     * @return array API-Response.
     */
    public function send()
    {
        if (!isset($this->mgDomain)) {
            throw new \Magnesium\Error('No Mailgun domain specified', 1);
        }

        if (!isset($this->mgKey)) {
            throw new \Magnesium\Error('No Mailgun key set', 1);
        }

        $count = $this->getRecipientCount();

        if ($count < 1) {
            throw new \Magnesium\Error('No recipients specified', 1);
        } elseif ($count === 1) {
            // Replace recipient variables for mailgun
            // Because Mailgun doesnt do recipvars if only sending to 1 email
            foreach (array_values($this->recipients)[0] as $key => $value) {
                if (isset($this->mgConfig['html'])) {
                    $val = $this->config['escapeHtml'] ? htmlspecialchars($value) : $value;

                    $this->mgConfig['html'] = str_replace('%recipient.'.$key.'%', $val, $this->mgConfig['html']);
                }
                if (isset($this->mgConfig['text'])) {
                    $this->mgConfig['text'] = str_replace('%recipient.'.$key.'%', $value, $this->mgConfig['text']);
                }
            }
        } else {
            // FIXME: Mailgun doesnt sanatize HTML chars
            // TODO: Sanize HTML (with option to disable)
            $this->mgConfig['recipient-variables'] = json_encode($this->recipients);
        }

        foreach ($this->customHeaders as $name => $value) {
            $this->mgConfig["h:$name"] = $value;
        }

        foreach ($this->customVariable as $key => $value) {
            $this->mgConfig["v:$key"] = $value;
            // TODO: Add recip vars support to custom variables
        }

        // TODO: Allow "First Last <firstlast@example.com>" notation
        $this->mgConfig['to'] = implode(', ', array_keys($this->recipients));
        // TODO: Set Reply-To to To, if no Reply-To.

        if (!isset($this->mgConfig['from'])) {
            $this->mgConfig['from'] = 'postmaster@'.$this->mgDomain;
        }

        $mgResponse = (new Mailgun($this->mgKey))->sendMessage($this->mgDomain, $this->mgConfig);

        return [
            'id' => $mgResponse->http_response_body->id,
            'message' => $mgResponse->http_response_body->message,
            'code' => $mgResponse->http_response_code,
        ];
    }
}
