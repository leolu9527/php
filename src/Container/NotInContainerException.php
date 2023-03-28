<?php

namespace Leolu9527\Container;

use Psr\Container\NotFoundExceptionInterface;

final class NotInContainerException extends \RuntimeException implements NotFoundExceptionInterface
{

}