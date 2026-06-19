<?php

/**
 * User entity test.
 */

namespace App\Tests\Entity;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    /**
     * Test getId().
     */
    public function testGetId(): void
    {
        // given
        $user = new User();

        // then
        self::assertNull($user->getId());
    }

    /**
     * Test setEmail().
     */
    public function testSetEmail(): void
    {
        // given
        $user = new User();
        $email = 'test@example.com';

        // when
        $user->setEmail($email);

        // then
        self::assertSame($email, $user->getEmail());
    }

    /**
     * Test getUserIdentifier().
     */
    public function testGetUserIdentifier(): void
    {
        // given
        $user = new User();
        $email = 'test@example.com';

        $user->setEmail($email);

        // then
        self::assertSame($email, $user->getUserIdentifier());
    }

    /**
     * Test getUsername().
     */
    public function testGetUsername(): void
    {
        // given
        $user = new User();
        $email = 'test@example.com';

        $user->setEmail($email);

        // then
        self::assertSame($email, $user->getUsername());
    }

    /**
     * Test setRoles().
     */
    public function testSetRoles(): void
    {
        // given
        $user = new User();

        // when
        $user->setRoles([
            UserRole::ROLE_ADMIN->value,
        ]);

        // then
        self::assertContains(
            UserRole::ROLE_ADMIN->value,
            $user->getRoles()
        );

        self::assertContains(
            UserRole::ROLE_USER->value,
            $user->getRoles()
        );

        self::assertCount(2, $user->getRoles());
    }

    /**
     * Test getRoles() adds ROLE_USER by default.
     */
    public function testGetRolesAddsDefaultRole(): void
    {
        // given
        $user = new User();

        // then
        self::assertSame(
            [UserRole::ROLE_USER->value],
            $user->getRoles()
        );
    }

    /**
     * Test getRoles() removes duplicates.
     */
    public function testGetRolesRemovesDuplicates(): void
    {
        // given
        $user = new User();

        // when
        $user->setRoles([
            UserRole::ROLE_USER->value,
            UserRole::ROLE_ADMIN->value,
        ]);

        // then
        self::assertSame(
            [
                UserRole::ROLE_USER->value,
                UserRole::ROLE_ADMIN->value,
            ],
            $user->getRoles()
        );
    }

    /**
     * Test setPassword().
     */
    public function testSetPassword(): void
    {
        // given
        $user = new User();
        $password = 'password123';

        // when
        $user->setPassword($password);

        // then
        self::assertSame($password, $user->getPassword());
    }

    /**
     * Test eraseCredentials().
     */
    public function testEraseCredentials(): void
    {
        // given
        $user = new User();

        // then
        self::assertNull(
            $user->eraseCredentials()
        );
    }

    /**
     * Test upgradePassword().
     */
    public function testUpgradePassword(): void
    {
        // given
        $user = new User();
        $password = 'newHashedPassword';

        // when
        $user->upgradePassword($password);

        // then
        self::assertSame(
            $password,
            $user->getPassword()
        );
    }
}
