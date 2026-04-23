<?php

namespace App\Row;

use App\Model\Row;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class RadioTableRowFactory
{
    /**
     * @param RowFactoryInterface[] $factories
     */
    public function __construct(
        #[AutowireIterator(RowFactoryInterface::class)] private iterable $factories,
    ) {}

    public function create(object $object): Row
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($object)) {
                return $factory->create($object);
            }
        }

        throw new RuntimeException(sprintf('Object "%s" is not supported.', $object::class));
    }
}
