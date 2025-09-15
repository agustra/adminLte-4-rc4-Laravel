<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    /**
     * Display user FileManager interface
     */
    public function index()
    {
        return view('user.filemanager.index');
    }
    
    /**
     * Display FileManager in popup mode for user
     */
    public function popup(Request $request)
    {
        $type = $request->get('type', 'file');
        return view('user.filemanager.popup', compact('type'));
    }
}