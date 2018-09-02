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

    public function findPublicOrderedByRadioStationsCount(): array
    {
        return $this->findBy(
            ['status' => RadioTable::STATUS_PUBLIC],
            ['radioStationsCount' => 'DESC']
        );
    }

    public function findPublicOrderedByLastUpdateTime(): array
    {
        return $this->findBy(
            ['status' => RadioTable::STATUS_PUBLIC],
            ['lastUpdateTime' => 'DESC']
        );
    }

    public function findPublicOrderedByUseKhz(): array
    {
        return $this->findBy(
            ['status' => RadioTable::STATUS_PUBLIC],
            ['useKhz' => 'DESC']
        );
    }

    public function findOwnedByUser(User $user): array
    {
        return $this->findBy(
            ['owner' => $user],
            ['lastUpdateTime' => 'DESC']
        );
    }
}
