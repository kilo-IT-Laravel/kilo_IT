<?php

namespace App\Repositories\Roles;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\Role\RoleCreated;
use App\Events\Role\RoleDeleted;
use App\Events\Role\RoleForceDeleted;
use App\Events\Role\RoleRestored;
use App\Events\Role\RoleUpdated;
use App\Models\Role;
use App\Services\AuditLogService;
use Exception;

class RoleController implements RoleInterface
{
    protected $logService;

    public function __construct(AuditLogService $logService)
    {
        $this->logService = $logService;
    }

    public function getRoles(string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        try {
            return Role::query()
                ->when(
                    $search ?? null,
                    fn($query, $search) =>
                    $query->where(
                        fn($q) =>
                        $q->where('name', 'LIKE', "%{$search}%")
                    )
                )
                ->latest()->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Error retrieving roles: ' . $e->getMessage());
            throw new Exception('Error retrieving roles');
        }
    }

    public function getRoleById(int $id): ?Role
    {
        try {
            return Role::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new Exception('Role not found');
        } catch (Exception $e) {
            Log::error('Error retrieving role: ' . $e->getMessage());
            throw new Exception('Error retrieving role');
        }
    }

    public function createRole(array $data): Role
    {
        try {
            $role = Role::create($data);
            $this->logService->log(Auth::id(), 'created_role', Role::class, $role->id, json_encode($data));
            return $role;
        } catch (Exception $e) {
            Log::error('Error creating role: ' . $e->getMessage());
            throw new Exception('Error creating role');
        }
    }


    public function updateRole(int $id, array $newsDetails): bool
    {
        try {
            $role = Role::findOrFail($id);
            $updated = $role->update($newsDetails);
            if($updated) {
                $this->logService->log(Auth::id(), 'updated_role', Role::class, $role->id, json_encode($newsDetails));
               
            }
            return $updated;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Role not found');
        } catch (Exception $e) {
            Log::error('Error updating role: ' . $e->getMessage());
            throw new Exception('Error updating role');
        }
    }

    public function deleteRole(int $id): bool
    {
        try {
            $role = Role::findOrFail($id);
            $deleted = $role->delete();
            if ($deleted) {
                $this->logService->log(Auth::id(), 'deleted_role', Role::class, $id, null);
               
            }
            return $deleted;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Role not found');
        } catch (Exception $e) {
            Log::error('Error deleting role: ' . $e->getMessage());
            throw new Exception('Error deleting role');
        }
    }

    public function restoreRole(int $id): bool
    {
        try {
            $role = Role::withTrashed()->findOrFail($id);
            $restored = $role->restore();
            if($restored){
                $this->logService->log(Auth::id(), 'restored_role', Role::class, $id, null);
                
            }
            return $restored;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Role not found');
        } catch (Exception $e) {
            Log::error('Error restoring role: ' . $e->getMessage());
            throw new Exception('Error restoring role');
        }
    }

    public function forceDeleteRole(int $id): bool
    {
        try {
            $role = Role::withTrashed()->findOrFail($id);

            $dataToDelete = [
                'id' => $role->id,
                'name' => $role->name,
            ];
    
            $forceDeleted = $role->forceDelete();
            
            if ($forceDeleted) {
                $this->logService->log(Auth::id(), 'force_deleted_role', Role::class, $id, json_encode([
                    'model' => get_class($role),
                    'data' => $dataToDelete
                ]));
               
            }
    
            return $forceDeleted;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Role not found');
        } catch (Exception $e) {
            Log::error('Error force deleting role: ' . $e->getMessage());
            throw new Exception('Error force deleting role');
        }
    }

    public function getTrashedRoles(string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        try {
            return Role::onlyTrashed()
                ->when(
                    $search ?? null,
                    fn($query, $search) =>
                    $query->where(
                        fn($q) =>
                        $q->where('name', 'LIKE', "%{$search}%")
                    )
                )
                ->latest()
                ->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Error retrieving trashed roles: ' . $e->getMessage());
            throw new Exception('Error retrieving trashed roles');
        }
    }
}