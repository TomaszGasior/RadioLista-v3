<?php

namespace App\DigitalMigration\Row;

use App\Entity\Enum\RadioStation\DabChannel;
use App\Entity\RadioStation;
use App\Model\Row;
use App\Row\RadioStationRowFactory;
use App\Row\RowFactoryInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: RadioStationRowFactory::class)]
class DigitalMigrationDecoratedRadioStationRowFactory implements RowFactoryInterface
{
    public function __construct(private RadioStationRowFactory $inner) {}

    public function create(object $object): Row
    {
        $row = $this->inner->create($object);

        if (!$object instanceof RadioStation) {
            throw new RuntimeException;
        }

        $radioStation = $object;

        $data = get_object_vars($row);

        $data['multiplex'] = $radioStation->getMultiplex();
        $data['dabChannel'] = $radioStation->getDabChannel() ? constant(DabChannel::class . '::CH_' . $radioStation->getDabChannel()) : null;

        return new Row(...$data);
    }

    public function supports(object $object): bool
    {
        return $this->inner->supports($object);
    }
}
