<?php

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface ReservationServiceInterface
{
    /**
     * Get paginated reservation list.
     *
     * @param int       $page Page number
     * @param User|null $user User entity
     * @param bool      $isAdmin Is admin
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(
        int $page,
        ?User $user,
        bool $isAdmin
    ): PaginationInterface;
}
