<?php

namespace App\Services;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use RuntimeException;

class VideoProcessingService
{
    public function generateThumbnail(
        string $videoPath,
        string $disk = 'public',
        int $second = 5
    ): string {

        $storage = Storage::disk($disk);

        // Get the absolute location of the original video.
        $inputPath = $storage->path($videoPath);

        if (! file_exists($inputPath)) {
            throw new RuntimeException('Video file does not exist.');
        }

        if ($second < 0) {
            throw new RuntimeException('Invalid extraction timestamp.');
        }

        // Prepare the thumbnail directory.
        $directory = 'videos/thumbnails';

        $storage->makeDirectory($directory);

        // Give the thumbnail a predictable filename.
        $filename = pathinfo($videoPath, PATHINFO_FILENAME)
            . '_thumbnail.jpg';

        $thumbnailPath = $directory . '/' . $filename;

        $outputPath = $storage->path($thumbnailPath);

        // Build the FFmpeg command safely.
        $process = new Process([
            config('media.ffmpeg_path'),

            '-hide_banner',
            '-loglevel', 'error',
            '-y',

            '-ss', (string) $second,
            '-i', $inputPath,

            '-frames:v', '1',

            '-vf', 'scale=' . config('media.thumbnail_width') . ':-2',

            '-q:v', (string) config('media.thumbnail_quality'),

            '-update', '1',

            $outputPath,
        ]);

        $process->setTimeout(
            config('media.processing_timeout')
        );

        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(
                'Thumbnail generation failed: '
                . $process->getErrorOutput()
            );
        }

        if (! file_exists($outputPath) || filesize($outputPath) === 0) {
            throw new RuntimeException(
                'FFmpeg did not produce a valid thumbnail.'
            );
        }

        // Return the storage-relative path.
        return $thumbnailPath;
    }


public function compress(
    string $videoPath,
    string $disk = 'public'
): string {

    $storage = Storage::disk($disk);

    // Locate original video.
    $inputPath = $storage->path($videoPath);

    if (! file_exists($inputPath)) {
        throw new RuntimeException('Video file does not exist.');
    }

    // Create output directory.
    $directory = 'videos/processed';

    $storage->makeDirectory($directory);

    // Generate a unique output filename.
    $filename = Str::uuid() . '_compressed.mp4';

    $outputRelativePath = $directory . '/' . $filename;

    $outputPath = $storage->path($outputRelativePath);

    // FFmpeg encoding settings.
    $process = new Process([
        config('media.ffmpeg_path'),

        '-hide_banner',
        '-loglevel', 'error',
        '-y',

        '-i', $inputPath,

        // Select the first video stream and optional audio.
        '-map', '0:v:0',
        '-map', '0:a?',

        // Video encoding.
        '-c:v', 'libx264',
        '-preset', 'medium',
        '-crf', '28',
        '-pix_fmt', 'yuv420p',

        // Audio encoding.
        '-c:a', 'aac',
        '-b:a', '96k',

        // Improve progressive MP4 playback.
        '-movflags', '+faststart',

        $outputPath,
    ]);

    $process->setTimeout(1800);

    $process->run();

    if (! $process->isSuccessful()) {

        // Remove incomplete output if FFmpeg failed.
        if (file_exists($outputPath)) {
            unlink($outputPath);
        }

        throw new RuntimeException(
            'Video compression failed: '
            . $process->getErrorOutput()
        );
    }

    if (! file_exists($outputPath) || filesize($outputPath) === 0) {
        throw new RuntimeException(
            'Compressed video was not generated correctly.'
        );
    }

    return $outputRelativePath;
}

}
