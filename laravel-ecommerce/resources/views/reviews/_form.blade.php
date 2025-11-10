<div class="max-w-3xl mx-auto py-6">

    <h2 class="text-2xl font-bold mb-4">Add Review</h2>

    <a href="{{ route('product.show', $product->slug) }}" class="inline-block mb-4 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
        ← Back to Product
    </a>

    <form class="review-form" method="POST" action="{{ route('products.reviews.store', $product) }}" enctype="multipart/form-data">
        @csrf

        <!-- Rating -->
        <div class="mb-3">
            <label class="block font-medium">Rate this product</label>
            <div class="star-rating flex gap-1">
            @for ($i=1; $i<=5; $i++)
                <button type="button" data-value="{{ $i }}" class="star-btn">
                    <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
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
            <input type="hidden" name="rating" class="rating-input" required>
            <p class="text-sm text-red-600 mt-1 rating-error hidden"></p>
        </div>

        <!-- Comment -->
        <div class="mb-3">
            <label class="block font-medium">Comment</label>
            <textarea name="comment" rows="4" class="w-full border rounded p-2 comment-input">{{ old('comment') }}</textarea>
            <p class="text-sm text-red-600 mt-1 comment-error hidden"></p>
        </div>

        <!-- Upload Images/Videos -->
        <div class="mb-3">
            <label class="block font-medium">Upload Images / Videos (Image - 1MB each & Video - 25 MB each)</label>
            <div class="drop-area mt-2 border-2 border-dashed border-gray-400 rounded p-4 text-center cursor-pointer hover:border-gray-600">
                Drag & Drop files here or click to select
            </div>
            <input type="file" name="images[]" accept="image/*,video/*" multiple class="file-input hidden">
            <p class="text-sm text-red-600 mt-1 media-error hidden"></p>
            <div class="file-preview mt-2 flex flex-wrap gap-2"></div>
        </div>

        <!-- Record Video -->
        <div class="mb-3">
            <label class="block font-medium">Record a video review</label>
            <div class="recorder mt-2">
                <video class="preview w-64 h-40 bg-black" autoplay muted></video>
                <div class="mt-2 flex gap-2">
                    <button type="button" class="start-rec px-3 py-1 bg-green-600 text-white rounded">Start</button>
                    <button type="button" class="stop-rec px-3 py-1 bg-red-600 text-white rounded" disabled>Stop</button>
                    <button type="button" class="upload-rec px-3 py-1 bg-blue-600 text-white rounded" disabled>Upload</button>
                </div>
                <input type="file" name="video" class="video-input hidden" />
                <p class="text-sm text-gray-600 mt-2 rec-status"></p>
            </div>
        </div>

        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Submit Review</button>
    </form>
</div>

<style>
.hidden { display: none; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function initReviewForm(form) {
        const stars = form.querySelectorAll('.star-btn');
        const ratingInput = form.querySelector('.rating-input');
        const ratingError = form.querySelector('.rating-error');

        stars.forEach(btn => {
            btn.addEventListener('click', () => {
                ratingInput.value = btn.dataset.value;
                stars.forEach(s => {
                    const svg = s.querySelector('svg');
                    svg.classList.remove('text-yellow-400');
                    svg.classList.add('text-gray-300');
                    if (parseInt(s.dataset.value) <= parseInt(btn.dataset.value)) {
                        svg.classList.add('text-yellow-400');
                        svg.classList.remove('text-gray-300');
                    }
                });
                ratingError.classList.add('hidden');
            });
        });

        const commentInput = form.querySelector('.comment-input');
        const commentError = form.querySelector('.comment-error');

        commentInput.addEventListener('input', () => {
            const val = commentInput.value.trim();
            if(!val){ commentError.textContent='Comment is required'; commentError.classList.remove('hidden'); }
            else if(val.length>1000){ commentError.textContent='Comment cannot exceed 1000 characters'; commentError.classList.remove('hidden'); }
            else commentError.classList.add('hidden');
        });

        // File upload
        const dropArea = form.querySelector('.drop-area');
        const fileInput = form.querySelector('.file-input');
        const filePreview = form.querySelector('.file-preview');
        const mediaError = form.querySelector('.media-error');
        let allFiles = [];

        function addFiles(files){
            mediaError.classList.add('hidden');
            const maxFiles = 5;
            const maxImageSize = 1*1024*1024;
            const maxVideoSize = 25*1024*1024;
            let errors = [];

            Array.from(files).forEach(file=>{
                if(file.type.startsWith('image/')){
                    if(file.size>maxImageSize){ errors.push(`${file.name} exceeds 1MB`); return; }
                    allFiles.push(file);
                } else if(file.type.startsWith('video/')){
                    if(file.size>maxVideoSize){ errors.push(`${file.name} exceeds 25MB`); return; }
                    allFiles.push(file);
                } else { errors.push(`${file.name} is not a valid image/video`); }
            });

            if(allFiles.length>maxFiles){ errors.push(`You can upload a maximum of ${maxFiles} files.`); allFiles = allFiles.slice(0,maxFiles); }

            if(errors.length){ mediaError.textContent=errors.join(', '); mediaError.classList.remove('hidden'); }

            renderPreviews();

            const dt = new DataTransfer();
            allFiles.forEach(f=>dt.items.add(f));
            fileInput.files = dt.files;
        }

        function renderPreviews(){
            filePreview.innerHTML='';
            allFiles.forEach((file,index)=>{
                const div = document.createElement('div');
                div.classList.add('relative');

                if(file.type.startsWith('image/')){
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.classList.add('w-24','h-24','object-cover','rounded');
                    div.appendChild(img);
                } else if(file.type.startsWith('video/')){
                    const video = document.createElement('video');
                    video.src = URL.createObjectURL(file);
                    video.controls=true;
                    video.classList.add('w-32','h-24','rounded');
                    div.appendChild(video);
                }

                const removeBtn = document.createElement('button');
                removeBtn.type='button';
                removeBtn.textContent='x';
                removeBtn.classList.add('absolute','top-0','right-0','bg-red-500','text-white','rounded-full','w-5','h-5','flex','items-center','justify-center','text-sm','cursor-pointer');
                removeBtn.addEventListener('click',()=>{
                    allFiles.splice(index,1);
                    renderPreviews();
                    const dt = new DataTransfer();
                    allFiles.forEach(f=>dt.items.add(f));
                    fileInput.files = dt.files;
                });
                div.appendChild(removeBtn);
                filePreview.appendChild(div);
            });
        }

        dropArea.addEventListener('click', ()=>fileInput.click());
        fileInput.addEventListener('change', ()=>addFiles(fileInput.files));
        dropArea.addEventListener('dragover', e=>{ e.preventDefault(); dropArea.classList.add('border-gray-600','bg-gray-50'); });
        dropArea.addEventListener('dragleave', e=>{ e.preventDefault(); dropArea.classList.remove('border-gray-600','bg-gray-50'); });
        dropArea.addEventListener('drop', e=>{ e.preventDefault(); dropArea.classList.remove('border-gray-600','bg-gray-50'); addFiles(e.dataTransfer.files); });

        // Form submit validation
        form.addEventListener('submit', e=>{
            let hasError=false;
            if(!ratingInput.value){ ratingError.textContent='Please select a rating'; ratingError.classList.remove('hidden'); hasError=true; }
            const val = commentInput.value.trim();
            if(!val){ commentError.textContent='Comment is required'; commentError.classList.remove('hidden'); hasError=true; }
            else if(val.length>1000){ commentError.textContent='Comment cannot exceed 1000 characters'; commentError.classList.remove('hidden'); hasError=true; }
            if(hasError) e.preventDefault();
        });

        // Video Recorder
        const previewVideo = form.querySelector('.preview');
        const startBtn = form.querySelector('.start-rec');
        const stopBtn = form.querySelector('.stop-rec');
        const uploadBtn = form.querySelector('.upload-rec');
        const recStatus = form.querySelector('.rec-status');
        const videoInput = form.querySelector('.video-input');
        let mediaRecorder, recordedBlobs, localStream;

        async function initMedia() {
            try { localStream = await navigator.mediaDevices.getUserMedia({video:true,audio:true}); previewVideo.srcObject = localStream; }
            catch(e){ recStatus.textContent='Camera/mic access denied.'; }
        }

        startBtn.addEventListener('click', async ()=>{
            await initMedia();
            recordedBlobs=[];
            const options={ mimeType:'video/webm;codecs=vp8,opus' };
            try { mediaRecorder=new MediaRecorder(localStream,options); }
            catch(e){ recStatus.textContent='Recorder not supported.'; return; }
            mediaRecorder.ondataavailable = e => { if(e.data && e.data.size) recordedBlobs.push(e.data); };
            mediaRecorder.start(1000);
            startBtn.disabled=true; stopBtn.disabled=false; uploadBtn.disabled=true;
            recStatus.textContent='Recording...';
        });

        stopBtn.addEventListener('click', ()=>{
            mediaRecorder.stop(); localStream.getTracks().forEach(t=>t.stop());
            startBtn.disabled=false; stopBtn.disabled=true; uploadBtn.disabled=false;
            recStatus.textContent='Recording stopped.';
            const blob = new Blob(recordedBlobs, {type:recordedBlobs[0]?.type||'video/webm'});
            const file = new File([blob],`review_${Date.now()}.webm`,{type:blob.type});
            const dt = new DataTransfer(); dt.items.add(file); videoInput.files=dt.files;
        });

        uploadBtn.addEventListener('click', async ()=>{
            if(!videoInput.files.length) return alert('No video recorded');
            const file = videoInput.files[0];
            const fd = new FormData(); fd.append('_token','{{ csrf_token() }}'); fd.append('video',file);
            recStatus.textContent='Uploading video...';
            const res = await fetch('{{ route("products.reviews.uploadVideo",$product) }}',{method:'POST',body:fd});
            const data = await res.json();
            recStatus.textContent = data.success ? 'Video uploaded successfully' : 'Upload failed';
            if(data.success) uploadBtn.disabled=true;
        });
    }

    // Initialize all review forms
    document.querySelectorAll('.review-form').forEach(form => initReviewForm(form));
});
</script>
