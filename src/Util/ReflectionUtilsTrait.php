<?php

namespace App\Util;

use ReflectionClass;

/**
 * Should only be used in data fixtures and tests.
 */
trait ReflectionUtilsTrait
{
    protected function setPrivateFieldOfObject(object $object, string $fieldName, mixed $value): void
    {
        $reflection = new ReflectionClass($object::class);

        while (!$reflection->hasProperty($fieldName) && $reflection->getParentClass()) {
            $reflection = $reflection->getParentClass();
        }

        $property = $reflection->getProperty($fieldName);

        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
