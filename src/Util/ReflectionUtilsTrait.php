<?php

namespace App\Util;

use ReflectionProperty;

/**
 * Should only be used in data fixtures and tests.
 */
trait ReflectionUtilsTrait
{
    protected function setPrivateFieldOfObject(object $object, string $fieldName, mixed $value): void
    {
        $reflection = new ReflectionProperty(get_class($object), $fieldName);

        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }
}
