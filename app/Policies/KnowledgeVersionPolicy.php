<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KnowledgeVersion;
use Illuminate\Auth\Access\HandlesAuthorization;

class KnowledgeVersionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any knowledge versions.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole(['Admin', 'User SKPD']);
    }

    /**
     * Determine whether the user can view the knowledge version.
     */
    public function view(User $user, KnowledgeVersion $knowledgeVersion)
    {
        // Admin can view all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // SKPD user can only view their own SKPD's versions
        if ($user->hasRole('User SKPD')) {
            return $user->skpd_id === $knowledgeVersion->skpd_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create knowledge versions.
     */
    public function create(User $user)
    {
        return $user->hasRole(['Admin', 'User SKPD']);
    }

    /**
     * Determine whether the user can update the knowledge version.
     */
    public function update(User $user, KnowledgeVersion $knowledgeVersion)
    {
        // Admin can update all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // SKPD user can only update their own SKPD's versions
        if ($user->hasRole('User SKPD')) {
            return $user->skpd_id === $knowledgeVersion->skpd_id && 
                   $knowledgeVersion->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the knowledge version.
     */
    public function delete(User $user, KnowledgeVersion $knowledgeVersion)
    {
        // Admin can delete all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // SKPD user can only delete their own versions if not published
        if ($user->hasRole('User SKPD')) {
            return $user->skpd_id === $knowledgeVersion->skpd_id && 
                   $knowledgeVersion->created_by === $user->id &&
                   $knowledgeVersion->status !== 'published';
        }

        return false;
    }

    /**
     * Determine whether the user can publish the knowledge version.
     */
    public function publish(User $user, KnowledgeVersion $knowledgeVersion)
    {
        // Only Admin can publish
        if ($user->hasRole('Admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can archive the knowledge version.
     */
    public function archive(User $user, KnowledgeVersion $knowledgeVersion)
    {
        // Only Admin can archive
        if ($user->hasRole('Admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can verify the knowledge version.
     */
    public function verify(User $user, KnowledgeVersion $knowledgeVersion)
    {
        // Only Admin can verify
        if ($user->hasRole('Admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can reject the knowledge version.
     */
    public function reject(User $user, KnowledgeVersion $knowledgeVersion)
    {
        // Only Admin can reject
        if ($user->hasRole('Admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can download attachments.
     */
    public function downloadAttachment(User $user, KnowledgeVersion $knowledgeVersion)
    {
        // Admin can download all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // SKPD user can download their own SKPD's attachments
        if ($user->hasRole('User SKPD')) {
            return $user->skpd_id === $knowledgeVersion->skpd_id;
        }

        return false;
    }

    /**
     * Determine whether the user can change status.
     */
    public function updateStatus(User $user, KnowledgeVersion $knowledgeVersion)
    {
        // Admin can change any status
        if ($user->hasRole('Admin')) {
            return true;
        }

        // SKPD user can only change status of their own versions (limited statuses)
        if ($user->hasRole('User SKPD')) {
            return $user->skpd_id === $knowledgeVersion->skpd_id && 
                   $knowledgeVersion->created_by === $user->id;
        }

        return false;
    }
}