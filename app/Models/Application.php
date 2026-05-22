<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Application extends Model
{
    protected $fillable = [
        'merchant_id',
        'app_id',
        'name',
        'surname',
        'phone',
        'amount',
        'webhook_url',
        'merchant_redirect_url',
        'lang',
        'city',
        'address',
        'salary',
        'token',
        'access_token',
        'token_expires_at',
        'status',
        'video_path',
        'video_disk',
        'video_size',
        'video_recorded_at',
        'sms_sent_at',
        'link_opened_at',
    ];

    protected $casts = [
        'token_expires_at'  => 'datetime',
        'video_recorded_at' => 'datetime',
        'sms_sent_at'       => 'datetime',
        'link_opened_at'    => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function scopeByPhone(Builder $query, string $phone): Builder
    {
        return $query->where('phone', 'like', '%' . $phone . '%');
    }

    public function scopeByDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeRecorded(Builder $query): Builder
    {
        return $query->where('status', 'recorded');
    }

    public function isTokenExpired(): bool
    {
        if ($this->token_expires_at === null) {
            return false;
        }
        return $this->token_expires_at->isPast();
    }

    public function isTokenUsed(): bool
    {
        return $this->video_path !== null;
    }

    public function videoUrl(): ?string
    {
        if (!$this->video_path) {
            return null;
        }
        return route('admin.videos.stream', $this->id);
    }

    public function publicStreamUrl(): ?string
    {
        if (!$this->video_path || !$this->app_id) {
            return null;
        }
        return route('video.stream', $this->app_id);
    }
}
