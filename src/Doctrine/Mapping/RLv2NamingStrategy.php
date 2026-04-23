<?php

namespace App\Doctrine\Mapping;

use Doctrine\ORM\Mapping\DefaultNamingStrategy;

class RLv2NamingStrategy extends DefaultNamingStrategy
{
    public function classToTableName($className): string
    {
        $tableName = parent::classToTableName($className);

        return $tableName . ($tableName[-1] === 'x' ? 'es' : 's');
    }

    public function joinColumnName($propertyName, $className = null): string
    {
        return $propertyName . 'Id';
    }
}
