<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\HandleErrors;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    use AuthorizesRequests, HandleErrors;

    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $this->authorize('read settings');

        return view('admin.settings.index');
    }

    // public function show($id)
    // {
    //     // Redirect to index since settings doesn't have individual show pages
    //     return redirect()->route('settings.index');
    // }

    public function store(Request $request)
    {
        $this->authorize('create settings');

        try {
            $data = $request->except('_token');

            foreach ($data as $key => $value) {
                if (! empty($value) || $value === '0') {
                    // Handle FileManager paths for logo fields
                    if (in_array($key, ['app_logo', 'nota_logo'])) {
                        $value = $this->processLogoPath($value);
                    }

                    Setting::updateOrCreate(['key' => $key], ['value' => $value]);
                }
            }

            Artisan::call('config:clear');

            return response()->json(['message' => 'Data saved successfully']);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    private function processLogoPath($path): string
    {
        if (empty($path)) {
            return '';
        }
        
        // If it's a full URL, extract the path part
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $path = parse_url($path, PHP_URL_PATH);
            $path = ltrim($path, '/');
        }
        
        // Remove 'storage/' prefix if present (since we'll add it in view)
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
        
        // Ensure path starts with filemanager/images/public/
        if (!str_starts_with($path, 'filemanager/images/public/')) {
            // If it's just a filename, assume it's in public folder
            if (!str_contains($path, '/')) {
                $path = 'filemanager/images/public/' . $path;
            }
        }
        
        return $path;
    }
}
