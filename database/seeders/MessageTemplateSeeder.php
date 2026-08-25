<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use App\Services\TemplateService;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ── SMS ──────────────────────────────────────────────────────
            [
                'key'          => 'sms_video_link',
                'label'        => 'Video link SMS-i',
                'group'        => 'sms',
                'placeholders' => ['{ad}', '{soyad}', '{ad_soyad}', '{link}', '{muddet}'],
                'content'      => 'Hörmətli {ad_soyad}, kredit müraciətiniz üçün video qeydiyyat linkiniz: {link} . Link {muddet} dəqiqə ərzində etibarlıdır.',
            ],

            // ── Page: SMS göndərildi ──────────────────────────────────────
            [
                'key'          => 'page_sent_title',
                'label'        => '"SMS Göndərildi" — başlıq',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'SMS Göndərildi!',
            ],
            [
                'key'          => 'page_sent_body',
                'label'        => '"SMS Göndərildi" — mətn',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'Telefon nömrənizə video qeydiyyat linki göndərildi. Zəhmət olmasa telefonunuzu yoxlayın.',
            ],
            [
                'key'          => 'page_sent_hint',
                'label'        => '"SMS Göndərildi" — ipucu',
                'group'        => 'page',
                'placeholders' => ['{muddet}'],
                'content'      => 'Link {muddet} dəqiqə ərzində etibarlıdır.',
            ],

            // ── Page: Tamamlandı ─────────────────────────────────────────
            [
                'key'          => 'page_complete_title',
                'label'        => '"Tamamlandı" — başlıq',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'Müraciətiniz qəbul edildi!',
            ],
            [
                'key'          => 'page_complete_body',
                'label'        => '"Tamamlandı" — mətn',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'Video müraciətiniz uğurla göndərildi. Tezliklə sizinlə əlaqə saxlanılacaq.',
            ],
            [
                'key'          => 'page_complete_greeting',
                'label'        => '"Tamamlandı" — salamlama',
                'group'        => 'page',
                'placeholders' => ['{ad}', '{soyad}', '{ad_soyad}'],
                'content'      => 'Hörmətli {ad_soyad}, müraciətiniz üçün təşəkkür edirik.',
            ],

            // ── Page: Xəta — müddət bitib ────────────────────────────────
            [
                'key'          => 'page_error_expired_title',
                'label'        => '"Xəta: müddət bitib" — başlıq',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'Link müddəti bitib',
            ],
            [
                'key'          => 'page_error_expired_body',
                'label'        => '"Xəta: müddət bitib" — mətn',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'Bu link artıq etibarlı deyil. Yeni müraciət etmək üçün formu doldurun.',
            ],

            // ── Page: Xəta — istifadə edilib ─────────────────────────────
            [
                'key'          => 'page_error_used_title',
                'label'        => '"Xəta: istifadə edilib" — başlıq',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'Link artıq istifadə edilib',
            ],
            [
                'key'          => 'page_error_used_body',
                'label'        => '"Xəta: istifadə edilib" — mətn',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'Bu link yalnız bir dəfə istifadə edilə bilər. Video artıq göndərilmişdir.',
            ],

            // ── Page: Xəta — tapılmadı ───────────────────────────────────
            [
                'key'          => 'page_error_notfound_title',
                'label'        => '"Xəta: tapılmadı" — başlıq',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'Link tapılmadı',
            ],
            [
                'key'          => 'page_error_notfound_body',
                'label'        => '"Xəta: tapılmadı" — mətn',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'Belə bir link mövcud deyil. Zəhmət olmasa linki yoxlayın.',
            ],

            // ── Page: Video çəkiliş skriptləri ────────────────────────────
            // Hansının göstərilməsi `applications.m_type` sütunundan asılıdır
            // (API-də `m_type` sahəsi): 1 → birinci, 2 → ikinci.
            [
                'key'          => 'page_record_script',
                'label'        => 'Video çəkiliş — oxunacaq mətn (Tip 1)',
                'group'        => 'page',
                'placeholders' => ['{ad}', '{soyad}', '{ad_soyad}', '{telefon}', '{mebleg}'],
                'content'      => 'Mən, {ad_soyad}, {mebleg} məbləğində kredit müraciəti etdiyimi təsdiq edirəm. Telefon nömrəm: {telefon}. Bu müraciəti şüurlu şəkildə edirəm.',
            ],
            [
                'key'          => 'page_record_script_2',
                'label'        => 'Video çəkiliş — oxunacaq mətn (Tip 2)',
                'group'        => 'page',
                'placeholders' => ['{ad}', '{soyad}', '{ad_soyad}', '{telefon}', '{mebleg}'],
                'content'      => 'Mən, {ad_soyad}, telefon nömrəm {telefon}, {mebleg} məbləğində kredit müqaviləsinin şərtləri ilə tanış olduğumu və razılaşdığımı təsdiq edirəm.',
            ],

            // ── Page: Video çəkiliş xəbərdarlığı ─────────────────────────
            [
                'key'          => 'page_record_warning',
                'label'        => 'Video çəkiliş — xəbərdarlıq mətni',
                'group'        => 'page',
                'placeholders' => [],
                'content'      => 'Video çəkilişi zamanı yanınızda kimsənin olmadığından əmin olun.',
            ],
        ];

        foreach ($templates as $data) {
            $existing = MessageTemplate::where('key', $data['key'])->first();

            // Mövcud şablonun mətni adminin redaktəsi ola bilər — onu əzmirik;
            // yalnız etiket və dəyişən siyahısını sinxronlaşdırırıq.
            if ($existing) {
                $existing->update([
                    'label'        => $data['label'],
                    'group'        => $data['group'],
                    'placeholders' => $data['placeholders'],
                ]);

                TemplateService::forget($data['key']);

                continue;
            }

            MessageTemplate::create($data);
        }
    }
}
