<div class="max-w-3xl mx-auto py-6">

    <h2 class="text-2xl font-bold mb-4">Add Review </h2>

    <a href="{{ route('product.show', $product->slug) }}" class="inline-block mb-4 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
        ← Back to Product
    </a>

    <form id="reviewForm" method="POST" action="{{ route('products.reviews.store', $product) }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="block font-medium">Rate this product</label>
            <div id="starRating" class="flex gap-1">
            @for ($i=1; $i<=5; $i++)
                <button type="button" data-value="{{ $i }}" class="star-btn">
                    <svg class="w-6 h-6 text-gray-300"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 
                            1.902 0l1.286 3.945a1 1 0 
                            00.95.69h4.157c.969 0 1.371 
                            1.24.588 1.81l-3.368 2.449a1 1 0 
                            00-.364 1.118l1.286 3.945c.3.921-.755 
                            1.688-1.538 1.118l-3.368-2.449a1 1 0 
                            00-1.176 0l-3.368 2.449c-.783.57-1.838-.197-1.538-1.118
                            l1.286-3.945a1 1 0 00-.364-1.118L2.22 
                            9.372c-.783-.57-.38-1.81.588-1.81h4.157a1 1 0 
                        00.95-.69l1.286-3.945z" />
                    </svg>
                </button>
            @endfor
        </div>
            <input type="hidden" name="rating" id="ratingInput" required>
        </div>

        <div class="mb-3">
            <label class="block font-medium">Comment</label>
            <textarea name="comment" rows="4" class="w-full border rounded p-2">{{ old('comment') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="block font-medium">Upload Images / Videos (max 1MB each)</label>
            <input type="file" name="images[]" accept="image/*,video/*" multiple class="mt-2" />
        </div>

        <div class="mb-3">
            <label class="block font-medium">Record a video review</label>
            <div id="recorder" class="mt-2">
                <video id="preview" class="w-64 h-40 bg-black" autoplay muted></video>
                <div class="mt-2 flex gap-2">
                    <button id="startRec" type="button" class="px-3 py-1 bg-green-600 text-white rounded">Start</button>
                    <button id="stopRec" type="button" class="px-3 py-1 bg-red-600 text-white rounded" disabled>Stop</button>
                    <button id="uploadRec" type="button" class="px-3 py-1 bg-blue-600 text-white rounded" disabled>Upload</button>
                </div>
                <input type="file" id="videoFileInput" name="video" hidden />
                <p id="recStatus" class="text-sm text-gray-600 mt-2"></p>
            </div>
        </div>

        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Submit Review</button>
    </form>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Star rating
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('ratingInput');
    stars.forEach(btn => {
        btn.addEventListener('click', () => {
            const val = parseInt(btn.dataset.value);
            ratingInput.value = val;
            stars.forEach(s => {
                const svg = s.querySelector('svg');
                if (parseInt(s.dataset.value) <= val) {
                    svg.classList.remove('text-gray-300');
                    svg.classList.add('text-yellow-400');
                } else {
                    svg.classList.add('text-gray-300');
                    svg.classList.remove('text-yellow-400');
                }
            });
        });
    });

    const form = document.getElementById('reviewForm');
    const commentInput = form.querySelector('textarea[name="comment"]');
    const imageInput = form.querySelector('input[name="images[]"]');
    const videoInput = document.getElementById('videoFileInput');

    form.addEventListener('submit', function(e) {
        let errors = [];

        // Rating validation
        if (!ratingInput.value) {
            errors.push('Please select a rating.');
        }

        // Comment validation
        const comment = commentInput.value.trim();
        if (!comment) {
            errors.push('Comment is required.');
        } else if (comment.length > 1000) {
            errors.push('Comment cannot exceed 1000 characters.');
        }

        // Image validation
        if (imageInput.files.length > 0) {

            if (imageInput.files.length > 5) {
                errors.push('You can upload a maximum of 5 files (images + videos).');
            }

            Array.from(imageInput.files).forEach(file => {

            // Image Validation
            if (file.type.startsWith('image/')) {
                if (file.size > 1 * 1024 * 1024) { // 1MB
                    errors.push(`Image ${file.name} exceeds 1MB.`);
                }
            }

            // Video Validation
            else if (file.type.startsWith('video/')) {
                if (file.size > 25 * 1024 * 1024) { // 25MB
                    errors.push(`Video ${file.name} exceeds 25MB.`);
                }
            }

            // Any other file type
            else {
                errors.push(`File ${file.name} is not a valid image or video.`);
            }   
        });
        }

        // Video validation (if recorded)
        if (videoInput.files.length > 0) {
            const file = videoInput.files[0];
            if (!file.type.startsWith('video/')) {
                errors.push('Recorded video must be a video file.');
            } else if (file.size > 50 * 1024 * 1024) { // optional 50MB max
                errors.push('Video size cannot exceed 50MB.');
            }
        }

        if (errors.length) {
            e.preventDefault();
            alert(errors.join('\n'));
        }
    });

    // WebRTC + MediaRecorder
    const preview = document.getElementById('preview');
    const startBtn = document.getElementById('startRec');
    const stopBtn = document.getElementById('stopRec');
    const uploadBtn = document.getElementById('uploadRec');
    const recStatus = document.getElementById('recStatus');

    let mediaRecorder, recordedBlobs, localStream;

    async function initMedia() {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            preview.srcObject = localStream;
        } catch (err) {
            console.error('getUserMedia error', err);
            recStatus.textContent = 'Camera or microphone access denied.';
        }
    }

    startBtn.addEventListener('click', async () => {
        await initMedia();
        recordedBlobs = [];
        const options = { mimeType: 'video/webm;codecs=vp8,opus' };
        try {
            mediaRecorder = new MediaRecorder(localStream, options);
        } catch (e) {
            recStatus.textContent = 'Recorder not supported in this browser';
            return;
        }
        mediaRecorder.ondataavailable = (e) => { if (e.data && e.data.size) recordedBlobs.push(e.data); };
        mediaRecorder.start(1000);
        startBtn.disabled = true;
        stopBtn.disabled = false;
        uploadBtn.disabled = true;
        recStatus.textContent = 'Recording...';
    });

    stopBtn.addEventListener('click', () => {
        mediaRecorder.stop();
        localStream.getTracks().forEach(t => t.stop());
        startBtn.disabled = false;
        stopBtn.disabled = true;
        uploadBtn.disabled = false;
        recStatus.textContent = 'Recording stopped. You can upload or re-record.';
        // create a blob and set to hidden file input for normal form submit or upload via fetch
        const blob = new Blob(recordedBlobs, { type: recordedBlobs[0]?.type || 'video/webm' });
        const file = new File([blob], `review_${Date.now()}.webm`, { type: blob.type });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        videoInput.files = dataTransfer.files;
    });

    uploadBtn.addEventListener('click', async () => {
        if (!videoInput.files.length) return alert('No video recorded');
        const file = videoInput.files[0];

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('video', file);

        recStatus.textContent = 'Uploading video...';

        const res = await fetch('{{ route("products.reviews.uploadVideo", $product) }}', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();
        if (data.success) {
            recStatus.textContent = 'Video uploaded successfully (You can now submit review)';
            uploadBtn.disabled = true; // optional: prevent double upload
        } 
        else {
            recStatus.textContent = 'Upload failed';
        }
    });
});
</script>
