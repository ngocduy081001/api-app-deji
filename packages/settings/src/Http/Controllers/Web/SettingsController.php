<?php

namespace Vendor\Settings\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Vendor\Settings\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Display a listing of settings.
     */
    public function index(Request $request)
    {

        $group = $request->get('group', 'general');
        $settings = Setting::byGroup($group)->get();

        // Get all groups with their display names, ordered by predefined order
        $allGroups = Setting::select('group')
            ->distinct()
            ->pluck('group')
            ->toArray();

        // Define the order of groups - chỉ hiển thị 3 tab
        $groupOrder = ['contact', 'email', 'general'];

        // Sort groups according to predefined order, then add any missing groups
        $groups = collect($groupOrder)
            ->filter(fn($group) => in_array($group, $allGroups))
            ->merge(collect($allGroups)->diff($groupOrder))
            ->values();

        // Resolve translated names for each group with sensible fallback
        $groupNames = $groups
            ->mapWithKeys(function ($groupName) {
                $translationKey = "settings.groups.{$groupName}";
                $translated = trans($translationKey);

                return [
                    $groupName => $translated === $translationKey
                        ? ucfirst($groupName)
                        : $translated,
                ];
            })
            ->toArray();

        return view('settings::admin.settings.index', compact('settings', 'groups', 'group', 'groupNames'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($request->input('settings', []) as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', __('settings.messages.updated'));
    }
}
