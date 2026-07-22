<?php

/**
 * User repository.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Upgrade password.
     *
     * @param PasswordAuthenticatedUserInterface $user              User
     * @param string                             $newHashedPassword New hashed password
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
    }

    /**
     * Save user entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * Delete user entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->remove($user);
        $entityManager->flush();
    }

    /**
     * Get all users query.
     *
     * This method returns a query builder instead of executing
     * the query, because the paginator needs to modify the query
     * with LIMIT/OFFSET internally.
     *
     * @return QueryBuilder Users query
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('user')
            ->orderBy('user.email', 'ASC');
    }
}
