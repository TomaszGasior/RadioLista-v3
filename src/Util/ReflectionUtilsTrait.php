<?php

namespace App\Util;

use ReflectionProperty;

trait ReflectionUtilsTrait
{
    protected function getPrivateFieldOfObject(object $object, string $fieldName): mixed
    {
        $reflection = new ReflectionProperty(get_class($object), $fieldName);

        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function setPrivateFieldOfObject(object $object, string $fieldName, mixed $value): void
    {
        $reflection = new ReflectionProperty(get_class($object), $fieldName);

        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }
}
