<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendApplicationSms;
use App\Models\Application;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $logDir = storage_path('logs/api-orders');
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            $logLine = sprintf(
                "[%s] IP: %s | Body: %s\n",
                now()->format('d.m.Y H:i:s'),
                $request->ip(),
                json_encode($request->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
            file_put_contents($logDir . '/log-' . now()->format('d.m.Y') . '.log', $logLine, FILE_APPEND | LOCK_EX);
        } catch (\Throwable) {
        }

        $validated = $request->validate([
            'app_id'       => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'amount'       => 'required|numeric|min:0',
            'webhook_url'  => 'nullable|url',
            'redirect_url' => 'nullable|url',
            'name'         => 'required|string|max:100',
            'lang'         => 'nullable|string|max:10',
            'city'         => 'nullable|string|max:100',
            'address'      => 'nullable|string|max:255',
            'salary'       => 'nullable|numeric|min:0',
        ]);

        /** @var Merchant $merchant */
        $merchant = $request->attributes->get('merchant');

        $ulid        = Str::ulid();
        $env         = app()->environment('production') ? 'live' : 'test';
        $videoToken  = "cs_{$env}_" . sha1(random_bytes(20)) . "_{$ulid}";
        $accessToken = "cs_{$env}_" . sha1(random_bytes(20)) . "_{$ulid}";

        $expiryMinutes = (int) config('sms.expiry_minutes', 60);
        $expiresAt     = now()->addMinutes($expiryMinutes);

        $application = Application::create([
            'merchant_id'          => $merchant->id,
            'app_id'               => $validated['app_id'],
            'name'                 => $validated['name'],
            'surname'              => '',
            'phone'                => $validated['phone'],
            'amount'               => $validated['amount'],
            'webhook_url'          => $validated['webhook_url'] ?? null,
            'merchant_redirect_url' => $validated['redirect_url'] ?? null,
            'lang'                 => $validated['lang'] ?? null,
            'city'                 => $validated['city'] ?? null,
            'address'              => $validated['address'] ?? null,
            'salary'               => $validated['salary'] ?? null,
            'token'                => $videoToken,
            'access_token'         => $accessToken,
            'token_expires_at'     => $expiresAt,
            'status'               => 'pending',
        ]);

        dispatch(new SendApplicationSms($application));

        $publicBase = rtrim(config('app.public_url'), '/');
        URL::forceRootUrl($publicBase);
        $toPublic = fn(string $url): string => $url;

        return response()->json([
            'success'      => 1,
            'message'      => 'Order created successfully',
            'redirect_url' => $toPublic(route('record.show', $videoToken)),
            // 'token'        => $accessToken,
            // 'expires_at'   => $expiresAt->toIso8601ZuluString(),
            // 'upload_url'   => $toPublic(url('/api/upload/video')),
            // 'show_link'    => $toPublic(route('video.show', $validated['app_id'])),
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function status(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'app_ids'   => 'required|array|min:1|max:500',
            'app_ids.*' => 'required|string|max:100',
        ]);

        /** @var \App\Models\Merchant $merchant */
        $merchant = $request->attributes->get('merchant');

        $recorded = Application::where('merchant_id', $merchant->id)
            ->whereIn('app_id', $validated['app_ids'])
            ->whereNotNull('video_path')
            ->pluck('app_id')
            ->unique()
            ->flip()
            ->all();

        $results = array_map(fn($id) => [
            'app_id'   => $id,
            'recorded' => isset($recorded[$id]),
        ], $validated['app_ids']);

        return response()->json(['results' => $results]);
    }
}
