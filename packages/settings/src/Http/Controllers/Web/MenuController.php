<?php

namespace Vendor\Settings\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Vendor\Product\Models\ProductCategory;
use Vendor\Settings\Models\Menu;
use Vendor\Settings\Models\MenuGroup;

class MenuController extends Controller
{
    /**
     * Display a listing of menus.
     */
    public function index(Request $request)
    {
        $menuGroups = MenuGroup::orderBy('name')->get();

        if ($menuGroups->isEmpty()) {
            return view('settings::admin.menus.index', [
                'menus' => collect(),
                'menuGroups' => $menuGroups,
                'currentGroup' => null,
            ]);
        }

        $selectedGroupId = (int) $request->get('menu_group_id', $menuGroups->first()->id);
        $currentGroup = $menuGroups->firstWhere('id', $selectedGroupId) ?? $menuGroups->first();

        $menus = Menu::byMenuGroup($currentGroup->id)
            ->with('children')
            ->root()
            ->orderBy('order')
            ->get();

        // Get category IDs that are already used in this menu group
        $usedCategoryIds = Menu::byMenuGroup($currentGroup->id)
            ->whereNotNull('category_id')
            ->pluck('category_id')
            ->toArray();

        return view('settings::admin.menus.index', [
            'menus' => $menus,
            'menuGroups' => $menuGroups,
            'currentGroup' => $currentGroup,
            'usedCategoryIds' => $usedCategoryIds,
        ]);
    }

    /**
     * Show the form for creating a new menu.
     */
    public function create()
    {
        $menuGroups = MenuGroup::orderBy('name')->get();
        if ($menuGroups->isEmpty()) {
            return redirect()->route('admin.menus.index')
                ->with('error', 'Vui lòng tạo nhóm menu trước khi thêm menu.');
        }
        $parentMenus = Menu::with('menuGroup')->orderBy('name')->get();
        $categories = ProductCategory::orderBy('name')->get();

        return view('settings::admin.menus.create', compact(
            'menuGroups',
            'parentMenus',
            'categories'
        ));
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request)
    {
        $request->merge([
            'parent_id' => $request->filled('parent_id') ? $request->input('parent_id') : null,
            'category_id' => $request->filled('category_id') ? $request->input('category_id') : null,
            'slug' => $request->filled('slug') ? $request->input('slug') : null,
            'order' => $request->filled('order') ? $request->input('order') : 0,
        ]);

        $validated = $request->validate([
            'menu_group_id' => 'required|exists:menu_groups,id',
            'type' => 'required|in:category',
            'name' => 'nullable|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('menus', 'slug'),
            ],
            'icon' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:menus,id',
            'order' => 'nullable|integer',
            'target' => 'nullable|in:_self,_blank',
            'is_active' => 'boolean',
            'category_id' => 'required|exists:product_categories,id',
        ]);

        $parentValidation = $this->validateParentMenu($validated['parent_id'] ?? null, $validated['menu_group_id']);
        if ($parentValidation !== true) {
            return $parentValidation;
        }

        $menuData = $this->buildMenuPayload($validated);

        Menu::create($menuData);

        return redirect()->route('admin.menus.index', ['menu_group_id' => $validated['menu_group_id']])
            ->with('success', 'Menu đã được tạo thành công.');
    }

    /**
     * Show the form for editing the specified menu.
     */
    public function edit(Menu $menu)
    {
        $menuGroups = MenuGroup::orderBy('name')->get();
        if ($menuGroups->isEmpty()) {
            return redirect()->route('admin.menus.index')
                ->with('error', 'Vui lòng tạo nhóm menu trước khi chỉnh sửa.');
        }
        $parentMenus = Menu::with('menuGroup')
            ->where('id', '!=', $menu->id)
            ->orderBy('name')
            ->get();
        $categories = ProductCategory::orderBy('name')->get();

        return view('settings::admin.menus.edit', compact(
            'menu',
            'menuGroups',
            'parentMenus',
            'categories'
        ));
    }

    /**
     * Update the specified menu.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->merge([
            'parent_id' => $request->filled('parent_id') ? $request->input('parent_id') : null,
            'category_id' => $request->filled('category_id') ? $request->input('category_id') : null,
            'slug' => $request->filled('slug') ? $request->input('slug') : null,
            'order' => $request->filled('order') ? $request->input('order') : 0,
        ]);

        $validated = $request->validate([
            'menu_group_id' => 'required|exists:menu_groups,id',
            'type' => 'required|in:category',
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('menus', 'slug')->ignore($menu->id),
            ],
            'icon' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:menus,id',
            'order' => 'nullable|integer',
            'target' => 'nullable|in:_self,_blank',
            'is_active' => 'boolean',
            'category_id' => 'nullable|exists:product_categories,id',
        ]);

        // Keep existing category_id if not provided (when editing)
        if (!isset($validated['category_id'])) {
            $validated['category_id'] = $menu->category_id;
        }

        $parentValidation = $this->validateParentMenu($validated['parent_id'] ?? null, $validated['menu_group_id'], $menu->id);
        if ($parentValidation !== true) {
            return $parentValidation;
        }

        $menuData = $this->buildMenuPayload($validated, $menu);

        $menu->update($menuData);

        return redirect()->route('admin.menus.index', ['menu_group_id' => $validated['menu_group_id']])
            ->with('success', 'Menu đã được cập nhật thành công.');
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(Menu $menu)
    {
        $menuGroupId = $menu->menu_group_id;
        $menu->delete();

        return redirect()->route('admin.menus.index', ['menu_group_id' => $menuGroupId])
            ->with('success', 'Menu đã được xóa thành công.');
    }

    /**
     * Update menu order and hierarchy.
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'menu_group_id' => 'required|exists:menu_groups,id',
            'order' => 'required|array',
            'order.*.id' => 'required|exists:menus,id',
            'order.*.order' => 'required|integer',
            'order.*.parent_id' => 'nullable|integer|exists:menus,id',
        ]);

        try {
            foreach ($validated['order'] as $item) {
                Menu::where('id', $item['id'])
                    ->where('menu_group_id', $validated['menu_group_id'])
                    ->update([
                        'order' => $item['order'],
                        'parent_id' => $item['parent_id'],
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật thứ tự menu thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate the selected parent menu belongs to the same group.
     */
    protected function validateParentMenu(?int $parentId, int $menuGroupId, ?int $currentMenuId = null)
    {
        if (!$parentId) {
            return true;
        }

        $parentMenu = Menu::find($parentId);

        if (!$parentMenu) {
            return back()->withErrors([
                'parent_id' => 'Menu cha không hợp lệ.',
            ])->withInput();
        }

        if ($parentMenu->menu_group_id !== $menuGroupId) {
            return back()->withErrors([
                'parent_id' => 'Menu cha phải thuộc cùng nhóm menu.',
            ])->withInput();
        }

        if ($currentMenuId && $parentMenu->id === $currentMenuId) {
            return back()->withErrors([
                'parent_id' => 'Không thể chọn chính nó làm menu cha.',
            ])->withInput();
        }

        return true;
    }

    /**
     * Build the payload for storing/updating menu records.
     */
    protected function buildMenuPayload(array $validated, ?Menu $menu = null): array
    {
        $data = [
            'menu_group_id' => $validated['menu_group_id'],
            'type' => $validated['type'],
            'name' => $validated['name'] ?? null,
            'slug' => $validated['slug'] ?? null,
            'url' => null,
            'route' => null,
            'icon' => $validated['icon'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'order' => $validated['order'] ?? 0,
            'target' => $validated['target'] ?? '_self',
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'category_id' => null,
        ];

        if ($data['type'] === 'category') {
            $category = ProductCategory::find($validated['category_id']);
            $data['category_id'] = $category?->id;
            $data['url'] = null;
            $data['route'] = null;
            $data['name'] = $validated['name'] ?? $menu?->name ?? $category?->name;
            $data['slug'] = $validated['slug']
                ?? Str::slug(($category?->slug ?? 'category') . '-' . $data['menu_group_id'] . '-' . Str::random(5));
        }

        return $data;
    }
}
