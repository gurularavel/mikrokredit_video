<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MerchantBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');

        if (!str_starts_with($header, 'Basic ')) {
            return $this->unauthorized();
        }

        $decoded = base64_decode(substr($header, 6));
        $parts   = explode(':', $decoded, 2);

        if (count($parts) !== 2) {
            return $this->unauthorized();
        }

        [$login, $authKey] = $parts;

        $merchant = Merchant::where('login', $login)->first();

        if (!$merchant || !hash_equals($merchant->auth_key, $authKey)) {
            return $this->unauthorized();
        }

        $request->attributes->set('merchant', $merchant);

        return $next($request);
    }

    private function unauthorized(): Response
    {
        return response()->json(['success' => 0, 'message' => 'Unauthorized'], 401);
    }
}
