<?php

namespace Vendor\News\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Có thể thêm logic authorize ở đây
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Lấy article từ route parameter (có thể là model object hoặc ID)
        $article = $this->route('article');
        $articleId = is_object($article) ? $article->id : ($article ?? null);

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:articles,slug,' . $articleId],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:news_categories,id'],
            'author_id' => ['nullable', 'integer', 'exists:users,id'],
            'featured_image' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string'],
            'status' => ['nullable', 'string', 'in:draft,published,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
            'meta_data' => ['nullable', 'array'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert tags from JSON string to array
        if ($this->has('tags') && is_string($this->input('tags'))) {
            $tags = json_decode($this->input('tags'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge([
                    'tags' => is_array($tags) ? $tags : [],
                ]);
            } else {
                $this->merge([
                    'tags' => [],
                ]);
            }
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề bài viết là bắt buộc',
            'title.max' => 'Tiêu đề bài viết không được vượt quá 255 ký tự',
            'slug.unique' => 'Slug này đã được sử dụng',
            'excerpt.max' => 'Tóm tắt không được vượt quá 1000 ký tự',
            'category_id.exists' => 'Danh mục không tồn tại',
            'author_id.exists' => 'Tác giả không tồn tại',
            'status.in' => 'Trạng thái không hợp lệ',
            'sort_order.min' => 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0',
            'published_at.date' => 'Ngày xuất bản phải là định dạng ngày hợp lệ',
            'tags.array' => 'Tags phải là mảng',
            'tags.*.max' => 'Mỗi tag không được vượt quá 50 ký tự',
        ];
    }
}
