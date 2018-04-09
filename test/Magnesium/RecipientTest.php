<?php

use Magnesium\Recipient;
use PHPUnit\Framework\TestCase;

class RecipientTest extends TestCase
{
    public function testRecipientConstruction()
    {
        $r = new Recipient('john.dee@example.com');

        $this->assertEquals('john.dee@example.com', $r->email);

        $r2 = new Recipient('john.dee@example.com', [
            'id' => 123,
            'name' => '<b>John Dee</b>',
        ]);

        $this->assertEquals('john.dee@example.com', $r2->email);
        $this->assertEquals(123, $r2->id);
        $this->assertEquals('<b>John Dee</b>', $r2->name);
    }

    public function testGettingVariables()
    {
        $r = new Recipient('john.dee@example.com', [
            'id' => 123,
            'name' => '<b>John Dee</b>',
            'nested' => [
                'thing' => 'foo',
            ],
        ]);

        $vars = $r->getVariables();

        $this->assertEquals('john.dee@example.com', $vars['email']);
        $this->assertEquals(123, $vars['id']);
        $this->assertEquals('&lt;b&gt;John Dee&lt;/b&gt;', $vars['name']);
        $this->assertEquals(['thing' => 'foo'], $vars['nested']);
        $this->assertEquals('foo', $vars['nested']['thing']);
    }
}
