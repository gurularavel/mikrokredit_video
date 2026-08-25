# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Serve + queue + logs + vite in parallel
composer dev

# Run migrations
php artisan migrate

# Seed admin user (reads ADMIN_DEFAULT_PASSWORD from .env)
php artisan db:seed --class=AdminSeeder

# Run tests
composer test

# Single test file
php artisan test tests/Feature/ExampleTest.php

# Clear config cache (required after .env changes)
php artisan config:clear

# Code style (Laravel Pint)
./vendor/bin/pint
```

## Architecture

### Request flows

**Public form** (`/`) → `ApplicationController@store` → creates `Application` (status `pending`) → dispatches `SendApplicationSms` job → user redirected to `/applications/sent`.

**Merchant API** (`POST /api/orders`) → `MerchantBasicAuth` middleware injects `$request->attributes->get('merchant')` → `Api\OrderController@store` → creates `Application` with `merchant_id` → dispatches `SendApplicationSms`. Optional `m_type` (1 or 2) selects which record-script template the customer sees.

**Video recording** → user opens SMS link `/record/{token}` → `RecordController@show` validates token (not expired, not used) → browser records via `public/js/recorder.js` (MediaRecorder API, 20 s max) → `POST /record/{token}/upload` → saves file to `storage/app/public/videos/Y/m/` → dispatches `CompressVideo` job → redirects to `/record/{token}/complete`.

**Admin panel** (`/admin/*`) → session-based auth (`admin_user` key in session, `AdminAuthenticated` middleware).

### Token lifecycle

- `Application.token` — the URL token sent by SMS. `isTokenExpired()` checks `token_expires_at`. `isTokenUsed()` returns `true` when `video_path` is set.
- `Application.access_token` — returned to merchant via API response; shares the same ULID suffix as `token` but has a different SHA1 prefix.
- Token format (API-created): `cs_{test|live}_{sha1(random_bytes)}_{ulid}`.
- Public-form applications use `Str::random(64)` for `token`; `access_token` is null.

### SMS / templates

`AppServiceProvider` binds `SmsServiceInterface` based on `SMS_DRIVER` env: `log` (default) | `twilio` | `smsaz`.

Message content is stored in the `message_templates` table and rendered by `TemplateService::render($key, $vars, $default)`. Results are cached for 600 s; call `TemplateService::forget($key)` after updates. Template keys used: `sms_video_link`, `page_record_script`, `page_record_script_2`.

### Record-script variants (`m_type`)

The teleprompter text on `/record/{token}` comes in two variants, chosen per application by `applications.m_type` (`unsignedTinyInteger`, default `1`):

| `m_type` | Template key |
|---|---|
| 1 | `page_record_script` |
| 2 | `page_record_script_2` |

`Application::recordScriptKeys()` returns the key list most-specific-first, and `TemplateService::renderFirst($keys, $vars, $default)` picks the first non-empty one — so an unknown or blank variant falls back to `page_record_script`. Adding a type 3 means one new seeder row (`page_record_script_3`) plus an entry in `Application::M_TYPES`; no other code changes.

`Application::templateVars()` is the single source of placeholder values (`{ad}`, `{soyad}`, `{ad_soyad}`, `{telefon}`, `{mebleg}`) — use it instead of rebuilding the array in views. `{mebleg}` is formatted by `Application::formattedAmount()` and is an empty string when no amount is set.

`m_type` is accepted by `POST /api/orders` (`nullable|integer|in:1,2`, defaults to 1) and by the admin "Yeni müraciət" form.

### Video compression

`CompressVideo` job runs after upload. It searches for `ffmpeg` in common paths (including `C:/ffmpeg/bin/ffmpeg.exe`). Compresses only if the output is smaller than the original; otherwise discards the temp file. Silently skips if ffmpeg is not found.

### Design system

Two hand-written stylesheets, no build step: `public/css/app.css` (public pages) and `public/css/admin.css` (admin console). Both share one aesthetic — **"Ledger"**: warm ivory paper, ink navy, copper accent.

- Fonts (Google Fonts, `latin-ext` for Azerbaijani `ə ğ ı ş ö ü ç`): **Fraunces** display serif (`--font-display`), **Instrument Sans** UI text (`--font` / `--font-text`), **JetBrains Mono** for IDs, phones, dates, money (`--font-mono`).
- Tokens: public uses `--ink-*` / `--paper-*` / `--copper-*` / `--moss-*` / `--rust-*`; admin uses `--bg --card --rule --text --acc --moss --amber --rust --slate` plus constant `--nav-*` chrome. Admin keeps `--color-*` legacy aliases so stray inline styles still resolve.
- Public pages use blocks: `.masthead` / `.container` / `.colophon` inside `.shell`, `.paper` cards, `.receipt` + `.stamp` for status pages, `.reveal reveal-1..4` for staggered page-load animation, `.grain` SVG-noise overlay.
- Admin dark mode via `html.dark` on `<html>`, set pre-paint from `localStorage.adminTheme`. In dark mode the accent lightens, so accent-filled surfaces (`.btn-primary`, `.page-item.active`, `.tab-btn.active`, flatpickr header) flip to ink text.
- Shared admin JS lives at the bottom of `layouts/admin.blade.php`: `[data-pw-toggle="<input id>"]` renders and toggles the eye icon; `[data-copy="…"]` copies and swaps `.icon-copy` / `.icon-check`; `window.copyText()` is the clipboard helper (`navigator.clipboard` with `execCommand('copy')` fallback for non-HTTPS). Page-level views should reuse these instead of re-declaring their own.
- The record page opts into the dark full-height variant via `@section('body_class', 'is-record')`.

### Key .env variables

| Variable | Purpose |
|---|---|
| `VIDEO_DURATION` | Max recording seconds (default 20) |
| `VIDEO_DISK` | Laravel filesystem disk for video storage |
| `SMS_DRIVER` | `log` / `twilio` / `smsaz` |
| `SMS_LINK_EXPIRY_MINUTES` | Token validity window |
| `ADMIN_DEFAULT_PASSWORD` | Used by `AdminSeeder` |

### Gotchas

- `config('sms.expiry_minutes')` returns a string — always cast to `(int)`.
- Merchant `auth_key` is stored as plain text (intentional — needed for display in admin).
- `navigator.clipboard` requires HTTPS or localhost; always provide `execCommand('copy')` fallback.
- When adding two icon buttons inside a single container, use flex row — `position:absolute` only works for one icon at a time.
- Migration order matters: `create_merchants_table` must run before `applications` table migrations that add `merchant_id` FK.
