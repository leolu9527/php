<?php

namespace Leolu9527\Test\UnitTest;

use Leolu9527\Container\Container;
use Leolu9527\Container\NotInContainerException;
use PHPUnit\Framework\TestCase;
use Leolu9527\Test\UnitTest\Fixtures\GreeterInterface;
use Leolu9527\Test\UnitTest\Fixtures\Greeter;
use Leolu9527\Test\UnitTest\Fixtures\Greeting;

class ContainerTest extends TestCase
{
    public function testSetGet()
    {
        $dummy = new \stdClass();
        $container = new Container(
            ['key' => $dummy]
        );

        $this->assertSame($dummy, $container->get('key'));
    }

    public function testGetNotFound()
    {
        $this->expectException(NotInContainerException::class);
        $container = new Container();
        $container->get('key');
    }

    public function testClosureIsResolved()
    {
        $closure = function () {
            return 'hello';
        };
        $container = new Container();
        $container->set('key', $closure);
        $this->assertEquals('hello', $container->get('key'));
    }

    public function testGetWithClassName()
    {
        $container = new Container;
        $container->set(\stdClass::class, \stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $container->get('stdClass'));
    }

    public function testGetWithInterface()
    {
        $container = new Container;
        $container->set(Greeting::class, Greeting::class);
        $container->set(GreeterInterface::class, Greeter::class);
        $this->assertInstanceOf(GreeterInterface::class, $container->get(GreeterInterface::class));
    }

    public function testGetResolvesEntryOnce()
    {
        $container = new Container;
        $container->set(\stdClass::class, \stdClass::class);
        $this->assertSame($container->get('stdClass'), $container->get('stdClass'));
    }
}