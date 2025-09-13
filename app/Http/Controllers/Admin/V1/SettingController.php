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
                    // Handle MediaPicker URLs for logo fields
                    if (in_array($key, ['app_logo', 'nota_logo']) && filter_var($value, FILTER_VALIDATE_URL)) {
                        $value = $this->extractFilename($value);
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

    private function extractFilename($url): string
    {
        $relativePath = str_replace(url('/'), '', $url);
        $relativePath = ltrim($relativePath, '/');

        // If file is already in settings folder, just use filename
        if (str_starts_with($relativePath, 'media/settings/')) {
            return basename($relativePath);
        }

        return basename(parse_url((string) $url, PHP_URL_PATH));
    }
}
