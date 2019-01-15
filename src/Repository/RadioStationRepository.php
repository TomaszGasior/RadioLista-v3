<?php

namespace App\Repository;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RadioStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method RadioStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method RadioStation[]    findAll()
 * @method RadioStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RadioStationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RadioStation::class);
    }

    public function findForRadioTable(RadioTable $radioTable): array
    {
        switch ($radioTable->getSorting()) {
            case RadioTable::SORTING_NAME:
                return $this->findBy(
                    ['radioTable' => $radioTable],
                    [RadioTable::SORTING_NAME => 'ASC', RadioTable::SORTING_FREQUENCY => 'ASC']
                );

            case RadioTable::SORTING_FREQUENCY:
                return $this->findBy(
                    ['radioTable' => $radioTable],
                    [RadioTable::SORTING_FREQUENCY => 'ASC']
                );

            case RadioTable::SORTING_PRIVATE_NUMBER:
                return $this->createQueryBuilder('radioStation')
                    ->addSelect(
                        // Move radiostations without private number to the end of the radiotable.
                        'CASE WHEN radioStation.privateNumber IS NULL THEN 1 ELSE 0 END AS HIDDEN privateNumberEmpty'
                    )
                    ->andWhere('radioStation.radioTable = :radioTable')
                    ->setParameter('radioTable', $radioTable)
                    ->addOrderBy('privateNumberEmpty', 'ASC')
                    ->addOrderBy('radioStation.privateNumber', 'ASC')
                    ->addOrderBy('radioStation.frequency', 'ASC')
                    ->getQuery()
                    ->getResult()
                ;
        }
    }

    public function getQueryForRadioTable(RadioTable $radioTable): QueryBuilder
    {
        return $this->createQueryBuilder('radioStation')
            ->andWhere('radioStation.radioTable = :radioTable')
            ->setParameter('radioTable', $radioTable)
            ->orderBy('radioStation.frequency')
        ;
    }
}
