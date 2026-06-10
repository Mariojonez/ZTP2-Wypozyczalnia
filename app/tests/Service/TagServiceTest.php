<?php

namespace App\Tests\Service;

use App\Entity\Tag;
use App\Service\TagService;
use App\Service\TagServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TagServiceTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;

    private ?TagServiceInterface $tagService = null;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->tagService = $container->get(TagService::class);
    }

    public function testSave(): void
    {
        $tag = new Tag();
        $tag->setTitle('Test Tag');

        $this->tagService->save($tag);

        $this->assertNotNull(
            $this->tagService->findOneByTitle('Test Tag')
        );
    }

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
