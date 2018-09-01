<?php

namespace App\Repository;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findForRadioTable(RadioTable $radioTable)
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
                        // It's needed to move radiostations without private number to the end of the radiotable.
                        'CASE WHEN radioStation.privateNumber = 0 THEN 1 ELSE 0 END AS HIDDEN privateNumberEmpty'
                    )
                    ->andWhere('radioStation.radioTable = :radioTable')
                    ->setParameter('radioTable', $radioTable)
                    ->addOrderBy('privateNumberEmpty', 'ASC')
                    ->addOrderBy('radioStation.privateNumber', 'ASC')
                    ->addOrderBy('radioStation.frequency', 'ASC')
                    ->getQuery()
                    ->getResult()
                ;
                break;
        }

    }

//    /**
//     * @return RadioStation[] Returns an array of RadioStation objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RadioStation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
