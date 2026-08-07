<?php

/**
 * Reservation service interface.
 */

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface ReservationServiceInterface.
 */
interface ReservationServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int       $page    Page number
     * @param User|null $user    User entity
     * @param bool      $isAdmin Admin flag
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page, ?User $user, bool $isAdmin): PaginationInterface;
}
