<?php

namespace Vendor\Settings\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Vendor\Settings\Models\Menu;
use Vendor\Settings\Http\Resources\MenuResource;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function getMain(Request $request)
    {
        $parentId = $request->query('parent_id');


        $parentId = is_numeric($parentId) ? (int) $parentId : null;


        $menuItems = Menu::with('children')->where('parent_id', $parentId)->where('menu_group_id', 5)->orderBy('order', 'asc')->get();

        if ($menuItems->isEmpty()) {
            return response()->json(['error' => 'No menu items found'], 404);
        }

        return MenuResource::collection($menuItems);
    }
}
