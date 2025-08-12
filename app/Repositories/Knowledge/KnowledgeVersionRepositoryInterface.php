<?php

namespace App\Repositories\Knowledge;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface KnowledgeVersionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get versions by knowledge ID
     */
    public function getByKnowledgeId(int $knowledgeId): Collection;

    /**
     * Get versions by status
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get versions by verification status
     */
    public function getByVerificationStatus(string $verificationStatus): Collection;

    /**
     * Get current version for knowledge
     */
    public function getCurrentVersion(int $knowledgeId);

    /**
     * Get published versions
     */
    public function getPublishedVersions(): Collection;

    /**
     * Get versions with filters
     */
    public function getWithFilters(array $filters): LengthAwarePaginator;

    /**
     * Get version statistics
     */
    public function getStatistics(): array;

    /**
     * Search versions
     */
    public function search(string $query): Collection;

    /**
     * Get versions by creator
     */
    public function getByCreator(int $creatorId): Collection;

    /**
     * Get versions by verifier
     */
    public function getByVerifier(int $verifierId): Collection;

    /**
     * Get versions by date range
     */
    public function getByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Get pending verification versions
     */
    public function getPendingVerification(): Collection;

    /**
     * Get expired versions
     */
    public function getExpiredVersions(): Collection;

    /**
     * Get versions with attachments
     */
    public function getWithAttachments(): Collection;
}