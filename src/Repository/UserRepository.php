<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllWithPublicProfile(): array
    {
        return $this->findBy(['publicProfile' => true]);
    }

    /**
     * @see PasswordUpgraderInterface
     */
    public function upgradePassword($user, string $encodedPassword): void
    {
        if ($user instanceof User) {
            $user->setPasswordHash($encodedPassword);

            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        }
    }
}
