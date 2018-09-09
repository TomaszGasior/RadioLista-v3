<?php

namespace App\Doctrine\Mapping;

use Doctrine\ORM\Mapping\DefaultNamingStrategy;

class RLv2NamingStrategy extends DefaultNamingStrategy
{
    public function classToTableName($className)
    {
        return parent::classToTableName($className) . 's';
    }

    public function joinColumnName($propertyName, $className = null)
    {
        return $propertyName . 'Id';
    }
}
