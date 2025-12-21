<?php

namespace Vendor\Settings\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Vendor\Settings\Models\MenuGroup;

class MenuGroupController extends Controller
{
    /**
     * Display a listing of menu groups.
     */
    public function index(Request $request)
    {
        $location = $request->get('location', 'header');
        $menuGroups = MenuGroup::byLocation($location)
            ->withCount('menus')
            ->orderBy('name')
            ->get();

        $locations = MenuGroup::select('location')->distinct()->pluck('location');

        return view('settings::admin.menu-groups.index', compact('menuGroups', 'locations', 'location'));
    }

    /**
     * Show the form for creating a new menu group.
     */
    public function create()
    {
        return view('settings::admin.menu-groups.create');
    }

    /**
     * Store a newly created menu group.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:menu_groups,slug',
            'description' => 'nullable|string|max:500',
            'location' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        MenuGroup::create($validated);

        return redirect()->route('admin.menu-groups.index')
            ->with('success', 'Nhóm menu đã được tạo thành công.');
    }

    /**
     * Show the form for editing the specified menu group.
     */
    public function edit(MenuGroup $menuGroup)
    {
        return view('settings::admin.menu-groups.edit', compact('menuGroup'));
    }

    /**
     * Update the specified menu group.
     */
    public function update(Request $request, MenuGroup $menuGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:menu_groups,slug,' . $menuGroup->id,
            'description' => 'nullable|string|max:500',
            'location' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $menuGroup->update($validated);

        return redirect()->route('admin.menu-groups.index')
            ->with('success', 'Nhóm menu đã được cập nhật thành công.');
    }

    /**
     * Remove the specified menu group.
     */
    public function destroy(MenuGroup $menuGroup)
    {
        $menuGroup->delete();

        return redirect()->route('admin.menu-groups.index')
            ->with('success', 'Nhóm menu đã được xóa thành công.');
    }
}

