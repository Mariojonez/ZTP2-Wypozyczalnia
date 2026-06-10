<?php
/**
 * Reservation entity test.
 */

namespace App\Tests\Entity;

use App\Entity\Reservation;
use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class ReservationTest.
 */
class ReservationTest extends TestCase
{
    /**
     * Test getId().
     */
    public function testGetId(): void
    {
        // given
        $reservation = new Reservation();

        // then
        self::assertNull($reservation->getId());
    }

    /**
     * Test setId().
     */
    public function testSetId(): void
    {
        // given
        $reservation = new Reservation();

        // when
        $reservation->setId(1);

        // then
        self::assertSame(1, $reservation->getId());
    }

    /**
     * Test setUser().
     */
    public function testSetUser(): void
    {
        // given
        $reservation = new Reservation();
        $user = new User();

        // when
        $reservation->setUser($user);

        // then
        self::assertSame($user, $reservation->getUser());
    }

    /**
     * Test setUser() with null.
     */
    public function testSetUserNull(): void
    {
        // given
        $reservation = new Reservation();

        // when
        $reservation->setUser(null);

        // then
        self::assertNull($reservation->getUser());
    }

    /**
     * Test setTask().
     */
    public function testSetTask(): void
    {
        // given
        $reservation = new Reservation();
        $task = new Task();

        // when
        $reservation->setTask($task);

        // then
        self::assertSame($task, $reservation->getTask());
    }

    /**
     * Test setTask() with null.
     */
    public function testSetTaskNull(): void
    {
        // given
        $reservation = new Reservation();

        // when
        $reservation->setTask(null);

        // then
        self::assertNull($reservation->getTask());
    }

    /**
     * Test setComment().
     */
    public function testSetComment(): void
    {
        // given
        $reservation = new Reservation();
        $comment = 'Test comment';

        // when
        $result = $reservation->setComment($comment);

        // then
        self::assertSame($comment, $reservation->getComment());
        self::assertSame($reservation, $result);
    }

    /**
     * Test setComment() with null.
     */
    public function testSetCommentNull(): void
    {
        // given
        $reservation = new Reservation();

        // when
        $result = $reservation->setComment(null);

        // then
        self::assertNull($reservation->getComment());
        self::assertSame($reservation, $result);
    }

    /**
     * Test setStatus().
     */
    public function testSetStatus(): void
    {
        // given
        $reservation = new Reservation();
        $status = 'approved';

        // when
        $result = $reservation->setStatus($status);

        // then
        self::assertSame($status, $reservation->getStatus());
        self::assertSame($reservation, $result);
    }
}
