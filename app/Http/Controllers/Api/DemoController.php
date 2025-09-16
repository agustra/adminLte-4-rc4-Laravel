<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DemoController extends Controller
{
    /**
     * Proxy endpoint for DummyJSON users API
     * Transforms response to Modern Table format
     */
    public function users(Request $request)
    {
        try {
            // Get Modern Table parameters
            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $searchTerm = $request->input('search.value', '');
            
            // For testing with large dataset, use a different approach
            // DummyJSON only has ~200 users, so we'll simulate 10000 by repeating data
            $dummyParams = [
                'limit' => 100, // Get max from DummyJSON
                'skip' => 0,
                'q' => $searchTerm
            ];
            
            // Call DummyJSON API
            $response = Http::get('https://dummyjson.com/users', $dummyParams);
            
            if (!$response->successful()) {
                throw new \Exception('Failed to fetch data from DummyJSON');
            }
            
            $data = $response->json();
            $baseUsers = $data['users'] ?? [];
            
            // Simulate 10000 users by repeating and modifying base data
            $simulatedUsers = [];
            $totalSimulated = 10000;
            
            for ($i = 0; $i < $totalSimulated; $i++) {
                $baseIndex = $i % count($baseUsers);
                $user = $baseUsers[$baseIndex];
                
                // Modify user data to make it unique
                $user['id'] = $i + 1;
                $user['firstName'] = $user['firstName'] . '_' . ($i + 1);
                $user['lastName'] = $user['lastName'] . '_' . ($i + 1);
                $user['email'] = 'user' . ($i + 1) . '@example.com';
                $user['phone'] = '+1-555-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
                
                $simulatedUsers[] = $user;
            }
            
            // Apply search filter if provided
            if (!empty($searchTerm)) {
                $simulatedUsers = array_filter($simulatedUsers, function($user) use ($searchTerm) {
                    $searchFields = [
                        $user['firstName'] ?? '',
                        $user['lastName'] ?? '',
                        $user['email'] ?? '',
                        $user['phone'] ?? ''
                    ];
                    
                    foreach ($searchFields as $field) {
                        if (stripos($field, $searchTerm) !== false) {
                            return true;
                        }
                    }
                    return false;
                });
                
                // Re-index array after filtering
                $simulatedUsers = array_values($simulatedUsers);
            }
            
            $filteredTotal = count($simulatedUsers);
            
            // Apply pagination
            $paginatedUsers = array_slice($simulatedUsers, $start, $length);
            
            // Transform to Modern Table format
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalSimulated,
                'recordsFiltered' => $filteredTotal,
                'data' => $paginatedUsers
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
}