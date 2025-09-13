<?php

namespace App\Http\Controllers;

use App\Traits\HandleErrors;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    use AuthorizesRequests, HandleErrors;

    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $this->authorize('read media');

        return view('media.index');
    }

    public function show($id)
    {
        try {
            $media = Media::findOrFail($id);

            return view('media.show', ['media' => $media]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getModal(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        // Return the exact same media library components in modal format
        return view('media.modal', [
            'mode' => 'picker',
            'modalId' => 'settingsMediaModal',
            'title' => 'Select Media for Settings',
        ]);
    }

    public function getPicker(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        // Return media library without layout for iframe
        return view('media.picker', [
            'mode' => 'picker',
        ]);
    }
}
