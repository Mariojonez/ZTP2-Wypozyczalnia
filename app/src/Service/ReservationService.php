<?php

/**
 * Reservation service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\ReservationRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ReservationService.
 */
class ReservationService implements ReservationServiceInterface
{
    /**
     * Items per page.
     *
     * @var int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ReservationRepository $reservationRepository Reservation repository
     * @param PaginatorInterface    $paginator             Paginator
     */
    public function __construct(private readonly ReservationRepository $reservationRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int       $page    Page number
     * @param User|null $user    User entity
     * @param bool      $isAdmin Admin flag
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page, ?User $user, bool $isAdmin): PaginationInterface
    {
        $query = $isAdmin
            ? $this->reservationRepository->queryAll()
            : $this->reservationRepository->queryByUser($user);

        return $this->paginator->paginate(
            $query,
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }
}
