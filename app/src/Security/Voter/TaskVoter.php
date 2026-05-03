<?php
/**
 * Task voter.
 */

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\Task;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TaskVoter.
 */
class TaskVoter extends Voter
{
    /**
     * Edit permission.
     *
     * @const string
     */
    private const EDIT = 'EDIT';

    /**
     * View permission.
     *
     * @const string
     */
    private const VIEW = 'VIEW';

    /**
     * Delete permission.
     *
     * @const string
     */
    private const DELETE = 'DELETE';

    /**
     * Create permission.
     *
     * @const string
     */
    private const CREATE = 'CREATE';

    /**
     * Edit category permission.
     *
     * @const string
     */
    private const EDIT_CATEGORY = 'EDIT_CATEGORY';

    /**
     * Delete category permission.
     *
     * @const string
     */
    private const DELETE_CATEGORY = 'DELETE_CATEGORY';

    /**
     * View category permission.
     *
     * @const string
     */
    private const VIEW_CATEGORY = 'VIEW_CATEGORY';

    /**
     * Create category permission.
     *
     * @const string
     */
    private const CREATE_CATEGORY = 'CREATE_CATEGORY';

    /**
     * Change status permission.
     *
     * @const string
     */
    private const CHANGE_STATUS = 'CHANGE_STATUS';

    /**
     * View list permission.
     *
     * @const string
     */
    private const LIST = 'LIST';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::CREATE, self::EDIT_CATEGORY, self::DELETE_CATEGORY, self::VIEW_CATEGORY, self::CREATE_CATEGORY, self::CHANGE_STATUS, self::LIST])
            && ($subject instanceof Task || $subject instanceof Category || $subject instanceof Reservation || $subject instanceof User);
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false; // If the user is not logged in, deny other actions
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            self::CREATE => $this->canCreate($user),
            self::VIEW => $this->canView($subject, $user),
            self::EDIT_CATEGORY => $this->canEditCategory($subject, $user),
            self::DELETE_CATEGORY => $this->canDeleteCategory($subject, $user),
            self::VIEW_CATEGORY => $this->canViewCategory($subject, $user),
            self::CREATE_CATEGORY => $this->canCreateCategory($user),
            self::CHANGE_STATUS => $this->canChangeStatus($subject, $user),
            self::LIST => $this->canList($user),
            default => false,
        };
    }

    /**
     * Checks if user can edit task.
     *
     * @param Task          $task Task entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canEdit(Task $task, UserInterface $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Checks if user can view task.
     *
     * @param Task          $task Task entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canView(Task $task, UserInterface $user): bool
    {
        return true;
    }

    /**
     * Checks if user can delete task.
     *
     * @param Task          $task Task entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canDelete(Task $task, UserInterface $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Checks if user can create task.
     *
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canCreate(UserInterface $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Checks if user can change the status of a reservation.
     *
     * @param Reservation   $reservation Reservation entity
     * @param UserInterface $user        User
     *
     * @return bool Result
     */
    private function canChangeStatus(Reservation $reservation, UserInterface $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Checks if user has admin role.
     *
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function isAdmin(UserInterface $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }

    /**
     * Checks if user can delete category.
     *
     * @param Category      $category Category entity
     * @param UserInterface $user     User
     *
     * @return bool Result
     */
    private function canDeleteCategory(Category $category, UserInterface $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Checks if user can edit category.
     *
     * @param Category      $category Category entity
     * @param UserInterface $user     User
     *
     * @return bool Result
     */
    private function canEditCategory(Category $category, UserInterface $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Checks if user can view category.
     *
     * @param Category      $category Category entity
     * @param UserInterface $user     User
     *
     * @return bool Result
     */
    private function canViewCategory(Category $category, UserInterface $user): bool
    {
        return true;
    }

    /**
     * Checks if user can create category.
     *
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canCreateCategory(UserInterface $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Checks if user can view whole list.
     *
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canList(UserInterface $user): bool
    {
        return $this->isAdmin($user);
    }
}
