<?php

namespace App\Repositories\Permissions;

use App\Events\Permission\PermissionCreated;
use App\Events\Permission\PermissionDeleted;
use App\Events\Permission\PermissionForceDeleted;
use App\Events\Permission\PermissionRestored;
use App\Events\Permission\PermissionUpdated;
use App\Models\Permission;
use App\Services\AuditLogService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PermissionController implements PermissionInterface
{

    protected $logService;

    public function __construct(AuditLogService $logService) 
    {
        $this->logService = $logService;
    }

    public function getPermissions(string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        try {
            return Permission::query()
                ->when(
                    $search ?? null,
                    fn($query, $search) =>
                    $query->where(
                        fn($q) =>
                        $q->where('name', 'LIKE', "%{$search}%")
                    )
                )->latest()->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Error retrieving permissions: ' . $e->getMessage());
            throw new Exception('Error retrieving permissions');
        }
    }

    public function getPermissionsId(int $id): ?Permission
    {
        try {
            return Permission::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new Exception('Permission not found');
        } catch (Exception $e) {
            Log::error('Error retrieving permission: ' . $e->getMessage());
            throw new Exception('Error retrieving permission');
        }
    }

    public function createPermission(array $data): Permission
    {
        try {
            $permission = Permission::create($data);
            $this->logService->log(Auth::id(), 'created_permission', Permission::class, $permission->id,json_encode($data));
            return $permission;
        } catch (Exception $e) {
            Log::error('Error creating permission: ' . $e->getMessage());
            throw new Exception('Error creating permission');
        }
    }

    public function updatePermission(int $id, array $newDetails): bool
    {
        try {
            $permission = Permission::findOrFail($id);
            $updated = $permission->update($newDetails);
            if($updated){
                $this->logService->log(Auth::id(), 'updated_permission', Permission::class, $permission->id,json_encode($newDetails));
                
            }
            return $updated;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Permission not found');
        } catch (Exception $e) {
            Log::error('Error updating permission: ' . $e->getMessage());
            throw new Exception('Error updating permission');
        }
    }

    public function getTrashedPermissions(string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        try {
            return Permission::onlyTrashed()->when(
                $search ?? null,
                fn($query, $search) =>
                $query->where(
                    fn($q) =>
                    $q->where('name', 'LIKE', "%{$search}%")
                )
            )->latest()->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Error retrieving trashed permissions: ' . $e->getMessage());
            throw new Exception('Error retrieving trashed permissions');
        }
    }

    public function deletePermission(int $id): bool
    {
        try {
            $permission = Permission::findOrFail($id);
            $deleted = $permission->delete();
            if($deleted){
                $this->logService->log(Auth::id(), 'deleted_permission', Permission::class, $id, null);
               
            }
            return $deleted;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Permission not found');
        } catch (Exception $e) {
            Log::error('Error deleting permission: ' . $e->getMessage());
            throw new Exception('Error deleting permission');
        }
    }

    public function restorePermission(int $id): bool
    {
        try {
            $permission = Permission::withTrashed()->findOrFail($id);
            $restored = $permission->restore();
            if($restored){
                $this->logService->log(Auth::id(), 'restored_permission', Permission::class, $id, null);
                
            }
            return $restored;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Permission not found');
        } catch (Exception $e) {
            Log::error('Error restoring permission: ' . $e->getMessage());
            throw new Exception('Error restoring permission');
        }
    }

    public function forceDeletePermission(int $id): bool
    {
        try {
            $permission = Permission::withTrashed()->findOrFail($id);
            $dataToDelete = [
                'id' => $permission->id,
                'name' => $permission->name
            ];

            $forceDeleted = $permission->forceDelete();

            if ($forceDeleted) {
                $this->logService->log(Auth::id(), 'force_deleted_permission', Permission::class, $id, json_encode([
                    'model' => get_class($permission),
                    'data' => $dataToDelete
                ]));
                
            }

            return $forceDeleted;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Permission not found');
        } catch (Exception $e) {
            Log::error('Error force deleting permission: ' . $e->getMessage());
            throw new Exception('Error force deleting permission');
        }
    }
}