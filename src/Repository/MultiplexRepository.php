<?php

namespace App\Repository;

use App\Entity\Multiplex;
use App\Entity\RadioTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Multiplex|null find($id, $lockMode = null, $lockVersion = null)
 * @method Multiplex|null findOneBy(array $criteria, array $orderBy = null)
 * @method Multiplex[]    findAll()
 * @method Multiplex[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MultiplexRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Multiplex::class);
    }

    /**
     * @return Multiplex[]
     */
    public function findForRadioTable(RadioTable $radioTable): array
    {
        return $this->findBy(['radioTable' => $radioTable]);
    }
}
