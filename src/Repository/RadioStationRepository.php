<?php

namespace App\Repository;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @method RadioStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method RadioStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method RadioStation[]    findAll()
 * @method RadioStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RadioStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RadioStation::class);
    }

    public function findForRadioTable(RadioTable $radioTable): array
    {
        $queryBuilder = $this->createQueryBuilder('radioStation')
            ->andWhere('radioStation.radioTable = :radioTable')
            ->setParameter('radioTable', $radioTable)
        ;

        switch ($radioTable->getSorting()) {
            case Column::NAME:
                $queryBuilder->addOrderBy('radioStation.name', 'ASC');
                break;

            case Column::PRIVATE_NUMBER:
                $queryBuilder
                    ->addSelect(
                        // Move radio stations without private number to the end of the radio table.
                        'CASE WHEN radioStation.privateNumber IS NULL THEN 1 ELSE 0 END AS HIDDEN privateNumberEmpty'
                    )
                    ->addOrderBy('privateNumberEmpty', 'ASC')
                    ->addOrderBy('radioStation.privateNumber', 'ASC')
                ;
                break;
        }

        $queryBuilder->addOrderBy('radioStation.frequency', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function findColumnAllValuesForRadioTable(RadioTable $radioTable, Column $column): array
    {
        switch ($column) {
            case Column::NAME:
            case Column::LOCATION:
            case Column::RADIO_GROUP:
            case Column::COUNTRY:
            case Column::REGION:
            case Column::MULTIPLEX:
                return $this->createQueryBuilder('radioStation')
                    ->select('DISTINCT radioStation.'.$column->value)
                    ->andWhere('radioStation.radioTable = :radioTable')
                    ->setParameter('radioTable', $radioTable)
                    ->andWhere('radioStation.'.$column->value.' IS NOT NULL')
                    ->andWhere('radioStation.'.$column->value.' != \'\'')
                    ->addOrderBy('radioStation.'.$column->value, 'ASC')
                    ->getQuery()
                    ->getSingleColumnResult()
                ;

            default:
                throw new RuntimeException(sprintf('Column "%s" is not supported.', $column->value));
        }
    }
}
