<?php

namespace App\Tests\Util;

use App\Util\ReflectionUtilsTrait;
use PHPUnit\Framework\TestCase;

class ReflectionUtilsTraitTest extends TestCase
{
    /** @var ReflectionUtilsTrait|MockObject */
    private $reflectionUtils;

    private $instance;

    public function setUp(): void
    {
        $this->reflectionUtils = new class {
            use ReflectionUtilsTrait {
                getPrivateFieldOfObject as public;
                setPrivateFieldOfObject as public;
                getPrefixedConstantsOfClass as public;
            }
        };

        $this->instance = new class {
            public const PREFIXED_CONST_FIRST = 'value_1';
            public const PREFIXED_CONST_SECOND = 'value_2';
            public const PREFIXED_CONST_THIRD = 'value_3';
            public const OTHER_PREFIXED_CONST_FIRST = 'x_value_1';
            public const OTHER_PREFIXED_CONST_SECOND = 'x_value_2';
            public const OTHER_PREFIXED_CONST_THIRD = 'x_value_3';

            private $privateField = 'private_data';

            public function getValueOfPrivateField()
            {
                return $this->privateField;
            }
        };
    }

    public function testGetPrivateFieldOfObject(): void
    {
        $data = $this->reflectionUtils->getPrivateFieldOfObject($this->instance, 'privateField');
        $this->assertEquals('private_data', $data);
    }

    public function testSetPrivateFieldOfObject(): void
    {
        $this->reflectionUtils->setPrivateFieldOfObject($this->instance, 'privateField', 'CHANGED');
        $this->assertEquals('CHANGED', $this->instance->getValueOfPrivateField());
    }

    public function testGetPrefixedConstantsOfClass(): void
    {
        $constants = [
            'PREFIXED_CONST_FIRST' => 'value_1',
            'PREFIXED_CONST_SECOND' => 'value_2',
            'PREFIXED_CONST_THIRD' => 'value_3',
        ];
        $result = $this->reflectionUtils->getPrefixedConstantsOfClass(get_class($this->instance), 'PREFIXED_');
        $this->assertEquals($constants, $result);
    }
}
