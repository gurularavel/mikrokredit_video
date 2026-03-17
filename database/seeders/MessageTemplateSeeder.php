<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
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

            // ── Page: Video çəkiliş skripti ───────────────────────────────
            [
                'key'          => 'page_record_script',
                'label'        => 'Video çəkiliş — oxunacaq mətn',
                'group'        => 'page',
                'placeholders' => ['{ad}', '{soyad}', '{ad_soyad}', '{telefon}'],
                'content'      => 'Mən, {ad_soyad}, bu video ilə kredit müraciəti etdiyimi təsdiq edirəm. Telefon nömrəm: {telefon}. Bu müraciəti şüurlu şəkildə edirəm.',
            ],
        ];

        foreach ($templates as $data) {
            MessageTemplate::updateOrCreate(['key' => $data['key']], $data);
        }
    }
}
