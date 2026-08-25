# SMS Sistemi Tətbiq Bələdçisi (Laravel)

Bu sənəd mövcud proyektdəki SMS sistemini **yeni bir Laravel proyektinə** tam şəkildə qurmaq üçün hazırlanmışdır.  
Sistem iki provider dəstəkləyir: **L.Sim** (əsas) və **Posta Güvercini** (ehtiyat). Queue (növbə) əsaslıdır.

---

## Ümumi Arxitektura

```
API isteği gəlir
    ↓
OrderController::store()
    ↓
SendSmsJob::dispatch([...])   ← queue-ya atılır
    ↓
Queue Worker işləyir
    ↓
SMSService::send()  →  L.Sim API
    ↓ (əgər L.Sim uğursuz olarsa)
PostaGuverciniSMSService::send()  →  Posta Güvercini API
    ↓
SmsLog::create([...])   ← nəticəni DB-yə yazır
```

---

## 1. Config Faylı

**`config/sms.php`** faylı yarat:

```php
<?php

return [
    // Aktiv provider: 'lsim' və ya 'postaGuvercini'
    'PROVIDER' => env('SMS_PROVIDER', 'lsim'),

    // L.Sim
    'API_URL'      => env('LSIM_API_URL', 'https://apps.lsim.az/quicksms/v1/send'),
    'API_KEY'      => env('LSIM_API_KEY', ''),
    'API_USERNAME' => env('LSIM_API_USERNAME', ''),
    'SENDER'       => env('LSIM_SENDER', 'YourBrand'),

    // Posta Güvercini
    'PG_API_URL'    => env('PG_API_URL', ''),
    'PG_PUBLIC_KEY' => env('PG_PUBLIC_KEY', ''),
    'PG_PRIVATE_KEY'=> env('PG_PRIVATE_KEY', ''),
    'PG_SENDER'     => env('PG_SENDER', 'YourBrand'),
];
```

**`.env`** faylına əlavə et:

```env
SMS_PROVIDER=lsim

LSIM_API_URL=https://apps.lsim.az/quicksms/v1/send
LSIM_API_KEY=your_lsim_api_key
LSIM_API_USERNAME=your_lsim_username
LSIM_SENDER=YourBrand

# Posta Güvercini (isteğe bağlı, fallback üçün)
PG_API_URL=
PG_PUBLIC_KEY=
PG_PRIVATE_KEY=
PG_SENDER=YourBrand

QUEUE_CONNECTION=database
```

---

## 2. Database Migration

**`database/migrations/xxxx_create_sms_logs_table.php`** faylı yarat:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_id')->index();   // və ya uuid('merchant_id') əgər UUID istifadə edirsənsə
            $table->string('session_id')->nullable();
            $table->string('phone');
            $table->text('message');
            $table->string('url')->nullable();
            $table->string('status')->default('sent'); // 'sent' və ya 'failed'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
```

```bash
php artisan migrate
```

---

## 3. SmsLog Modeli

**`app/Models/SmsLog.php`** faylı yarat:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'merchant_id',
        'session_id',
        'phone',
        'message',
        'url',
        'status',
    ];

    // Əgər proyektdə Merchant modeli varsa:
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
```

> **Qeyd:** Əgər proyektdə `merchant_id` konsepti yoxdursa, həmin sütunu `user_id` və ya başqa münasib sütunla əvəz et.

---

## 4. SMS Servisləri

### 4.1 L.Sim Servisi

**`app/Services/SMS/SMSService.php`** faylı yarat:

```php
<?php

namespace App\Services\SMS;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class SMSService
{
    protected Client $httpClient;
    protected string $apiUrl;
    protected string $apiKey;
    protected string $apiUsername;
    protected string $sender;

    public function __construct()
    {
        $this->httpClient  = new Client;
        $this->apiUrl      = config('sms.API_URL');
        $this->apiKey      = config('sms.API_KEY');
        $this->apiUsername = config('sms.API_USERNAME');
        $this->sender      = config('sms.SENDER');
    }

    public function send(string $phone, string $message): array
    {
        // Azərbaycan üçün 994 prefiksi — 9 rəqəmli nömrə üçün
        $phone = '994' . $phone;

        // L.Sim imza generasiyası
        $passwordHashed = md5($this->apiKey);
        $key = md5($passwordHashed . $this->apiUsername . $message . $phone . $this->sender);

        $queryParams = [
            'login'   => $this->apiUsername,
            'key'     => $key,
            'msisdn'  => $phone,
            'sender'  => $this->sender,
            'text'    => $message,
            'unicode' => 0,
        ];

        $url = $this->apiUrl . '?' . http_build_query($queryParams);
        $output = [];

        try {
            $response = $this->httpClient->get($url);
            $response = json_decode($response->getBody()->getContents(), true);

            if (!$response['errorCode']) {
                $output['success'] = 1;
                $output['status']  = 'sent';
            } else {
                $output['success'] = 0;
                $output['status']  = 'failed';
            }

            return $output;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return [
                    'success' => 0,
                    'status'  => 'failed',
                    'message' => $e->getResponse()->getReasonPhrase(),
                ];
            }

            return [
                'success' => 0,
                'status'  => 'failed',
                'message' => $e->getMessage(),
            ];
        }
    }
}
```

### 4.2 Posta Güvercini Servisi (ehtiyat)

**`app/Services/SMS/PostaGuverciniSMSService.php`** faylı yarat:

```php
<?php

namespace App\Services\SMS;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PostaGuverciniSMSService
{
    protected Client $httpClient;
    protected string $apiUrl;
    protected string $publicKey;
    protected string $privateKey;
    protected string $sender;

    public function __construct()
    {
        $this->httpClient = new Client;
        $this->apiUrl     = config('sms.PG_API_URL');
        $this->publicKey  = config('sms.PG_PUBLIC_KEY');
        $this->privateKey = config('sms.PG_PRIVATE_KEY');
        $this->sender     = config('sms.PG_SENDER');
    }

    public function send(string $phone, string $message): array
    {
        $phone = '994' . $phone;

        $url = rtrim($this->apiUrl, '/') . '/gateway/api/sms/v1/message/send'
            . '?publicKey=' . urlencode($this->publicKey);

        try {
            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->privateKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'Text'    => $message,
                    'Purpose' => 'INF',
                    'Options' => [
                        'Originator'  => $this->sender,
                        'SendTime'    => null,
                        'ExpireTime'  => null,
                        'Encoding'    => 'LATIN',
                        'SmsType'     => 'SMS',
                        'ReportLabel' => null,
                    ],
                    'Receivers' => [
                        ['Receiver' => $phone],
                    ],
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (($body['Status'] ?? null) === 200) {
                return ['success' => 1, 'status' => 'sent'];
            }

            return [
                'success' => 0,
                'status'  => 'failed',
                'message' => $body['Description'] ?? 'Unknown error',
            ];
        } catch (RequestException $e) {
            return [
                'success' => 0,
                'status'  => 'failed',
                'message' => $e->getMessage(),
            ];
        }
    }
}
```

---

## 5. Queue Job

**`app/Jobs/SendSmsJob.php`** faylı yarat:

```php
<?php

namespace App\Jobs;

use App\Models\SmsLog;
use App\Services\SMS\SMSService;
use App\Services\SMS\PostaGuverciniSMSService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public array $data;

    public $tries = 1;    // yalnız 1 cəhd
    public $timeout = 10; // 10 saniyə timeout

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Eyni session_id + phone üçün dublikat göndərilməsin.
     */
    public function uniqueId(): string
    {
        return $this->data['session_id'] . '_' . $this->data['phone'];
    }

    public function handle(): void
    {
        $provider = config('sms.PROVIDER', 'lsim');
        $phone    = $this->data['phone'];
        $message  = $this->data['message'];

        if ($provider === 'postaGuvercini') {
            $result = (new PostaGuverciniSMSService())->send($phone, $message);
        } else {
            $result = (new SMSService())->send($phone, $message);

            // L.Sim uğursuz olarsa və PG konfiqurasiyası varsa, fallback et
            if (($result['success'] ?? 0) === 0 && config('sms.PG_API_URL')) {
                Log::warning('L.Sim SMS failed, trying Posta Güvercini fallback', [
                    'phone'       => $phone,
                    'lsim_result' => $result,
                ]);
                $result = (new PostaGuverciniSMSService())->send($phone, $message);
                Log::info('Posta Güvercini response', ['phone' => $phone, 'result' => $result]);
            }
        }

        SmsLog::create([
            'merchant_id' => $this->data['merchant_id'] ?? null,
            'session_id'  => $this->data['session_id']  ?? null,
            'phone'       => $this->data['phone'],
            'message'     => $this->data['message'],
            'status'      => $result['status'] ?? 'failed',
            'url'         => $this->data['url'] ?? null,
        ]);
    }
}
```

---

## 6. Jobs Tablosu (queue üçün)

```bash
php artisan queue:table
php artisan migrate
```

---

## 7. SMS Göndərmə — İstifadə Nümunəsi

Controller-də və ya hər hansı yerdə belə istifadə et:

```php
use App\Jobs\SendSmsJob;

SendSmsJob::dispatch([
    'merchant_id' => $merchantId,         // nullable
    'session_id'  => $sessionId,          // dublikat filtri üçün
    'phone'       => '501234567',         // 9 rəqəm, prefikssiz
    'message'     => 'Videonu bu linkdən çəkin: https://example.com/record/...',
    'url'         => 'https://example.com/record/...',  // nullable
]);
```

> **Vacib:** `phone` rəqəmi **9 rəqəmli** (prefikssiz) göndər. Servis avtomatik `994` əlavə edir.

---

## 8. Queue Worker Başlatmaq

```bash
# İnkişaf mühiti üçün
php artisan queue:work

# Production (supervisor ilə)
php artisan queue:work --sleep=3 --tries=1 --timeout=60

# Failed jobs
php artisan queue:failed
php artisan queue:retry all
```

**Supervisor konfiqurasiyası** (`/etc/supervisor/conf.d/laravel-worker.conf`):

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work database --sleep=3 --tries=1 --timeout=60
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
```

---

## 9. Guvercini qur (isteğe bağlı)

Yalnız L.Sim API-nin uğursuz olduğu hallarda fallback lazımdırsa, `.env`-ə Posta Güvercini məlumatlarını əlavə et. Əgər `PG_API_URL` boş qalsa, fallback işləməyəcək.

---

## 10. Composer Asılılığı

Sistemi işlətmək üçün **Guzzle HTTP** lazımdır:

```bash
composer require guzzlehttp/guzzle
```

---

## Qısa Özet

| Fayl | Məqsəd |
|------|--------|
| `config/sms.php` | Provider seçimi, API məlumatları |
| `app/Services/SMS/SMSService.php` | L.Sim API inteqrasiyası |
| `app/Services/SMS/PostaGuverciniSMSService.php` | Posta Güvercini API inteqrasiyası |
| `app/Jobs/SendSmsJob.php` | Queue job, fallback məntiq, log yazmaq |
| `app/Models/SmsLog.php` | SMS log modeli |
| `database/migrations/..._create_sms_logs_table.php` | Verilənlər bazası cədvəli |
| `.env` | API açarları və queue konfiqurasiyası |

---

## L.Sim API İmza Məntiq

```
passwordHashed = md5(API_KEY)
key = md5(passwordHashed + API_USERNAME + message + phone + sender)
```

Bu imza hər SMS üçün unikal generasiya edilir. Təhlükəsizlik üçün `key` query param olaraq göndərilir.
