<?php

namespace App\Http\Controllers\Admin\V1\FileManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    /**
     * Display FileManager interface
     */
    public function index()
    {
        return view('admin.filemanager.index');
    }
    
    /**
     * Display FileManager in popup mode
     */
    public function popup(Request $request)
    {
        $type = $request->get('type', 'file');
        return view('admin.filemanager.popup', compact('type'));
    }
}