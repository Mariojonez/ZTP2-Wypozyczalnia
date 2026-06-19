<?php

/**
 * Task entity test.
 */

namespace App\Tests\Entity;

use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\Category;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class TaskTest.
 */
class TaskTest extends TestCase
{
    /**
     * Test setCreatedAt().
     */
    public function testSetCreatedAt(): void
    {
        // given
        $task = new Task();
        $date = new \DateTimeImmutable();

        // when
        $task->setCreatedAt($date);

        // then
        self::assertSame($date, $task->getCreatedAt());
    }

    /**
     * Test setUpdatedAt().
     */
    public function testSetUpdatedAt(): void
    {
        // given
        $task = new Task();
        $date = new \DateTimeImmutable();

        // when
        $task->setUpdatedAt($date);

        // then
        self::assertSame($date, $task->getUpdatedAt());
    }

    /**
     * Test addTag().
     */
    public function testAddTag(): void
    {
        // given
        $task = new Task();
        $tag = new Tag();

        // when
        $task->addTag($tag);

        // then
        self::assertCount(1, $task->getTags());
        self::assertTrue($task->getTags()->contains($tag));
    }

    /**
     * Test addTag() does not add duplicates.
     */
    public function testAddTagDoesNotDuplicate(): void
    {
        // given
        $task = new Task();
        $tag = new Tag();

        // when
        $task->addTag($tag);
        $task->addTag($tag);

        // then
        self::assertCount(1, $task->getTags());
    }

    /**
     * Test removeTag().
     */
    public function testRemoveTag(): void
    {
        // given
        $task = new Task();
        $tag = new Tag();

        $task->addTag($tag);

        // precondition
        self::assertTrue($task->getTags()->contains($tag));

        // when
        $task->removeTag($tag);

        // then
        self::assertFalse($task->getTags()->contains($tag));
        self::assertCount(0, $task->getTags());
    }

    /**
     * Test setCategory() and getCategory().
     */
    public function testCategory(): void
    {
        // given
        $task = new Task();
        $category = new Category();

        // when
        $task->setCategory($category);

        // then
        self::assertSame($category, $task->getCategory());
    }

    /**
     * Test setAuthor() and getAuthor().
     */
    public function testAuthor(): void
    {
        // given
        $task = new Task();
        $author = new User();

        // when
        $task->setAuthor($author);

        // then
        self::assertSame($author, $task->getAuthor());
    }

    /**
     * Test setting category to null.
     */
    public function testCategoryCanBeNull(): void
    {
        // given
        $task = new Task();

        // when
        $task->setCategory(null);

        // then
        self::assertNull($task->getCategory());
    }

    /**
     * Test setting author to null.
     */
    public function testAuthorCanBeNull(): void
    {
        // given
        $task = new Task();

        // when
        $task->setAuthor(null);

        // then
        self::assertNull($task->getAuthor());
    }
}
