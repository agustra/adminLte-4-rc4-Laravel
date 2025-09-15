<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function index()
    {
        return view('user.profile.index', [
            'user' => auth()->user()
        ]);
    }
    
    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_photo_path' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'profile_photo_path' => $request->profile_photo_path,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ]);
        
        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        
        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->route('profile.index')
            ->with('success', 'Password updated successfully!');
    }
}