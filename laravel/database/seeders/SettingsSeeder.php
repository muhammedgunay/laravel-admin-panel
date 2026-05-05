<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Site ──────────────────────────────────────────────
            [
                'group'       => 'site',
                'key'         => 'site.name',
                'value'       => 'My Application',
                'type'        => 'string',
                'label'       => 'Site Adı',
                'description' => 'Uygulamanın görünen adı.',
                'is_public'   => true,
                'is_locked'   => false,
            ],
            [
                'group'       => 'site',
                'key'         => 'site.description',
                'value'       => '',
                'type'        => 'text',
                'label'       => 'Site Açıklaması',
                'description' => 'SEO ve meta açıklaması için kullanılır.',
                'is_public'   => true,
                'is_locked'   => false,
            ],
            [
                'group'       => 'site',
                'key'         => 'site.logo_url',
                'value'       => null,
                'type'        => 'string',
                'label'       => 'Logo URL',
                'description' => 'Panel ve frontend logosu için URL.',
                'is_public'   => true,
                'is_locked'   => false,
            ],
            [
                'group'       => 'site',
                'key'         => 'site.maintenance_mode',
                'value'       => 'false',
                'type'        => 'boolean',
                'label'       => 'Bakım Modu',
                'description' => 'Aktifken kullanıcılar siteye erişemez.',
                'is_public'   => false,
                'is_locked'   => false,
            ],
            [
                'group'       => 'site',
                'key'         => 'site.timezone',
                'value'       => 'Europe/Istanbul',
                'type'        => 'string',
                'label'       => 'Zaman Dilimi',
                'description' => 'Sistemin kullandığı zaman dilimi.',
                'is_public'   => false,
                'is_locked'   => false,
            ],

            // ── Mail ──────────────────────────────────────────────
            [
                'group'       => 'mail',
                'key'         => 'mail.from_address',
                'value'       => 'noreply@example.com',
                'type'        => 'string',
                'label'       => 'Gönderen E-posta Adresi',
                'description' => 'Sistem maillerinde kullanılacak gönderen adresi.',
                'is_public'   => false,
                'is_locked'   => false,
            ],
            [
                'group'       => 'mail',
                'key'         => 'mail.from_name',
                'value'       => 'My Application',
                'type'        => 'string',
                'label'       => 'Gönderen Adı',
                'description' => 'E-postalarda görünecek gönderen ismi.',
                'is_public'   => false,
                'is_locked'   => false,
            ],

            // ── Security ──────────────────────────────────────────
            [
                'group'       => 'security',
                'key'         => 'security.session_lifetime',
                'value'       => '120',
                'type'        => 'integer',
                'label'       => 'Oturum Süresi (dakika)',
                'description' => 'Kullanıcı oturumunun kaç dakika sonra sona ereceği.',
                'is_public'   => false,
                'is_locked'   => false,
            ],
            [
                'group'       => 'security',
                'key'         => 'security.max_login_attempts',
                'value'       => '5',
                'type'        => 'integer',
                'label'       => 'Maksimum Giriş Denemesi',
                'description' => 'Hesap kilitlenmeden önce izin verilen maksimum giriş denemesi.',
                'is_public'   => false,
                'is_locked'   => false,
            ],

            // ── UI ────────────────────────────────────────────────
            [
                'group'       => 'ui',
                'key'         => 'ui.items_per_page',
                'value'       => '15',
                'type'        => 'integer',
                'label'       => 'Sayfa Başına Kayıt',
                'description' => 'Listeleme sayfalarında gösterilecek varsayılan kayıt sayısı.',
                'is_public'   => false,
                'is_locked'   => false,
            ],
            [
                'group'       => 'ui',
                'key'         => 'ui.date_format',
                'value'       => 'd/m/Y',
                'type'        => 'string',
                'label'       => 'Tarih Formatı',
                'description' => 'Panelde tarihlerin görüntülenme formatı.',
                'is_public'   => false,
                'is_locked'   => false,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
