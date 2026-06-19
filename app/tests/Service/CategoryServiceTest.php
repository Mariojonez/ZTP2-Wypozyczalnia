<?php

/**
 * Category service test.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Task;
use App\Entity\User;
use App\Service\CategoryService;
use App\Service\CategoryServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CategoryServiceTest.
 */
class CategoryServiceTest extends KernelTestCase
{
    /**
     * Entity manager.
     */
    private ?EntityManagerInterface $entityManager = null;

    /**
     * Category service.
     */
    private ?CategoryServiceInterface $categoryService = null;

    /**
     * Setup test environment.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->categoryService = $container->get(CategoryService::class);
    }

    /**
     * Test saving category.
     */
    public function testSave(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Test Category');

        // when
        $this->categoryService->save($category);

        // then
        $this->assertNotNull(
            $this->categoryService->getPaginatedList(1)
        );
    }

    /**
     * Test deleting category.
     */
    public function testDelete(): void
    {
        // given
        $category = new Category();
        $category->setTitle('To Delete');

        $this->categoryService->save($category);
        $id = $category->getId();

        // when
        $this->categoryService->delete($category);

        // then
        $result = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Category::class, 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($result);
    }

    /**
     * Test paginated list retrieval.
     */
    public function testGetPaginatedList(): void
    {
        // given
        for ($i = 0; $i < 3; ++$i) {
            $category = new Category();
            $category->setTitle('Cat '.$i);
            $this->categoryService->save($category);
        }

        // when
        $result = $this->categoryService->getPaginatedList(1);

        // then
        $this->assertEquals(3, $result->count());
    }

    /**
     * Test canBeDeleted returns true for empty category.
     */
    public function testCanBeDeletedTrue(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Empty Category');
        $this->categoryService->save($category);

        // when
        $result = $this->categoryService->canBeDeleted($category);

        // then
        $this->assertTrue($result);
    }

    /**
     * Test canBeDeleted returns false when category has tasks.
     */
    public function testCanBeDeletedFalse(): void
    {
        // given
        $category = new Category();
        $category->setTitle('With Task');
        $this->categoryService->save($category);

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password');

        $this->entityManager->persist($user);

        $task = new Task();
        $task->setTitle('Task');
        $task->setCategory($category);
        $task->setAuthor($user);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        // when
        $result = $this->categoryService->canBeDeleted($category);

        // then
        $this->assertFalse($result);
    }

    /**
     * Test canBeDeleted handles repository exception.
     */
    public function testCanBeDeletedException(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Category');

        $taskRepository = $this->createStub(
            \App\Repository\TaskRepository::class
        );

        $taskRepository
            ->method('countByCategory')
            ->willThrowException(
                new \Doctrine\ORM\NoResultException()
            );

        $categoryRepository = $this->createStub(
            \App\Repository\CategoryRepository::class
        );

        $paginator = $this->createStub(
            \Knp\Component\Pager\PaginatorInterface::class
        );

        $service = new CategoryService(
            $categoryRepository,
            $taskRepository,
            $paginator
        );

        // when
        $result = $service->canBeDeleted($category);

        // then
        $this->assertFalse($result);
    }
}
