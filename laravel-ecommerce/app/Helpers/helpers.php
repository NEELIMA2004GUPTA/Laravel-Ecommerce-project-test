<?php
    function isImageFile($file) {
        return preg_match('/\.(jpg|jpeg|png|webp|jfif)$/i', $file) === 1;
    }
    function isVideoFile($file) {
        return preg_match('/\.(mp4|webm|ogg)$/i', $file) === 1;
    }
