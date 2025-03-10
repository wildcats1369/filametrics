<?php

namespace wildcats1369\Filametrics\Policies;

use App\Models\User;
use wildcats1369\Filametrics\Models\FilametricsSite;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilametricsSitePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_filametrics::site');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FilametricsSite $filametrics_site): bool
    {
        return $user->can('view_filametrics::site');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_filametrics::site');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FilametricsSite $filametrics_site): bool
    {
        return $user->can('update_filametrics::site');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FilametricsSite $filametrics_site): bool
    {
        return $user->can('delete_filametrics::site');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_filametrics::site');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, FilametricsSite $filametrics_site): bool
    {
        return $user->can('force_delete_filametrics::site');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_filametrics::site');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, FilametricsSite $filametrics_site): bool
    {
        return $user->can('restore_filametrics::site');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_filametrics::site');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, FilametricsSite $filametrics_site): bool
    {
        return $user->can('replicate_filametrics::site');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_filametrics::site');
    }
}
