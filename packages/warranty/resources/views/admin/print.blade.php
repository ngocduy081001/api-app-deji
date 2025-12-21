<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">In QR - {{ $product->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">Có {{ $warranties->count() }} mã chưa in</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="printQrList()"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-black rounded-md hover:bg-gray-800">
                    In ngay
                </button>
                <a href="{{ route('admin.warranties.show', $product) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Quay lại
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-admin.card class="print-controls">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-600">Hãy sử dụng nút <strong>In ngay</strong> hoặc phím tắt
                        <kbd class="px-2 py-1 text-xs border rounded">Cmd/Ctrl + P</kbd> để mở hộp thoại in.
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Sau khi in xong, bấm nút bên dưới để đánh dấu các mã đã in.
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.warranties.print.mark', $product) }}">
                    @csrf
                    @foreach ($warranties as $warranty)
                        <input type="hidden" name="warranty_ids[]" value="{{ $warranty->id }}">
                    @endforeach
                    <x-admin.button type="submit">
                        Đánh dấu đã in
                    </x-admin.button>
                </form>
            </div>
        </x-admin.card>

        <div id="qr-print-area">
            @include('warranty::admin.list-qr', ['warranties' => $warranties])
        </div>
    </div>

    @push('styles')
        <style>
            #qr-print-area .qr-print-sheet {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
            }

            #qr-print-area .qr-item {
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            @media print {
                @page {
                    size: auto;
                    margin: 5mm;
                }

                body {
                    background: #fff;
                    margin: 0 !important;
                }

                header,
                nav,
                .print-controls,
                .print-controls * {
                    display: none !important;
                }

                #qr-print-area .qr-print-sheet {
                    gap: 0 !important;
                }

                #qr-print-area .qr-item {
                    width: 50px !important;
                    height: 50px !important;
                }
            }

            .qr-image {
                width: 50px;
                height: 50px;
                object-fit: contain;
                display: block;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function printQrList() {
                const previewUrl = "{{ route('admin.warranties.print.preview', $product) }}";
                window.open(previewUrl, '_blank', 'width=900,height=700');
            }
        </script>
    @endpush
</x-admin-layout>
