<?php

namespace App\Jobs;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompressVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 300;

    public function __construct(private Application $application) {}

    public function handle(): void
    {
        if (!$this->application->video_path || !$this->application->video_disk) {
            return;
        }

        $ffmpeg = $this->findFfmpeg();
        if (!$ffmpeg) {
            Log::warning('CompressVideo: ffmpeg not found, skipping compression.');
            return;
        }

        $disk    = $this->application->video_disk;
        $path    = $this->application->video_path;
        $storage = Storage::disk($disk);

        if (!$storage->exists($path)) {
            return;
        }

        $inputPath  = $storage->path($path);
        $ext        = pathinfo($inputPath, PATHINFO_EXTENSION);
        $tempPath   = $inputPath . '.tmp.' . $ext;

        $isWebm = strtolower($ext) === 'webm';

        if ($isWebm) {
            $cmd = sprintf(
                '%s -y -i %s -vf "scale=\'min(640,iw)\':-2" -c:v libvpx-vp9 -crf 36-b:v 0 -c:a libopus -b:a 64k %s 2>&1',
                escapeshellcmd($ffmpeg),
                escapeshellarg($inputPath),
                escapeshellarg($tempPath)
            );
        } else {
            $cmd = sprintf(
                '%s -y -i %s -vf "scale=\'min(640,iw)\':-2" -c:v libx264 -crf 30 -preset fast -c:a aac -b:a 64k -movflags +faststart %s 2>&1',
                escapeshellcmd($ffmpeg),
                escapeshellarg($inputPath),
                escapeshellarg($tempPath)
            );
        }

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            Log::error('CompressVideo: ffmpeg failed.', ['output' => implode("\n", $output)]);
            @unlink($tempPath);
            return;
        }

        $newSize = filesize($tempPath);
        if ($newSize && $newSize < $this->application->video_size) {
            rename($tempPath, $inputPath);
            $this->application->update(['video_size' => $newSize]);
        } else {
            @unlink($tempPath);
        }
    }

    private function findFfmpeg(): ?string
    {
        $candidates = [
            'ffmpeg',
            '/usr/bin/ffmpeg',
            '/usr/local/bin/ffmpeg',
            'C:/ffmpeg/bin/ffmpeg.exe',
            'C:/Program Files/ffmpeg/bin/ffmpeg.exe',
        ];

        foreach ($candidates as $candidate) {
            exec(escapeshellcmd($candidate) . ' -version 2>&1', $out, $code);
            if ($code === 0) {
                return $candidate;
            }
        }

        return null;
    }
}
