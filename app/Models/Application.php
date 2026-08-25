<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Application extends Model
{
    /** Video mətn şablonu tipləri — açar `m_type` sütununun dəyəridir. */
    public const M_TYPES = [
        1 => 'Tip 1 — standart mətn',
        2 => 'Tip 2 — alternativ mətn',
    ];

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
        'm_type',
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
        'admin_notes',
    ];

    protected $casts = [
        'm_type'            => 'integer',
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

    /**
     * Bu müraciət üçün video çəkiliş mətninin şablon açarları —
     * ən spesifikdən ümumiyə doğru (tip 2 boşdursa, tip 1-ə düşür).
     *
     * @return list<string>
     */
    public function recordScriptKeys(): array
    {
        $type = (int) ($this->m_type ?: 1);

        return $type > 1
            ? ['page_record_script_' . $type, 'page_record_script']
            : ['page_record_script'];
    }

    /**
     * Mesaj şablonlarında istifadə olunan dəyişənlər.
     *
     * @return array<string, string>
     */
    public function templateVars(): array
    {
        return [
            'ad'       => (string) $this->name,
            'soyad'    => (string) $this->surname,
            'ad_soyad' => trim($this->name . ' ' . $this->surname),
            'telefon'  => (string) $this->phone,
            'mebleg'   => $this->formattedAmount(),
        ];
    }

    /** Məbləği "1 500" / "1 500.50" formatında qaytarır; məbləğ yoxdursa boş sətir. */
    public function formattedAmount(): string
    {
        if (!$this->amount) {
            return '';
        }

        $decimals = fmod((float) $this->amount, 1) == 0 ? 0 : 2;

        return number_format((float) $this->amount, $decimals, '.', ' ') . ' AZN';
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
