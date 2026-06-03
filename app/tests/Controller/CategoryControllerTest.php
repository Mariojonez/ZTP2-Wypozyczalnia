<?php
/**
 * Category controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CategoryControllerTest.
 */
class CategoryControllerTest extends WebTestCase
{
    /**
     * Base route.
     */
    private const ROUTE = '/category';

    /**
     * HTTP client.
     */
    private KernelBrowser $client;

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Anonymous user can access index.
     */
    public function testIndexAnonymous(): void
    {
        // when
        $this->client->request(
            'GET',
            self::ROUTE
        );

        // then
        self::assertResponseIsSuccessful();
    }

    /**
     * Logged user can access index.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testIndexLoggedUser(): void
    {
        // given
        $user = $this->createUser([
            UserRole::ROLE_USER->value,
        ]);

        $this->client->loginUser($user);

        // when
        $this->client->request(
            'GET',
            self::ROUTE
        );

        // then
        self::assertResponseIsSuccessful();
    }

    /**
     * Admin can open create page.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCreatePageAdmin(): void
    {
        // given
        $admin = $this->createUser([
            UserRole::ROLE_USER->value,
            UserRole::ROLE_ADMIN->value,
        ]);

        $this->client->loginUser($admin);

        // when
        $this->client->request(
            'GET',
            self::ROUTE.'/create'
        );

        // then
        self::assertResponseIsSuccessful();
    }

    /**
     * User cannot create category.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCreatePageRegularUserForbidden(): void
    {
        // given
        $user = $this->createUser([
            UserRole::ROLE_USER->value,
        ]);

        $this->client->loginUser($user);

        // when
        $this->client->request(
            'GET',
            self::ROUTE.'/create'
        );

        // then
        self::assertResponseStatusCodeSame(403);
    }

    /**
     * Category details page.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testShowCategory(): void
    {
        // given
        $user = $this->createUser([
            UserRole::ROLE_USER->value,
        ]);

        $category = $this->createCategory();

        $this->client->loginUser($user);

        // when
        $this->client->request(
            'GET',
            self::ROUTE.'/'.$category->getId()
        );

        // then
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * Category edit page.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testEditCategoryPage(): void
    {
        // given
        $admin = $this->createUser([
            UserRole::ROLE_ADMIN->value,
            UserRole::ROLE_USER->value,
        ]);

        $category = $this->createCategory();

        $this->client->loginUser($admin);

        // when
        $this->client->request(
            'GET',
            sprintf(
                '%s/%d/edit',
                self::ROUTE,
                $category->getId()
            )
        );

        // then
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * Category delete page.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testDeletePage(): void
    {
        // given
        $admin = $this->createUser([
            UserRole::ROLE_ADMIN->value,
            UserRole::ROLE_USER->value,
        ]);

        $category = $this->createCategory();

        $this->client->loginUser($admin);

        // when
        $this->client->request(
            'GET',
            sprintf(
                '%s/%d/delete',
                self::ROUTE,
                $category->getId()
            )
        );

        // then
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * Show tasks page.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testShowTasks(): void
    {
        // given
        $user = $this->createUser([
            UserRole::ROLE_USER->value,
        ]);

        $category = $this->createCategory();

        $this->client->loginUser($user);

        // when
        $this->client->request(
            'GET',
            sprintf(
                '%s/%d/tasks',
                self::ROUTE,
                $category->getId()
            )
        );

        // then
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * Create category helper.
     *
     * @return Category
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createCategory(): Category
    {
        $category = new Category();

        // entity uses title instead of name
        $category->setTitle('Test category');

        $repository = static::getContainer()
            ->get(CategoryRepository::class);

        $repository->save($category);

        return $category;
    }

    /**
     * Create user helper.
     *
     * @param array $roles User roles
     *
     * @return User
     *
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
