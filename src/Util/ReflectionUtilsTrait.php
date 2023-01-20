<?php

namespace App\Util;

use ReflectionClass;
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

    /**
     * Returns array with constant names as keys and constant values as values.
     */
    protected function getPrefixedConstantsOfClass(string $className, string $prefix): array
    {
        $reflection = new ReflectionClass($className);
        $constants = $reflection->getConstants();

        return array_filter(
            $constants,
            function ($constantName) use ($prefix){
                return 0 === strpos($constantName, $prefix);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
