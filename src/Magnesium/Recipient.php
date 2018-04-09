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
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->vars[$key] ?? null;
    }

    /**
     * Set a recipient variable.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * Returns the recipient variables (HTML-escaped).
     *
     * @return array
     */
    public function getVariables(): array
    {
        $vars = $this->vars;

        array_walk_recursive($vars, function (&$value) {
            $value = htmlspecialchars($value, ENT_QUOTES);
        });

        return $vars;
    }
}
