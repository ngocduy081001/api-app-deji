<?php

namespace Vendor\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vendor\Product\Models\PopularSearch;

/**
 * Popular Search Controller
 * 
 * Returns popular search keywords based on search frequency
 * 
 * Response: { "data": ["keyword1", "keyword2", ...] }
 */
class PopularSearchController extends Controller
{
    /**
     * Get popular searches
     * Returns top N most searched keywords
     * 
     * Query params:
     * - limit: Number of results (default: 10)
     */
    public function index(Request $request)
    {
        $limit = (int) $request->input('limit', 10);
        $limit = min(max($limit, 1), 50); // Clamp between 1 and 50

        // Priority 1: Get managed popular searches from database (admin-managed)
        $managedSearches = \Vendor\Product\Models\PopularSearch::active()
            ->ordered()
            ->limit($limit)
            ->pluck('keyword')
            ->toArray();

        // If we have enough managed searches, return them
        if (count($managedSearches) >= $limit) {
            return response()->json([
                'data' => $managedSearches
            ], 200);
        }

        // Priority 2: Fill remaining slots from search_history (auto-generated)
        $remaining = $limit - count($managedSearches);
        $autoSearches = DB::table('search_history')
            ->select('keyword', DB::raw('COUNT(*) as count'), DB::raw('MAX(searched_at) as last_searched'))
            ->groupBy('keyword')
            ->orderBy('count', 'desc')
            ->orderBy('last_searched', 'desc')
            ->limit($remaining)
            ->pluck('keyword')
            ->toArray();

        // Combine managed + auto-generated
        $popular = array_merge($managedSearches, $autoSearches);

        // Priority 3: If still empty, return default popular searches
        if (empty($popular)) {
            $popular = [
                'Laptop gaming',
                'Tai nghe không dây',
                'Đồng hồ thông minh',
                'Máy ảnh',
                'Gaming gear',
                'iPhone',
                'MacBook',
                'AirPods',
                'Samsung Galaxy',
                'iPad',
            ];
        }

        return response()->json([
            'data' => array_slice($popular, 0, $limit)
        ], 200);
    }
}
