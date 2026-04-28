<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendApplicationSms;
use App\Models\Application;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'app_id'       => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'amount'       => 'required|numeric|min:0',
            'webhook_url'  => 'required|url',
            'redirect_url' => 'required|url',
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
            'webhook_url'          => $validated['webhook_url'],
            'merchant_redirect_url' => $validated['redirect_url'],
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

        return response()->json([
            'success'     => 1,
            'message'     => 'Order created successfully',
            'redirect_url' => route('record.show', $videoToken),
            'token'       => $accessToken,
            'expires_at'  => $expiresAt->toIso8601ZuluString(),
            'upload_url'  => url('/api/upload/video'),
        ]);
    }
}
