<?php

namespace App\Http\Controllers;

use App\Jobs\CompressVideo;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class RecordController extends Controller
{
    public function show(string $token)
    {
        $application = Application::where('token', $token)->first();

        if (! $application) {
            return view('public.error', ['reason' => 'not_found']);
        }

        if ($application->isTokenExpired()) {
            return view('public.error', ['reason' => 'expired']);
        }

        if ($application->isTokenUsed()) {
            return view('public.error', ['reason' => 'used']);
        }

        if (! $application->link_opened_at) {
            $application->update(['link_opened_at' => now()]);
        }

        return view('public.record', [
            'application' => $application,
            'duration' => config('video.duration', 15),
        ]);
    }

    public function upload(Request $request, string $token)
    {
        $logCtx = ['token' => substr($token, 0, 12).'...'];

        Log::info('UPLOAD_LOG: Upload başladı', array_merge($logCtx, [
            'ip' => $request->ip(),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
            'has_file' => $request->hasFile('video'),
            'files' => array_keys($request->allFiles()),
        ]));

        $application = Application::where('token', $token)->first();

        // Token xətaları qalıcıdır — retry heç vaxt uğurlu olmayacaq.
        // Ona görə 422 (retry oluna bilər) DEYİL, non-retryable statuslar qaytarırıq.
        if (! $application) {
            Log::warning('UPLOAD_LOG: Token tapılmadı', $logCtx);

            return response()->json([
                'success' => false,
                'retryable' => false,
                'reason' => 'not_found',
                'message' => 'Link etibarsızdır.',
            ], 404);
        }

        if ($application->isTokenExpired()) {
            Log::warning('UPLOAD_LOG: Token vaxtı bitib', $logCtx);

            return response()->json([
                'success' => false,
                'retryable' => false,
                'reason' => 'expired',
                'message' => 'Linkin vaxtı bitib.',
            ], 410);
        }

        if ($application->isTokenUsed()) {
            Log::warning('UPLOAD_LOG: Token artıq istifadə edilib', $logCtx);

            return response()->json([
                'success' => false,
                'retryable' => false,
                'reason' => 'used',
                'message' => 'Bu link artıq istifadə olunub.',
            ], 409);
        }

        $maxKb = config('video.max_size_kb', 51200);
        $minKb = (int) config('video.min_size_kb', 30);

        Log::info('UPLOAD_LOG: Validation başladı', array_merge($logCtx, [
            'max_kb' => $maxKb,
            'min_kb' => $minKb,
            'file_size' => $request->hasFile('video') ? $request->file('video')->getSize() : null,
            'file_error' => $request->hasFile('video') ? $request->file('video')->getError() : null,
            'mime_type' => $request->input('mime_type'),
        ]));

        try {
            $request->validate([
                'video' => ['required', 'file', 'min:'.$minKb, 'max:'.$maxKb],
                'mime_type' => ['required', 'string'],
            ], [
                'video.min' => 'Video faylı çox kiçikdir. Zəhmət olmasa yenidən çəkin.',
            ]);
        } catch (ValidationException $e) {
            Log::error('UPLOAD_LOG: Validation xətası', array_merge($logCtx, [
                'errors' => $e->errors(),
            ]));
            throw $e;
        }

        $disk = config('video.disk', 'public');
        $dir = 'videos/'.now()->format('Y/m');
        $ext = str_contains($request->mime_type, 'mp4') ? 'mp4' : 'webm';
        $filename = $application->id.'_'.now()->format('His').'.'.$ext;
        $path = $dir.'/'.$filename;

        Log::info('UPLOAD_LOG: Saxlanılır', array_merge($logCtx, [
            'disk' => $disk,
            'path' => $path,
            'size' => $request->file('video')->getSize(),
        ]));

        try {
            Storage::disk($disk)->putFileAs($dir, $request->file('video'), $filename);
        } catch (\Throwable $e) {
            Log::error('UPLOAD_LOG: Storage xətası', array_merge($logCtx, [
                'error' => $e->getMessage(),
            ]));

            return response()->json(['success' => false, 'message' => 'Fayl saxlanılmadı: '.$e->getMessage()], 500);
        }

        $application->update([
            'video_path' => $path,
            'video_disk' => $disk,
            'video_size' => $request->file('video')->getSize(),
            'video_recorded_at' => now(),
            'status' => 'recorded',
        ]);

        CompressVideo::dispatch($application);

        Log::info('UPLOAD_LOG: Uğurlu', array_merge($logCtx, ['path' => $path]));

        return response()->json([
            'success' => true,
            'redirect' => route('record.complete', $token),
        ]);
    }

    public function complete(string $token)
    {
        $application = Application::where('token', $token)->first();

        if (! $application) {
            return view('public.error', ['reason' => 'not_found']);
        }

        return view('public.complete', ['application' => $application]);
    }

    public function showVideo(string $appId)
    {
        $application = Application::where('app_id', $appId)->latest()->first();

        if (! $application || ! $application->video_path) {
            abort(404);
        }

        return view('public.video', ['application' => $application]);
    }

    public function streamVideo(string $appId)
    {
        $application = Application::where('app_id', $appId)->latest()->first();

        if (! $application || ! $application->video_path) {
            abort(404);
        }

        $disk = $application->video_disk ?: 'local';
        $fullPath = Storage::disk($disk)->path($application->video_path);

        if (! file_exists($fullPath)) {
            abort(404);
        }

        $mime = str_ends_with($application->video_path, '.mp4') ? 'video/mp4' : 'video/webm';

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'no-store, private',
        ]);
    }
}
