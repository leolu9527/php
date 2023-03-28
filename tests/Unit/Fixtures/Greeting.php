<?php

namespace Leolu9527\Test\UnitTest\Fixtures;

class Greeting
{
    private string $message;

    public function __construct(string $message = 'Hello', callable $fun = null)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}