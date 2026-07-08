<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Media</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Media Library</h1>
                <p class="mt-0.5 text-xs text-slate-400">All images in <code class="px-1 py-0.5 rounded bg-blue-500/20 text-blue-300">public/images/</code> — upload, rename, and manage.</p>
            </div>
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Upload Image
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 text-xs text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg px-4 py-3 fade-in">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-center gap-2 text-xs text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg px-4 py-3 fade-in">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Search/Filter bar --}}
    <div class="flex items-center gap-3">
        <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="mediaSearch" placeholder="Search images..." oninput="filterMedia(this.value)"
                   class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <span class="text-xs text-gray-400">{{ $media->count() }} file(s)</span>
    </div>

    <div id="mediaGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($media as $item)
        <div class="media-item group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition" data-filename="{{ strtolower($item['original_filename'] ?? $item['filename']) }}">
            <div class="aspect-square bg-gray-50 dark:bg-gray-700 flex items-center justify-center p-2">
                <img src="{{ $item['url'] }}" alt="{{ $item['original_filename'] ?? $item['filename'] }}" class="max-w-full max-h-full object-contain"
                     onclick="previewImage('{{ $item['url'] }}', '{{ $item['original_filename'] ?? $item['filename'] }}')"
                     style="cursor:pointer">
            </div>
            <div class="p-2">
                <p class="text-[10px] text-gray-600 dark:text-gray-400 truncate" title="{{ $item['original_filename'] ?? $item['filename'] }}">{{ $item['original_filename'] ?? $item['filename'] }}</p>
                <p class="text-[9px] text-gray-400">{{ number_format(($item['file_size'] ?? $item['size']) / 1024, 1) }} KB</p>
            </div>
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition">
                <button onclick="copyUrl('{{ $item['url'] }}')" class="p-1.5 bg-white/90 rounded-lg hover:bg-white transition" title="Copy URL">
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
                <button onclick="openRenameModal('{{ $item['path'] }}', '{{ $item['filename'] }}')" class="p-1.5 bg-white/90 rounded-lg hover:bg-white transition" title="Rename">
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/></svg>
                </button>
                @if($item['id'])
                <form action="{{ route('admin.media.destroy', $item['id']) }}" method="POST" onsubmit="return confirm('Delete this image permanently?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 bg-red-500/90 rounded-lg hover:bg-red-500 transition" title="Delete">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
                @else
                <form action="{{ route('admin.media.destroy-file', base64_encode($item['path'])) }}" method="POST" onsubmit="return confirm('Delete this image permanently? This file is not tracked in the database.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 bg-red-500/90 rounded-lg hover:bg-red-500 transition" title="Delete">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
                @endif
            </div>
            @if(!$item['id'])
            <div class="absolute top-1 right-1">
                <span class="text-[8px] font-semibold px-1 py-0.5 rounded bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">FS</span>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full text-center py-16">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No images found</p>
            <p class="text-xs text-gray-400 mt-1">Upload an image or add files to <code class="text-blue-500">public/images/</code></p>
        </div>
        @endforelse
    </div>
</div>

{{-- Upload Modal --}}
<div id="uploadModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('uploadModal').classList.add('hidden')"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white dark:bg-gray-900 shadow-2xl flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between shrink-0">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Media</p>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">Upload Image</h2>
            </div>
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <form id="uploadForm" action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-blue-400 transition cursor-pointer" onclick="document.getElementById('fileInput').click()" id="dropZone">
                    <svg class="w-10 h-10 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Click to upload or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF, SVG, WebP, ICO (max 10MB)</p>
                    <input type="file" id="fileInput" name="file" accept="image/*" class="hidden" onchange="previewUpload(this)" multiple>
                </div>
                <div id="uploadPreview" class="hidden">
                    <div id="previewList" class="grid grid-cols-3 gap-2 mb-3"></div>
                    <p class="text-xs text-center text-gray-500" id="uploadFileCount"></p>
                </div>
                <div id="uploadProgress" class="hidden">
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-blue-600 h-1.5 rounded-full transition-all" id="progressBar" style="width:0%"></div>
                    </div>
                </div>
                <button type="submit" id="uploadBtn" class="w-full px-4 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition disabled:opacity-50" disabled>
                    Upload
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Rename Modal --}}
<div id="renameModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeRenameModal()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-sm bg-white dark:bg-gray-900 shadow-2xl flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between shrink-0">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Media</p>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">Rename File</h2>
            </div>
            <button onclick="closeRenameModal()" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 px-6 py-5">
            <form id="renameForm" action="{{ route('admin.media.rename') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Current name</label>
                    <p class="text-sm text-gray-800 dark:text-gray-200 px-3 py-2 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700" id="renameCurrentName"></p>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">New filename</label>
                    <input type="text" name="new_name" id="renameNewName" required
                           class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <p class="text-[10px] text-gray-400 mt-1">Letters, numbers, hyphens, underscores, dots only.</p>
                    <input type="hidden" name="old_path" id="renameOldPath">
                </div>
                <button type="submit"
                        class="w-full px-4 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Rename File
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Preview Modal --}}
<div id="previewModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60" onclick="document.getElementById('previewModal').classList.add('hidden')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl max-h-[90vh] overflow-auto shadow-2xl">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300" id="previewFileName"></p>
                <button onclick="document.getElementById('previewModal').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-700 dark:hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <img src="" alt="Preview" id="previewImage" class="max-h-[70vh] w-auto mx-auto p-4">
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-2">
                <button onclick="copyUrlFromPreview()" class="px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Copy URL</button>
                <button onclick="document.getElementById('previewModal').classList.add('hidden')" class="px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPreviewUrl = '';

function previewImage(url, filename) {
    currentPreviewUrl = url;
    document.getElementById('previewImage').src = url;
    document.getElementById('previewFileName').textContent = filename;
    document.getElementById('previewModal').classList.remove('hidden');
}

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        toast('URL copied to clipboard');
    });
}

function copyUrlFromPreview() {
    copyUrl(currentPreviewUrl);
    document.getElementById('previewModal').classList.add('hidden');
}

function openRenameModal(path, filename) {
    document.getElementById('renameCurrentName').textContent = filename;
    document.getElementById('renameNewName').value = filename;
    document.getElementById('renameOldPath').value = path;
    document.getElementById('renameModal').classList.remove('hidden');
}

function closeRenameModal() {
    document.getElementById('renameModal').classList.add('hidden');
}

function filterMedia(query) {
    query = query.toLowerCase();
    document.querySelectorAll('.media-item').forEach(function(el) {
        var name = el.getAttribute('data-filename') || '';
        el.style.display = name.includes(query) ? '' : 'none';
    });
}

function previewUpload(input) {
    const files = input.files;
    if (!files || !files.length) return;
    const list = document.getElementById('previewList');
    const count = document.getElementById('uploadFileCount');
    list.innerHTML = '';
    for (let i = 0; i < files.length; i++) {
        (function(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600';
                div.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                list.appendChild(div);
            };
            reader.readAsDataURL(file);
        })(files[i]);
    }
    count.textContent = files.length + ' file(s) selected';
    document.getElementById('uploadPreview').classList.remove('hidden');
    document.getElementById('uploadBtn').disabled = false;
}

document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const files = document.getElementById('fileInput').files;
    if (!files || !files.length) return;

    const btn = document.getElementById('uploadBtn');
    const progress = document.getElementById('uploadProgress');
    const bar = document.getElementById('progressBar');
    const grid = document.getElementById('mediaGrid');

    btn.disabled = true;
    progress.classList.remove('hidden');

    var uploaded = 0;
    var total = files.length;
    var failed = 0;

    function uploadNext(index) {
        if (index >= total) {
            btn.textContent = 'Upload';
            btn.disabled = false;
            var msg = uploaded + ' file(s) uploaded';
            if (failed > 0) msg += ', ' + failed + ' failed';
            toast(msg, failed > 0 && uploaded === 0 ? 'error' : 'success');
            document.getElementById('fileInput').value = '';
            document.getElementById('uploadPreview').classList.add('hidden');
            progress.classList.add('hidden');
            setTimeout(function() { location.reload(); }, 1500);
            return;
        }

        bar.style.width = '0%';
        btn.textContent = 'Uploading ' + (index + 1) + ' of ' + total + '...';

        var fd = new FormData();
        fd.append('file', files[index]);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("admin.media.upload") }}');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                bar.style.width = (e.loaded / e.total * 100) + '%';
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                uploaded++;
                try {
                    var resp = JSON.parse(xhr.responseText);
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
});

const dropZone = document.getElementById('dropZone');
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
    const files = e.dataTransfer.files;
    if (files && files.length) {
        document.getElementById('fileInput').files = files;
        previewUpload(document.getElementById('fileInput'));
    }
});

function toast(msg, type) {
    const t = document.getElementById('toast') || (() => {
        const el = document.createElement('div');
        el.id = 'toast';
        el.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:12px;font-size:12px;font-weight:600;color:#fff;max-width:320px;box-shadow:0 8px 32px rgba(0,0,0,0.15);transition:all 0.3s;transform:translateY(20px);opacity:0';
        document.body.appendChild(el);
        return el;
    })();
    t.style.background = type === 'error' ? '#e53e3e' : '#1756c8';
    t.textContent = msg;
    t.style.transform = 'translateY(0)';
    t.style.opacity = '1';
    setTimeout(() => { t.style.transform = 'translateY(20px)'; t.style.opacity = '0'; }, 3000);
}
</script>
@endpush
</x-app-layout>