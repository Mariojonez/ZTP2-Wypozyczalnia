<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private const ROUTE = '/task';

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndexAnonymous(): void
    {
        $this->client->request('GET', self::ROUTE);

        self::assertResponseIsSuccessful();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testIndexLoggedUser(): void
    {
        $user = $this->createUser([
            UserRole::ROLE_USER->value,
        ]);

        $this->client->loginUser($user);

        $this->client->request('GET', self::ROUTE);

        self::assertResponseIsSuccessful();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCreatePageAdmin(): void
    {
        $admin = $this->createUser([
            UserRole::ROLE_USER->value,
            UserRole::ROLE_ADMIN->value,
        ]);

        $this->client->loginUser($admin);

        $this->client->request('GET', self::ROUTE.'/create');

        self::assertResponseIsSuccessful();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCreatePageRegularUserForbidden(): void
    {
        $user = $this->createUser([
            UserRole::ROLE_USER->value,
        ]);

        $this->client->loginUser($user);

        $this->client->request('GET', self::ROUTE.'/create');

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testShowTask(): void
    {
        $user = $this->createUser([
            UserRole::ROLE_USER->value,
        ]);

        $task = $this->createTask($user);

        $this->client->loginUser($user);

        $this->client->request(
            'GET',
            self::ROUTE.'/'.$task->getId()
        );

        self::assertResponseIsSuccessful();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEditTaskPage(): void
    {
        $admin = $this->createUser([
            UserRole::ROLE_ADMIN->value,
            UserRole::ROLE_USER->value,
        ]);

        $task = $this->createTask($admin);

        $this->client->loginUser($admin);

        $this->client->request(
            'GET',
            sprintf('%s/%d/edit', self::ROUTE, $task->getId())
        );

        self::assertResponseIsSuccessful();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testDeletePage(): void
    {
        $admin = $this->createUser([
            UserRole::ROLE_ADMIN->value,
            UserRole::ROLE_USER->value,
        ]);

        $task = $this->createTask($admin);

        $this->client->loginUser($admin);

        $this->client->request(
            'GET',
            sprintf('%s/%d/delete', self::ROUTE, $task->getId())
        );

        self::assertResponseIsSuccessful();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCreateTask(): void
    {
        $admin = $this->createUser([
            UserRole::ROLE_USER->value,
            UserRole::ROLE_ADMIN->value,
        ]);

        $this->client->loginUser($admin);

        $category = $this->createCategory();

        $crawler = $this->client->request(
            'GET',
            self::ROUTE.'/create'
        );

        $form = $crawler->filter('form')->form([
            'task[title]' => 'Created task',
            'task[category]' => $category->getId(),
            'task[tags]' => '',
        ]);

        $this->client->submit($form);

        self::assertResponseRedirects(self::ROUTE);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createTask(User $author): Task
    {
        $task = new Task();

        $task->setTitle('Test task');
        $task->setAuthor($author);
        $task->setCategory($this->createCategory());

        $repository = static::getContainer()
            ->get(TaskRepository::class);

        $repository->save($task);

        return $task;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createCategory(): Category
    {
        $category = new Category();

        $category->setTitle(
            'Category '.uniqid()
        );

        $repository = static::getContainer()
            ->get(CategoryRepository::class);

        $repository->save($category);

        return $category;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createUser(array $roles): User
    {
        $passwordHasher = static::getContainer()
            ->get('security.password_hasher');

        $user = new User();

        $user->setEmail(
            uniqid('', true).'@test.com'
        );

        $user->setRoles($roles);

        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'password123'
            )
        );

        $repository = static::getContainer()
            ->get(UserRepository::class);

        $repository->save($user);

        return $user;
    }
}
