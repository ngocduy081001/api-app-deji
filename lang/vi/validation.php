<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted' => ':attribute phải được chấp nhận.',
    'active_url' => ':attribute không phải là một URL hợp lệ.',
    'after' => ':attribute phải là một ngày sau ngày :date.',
    'after_or_equal' => ':attribute phải là một ngày sau hoặc bằng ngày :date.',
    'alpha' => ':attribute chỉ có thể chứa các chữ cái.',
    'alpha_dash' => ':attribute chỉ có thể chứa chữ cái, số và dấu gạch ngang.',
    'alpha_num' => ':attribute chỉ có thể chứa chữ cái và số.',
    'array' => ':attribute phải là một mảng.',
    'before' => ':attribute phải là một ngày trước ngày :date.',
    'before_or_equal' => ':attribute phải là một ngày trước hoặc bằng ngày :date.',
    'between' => [
        'numeric' => ':attribute phải nằm trong khoảng :min - :max.',
        'file' => ':attribute phải nằm trong khoảng :min - :max kilobytes.',
        'string' => ':attribute phải nằm trong khoảng :min - :max ký tự.',
        'array' => ':attribute phải có từ :min - :max phần tử.',
    ],
    'boolean' => ':attribute phải là true hoặc false.',
    'confirmed' => 'Xác nhận :attribute không khớp.',
    'current_password' => 'Mật khẩu không chính xác.',
    'date' => ':attribute không phải là một ngày hợp lệ.',
    'date_equals' => ':attribute phải là một ngày bằng với :date.',
    'date_format' => ':attribute không khớp với định dạng :format.',
    'different' => ':attribute và :other phải khác nhau.',
    'digits' => ':attribute phải có :digits chữ số.',
    'digits_between' => ':attribute phải có từ :min đến :max chữ số.',
    'dimensions' => ':attribute có kích thước hình ảnh không hợp lệ.',
    'distinct' => ':attribute có giá trị trùng lặp.',
    'email' => ':attribute phải là một địa chỉ email hợp lệ.',
    'ends_with' => ':attribute phải kết thúc bằng một trong những giá trị sau: :values',
    'exists' => ':attribute đã chọn không hợp lệ.',
    'file' => ':attribute phải là một tệp tin.',
    'filled' => ':attribute không được bỏ trống.',
    'gt' => [
        'numeric' => ':attribute phải lớn hơn :value.',
        'file' => ':attribute phải lớn hơn :value kilobytes.',
        'string' => ':attribute phải nhiều hơn :value ký tự.',
        'array' => ':attribute phải có nhiều hơn :value phần tử.',
    ],
    'gte' => [
        'numeric' => ':attribute phải lớn hơn hoặc bằng :value.',
        'file' => ':attribute phải lớn hơn hoặc bằng :value kilobytes.',
        'string' => ':attribute phải nhiều hơn hoặc bằng :value ký tự.',
        'array' => ':attribute phải có :value phần tử trở lên.',
    ],
    'image' => ':attribute phải là một hình ảnh.',
    'in' => ':attribute đã chọn không hợp lệ.',
    'in_array' => ':attribute không tồn tại trong :other.',
    'integer' => ':attribute phải là một số nguyên.',
    'ip' => ':attribute phải là một địa chỉ IP hợp lệ.',
    'ipv4' => ':attribute phải là một địa chỉ IPv4 hợp lệ.',
    'ipv6' => ':attribute phải là một địa chỉ IPv6 hợp lệ.',
    'json' => ':attribute phải là một chuỗi JSON hợp lệ.',
    'lt' => [
        'numeric' => ':attribute phải nhỏ hơn :value.',
        'file' => ':attribute phải nhỏ hơn :value kilobytes.',
        'string' => ':attribute phải ít hơn :value ký tự.',
        'array' => ':attribute phải có ít hơn :value phần tử.',
    ],
    'lte' => [
        'numeric' => ':attribute phải nhỏ hơn hoặc bằng :value.',
        'file' => ':attribute phải nhỏ hơn hoặc bằng :value kilobytes.',
        'string' => ':attribute phải ít hơn hoặc bằng :value ký tự.',
        'array' => ':attribute không được có nhiều hơn :value phần tử.',
    ],
    'max' => [
        'numeric' => ':attribute không được lớn hơn :max.',
        'file' => ':attribute không được lớn hơn :max kilobytes.',
        'string' => ':attribute không được lớn hơn :max ký tự.',
        'array' => ':attribute không được có nhiều hơn :max phần tử.',
    ],
    'mimes' => ':attribute phải là một tệp tin có định dạng: :values.',
    'mimetypes' => ':attribute phải là một tệp tin có định dạng: :values.',
    'min' => [
        'numeric' => ':attribute phải tối thiểu là :min.',
        'file' => ':attribute phải tối thiểu :min kilobytes.',
        'string' => ':attribute phải có tối thiểu :min ký tự.',
        'array' => ':attribute phải có tối thiểu :min phần tử.',
    ],
    'not_in' => ':attribute đã chọn không hợp lệ.',
    'not_regex' => 'Định dạng :attribute không hợp lệ.',
    'numeric' => ':attribute phải là một số.',
    'password' => 'Mật khẩu không chính xác.',
    'present' => ':attribute phải được cung cấp.',
    'regex' => 'Định dạng :attribute không hợp lệ.',
    'required' => ':attribute không được bỏ trống.',
    'required_if' => ':attribute không được bỏ trống khi :other là :value.',
    'required_unless' => ':attribute không được bỏ trống trừ khi :other là :values.',
    'required_with' => ':attribute không được bỏ trống khi :values có giá trị.',
    'required_with_all' => ':attribute không được bỏ trống khi :values có giá trị.',
    'required_without' => ':attribute không được bỏ trống khi :values không có giá trị.',
    'required_without_all' => ':attribute không được bỏ trống khi tất cả :values không có giá trị.',
    'same' => ':attribute và :other phải giống nhau.',
    'size' => [
        'numeric' => ':attribute phải bằng :size.',
        'file' => ':attribute phải bằng :size kilobytes.',
        'string' => ':attribute phải chứa :size ký tự.',
        'array' => ':attribute phải chứa :size phần tử.',
    ],
    'starts_with' => ':attribute phải bắt đầu bằng một trong những giá trị sau: :values',
    'string' => ':attribute phải là một chuỗi ký tự.',
    'timezone' => ':attribute phải là một múi giờ hợp lệ.',
    'unique' => ':attribute đã tồn tại.',
    'uploaded' => ':attribute tải lên thất bại.',
    'url' => 'Định dạng :attribute không hợp lệ.',
    'uuid' => ':attribute phải là một UUID hợp lệ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'tên',
        'username' => 'tên đăng nhập',
        'email' => 'email',
        'password' => 'mật khẩu',
        'password_confirmation' => 'xác nhận mật khẩu',
        'city' => 'thành phố',
        'country' => 'quốc gia',
        'address' => 'địa chỉ',
        'phone' => 'số điện thoại',
        'mobile' => 'di động',
        'age' => 'tuổi',
        'sex' => 'giới tính',
        'gender' => 'giới tính',
        'day' => 'ngày',
        'month' => 'tháng',
        'year' => 'năm',
        'hour' => 'giờ',
        'minute' => 'phút',
        'second' => 'giây',
        'title' => 'tiêu đề',
        'content' => 'nội dung',
        'description' => 'mô tả',
        'excerpt' => 'trích dẫn',
        'date' => 'ngày',
        'time' => 'thời gian',
        'available' => 'có sẵn',
        'size' => 'kích thước',
        'price' => 'giá',
        'qty' => 'số lượng',
        'sku' => 'mã sản phẩm',
    ],
];
