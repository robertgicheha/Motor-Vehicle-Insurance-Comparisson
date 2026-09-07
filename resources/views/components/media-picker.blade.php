@once
<div id="mediaPickerModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="window.closeMediaPicker()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-2xl bg-white dark:bg-gray-900 shadow-2xl flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between shrink-0">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Media Library</p>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">Select an Image</h2>
            </div>
            <button onclick="window.closeMediaPicker()" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="px-6 pt-4 shrink-0 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.mediaPickerShowTab('library')" id="mediaPickerTabLibrary"
                        class="px-3 py-1.5 text-[11px] font-semibold rounded-lg bg-blue-600 text-white transition">Media Library</button>
                <button type="button" onclick="window.mediaPickerShowTab('upload')" id="mediaPickerTabUpload"
                        class="px-3 py-1.5 text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition">Upload from Computer</button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            <div id="mediaPickerGrid" class="grid grid-cols-3 sm:grid-cols-4 gap-4">
                <div class="col-span-full text-center py-12 text-sm text-gray-400">Loading...</div>
            </div>

            <div id="mediaPickerUpload" class="hidden">
                <div id="mediaPickerDrop" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-blue-400 transition cursor-pointer">
                    <svg class="w-10 h-10 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Click to upload or drag &amp; drop</p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF, SVG, WebP, ICO (max 10MB)</p>
                    <input type="file" id="mediaPickerFile" name="file" accept="image/*" class="hidden" multiple>
                </div>
                <div id="mediaPickerUploadStatus" class="hidden mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-blue-600 h-1.5 rounded-full transition-all" id="mediaPickerProgressBar" style="width:0%"></div>
                    </div>
                    <p id="mediaPickerUploadMsg" class="text-xs text-gray-500 mt-2"></p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var mediaPickerCallback = null;
    var mediaPickerSelectedPath = null;

    window.openMediaPicker = function(options) {
        options = options || {};
        mediaPickerCallback = options.onSelect || null;
        mediaPickerSelectedPath = options.selectedPath || null;
        document.getElementById('mediaPickerModal').classList.remove('hidden');
        window.mediaPickerShowTab('library');
        loadMediaPickerGrid();
    };

    window.closeMediaPicker = function() {
        document.getElementById('mediaPickerModal').classList.add('hidden');
        mediaPickerCallback = null;
        mediaPickerSelectedPath = null;
        document.getElementById('mediaPickerFile').value = '';
        document.getElementById('mediaPickerUploadStatus').classList.add('hidden');
    };

    window.mediaPickerShowTab = function(tab) {
        var grid = document.getElementById('mediaPickerGrid');
        var upload = document.getElementById('mediaPickerUpload');
        var tabLib = document.getElementById('mediaPickerTabLibrary');
        var tabUp = document.getElementById('mediaPickerTabUpload');

        if (tab === 'upload') {
            grid.classList.add('hidden');
            upload.classList.remove('hidden');
            tabLib.className = 'px-3 py-1.5 text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition';
            tabUp.className = 'px-3 py-1.5 text-[11px] font-semibold bg-blue-600 text-white rounded-lg transition';
        } else {
            upload.classList.add('hidden');
            grid.classList.remove('hidden');
            if (!grid.dataset.loaded) loadMediaPickerGrid();
            tabUp.className = 'px-3 py-1.5 text-[11px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition';
            tabLib.className = 'px-3 py-1.5 text-[11px] font-semibold bg-blue-600 text-white rounded-lg transition';
        }
    };

    function pickMediaPickerItem(item) {
        if (mediaPickerCallback) mediaPickerCallback(item);
        window.closeMediaPicker();
    }

    function loadMediaPickerGrid() {
        var grid = document.getElementById('mediaPickerGrid');
        grid.dataset.loaded = '1';
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-sm text-gray-400">Loading...</div>';
        fetch('{{ route("admin.media.list") }}')
            .then(function(r) { return r.json(); })
            .then(function(items) {
                if (!items || !items.length) {
                    grid.innerHTML = '<div class="col-span-full text-center py-12 text-sm text-gray-400">No images yet — upload one from your computer above, or from the Media Library page.</div>';
                    return;
                }
                grid.innerHTML = '';
                items.forEach(function(m) {
                    var div = document.createElement('div');
                    div.className = 'relative bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 transition' +
                        (mediaPickerSelectedPath && mediaPickerSelectedPath === m.path ? ' ring-2 ring-blue-500 border-blue-500' : '');
                    div.innerHTML = '<div class="aspect-square flex items-center justify-center p-2"><img src="' + m.url + '" alt="' + m.filename + '" class="max-w-full max-h-full object-contain"></div><div class="p-2"><p class="text-[10px] text-gray-600 dark:text-gray-400 truncate">' + m.filename + '</p></div>';
                    div.onclick = function() {
                        pickMediaPickerItem({ id: m.id, path: m.path || null, url: m.url, filename: m.filename });
                    };
                    grid.appendChild(div);
                });
            })
            .catch(function() {
                grid.innerHTML = '<div class="col-span-full text-center py-12 text-sm text-red-400">Failed to load media.</div>';
            });
    }

    function uploadMediaPickerFiles(files) {
        if (!files || !files.length) return;
        var status = document.getElementById('mediaPickerUploadStatus');
        var bar = document.getElementById('mediaPickerProgressBar');
        var msg = document.getElementById('mediaPickerUploadMsg');
        status.classList.remove('hidden');

        var uploaded = 0;
        var total = files.length;
        var failed = 0;
        var uploadedItems = [];

        function uploadNext(index) {
            if (index >= total) {
                bar.style.width = '100%';
                loadMediaPickerGrid();
                window.mediaPickerShowTab('library');
                if (uploaded === 1) {
                    pickMediaPickerItem(uploadedItems[0]);
                } else {
                    setTimeout(function() {
                        status.classList.add('hidden');
                        msg.textContent = '';
                    }, 1500);
                }
                return;
            }

            bar.style.width = (index / total * 100) + '%';
            msg.textContent = 'Uploading ' + (index + 1) + ' of ' + total + '...';

            var fd = new FormData();
            fd.append('file', files[index]);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("admin.media.upload") }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    bar.style.width = ((index + e.loaded / e.total) / total * 100) + '%';
                }
            };

            xhr.onload = function() {
                if (xhr.status === 200) {
                    uploaded++;
                    try {
                        var resp = JSON.parse(xhr.responseText);
                        if (resp.media) {
                            uploadedItems.push({ id: resp.media.id, path: resp.media.path, url: resp.url, filename: resp.media.original_filename });
                        }
                    } catch(e) {}
                } else {
                    failed++;
                }
                uploadNext(index + 1);
            };

            xhr.onerror = function() {
                failed++;
                uploadNext(index + 1);
            };

            xhr.send(fd);
        }

        uploadNext(0);
    }

    var fileInput = document.getElementById('mediaPickerFile');
    var dropZone = document.getElementById('mediaPickerDrop');

    fileInput.addEventListener('change', function() {
        uploadMediaPickerFiles(this.files);
        this.value = '';
    });

    dropZone.addEventListener('click', function() {
        fileInput.click();
    });

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        var files = e.dataTransfer.files;
        if (files && files.length) uploadMediaPickerFiles(files);
    });
})();
</script>
@endpush
@endonce