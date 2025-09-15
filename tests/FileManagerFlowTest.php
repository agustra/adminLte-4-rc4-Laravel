<?php

/**
 * FileManager Flow Test Script
 * Run: php artisan tinker < tests/FileManagerFlowTest.php
 */

echo "=== FileManager Flow Stability Test ===\n\n";

// Test 1: Check Users and Roles
echo "1. Testing Users and Roles:\n";
$admin = App\Models\User::whereHas('roles', function($q) { $q->where('name', 'super admin'); })->first();
$auditor = App\Models\User::whereHas('roles', function($q) { $q->where('name', 'auditor'); })->first();

echo "   Admin found: " . ($admin ? "✅ {$admin->name} (ID: {$admin->id})" : "❌ Not found") . "\n";
echo "   Auditor found: " . ($auditor ? "✅ {$auditor->name} (ID: {$auditor->id})" : "❌ Not found") . "\n";

// Test 2: Check Permissions
echo "\n2. Testing Permissions:\n";
if ($admin) {
    echo "   Admin can create users: " . ($admin->can('create users') ? "✅ YES" : "❌ NO") . "\n";
}
if ($auditor) {
    echo "   Auditor can create users: " . ($auditor->can('create users') ? "❌ YES (PROBLEM!)" : "✅ NO") . "\n";
    echo "   Auditor can read users: " . ($auditor->can('read users') ? "✅ YES" : "❌ NO") . "\n";
}

// Test 3: Check Menu Visibility
echo "\n3. Testing Menu Visibility:\n";
$adminMenu = App\Models\Menu::where('name', 'All Files (Admin)')->first();
if ($adminMenu) {
    echo "   Admin FileManager menu permission: {$adminMenu->permission}\n";
    if ($admin) {
        echo "   Admin can see menu: " . ($admin->can($adminMenu->permission) ? "✅ YES" : "❌ NO") . "\n";
    }
    if ($auditor) {
        echo "   Auditor can see menu: " . ($auditor->can($adminMenu->permission) ? "❌ YES (PROBLEM!)" : "✅ NO") . "\n";
    }
}

// Test 4: Check Folder Structure
echo "\n4. Testing Folder Structure:\n";
$imagesFolders = glob(storage_path('app/public/filemanager/images/*'));
$filesFolders = glob(storage_path('app/public/filemanager/files/*'));

echo "   Images folders: " . count($imagesFolders) . " found\n";
foreach ($imagesFolders as $folder) {
    $folderName = basename($folder);
    $fileCount = count(glob($folder . '/*'));
    echo "     - {$folderName}: {$fileCount} files\n";
}

echo "   Files folders: " . count($filesFolders) . " found\n";
foreach ($filesFolders as $folder) {
    $folderName = basename($folder);
    $fileCount = count(glob($folder . '/*'));
    echo "     - {$folderName}: {$fileCount} files\n";
}

// Test 5: Check Handler Configuration
echo "\n5. Testing Handler Configuration:\n";
$handlerClass = config('lfm.private_folder_name');
echo "   Handler class: {$handlerClass}\n";
echo "   Allow private folder: " . (config('lfm.allow_private_folder') ? "✅ YES" : "❌ NO") . "\n";
echo "   Allow shared folder: " . (config('lfm.allow_shared_folder') ? "✅ YES" : "❌ NO") . "\n";
echo "   Shared folder name: " . config('lfm.shared_folder_name') . "\n";

echo "\n=== Test Complete ===\n";