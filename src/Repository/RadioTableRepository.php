<?php

namespace App\Repository;

use App\Entity\RadioTable;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RadioTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method RadioTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method RadioTable[]    findAll()
 * @method RadioTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RadioTableRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RadioTable::class);
    }

    public function findPublicOrderedByRadioStationsCount()
    {
        return $this->findBy(
            ['status' => RadioTable::STATUS_PUBLIC],
            ['radioStationsCount' => 'DESC']
        );
    }

    public function findPublicOrderedByLastUpdateTime()
    {
        return $this->findBy(
            ['status' => RadioTable::STATUS_PUBLIC],
            ['lastUpdateTime' => 'DESC']
        );
    }

    public function findPublicOrderedByUseKhz()
    {
        return $this->findBy(
            ['status' => RadioTable::STATUS_PUBLIC],
            ['useKhz' => 'DESC']
        );
    }

    public function findOwnedByUser(User $user)
    {
        return $this->findBy(
            ['owner' => $user],
            ['lastUpdateTime' => 'DESC']
        );
    }

//    /**
//     * @return RadioTable[] Returns an array of RadioTable objects
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
    public function findOneBySomeField($value): ?RadioTable
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
