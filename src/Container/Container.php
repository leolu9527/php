<?php

namespace Leolu9527\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /**
     * @var array<string, callable|object|string>
     */
    private array $definitions;

    /**
     * @var array
     */
    private array $services = [];

    /**
     * Create a container object with a set of definitions.
     * @param array<string, callable|object|string> $definitions
     */
    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;
    }

    public function set(string $id, $definition)
    {
        if (!array_key_exists($id, $this->definitions)) {
            $this->definitions[$id] = $definition;
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        if (array_key_exists($id, $this->services)) {
            return $this->services[$id];
        }

        if (!$this->has($id)) {
            throw new NotInContainerException(sprintf(
                'There is not service with id "%s" in the container.',
                $id,
            ));
        }

        $definition = $this->definitions[$id];

        $service = $this->services[$id] = $this->getService($id, $definition);

        return $service;
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions);
    }

    private function getService($id, $definition)
    {
        if (is_callable($definition)) {
            try {
                return $definition();
            } catch (\Throwable $e) {
                throw new ContainerException(
                    sprintf('Error while invoking callable for "%s"', $id),
                    0,
                    $e,
                );
            }
        } elseif (is_object($definition)) {
            return $definition;
        } elseif (is_string($definition)) {
            if (class_exists($definition)) {
                try {
                    return $this->buildInstance($definition);
                } catch (\Throwable $e) {
                    throw new ContainerException(sprintf('Could not instantiate class "%s"', $id), 0, $e);
                }
            }

            throw new ContainerException(sprintf(
                'Could not instantiate class "%s". Class was not found.',
                $id,
            ));
        } else {
            throw new ContainerException(sprintf(
                'Invalid type for definition with id "%s"',
                $id,
            ));
        }
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     * @throws ContainerExceptionInterface
     */
    private function buildInstance(string $className)
    {
        $reflectionClass = new \ReflectionClass($className);

        if (!$reflectionClass->isInstantiable()) {
            throw new \ReflectionException("{$className} is not instantiable");
        }

        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        $dependencies = $this->getDependencies($constructor->getParameters());

        return $reflectionClass->newInstanceArgs($dependencies);
    }


    /**
     * @param \ReflectionParameter[] $parameters
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    private function getDependencies($parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getType();

            if (($dependency !== null && $dependency->isBuiltin()) || $dependency === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \ReflectionException("Unable to resolve dependency for parameter {$parameter->getName()}");
                }
            } else {
                $dependencies[] = $this->get($dependency->getName());
            }
        }

        return $dependencies;
    }
}