{{-- to see preview of edit uploaded image --}}

<script>
    const imageInput = document.querySelector('.vendor-image-input');
    const imagePreview = document.querySelector('.vendor-image-preview');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function () {
            const selectedFile = imageInput.files[0];

            if (selectedFile) {
                imagePreview.src = URL.createObjectURL(selectedFile);
                imagePreview.hidden = false;
            }
        });
    }
</script>
