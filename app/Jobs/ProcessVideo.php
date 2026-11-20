<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\VideoProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

    public $timeout = 1800;

    public function __construct(
        public int $videoId
    ) {}

    public function handle(VideoProcessingService $service): void
    {
        $video = Video::find($this->videoId);

        if (! $video) {
            return;
        }

        if ($video->processing_status === 'ready') {
            return;
        }

        $video->update([
            'processing_status' => 'processing',
            'processing_error' => null,
        ]);

        $compressedPath = null;
        $thumbnailPath = null;

        try {
            // Step 1: Compress the original video.
            $compressedPath = $service->compress(
                $video->original_video_path
            );

            // Step 2: Generate thumbnail from compressed video.
            $thumbnailPath = $service->generateThumbnail(
                $compressedPath
            );

            // Step 3: Publish the processed paths.
            $video->update([
                'video_path' => $compressedPath,
                'thumbnail_path' => $thumbnailPath,
                'processing_status' => 'ready',
                'processing_error' => null,
            ]);

        } catch (Throwable $exception) {

            // Clean up incomplete outputs before a retry.
            Storage::disk('public')->delete(
                array_filter([$compressedPath, $thumbnailPath])
            );

            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        Video::whereKey($this->videoId)->update([
            'processing_status' => 'failed',
            'processing_error' => $exception->getMessage(),
        ]);
    }
}