<?php

namespace Vendor\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Vendor\Customer\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Search History Controller
 * 
 * Handles search history management for authenticated users
 * - GET: Retrieve user's search history
 * - POST: Save a search keyword
 * - DELETE: Clear user's search history
 */
class SearchHistoryController extends Controller
{
    /**
     * Get user's search history
     * Returns recent searches ordered by searched_at desc
     * 
     * Response: { "data": [{ "keyword": "...", "searched_at": "..." }] }
     */
    public function index(Request $request)
    {
        $customer = $request->user();

        if (!$customer) {
            return response()->json([
                'data' => []
            ], 200);
        }

        // Get search history from database
        $history = DB::table('search_history')
            ->where('user_id', $customer->id)
            ->orderBy('searched_at', 'desc')
            ->limit(20) // Max 20 recent searches
            ->get(['keyword', 'searched_at'])
            ->map(function ($item) {
                return [
                    'keyword' => $item->keyword,
                    'searched_at' => $item->searched_at,
                ];
            });

        return response()->json([
            'data' => $history
        ], 200);
    }

    /**
     * Save search keyword
     * Creates or updates search history entry
     * 
     * Request: { "keyword": "..." }
     * Response: { "data": { "keyword": "...", "searched_at": "..." } }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:255',
        ]);

        $customer = $request->user();

        if (!$customer) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $keyword = trim($validated['keyword']);

        if (empty($keyword)) {
            return response()->json([
                'message' => 'Keyword cannot be empty'
            ], 400);
        }

        // Check if keyword already exists for this user
        $existing = DB::table('search_history')
            ->where('user_id', $customer->id)
            ->where('keyword', $keyword)
            ->first();

        if ($existing) {
            // Update searched_at timestamp
            DB::table('search_history')
                ->where('id', $existing->id)
                ->update(['searched_at' => now()]);
        } else {
            // Insert new entry
            DB::table('search_history')->insert([
                'user_id' => $customer->id,
                'keyword' => $keyword,
                'searched_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'data' => [
                'keyword' => $keyword,
                'searched_at' => now()->toIso8601String(),
            ]
        ], 200);
    }

    /**
     * Clear user's search history
     * 
     * Response: { "message": "Search history cleared" }
     */
    public function destroy(Request $request)
    {
        $customer = $request->user();

        if (!$customer) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        DB::table('search_history')
            ->where('user_id', $customer->id)
            ->delete();

        return response()->json([
            'message' => 'Search history cleared'
        ], 200);
    }
}
