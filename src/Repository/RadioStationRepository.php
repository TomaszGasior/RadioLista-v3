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
                        // Move radiostations without private number to the end of the radiotable.
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
}
