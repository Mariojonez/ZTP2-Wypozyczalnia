<?php

/**
 * Task service test.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Task;
use App\Entity\User;
use App\Service\TaskService;
use App\Service\TaskServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TaskServiceTest.
 */
class TaskServiceTest extends KernelTestCase
{
    /**
     * Entity manager.
     */
    private ?EntityManagerInterface $entityManager = null;

    /**
     * Task service.
     */
    private ?TaskServiceInterface $taskService = null;

    /**
     * Setup test environment.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->taskService = $container->get(TaskService::class);
    }

    /**
     * Test saving task.
     */
    public function testSave(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Category');
        $this->entityManager->persist($category);

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password');
        $this->entityManager->persist($user);

        $task = new Task();
        $task->setTitle('Task');
        $task->setCategory($category);
        $task->setAuthor($user);

        // when
        $this->taskService->save($task);

        // then
        $this->assertNotNull($task->getId());
    }

    /**
     * Test deleting task.
     */
    public function testDelete(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Category');
        $this->entityManager->persist($category);

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

        $id = $task->getId();

        // when
        $this->taskService->delete($task);

        // then
        $result = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where('t.id = :id')
            ->setParameter('id', $id, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($result);
    }

    /**
     * Test getting tasks by category.
     */
    public function testGetTasksByCategory(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Category');
        $this->entityManager->persist($category);

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password');
        $this->entityManager->persist($user);

        $task = new Task();
        $task->setTitle('Task 1');
        $task->setCategory($category);
        $task->setAuthor($user);
        $this->entityManager->persist($task);

        $this->entityManager->flush();

        // when
        $result = $this->taskService->getTasksByCategory($category);

        // then
        $this->assertCount(1, $result);
        $this->assertSame('Task 1', $result[0]->getTitle());
    }

    /**
     * Test paginated task list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $category = new Category();
        $category->setTitle('Category');
        $this->entityManager->persist($category);

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password');
        $this->entityManager->persist($user);

        for ($i = 0; $i < 3; ++$i) {
            $task = new Task();
            $task->setTitle('Task '.$i);
            $task->setCategory($category);
            $task->setAuthor($user);
            $this->entityManager->persist($task);
        }

        $this->entityManager->flush();

        // when
        $result = $this->taskService->getPaginatedList(1);

        // then
        $this->assertEquals(3, $result->count());
    }
}
