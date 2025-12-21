<option value="{{ $category->id }}" {{ $selected == $category->id ? 'selected' : '' }}>
    {{ str_repeat('— ', $level) }}{{ $category->name }}
</option>
@foreach ($category->children as $child)
    @include('product::admin.categories.partials.tree-option', [
        'category' => $child,
        'level' => $level + 1,
        'selected' => $selected,
    ])
@endforeach
