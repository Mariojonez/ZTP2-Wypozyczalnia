<?php

/**
 * UserRole enum test.
 */

namespace App\Tests\Entity\Enum;

use App\Entity\Enum\UserRole;
use PHPUnit\Framework\TestCase;

/**
 * Class UserRoleTest.
 */
class UserRoleTest extends TestCase
{
    /**
     * Test ROLE_USER value.
     */
    public function testRoleUserValue(): void
    {
        // then
        self::assertSame(
            'ROLE_USER',
            UserRole::ROLE_USER->value
        );
    }

    /**
     * Test ROLE_ADMIN value.
     */
    public function testRoleAdminValue(): void
    {
        // then
        self::assertSame(
            'ROLE_ADMIN',
            UserRole::ROLE_ADMIN->value
        );
    }

    /**
     * Test ROLE_USER label.
     */
    public function testRoleUserLabel(): void
    {
        // then
        self::assertSame(
            'label.role_user',
            UserRole::ROLE_USER->label()
        );
    }

    /**
     * Test ROLE_ADMIN label.
     */
    public function testRoleAdminLabel(): void
    {
        // then
        self::assertSame(
            'label.role_admin',
            UserRole::ROLE_ADMIN->label()
        );
    }
}
