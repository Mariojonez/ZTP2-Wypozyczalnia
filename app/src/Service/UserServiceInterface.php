<?php

/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;


    /**
     * Delete entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void;


    /**
     * Get all users.
     *
     * @return array<int, User>
     */
    public function getUsers(): array;


    /**
     * Change user password.
     *
     * @param User   $user           User entity
     * @param string $hashedPassword Hashed password
     */
    public function changePassword(User $user, string $hashedPassword): void;
}
