<?php

namespace App\Repository;

use App\Entity\DigitalRadioStation;
use App\Entity\RadioTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DigitalRadioStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DigitalRadioStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DigitalRadioStation[]    findAll()
 * @method DigitalRadioStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DigitalRadioStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DigitalRadioStation::class);
    }

    /**
     * @return DigitalRadioStation[]
     */
    public function findForRadioTable(RadioTable $radioTable): array
    {
        $queryBuilder = $this->createQueryBuilder('digitalRadioStation')
            ->innerJoin('digitalRadioStation.multiplex', 'multiplex')
            ->addSelect('multiplex')
            ->andWhere('multiplex.radioTable = :radioTable')
            ->setParameter('radioTable', $radioTable)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
