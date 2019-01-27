<?php

namespace App\Doctrine\Mapping;

use Doctrine\ORM\Mapping\DefaultNamingStrategy;

class RLv2NamingStrategy extends DefaultNamingStrategy
{
    public function classToTableName($className): string
    {
        return parent::classToTableName($className) . 's';
    }

    public function joinColumnName($propertyName, $className = null): string
    {
        return $propertyName . 'Id';
    }
}
