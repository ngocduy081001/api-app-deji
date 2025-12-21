<?php

namespace Vendor\Settings\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Vendor\Settings\Models\Banner;

class BannerController extends Controller
{
    /**
     * Display a listing of banners.
     */
    public function index(Request $request)
    {
        $position = $request->get('position', 'top');
        $banners = Banner::byPosition($position)
            ->orderBy('order')
            ->paginate(20);

        $positions = Banner::select('position')->distinct()->pluck('position');

        return view('settings::admin.banners.index', compact('banners', 'positions', 'position'));
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        return view('settings::admin.banners.create');
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'position' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        Banner::create($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được tạo thành công.');
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(Banner $banner)
    {
        return view('settings::admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified banner.
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'position' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $banner->update($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được cập nhật thành công.');
    }

    /**
     * Remove the specified banner.
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được xóa thành công.');
    }
}
