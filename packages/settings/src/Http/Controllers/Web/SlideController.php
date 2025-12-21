<?php

namespace Vendor\Settings\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Vendor\Settings\Models\Slider;
use Vendor\Settings\Models\SliderItem;

class SlideController extends Controller
{
    /**
     * Display a listing of slider items for a selected slider.
     */
    public function index(Request $request)
    {
        $sliders = Slider::orderBy('name')->get();

        if ($sliders->isEmpty()) {
            return view('settings::admin.slides.index', [
                'sliderItems' => collect(),
                'sliders' => $sliders,
                'currentSlider' => null,
            ]);
        }

        $selectedSliderId = (int) $request->get('slider_id', $sliders->first()->id);
        $currentSlider = $sliders->firstWhere('id', $selectedSliderId) ?? $sliders->first();

        $sliderItems = SliderItem::where('slider_id', $currentSlider->id)
            ->orderBy('order')
            ->get();

        return view('settings::admin.slides.index', [
            'sliderItems' => $sliderItems,
            'sliders' => $sliders,
            'currentSlider' => $currentSlider,
        ]);
    }

    /**
     * Store a newly created slider item.
     */
    public function store(Request $request)
    {
        $request->merge([
            'order' => $request->filled('order') ? $request->input('order') : 0,
        ]);

        $validated = $request->validate([
            'slider_id' => 'required|exists:sliders,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_mobile' => 'nullable|string',
            'link' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        SliderItem::create($validated);

        return redirect()->route('admin.slides.index', ['slider_id' => $validated['slider_id']])
            ->with('success', 'Slider item đã được tạo thành công.');
    }

    /**
     * Update the specified slider item.
     */
    public function update(Request $request, SliderItem $sliderItem)
    {
        $request->merge([
            'order' => $request->filled('order') ? $request->input('order') : 0,
        ]);

        $validated = $request->validate([
            'slider_id' => 'required|exists:sliders,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_mobile' => 'nullable|string',
            'link' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $sliderItem->update($validated);

        return redirect()->route('admin.slides.index', ['slider_id' => $validated['slider_id']])
            ->with('success', 'Slider item đã được cập nhật thành công.');
    }

    /**
     * Remove the specified slider item.
     */
    public function destroy(SliderItem $sliderItem)
    {
        $sliderId = $sliderItem->slider_id;
        $sliderItem->delete();

        return redirect()->route('admin.slides.index', ['slider_id' => $sliderId])
            ->with('success', 'Slider item đã được xóa thành công.');
    }

    /**
     * Update slider items order.
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'slider_id' => 'required|exists:sliders,id',
            'order' => 'required|array',
            'order.*.id' => 'required|exists:slider_items,id',
            'order.*.order' => 'required|integer',
        ]);

        try {
            foreach ($validated['order'] as $item) {
                SliderItem::where('id', $item['id'])
                    ->where('slider_id', $validated['slider_id'])
                    ->update([
                        'order' => $item['order'],
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật thứ tự slider items thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
