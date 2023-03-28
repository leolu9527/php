<?php

namespace Leolu9527\Test\UnitTest\Fixtures;

class Greeter implements GreeterInterface
{
    private Greeting $greeting;

    public function __construct(Greeting $greeting)
    {
        $this->greeting = $greeting;
    }

    public function greet(string $name): string
    {
        return $this->greeting->getMessage() . ' ' . $name . '!';
    }
}