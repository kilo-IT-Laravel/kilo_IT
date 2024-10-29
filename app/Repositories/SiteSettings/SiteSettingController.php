<?php

namespace App\Repositories\SiteSettings;

use App\Models\SiteSetting;
use App\Services\AuditLogService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SiteSettingController implements SiteSettingInterface
{
    protected $logService;

    public function __construct(AuditLogService $logService)
    {
        $this->logService = $logService;
    }

    public function getAllSettings(string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        try {
            return Cache::remember('site_settings', 3600, function () use ($search, $perPage) {
                $query = SiteSetting::query()
                    ->when($search, function ($query) use ($search) {
                        return $query->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orderBy('created_at', 'desc');

                return $query->paginate($perPage);
            });
        } catch (Exception $e) {
            Log::error('Error retrieving all settings: ' . $e->getMessage());
            throw new Exception('Error retrieving all settings');
        }
    }

    public function getSetting(string $key): array
    {
        try {
            $setting = Cache::remember("site_setting_{$key}", 3600, function () use ($key) {
                return SiteSetting::where('key', $key)->first();
            });

            if (!$setting) {
                throw new ModelNotFoundException('Setting not found');
            }

            return [
                'success' => true,
                'data' => [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'input_type' => $setting->input_type,
                ],
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => 'Setting not found',
            ];
        } catch (Exception $e) {
            Log::error('Error retrieving setting: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error retrieving setting',
            ];
        }
    }

    public function updateSetting(string $key, Request $req): array
    {
        $req->validate([
            'name' => 'required|string',
            'input_type' => 'required|string',
            'value' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            $setting = SiteSetting::where('key', $key)->first();
            if (!$setting) {
                return [
                    'success' => false,
                    'message' => 'Setting not found',
                ];
            }

            $data = [
                'name' => $req->name,
                'input_type' => $req->input_type,
            ];

            if ($req->hasFile('image')) {
                Storage::disk('s3')->delete($setting->value);
                $data['value'] = $req->file('image')->store('site_image', 's3');
            } else {
                $data['value'] = $req->value;
            }

            $setting->update(array_filter($data));
            Cache::forget("site_setting_{$key}");
            Cache::forget('site_settings');

            $this->logService->log(Auth::id(), 'updated_setting', SiteSetting::class, $setting->id, json_encode([
                'key' => $key,
                'old_value' => $setting->getOriginal('value'),
                'new_value' => $data['value'],
            ]));

            return [
                'success' => true,
                'data' => [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'input_type' => $setting->input_type,
                ],
            ];
        } catch (Exception $e) {
            Log::error('Error updating setting: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error updating setting',
            ];
        }
    }

    public function createSetting(Request $req): array
    {
        $req->validate([
            'key' => 'required|string|unique:site_settings,key',
            'name' => 'required|string',
            'input_type' => 'required|string',
            'value' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            $data = [
                'key' => $req->key,
                'name' => $req->name,
                'value' => $req->hasFile('image') ? $req->file('image')->store('site_image', 's3') : $req->value,
                'input_type' => $req->input_type,
            ];

            $setting = SiteSetting::create($data);
            Cache::forget('site_settings');

            $this->logService->log(Auth::id(), 'created_setting', SiteSetting::class, $setting->id, json_encode($data));

            return [
                'success' => true,
                'data' => [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'input_type' => $setting->input_type,
                ],
            ];
        } catch (Exception $e) {
            Log::error('Error creating setting: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error creating setting',
            ];
        }
    }

    public function deleteSetting(string $key): array
    {
        try {
            $setting = SiteSetting::where('key', $key)->first();
            if (!$setting) {
                return [
                    'success' => false,
                    'message' => 'Setting not found',
                ];
            }

            $setting->delete();
            Cache::forget("site_setting_{$key}");
            Cache::forget('site_settings');

            $this->logService->log(Auth::id(), 'deleted_setting', SiteSetting::class, $setting->id, json_encode([
                'model' => get_class($setting),
                'key' => $setting->key,
            ]));

            return [
                'success' => true,
                'message' => 'Setting deleted successfully',
            ];
        } catch (Exception $e) {
            Log::error('Error deleting setting: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error deleting setting',
            ];
        }
    }
}
