<?php

/**
 * Reservation repository.
 */

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Save.
     *
     * @param Reservation $reservation reservation
     */
    public function save(Reservation $reservation): void
    {
        $this->getEntityManager()->persist($reservation);
        $this->getEntityManager()->flush();
    }

    /**
     * Query all reservations.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('reservation')
            ->join('reservation.user', 'user')
            ->join('reservation.task', 'task')
            ->addSelect('user', 'task')
            ->orderBy('reservation.id', 'DESC');
    }

    /**
     * Query reservations by user.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('reservation')
            ->join('reservation.user', 'user')
            ->join('reservation.task', 'task')
            ->addSelect('user', 'task')
            ->where('reservation.user = :user')
            ->setParameter('user', $user)
            ->orderBy('reservation.id', 'DESC');
    }
}
