<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\ReservationRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ReservationService implements ReservationServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    public function __construct(
        private readonly ReservationRepository $reservationRepository,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    public function getPaginatedList(
        int $page,
        ?User $user,
        bool $isAdmin
    ): PaginationInterface {
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
