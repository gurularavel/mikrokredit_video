<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateVideosToPrivate extends Command
{
    protected $signature = 'videos:migrate-to-private';
    protected $description = 'Move videos from public disk to local (private) disk';

    public function handle(): int
    {
        $applications = Application::whereNotNull('video_path')
            ->where('video_disk', 'public')
            ->get();

        if ($applications->isEmpty()) {
            $this->info('No videos on public disk. Nothing to migrate.');
            return 0;
        }

        $this->info("Found {$applications->count()} video(s) to migrate.");

        $moved = 0;
        $failed = 0;

        foreach ($applications as $app) {
            $sourcePath = $app->video_path;

            if (!Storage::disk('public')->exists($sourcePath)) {
                $this->warn("  [#{$app->id}] File not found on disk: {$sourcePath}");
                $failed++;
                continue;
            }

            $contents = Storage::disk('public')->get($sourcePath);
            Storage::disk('local')->put($sourcePath, $contents);

            if (!Storage::disk('local')->exists($sourcePath)) {
                $this->error("  [#{$app->id}] Failed to write to local disk.");
                $failed++;
                continue;
            }

            Storage::disk('public')->delete($sourcePath);
            $app->update(['video_disk' => 'local']);

            $this->line("  [#{$app->id}] Moved: {$sourcePath}");
            $moved++;
        }

        $this->info("Done. Moved: {$moved}, Failed: {$failed}");

        return $failed > 0 ? 1 : 0;
    }
}
