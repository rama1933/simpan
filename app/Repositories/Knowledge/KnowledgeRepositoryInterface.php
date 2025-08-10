<?php

namespace App\Repositories\Knowledge;

use App\Repositories\BaseRepositoryInterface;

interface KnowledgeRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get knowledge by status
     */
    public function getByStatus(string $status);

    /**
     * Get knowledge by category
     */
    public function getByCategory(string $category);

    /**
     * Search knowledge by title or content
     */
    public function search(string $query);

    /**
     * Get knowledge by author
     */
    public function getByAuthor(int $authorId);

    /**
     * Get knowledge with tags
     */
    public function getWithTags(array $tags);

    /**
     * Get published knowledge for public view
     */
    public function getPublishedKnowledge();

    /**
     * Get knowledge statistics
     */
    public function getStatistics();
}

