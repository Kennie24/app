# Redeem · SoundRedeem Platform

A premium music-redemption portal styled with the **Sonic Spotify** design system.

This monorepo contains two apps:

| Folder           | Stack                                  | Purpose                                     |
| ---------------- | -------------------------------------- | ------------------------------------------- |
| `./`             | React 19 + Vite + Tailwind + shadcn/ui | Consumer-facing redemption portal & profile |
| `./super-admin/` | Laravel 12 + Blade + Tailwind v4       | Super-admin operations dashboard            |

## React portal

- Routes: `/scan`, `/redeem`, `/download`, `/token`, `/admin`, `/profile`, `/profile/settings`
- Real camera-based QR scanner on `/scan` (no decorative placeholder — reads actual codes off cards/paper)
- Framer-motion scroll reveals and route transitions
- 21st.dev / stagewise toolbar in dev for live component editing

```bash
npm install
npm run dev
```

## Laravel super-admin

- Sonic Spotify theme ported into Tailwind v4 (`@theme`) with full container scale
- Sidebar + glass topbar shell, scroll progress bar, IntersectionObserver scroll reveals
- 8 pages: Overview, Redemptions, Users, Assets, Revenue, System Health, Audit Log, Settings
- Seeded super-admin account with `is_super_admin` boolean column on `users`

```bash
cd super-admin
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed   # creates the super-admin account
npm run build
php artisan serve            # or use Laravel Herd
```

### Default super-admin credentials

> Provisioned by `SuperAdminSeeder`. Change the password after first login.

```
Email    : kennethkenzie48@gmail.com
Password : SoundRedeem!2026
```

You can override before seeding:

```bash
ADMIN_EMAIL=me@you.com ADMIN_PASSWORD='StrongPass!' php artisan db:seed
```

## Design system

See `stitch_premium_music_redemption_portal/sonic_spotify_theme/DESIGN.md` for the full token reference (colors, type ramp, spacing, shape language).

— Generated with the help of Claude Code.
