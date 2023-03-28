<?php

namespace Leolu9527\Container;

use Psr\Container\ContainerInterface;

final class ContainerBuilder
{
    /**
     * @var array<string, callable|object|string>
     */
    private $definitions = [];

    /**
     * @param callable|object|string $value
     *
     * @throws \InvalidArgumentException
     */
    public function add($value, string $name = null): self
    {
        if (!is_string($value) && !is_callable($value) && !is_object($value)) {
            throw new \InvalidArgumentException(sprintf(
                'First argument to "%s::add()" must be a string, callable or object.',
                self::class,
            ));
        }

        if ($name === null) {
            if (is_string($value)) {
                $name = $value;
            } elseif (is_object($value)) {
                $name = get_class($value);
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Second argument to "%s::add()" is required not passing a string or object as first argument',
                    self::class,
                ));
            }
        }

        $this->definitions[$name] = $value;

        return $this;
    }

    public function build(): ContainerInterface
    {
        return new Container($this->definitions);
    }

    /**
     * Get an array with extension that represent the default English
     * functionality.
     */
    public static function defaultExtensions(): array
    {
        return [
            \Leolu9527\App\GreeterInterface::class => \Leolu9527\App\Greeter::class,
            \Leolu9527\App\Greeting::class => \Leolu9527\App\Greeting::class,
        ];
    }

    public static function getDefault(): ContainerInterface
    {
        $instance = new self();

        foreach (self::defaultExtensions() as $id => $definition) {
            $instance->add($definition, $id);
        }

        return $instance->build();
    }
}