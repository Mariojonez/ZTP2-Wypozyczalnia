<?php

/**
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * Constructor.
     *
     * @param UserRepository $userRepository User repository
     */
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Change user password.
     *
     * @param User   $user           User entity
     * @param string $hashedPassword Hashed password
     */
    public function changePassword(User $user, string $hashedPassword): void
    {
        $user->setPassword($hashedPassword);

        $this->userRepository->save($user);
    }

    /**
     * Delete entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void
    {
        $this->userRepository->delete($user);
    }


    /**
     * Get users list.
     *
     * @return array<int, User>
     */
    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }
}
