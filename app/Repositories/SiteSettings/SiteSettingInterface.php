<?php

namespace App\Repositories\SiteSettings;

use App\Models\SiteSetting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface SiteSettingInterface
{
    public function getAllSettings(string $search = null, int $perPage = 10): LengthAwarePaginator;

    public function getSetting(string $key): array; 

    public function updateSetting(string $key, Request $req): array; 
    
    public function createSetting(Request $req): array; 

    public function deleteSetting(string $key): array; 

}
