<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
require_once __DIR__ . '/../../app/Helpers/helpers.php';

class HelpersTest extends TestCase
{
    #[Test]
    public function it_identifies_image_files_correctly()
    {
        $this->assertTrue(isImageFile('photo.jpg'));
        $this->assertTrue(isImageFile('image.jpeg'));
        $this->assertTrue(isImageFile('picture.png'));
        $this->assertTrue(isImageFile('wallpaper.webp'));
        $this->assertTrue(isImageFile('scan.jfif'));

        // Should return false for non-image files
        $this->assertFalse(isImageFile('video.mp4'));
        $this->assertFalse(isImageFile('document.pdf'));
        $this->assertFalse(isImageFile('archive.zip'));
    }

    #[Test]
    public function it_identifies_video_files_correctly()
    {
        $this->assertTrue(isVideoFile('movie.mp4'));
        $this->assertTrue(isVideoFile('clip.webm'));
        $this->assertTrue(isVideoFile('animation.ogg'));

        // Should return false for non-video files
        $this->assertFalse(isVideoFile('photo.jpg'));
        $this->assertFalse(isVideoFile('image.png'));
        $this->assertFalse(isVideoFile('document.pdf'));
    }

    #[Test]
    public function it_is_case_insensitive()
    {
        $this->assertTrue(isImageFile('PHOTO.JPG'));
        $this->assertTrue(isVideoFile('VIDEO.MP4'));
    }

    #[Test]
    public function it_returns_zero_for_files_without_extension()
    {
        $this->assertFalse(isImageFile('file_without_ext'));
        $this->assertFalse(isVideoFile('file_without_ext'));
    }
}

