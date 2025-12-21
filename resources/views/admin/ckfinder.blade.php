<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>File Manager</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            margin-bottom: 20px;
            background: #fafafa;
        }

        .upload-area.dragover {
            border-color: #000;
            background: #f0f0f0;
        }

        .file-input {
            display: none;
        }

        .btn {
            background: #000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #333;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .image-item {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
        }

        .image-item:hover {
            border-color: #000;
        }

        .image-item.selected {
            border-color: #000;
            border-width: 2px;
        }

        .image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }

        .image-name {
            padding: 8px;
            font-size: 12px;
            background: white;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .preview {
            margin-top: 20px;
            text-align: center;
        }

        .preview img {
            max-width: 100%;
            max-height: 400px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Quản lý File</h1>

        <div class="upload-area" id="uploadArea">
            <p>Kéo thả file vào đây hoặc</p>
            <button class="btn" onclick="document.getElementById('fileInput').click()">Chọn File</button>
            <input type="file" id="fileInput" class="file-input" multiple accept="image/*">
        </div>

        <div class="gallery" id="gallery"></div>

        <div class="preview" id="preview"></div>

        <div class="actions">
            <button class="btn" onclick="selectFile()">Chọn</button>
            <button class="btn" onclick="window.close()">Đóng</button>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const callback = urlParams.get('CKEditorFuncNum');
        const type = urlParams.get('Type') || 'Images';
        let selectedFile = null;

        // Load existing images
        loadFiles();

        // Upload area handlers
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    uploadFile(file);
                }
            });
        }

        function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);

            fetch('{{ url('/admin/upload-image') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadFiles();
                    } else {
                        alert('Lỗi: ' + (data.message || 'Upload thất bại'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi upload file');
                });
        }

        function loadFiles() {
            fetch('{{ url('/admin/images') }}')
                .then(response => response.json())
                .then(data => {
                    const gallery = document.getElementById('gallery');
                    gallery.innerHTML = '';

                    if (data.images && data.images.length > 0) {
                        data.images.forEach(image => {
                            const item = document.createElement('div');
                            item.className = 'image-item';
                            item.innerHTML = `
                                <img src="${image.url}" alt="${image.name}">
                                <div class="image-name">${image.name}</div>
                            `;
                            item.addEventListener('click', () => {
                                document.querySelectorAll('.image-item').forEach(i => i.classList
                                    .remove('selected'));
                                item.classList.add('selected');
                                selectedFile = image.url;

                                // Show preview
                                const preview = document.getElementById('preview');
                                preview.innerHTML = `<img src="${image.url}" alt="Preview">`;
                            });
                            gallery.appendChild(item);
                        });
                    } else {
                        gallery.innerHTML =
                            '<p style="grid-column: 1/-1; text-align: center; color: #999;">Chưa có file nào</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function selectFile() {
            if (selectedFile && callback) {
                if (window.opener && window.opener.CKEDITOR) {
                    window.opener.CKEDITOR.tools.callFunction(callback, selectedFile);
                }
                window.close();
            } else {
                alert('Vui lòng chọn một file');
            }
        }
    </script>
</body>

</html>
