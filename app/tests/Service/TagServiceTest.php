<?php

/**
 * Tag service test.
 */

namespace App\Tests\Service;

use App\Entity\Tag;
use App\Service\TagService;
use App\Service\TagServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TagServiceTest.
 */
class TagServiceTest extends KernelTestCase
{
    /**
     * Entity manager.
     */
    private ?EntityManagerInterface $entityManager = null;

    /**
     * Tag service.
     */
    private ?TagServiceInterface $tagService = null;

    /**
     * Setup test environment.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->tagService = $container->get(TagService::class);
    }

    /**
     * Test saving tag.
     */
    public function testSave(): void
    {
        $tag = new Tag();
        $tag->setTitle('Test Tag');

        $this->tagService->save($tag);

        $this->assertNotNull(
            $this->tagService->findOneByTitle('Test Tag')
        );
    }

    /**
     * Test finding tag by title.
     */
    public function testFindOneByTitle(): void
    {
        $tag = new Tag();
        $tag->setTitle('Symfony');

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $result = $this->tagService->findOneByTitle('Symfony');

        $this->assertNotNull($result);
        $this->assertEquals('Symfony', $result->getTitle());
    }
}
