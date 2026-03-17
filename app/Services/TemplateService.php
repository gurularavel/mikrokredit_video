<?php

namespace App\Services;

use App\Models\MessageTemplate;
use Illuminate\Support\Facades\Cache;

class TemplateService
{
    /**
     * Render a template by key, replacing placeholders with given values.
     * Falls back to $default if the key is not found.
     */
    public static function render(string $key, array $vars = [], string $default = ''): string
    {
        $content = Cache::remember('template_' . $key, 600, function () use ($key) {
            return MessageTemplate::where('key', $key)->value('content');
        });

        if (!$content) {
            return $default;
        }

        foreach ($vars as $placeholder => $value) {
            $content = str_replace('{' . $placeholder . '}', $value, $content);
        }

        return $content;
    }

    /**
     * Bust the cache for a given key after update.
     */
    public static function forget(string $key): void
    {
        Cache::forget('template_' . $key);
    }
}
