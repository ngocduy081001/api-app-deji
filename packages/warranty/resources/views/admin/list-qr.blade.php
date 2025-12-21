<style>
    .qr-print-sheet {
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
<div class="qr-print-sheet">
    @foreach ($warranties as $warranty)
        <div class="qr-item">
            @if ($warranty->qr_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($warranty->qr_path) }}"
                    alt="QR {{ $warranty->warranty_code }}" class="qr-image">
            @endif
        </div>
    @endforeach
</div>
