<?php

namespace App\Repository;

use App\Entity\RadioTable;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RadioTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method RadioTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method RadioTable[]    findAll()
 * @method RadioTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RadioTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RadioTable::class);
    }

    public function findPublicOrderedByRadioStationsCount(): array
    {
        return $this->findAllPublic('radioStationsCount', null);
    }

    public function findPublicOrderedByLastUpdateTime(int $limit = null): array
    {
        return $this->findAllPublic('lastUpdateTime', $limit);
    }

    public function findPublicOrderedByUseKhz(): array
    {
        return $this->findAllPublic('useKhz', null);
    }

    public function findPublicOrderedByIdDesc(int $limit = null): array
    {
        return $this->findAllPublic('id', $limit);
    }

    private function findAllPublic(string $orderBy, ?int $limit): array
    {
        $query = $this->createQueryBuilder('radioTable')
            ->andWhere('radioTable.status = :status')
            ->setParameter('status', RadioTable::STATUS_PUBLIC)
            ->orderBy('radioTable.'.$orderBy, 'DESC')
        ;

        if ($limit) {
            $query->setMaxResults($limit);
        }
        else {
            // If there is no limit, result will be probably used on the radiotables list.
            // Optimize query for fetching radiotable owners.
            $query->innerJoin('radioTable.owner', 'user')->addSelect('user');
        }

        return $query->getQuery()->setCacheable(true)->getResult();
    }

    public function findPublicBySearchTerm(string $searchTerm): array
    {
        // Search term equal to "*" causes MySQL error.
        if ('*' === $searchTerm) {
            return [];
        }

        // MATCH AGAINST keyword works only with MySQL-like databases.
        if (!($this->getEntityManager()->getConnection()->getDatabasePlatform() instanceof MySqlPlatform)) {
            return [];
        }

        return $this->createQueryBuilder('radioTable')
            ->andWhere('radioTable.status = :status')
            ->setParameter('status', RadioTable::STATUS_PUBLIC)
            ->andWhere('MATCH(radioTable.name, radioTable.description) AGAINST(:searchTerm BOOLEAN) > 0.0')
            ->setParameter('searchTerm', $searchTerm)
            ->innerJoin('radioTable.owner', 'user')->addSelect('user')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPublicOwnedByUser(User $user): array
    {
        return $this->findBy(
            ['owner' => $user, 'status' => RadioTable::STATUS_PUBLIC],
            ['lastUpdateTime' => 'DESC']
        );
    }

    public function findAllOwnedByUser(User $user): array
    {
        return $this->findBy(
            ['owner' => $user],
            ['lastUpdateTime' => 'DESC']
        );
    }
}
