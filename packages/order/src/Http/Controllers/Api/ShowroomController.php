<?php

namespace Vendor\Order\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\Order\Models\Showroom;

class ShowroomController extends Controller
{
    /**
     * Display a listing of showrooms.
     */
    public function index(Request $request)
    {
        $query = Showroom::query();

        // Filter by status (default: only published)
        $status = $request->input('status', 'published');
        if ($status) {
            $query->where('status', $status);
        }

        // Order by order field
        $showrooms = $query->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'data' => $showrooms,
        ]);
    }

    /**
     * Display the specified showroom.
     */
    public function show($id)
    {
        $showroom = Showroom::find($id);

        if (!$showroom) {
            return response()->json([
                'error' => 'Showroom not found',
            ], 404);
        }

        return response()->json([
            'data' => $showroom,
        ]);
    }
}

