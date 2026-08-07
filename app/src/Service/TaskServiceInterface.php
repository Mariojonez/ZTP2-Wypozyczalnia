<?php

/**
 * Task service interface.
 */

namespace App\Service;

use App\Entity\Category;
use App\Entity\Task;
use App\Entity\Tag;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TaskServiceInterface.
 */
interface TaskServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Task $task Task entity
     */
    public function save(Task $task): void;

    /**
     * Delete entity.
     *
     * @param Task $task Task entity
     */
    public function delete(Task $task): void;

    /**
     * Get tasks by category.
     *
     * @param Category $category Category
     *
     * @return Task[]
     */
    public function getTasksByCategory(Category $category): array;

    /**
     * Get paginated list by category.
     *
     * @param int      $page     Page number
     * @param Category $category Category
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface;

    /**
     * Get paginated list by tag.
     *
     * @param int $page Page number
     * @param Tag $tag  Tag entity
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByTag(int $page, Tag $tag): PaginationInterface;
}
