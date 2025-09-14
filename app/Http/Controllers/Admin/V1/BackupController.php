<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Traits\HandleErrors;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BackupController extends Controller
{
    use AuthorizesRequests, HandleErrors;

    protected $authorizeAction = 'backup';

    public function __construct(protected \App\Services\BackupService $backupService) {}

    public function index()
    {
        try {
            $this->authorize('read '.$this->authorizeAction, 'web');

            return view('admin.backup.index');
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function show()
    {
        return false;
    }
}
