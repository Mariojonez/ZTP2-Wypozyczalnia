<?php
/**
 * Task voter test.
 */

namespace App\Tests\Security\Voter;

use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Entity\Reservation;
use App\Entity\Task;
use App\Entity\User;
use App\Security\Voter\TaskVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class TaskVoterTest.
 */
class TaskVoterTest extends TestCase
{
    /**
     * Voter under test.
     */
    private TaskVoter $voter;

    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->voter = new TaskVoter();
    }

    /**
     * Anonymous user is denied EDIT on a Task.
     */
    public function testVoteOnAttributeDeniesAnonymousUser(): void
    {
        $token = $this->createAnonymousToken();
        $task  = new Task();

        $result = $this->invokeVote('EDIT', $task, $token);

        self::assertFalse($result);
    }

    /**
     * Admin can edit a task.
     */
    public function testCanEditAllowsAdmin(): void
    {
        $admin = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value]);
        $token = $this->createToken($admin);
        $task  = new Task();

        $result = $this->invokeVote('EDIT', $task, $token);

        self::assertTrue($result);
    }

    /**
     * Regular user cannot edit a task.
     */
    public function testCanEditDeniesRegularUser(): void
    {
        $user  = $this->createUser([UserRole::ROLE_USER->value]);
        $token = $this->createToken($user);
        $task  = new Task();

        $result = $this->invokeVote('EDIT', $task, $token);

        self::assertFalse($result);
    }


    /**
     * Any logged-in user can view a task.
     */
    public function testCanViewAllowsRegularUser(): void
    {
        $user  = $this->createUser([UserRole::ROLE_USER->value]);
        $token = $this->createToken($user);
        $task  = new Task();

        $result = $this->invokeVote('VIEW', $task, $token);

        self::assertTrue($result);
    }

    /**
     * Admin can also view a task.
     */
    public function testCanViewAllowsAdmin(): void
    {
        $admin = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value]);
        $token = $this->createToken($admin);
        $task  = new Task();

        $result = $this->invokeVote('VIEW', $task, $token);

        self::assertTrue($result);
    }


    /**
     * Admin can delete a task.
     */
    public function testCanDeleteAllowsAdmin(): void
    {
        $admin = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value]);
        $token = $this->createToken($admin);
        $task  = new Task();

        $result = $this->invokeVote('DELETE', $task, $token);

        self::assertTrue($result);
    }

    /**
     * Regular user cannot delete a task.
     */
    public function testCanDeleteDeniesRegularUser(): void
    {
        $user  = $this->createUser([UserRole::ROLE_USER->value]);
        $token = $this->createToken($user);
        $task  = new Task();

        $result = $this->invokeVote('DELETE', $task, $token);

        self::assertFalse($result);
    }

    /**
     * Admin can create a task.
     */
    public function testCanCreateAllowsAdmin(): void
    {
        $admin = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value]);
        $token = $this->createToken($admin);
        $task  = new Task();

        $result = $this->invokeVote('CREATE', $task, $token);

        self::assertTrue($result);
    }

    /**
     * Regular user cannot create a task.
     */
    public function testCanCreateDeniesRegularUser(): void
    {
        $user  = $this->createUser([UserRole::ROLE_USER->value]);
        $token = $this->createToken($user);
        $task  = new Task();

        $result = $this->invokeVote('CREATE', $task, $token);

        self::assertFalse($result);
    }

    /**
     * Admin can change the status of a reservation.
     */
    public function testCanChangeStatusAllowsAdmin(): void
    {
        $admin       = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value]);
        $token       = $this->createToken($admin);
        $reservation = new Reservation();

        $result = $this->invokeVote('CHANGE_STATUS', $reservation, $token);

        self::assertTrue($result);
    }

    /**
     * Regular user cannot change the status of a reservation.
     */
    public function testCanChangeStatusDeniesRegularUser(): void
    {
        $user        = $this->createUser([UserRole::ROLE_USER->value]);
        $token       = $this->createToken($user);
        $reservation = new Reservation();

        $result = $this->invokeVote('CHANGE_STATUS', $reservation, $token);

        self::assertFalse($result);
    }

    /**
     * Admin can create a category.
     */
    public function testCanCreateCategoryAllowsAdmin(): void
    {
        $admin    = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value]);
        $token    = $this->createToken($admin);
        $category = new Category();

        $result = $this->invokeVote('CREATE_CATEGORY', $category, $token);

        self::assertTrue($result);
    }

    /**
     * Regular user cannot create a category.
     */
    public function testCanCreateCategoryDeniesRegularUser(): void
    {
        $user     = $this->createUser([UserRole::ROLE_USER->value]);
        $token    = $this->createToken($user);
        $category = new Category();

        $result = $this->invokeVote('CREATE_CATEGORY', $category, $token);

        self::assertFalse($result);
    }

    /**
     * Admin can view the full list.
     */
    public function testCanListAllowsAdmin(): void
    {
        $admin = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value]);
        $token = $this->createToken($admin);
        $task  = new Task();

        $result = $this->invokeVote('LIST', $task, $token);

        self::assertTrue($result);
    }

    /**
     * Regular user cannot view the full list.
     */
    public function testCanListDeniesRegularUser(): void
    {
        $user  = $this->createUser([UserRole::ROLE_USER->value]);
        $token = $this->createToken($user);
        $task  = new Task();

        $result = $this->invokeVote('LIST', $task, $token);

        self::assertFalse($result);
    }


    /**
     * Calls the voter and returns the boolean result.
     *
     * @param string         $attribute Permission string
     * @param mixed          $subject   Subject entity
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    private function invokeVote(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $result = $this->voter->vote($token, $subject, [$attribute]);

        return $result === 1;
    }

    /**
     * Creates a User entity with the given roles.
     *
     * @param array $roles List of role strings
     *
     * @return User
     */
    private function createUser(array $roles): User
    {
        $user = new User();
        $user->setEmail(uniqid('', true) . '@test.com');
        $user->setRoles($roles);
        $user->setPassword('hashed-password');

        return $user;
    }

    /**
     * Creates an authenticated token for the given user.
     *
     * @param User $user
     *
     * @return TokenInterface
     */
    private function createToken(User $user): TokenInterface
    {
        return new UsernamePasswordToken($user, 'main', $user->getRoles());
    }

    /**
     * Creates an anonymous (unauthenticated) token.
     *
     * @return TokenInterface
     */
    private function createAnonymousToken(): TokenInterface
    {
        $stub = $this->createStub(TokenInterface::class);
        $stub->method('getUser')->willReturn(null);

        return $stub;
    }

}
