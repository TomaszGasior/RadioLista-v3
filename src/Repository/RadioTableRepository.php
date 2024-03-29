<?php

namespace App\Repository;

use App\Entity\Enum\RadioTable\Status;
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

    public function findPublicOrderedByFrequencyUnit(): array
    {
        return $this->findAllPublic('frequencyUnit', null);
    }

    private function findAllPublic(string $orderBy, ?int $limit): array
    {
        $query = $this->createQueryBuilder('radioTable')
            ->andWhere('radioTable.status = :status')
            ->setParameter('status', Status::PUBLIC)
            ->andWhere('radioTable.radioStationsCount > 0')
            ->orderBy('radioTable.'.$orderBy, 'DESC')
        ;

        if ($limit) {
            $query->setMaxResults($limit);
        }
        else {
            // If there is no limit, result will be probably used on the radio tables list.
            // Optimize query for fetching radio table owners.
            $query->innerJoin('radioTable.owner', 'user')->addSelect('user');
        }

        return $query->getQuery()->setCacheable(true)->getResult();
    }

    public function findPublicBySearchTerm(string $searchTerm): array
    {
        // MATCH AGAINST keyword works only with MySQL-like databases.
        if (!($this->getEntityManager()->getConnection()->getDatabasePlatform() instanceof MySqlPlatform)) {
            return [];
        }

        $resultSetMapper = $this->createResultSetMappingBuilder('radioTable');
        $tableName = $this->getClassMetadata()->getTableName();

        $sql = <<< SQL
            SELECT {$resultSetMapper->generateSelectClause()} FROM {$tableName} as radioTable
            WHERE radioTable.Status = :status
            AND MATCH (radioTable.name, radioTable.description) AGAINST (:searchTerm) > 0.0
        SQL;

        return $this->getEntityManager()
            ->createNativeQuery($sql, $resultSetMapper)
            ->setParameter('status', Status::PUBLIC)
            ->setParameter('searchTerm', $searchTerm)
            ->getResult()
        ;
    }

    public function findPublicOwnedByUser(User $user): array
    {
        return $this->findBy(
            ['owner' => $user, 'status' => Status::PUBLIC],
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
