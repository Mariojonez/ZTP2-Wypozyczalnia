<?php
/**
 * Tag entity test.
 */

namespace App\Tests\Entity;

use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

/**
 * Class TagTest.
 */
class TagTest extends TestCase
{
    /**
     * Test setCreatedAt().
     */
    public function testSetCreatedAt(): void
    {
        // given
        $tag = new Tag();
        $date = new \DateTimeImmutable();

        // when
        $tag->setCreatedAt($date);

        // then
        self::assertSame($date, $tag->getCreatedAt());
    }

    /**
     * Test setUpdatedAt().
     */
    public function testSetUpdatedAt(): void
    {
        // given
        $tag = new Tag();
        $date = new \DateTimeImmutable();

        // when
        $tag->setUpdatedAt($date);

        // then
        self::assertSame($date, $tag->getUpdatedAt());
    }

    /**
     * Test setTitle().
     */
    public function testSetTitle(): void
    {
        // given
        $tag = new Tag();
        $title = 'Symfony';

        // when
        $tag->setTitle($title);

        // then
        self::assertSame($title, $tag->getTitle());
    }

    /**
     * Test setSlug().
     */
    public function testSetSlug(): void
    {
        // given
        $tag = new Tag();
        $slug = 'symfony';

        // when
        $result = $tag->setSlug($slug);

        // then
        self::assertSame($slug, $tag->getSlug());
        self::assertSame($tag, $result);
    }
}
