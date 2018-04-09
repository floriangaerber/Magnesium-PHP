<?php

namespace Magnesium;

class Recipient
{
    /**
     * Recipient's email address.
     *
     * @var string
     */
    protected $email;

    /**
     * Recipient variables.
     *
     * @var array
     */
    protected $vars;

    /**
     * Creates a new Recipient instance.
     *
     * Adds email as variable.
     *
     * @param string $email
     * @param array  $vars
     */
    public function __construct(string $email, array $vars = [])
    {
        $this->email = $email;
        $this->vars = $vars;
        $this->vars['email'] = $email;
    }

    /**
     * Get a recipient variable.
     *
     * @param mixed $key
     */
    public function __get($key)
    {
        return $this->vars[$key] ?? null;
    }
}
