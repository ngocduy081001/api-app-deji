<div class="slider-item" data-id="{{ $item->id }}">
    <div class="slider-item-header">
        <div class="slider-item-title">
            <svg class="slider-item-handle w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
            </svg>
            <div class="slider-item-info">
                <div class="slider-item-name flex items-center gap-2">
                    @if ($item->image)
                        @php
                            $imageUrl = $item->image;
                            if (!str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, '/')) {
                                $imageUrl = asset('storage/' . ltrim($imageUrl, '/'));
                            } elseif (!str_starts_with($imageUrl, 'http')) {
                                $imageUrl = asset($imageUrl);
                            }
                        @endphp
                        <img src="{{ $imageUrl }}" alt="Slider image" class="w-16 h-10 object-cover rounded">
                    @endif
                    <span class="font-semibold text-gray-900">{{ $item->title ?: 'Không có tiêu đề' }}</span>
                </div>
                @if ($item->description)
                    <div class="slider-item-description">
                        {{ Str::limit($item->description, 50) }}
                    </div>
                @endif
                @if ($item->link)
                    <div class="slider-item-description text-xs">
                        <a href="{{ $item->link }}" target="_blank" class="text-blue-600 hover:underline">
                            {{ $item->link }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="slider-item-actions">
            <button type="button" class="btn-edit"
                onclick="editSliderItem({{ $item->id }}, {{ json_encode([
                    'title' => $item->title,
                    'description' => $item->description,
                    'image' => $item->image,
                    'image_mobile' => $item->image_mobile,
                    'link' => $item->link,
                ]) }})">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
            <button type="button" class="btn-delete"
                onclick="deleteSliderItem({{ $item->id }}, '{{ addslashes($item->title ?: 'này') }}')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
</div>
