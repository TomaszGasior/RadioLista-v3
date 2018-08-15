<?php

namespace App\Repository;

use App\Entity\RadioStation;
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
