<?php

namespace App\Repository;

use App\Entity\RadioTable;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
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

    static public function getPublicCriteria(): Criteria
    {
        return new Criteria(
            Criteria::expr()->eq('status', RadioTable::STATUS_PUBLIC),
        );
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
            ->addCriteria(self::getPublicCriteria())
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

        return $query->getQuery()->getResult();
    }

    public function findPublicBySearchTerm(string $searchTerm): array
    {
        // Search term equal to "*" causes MySQL error.
        if ('*' === $searchTerm) {
            return [];
        }

        return $this->createQueryBuilder('radioTable')
            ->addCriteria(self::getPublicCriteria())
            ->andWhere('MATCH(radioTable.name, radioTable.description) AGAINST(:searchTerm BOOLEAN) > 0.0')
            ->setParameter('searchTerm', $searchTerm)
            ->innerJoin('radioTable.owner', 'user')->addSelect('user')
            ->getQuery()
            ->getResult()
        ;
    }
}
