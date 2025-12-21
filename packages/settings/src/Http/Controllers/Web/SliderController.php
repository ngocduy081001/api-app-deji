<?php

namespace Vendor\Settings\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Vendor\Settings\Models\Slider;

class SliderController extends Controller
{
    /**
     * Display a listing of sliders.
     */
    public function index(Request $request)
    {
        $sliders = Slider::withCount('items')
            ->orderBy('name')
            ->get();

        return view('settings::admin.sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new slider.
     */
    public function create()
    {
        return view('settings::admin.sliders.create');
    }

    /**
     * Store a newly created slider.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'nullable|string|max:255|unique:sliders,key',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['name']);
        }

        Slider::create($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider đã được tạo thành công.');
    }

    /**
     * Show the form for editing the specified slider.
     */
    public function edit(Slider $slider)
    {
        return view('settings::admin.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified slider.
     */
    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'nullable|string|max:255|unique:sliders,key,' . $slider->id,
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['name']);
        }

        $slider->update($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider đã được cập nhật thành công.');
    }

    /**
     * Remove the specified slider.
     */
    public function destroy(Slider $slider)
    {
        $slider->delete();

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider đã được xóa thành công.');
    }
}

