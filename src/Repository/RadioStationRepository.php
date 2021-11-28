<?php

namespace App\Repository;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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
        return $this->getQueryBuilderForRadioTable($radioTable)
            ->getQuery()->getResult();
    }

    public function getQueryBuilderForRadioTable(RadioTable $radioTable): QueryBuilder
    {
        $query = $this->createQueryBuilder('radioStation')
            ->andWhere('radioStation.radioTable = :radioTable')
            ->setParameter('radioTable', $radioTable)
        ;

        switch ($radioTable->getSorting()) {
            case RadioTable::SORTING_NAME:
                $query->addOrderBy('radioStation.name', 'ASC');
                break;

            case RadioTable::SORTING_PRIVATE_NUMBER:
                $query
                    ->addSelect(
                        // Move radio stations without private number to the end of the radio table.
                        'CASE WHEN radioStation.privateNumber IS NULL THEN 1 ELSE 0 END AS HIDDEN privateNumberEmpty'
                    )
                    ->addOrderBy('privateNumberEmpty', 'ASC')
                    ->addOrderBy('radioStation.privateNumber', 'ASC')
                ;
                break;
        }

        $query->addOrderBy('radioStation.frequency', 'ASC');

        return $query;
    }

    public function findColumnAllValuesForRadioTable(RadioTable $radioTable, string $column): array
    {
        switch ($column) {
            case RadioTable::COLUMN_NAME:
            case RadioTable::COLUMN_LOCATION:
            case RadioTable::COLUMN_RADIO_GROUP:
            case RadioTable::COLUMN_COUNTRY:
            case RadioTable::COLUMN_MULTIPLEX:
                return $this->createQueryBuilder('radioStation')
                    ->select('DISTINCT radioStation.'.$column)
                    ->andWhere('radioStation.radioTable = :radioTable')
                    ->setParameter('radioTable', $radioTable)
                    ->andWhere('radioStation.'.$column.' IS NOT NULL')
                    ->addOrderBy('radioStation.'.$column, 'ASC')
                    ->getQuery()
                    ->getSingleColumnResult()
                ;

            default:
                throw new \Exception(sprintf('Column "%s" is not supported.', $column));
        }
    }
}
