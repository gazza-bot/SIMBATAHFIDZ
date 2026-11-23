# AGENTS.md — SIMBA TAHFIDZ

Sistem Informasi Mutaba'ah & Belajar Al-Qur'an — Pondok Pesantren Darut Taqwa Ponorogo.

> This file is the entry point for AI agents. Read `antislop.md` (binding UI filter) and `Roadmap.md` (sprint state) before building anything.

---

## 1. Project Overview

**SIMBA TAHFIDZ** = Sistem Informasi Mutaba'ah & Belajar Al-Qur'an
- **Client**: Pondok Pesantren Darut Taqwa Ponorogo
- **Purpose**: Track santri Quran memorization (setoran hafalan), attendance, and progress across 30 Juz
- **Users**: 4 roles — Admin, Ustadz, Kepala Tahfidz, Wali
- **Methodology**: Agile, 8 sprints (Sprint 0 done, Sprint 1 next)

---

## 2. Tech Stack

| Layer | Technology | Version / Note |
|-------|------------|----------------|
| Backend | Laravel | 12.x (stable) |
| Language | PHP | 8.2 |
| Database | MySQL | 8.x (XAMPP). NOT sqlite |
| Frontend | Tailwind CSS | v4, JIT via Vite |
| Templates | Blade | Laravel native |
| Auth | Custom | username/password, 4 roles, no email |
| PDF | barryvdh/laravel-dompdf | latest |
| Charts | Chart.js | 4.x (Sprint 4) |
| Testing | Pest PHP | ^3.8 (PHP 8.2 constraint) |
| Icons | Inline SVG | no external icon library |
| Fonts | Google Fonts | Nunito Sans, Amiri, JetBrains Mono |

---

## 3. Directory Structure (Key Files)

```
/opt/lampp/htdocs/SIMBA_TAHIDZ_RIL/
├── antislop.md              # Binding UI rules (read before UI work)
├── Roadmap.md               # Sprint 0-8 checklists (tracked in git)
├── AGENTS.md                # This file
├── .env                     # Local only (gitignored); MySQL config
├── .env.example             # Template with MySQL defaults
├── resources/
│   ├── css/app.css          # @theme tokens: color, type, spacing, motion, RTL
│   ├── js/app.js            # Sidebar toggle, dropdown behavior
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php      # Authenticated shell (sidebar, bottom bar, header)
│       │   ├── guest.blade.php    # Split-screen login (branding + form)
│       │   └── print.blade.php    # PDF layout
│       ├── components/            # 16 Blade components
│       ├── auth/login.blade.php   # Login form (extends guest)
│       ├── admin/dashboard.blade.php
│       ├── ustadz/dashboard.blade.php
│       ├── kepala/dashboard.blade.php
│       └── wali/mutabaah.blade.php
├── routes/web.php             # All routes (role-grouped)
├── app/
│   ├── Http/Controllers/Auth/LoginController.php  # Custom auth + role redirect
│   ├── Http/Middleware/RoleMiddleware.php         # Alias 'role'
│   └── Models/                # 10 Eloquent models
├── database/
│   ├── migrations/            # 11 tables
│   └── seeders/               # SurahSeeder (114 surah) + DatabaseSeeder (dev data)
└── public/build/              # Vite output (gitignored)
```

---

## 4. Design System (from `resources/css/app.css`)

All visual values come from `@theme` tokens. Never hardcode a color or font.

### Colors
| Token | Value | Usage |
|-------|-------|-------|
| `--color-primary-500` | `#4A7C59` | Forest Sage: brand, left login panel |
| `--color-primary-600` | `#3D6B4A` | Hover/active |
| `--color-secondary-500` | `#C87D46` | Warm Terra: secondary accent |
| `--color-accent-500` | `#EAB308` | Quranic Gold: quote labels, highlights |
| `--color-success-500` | `#16A34A` | Mutqin |
| `--color-info-500` | `#2563EB` | Ziyadah |
| `--color-warning-500` | `#F59E0B` | Warning |
| `--color-error-500` | `#DC2626` | Error / Dhaif |
| `--color-neutral-50`..`950` | gray scale | Text, borders, backgrounds |

### Typography
- `--font-sans`: Nunito Sans (Latin UI)
- `--font-arabic`: Amiri (Quranic text, always pair with `dir="rtl"`)
- `--font-mono`: JetBrains Mono (NIS, code)

### Spacing / Radius / Motion
- 4px base spacing scale
- Radius ladder `rounded` → `rounded-2xl` (consistent, never pill-everything; R-11)
- `--motion-fast` 150ms, `--motion-normal` 200ms, `--motion-slow` 300ms
- Celebration 600ms max; `prefers-reduced-motion` honored (R-39)

### Breakpoints
sm 640, md 768, lg 1024, xl 1280, 2xl 1536

---

## 5. Architecture & Conventions

### Auth Flow
- Login: `GET/POST /login`, CSRF protected
- Roles: `admin`, `ustadz`, `kepala_tahfidz`, `wali`
- Redirect in `LoginController::redirectToDashboard()`:
  - admin → `/admin/dashboard`
  - ustadz → `/ustadz/dashboard`
  - kepala_tahfidz → `/kepala/dashboard`
  - wali → `/wali/mutabaah`
- Middleware alias `role`, e.g. `role:admin|ustadz`
- Dev credentials: `admin` / `ustadz` / `kepala` / `wali`, password `password`

### Route Groups
| Role | Prefix | Name | Landing |
|------|--------|------|---------|
| admin | `/admin` | `admin.*` | `admin.dashboard` |
| ustadz | `/ustadz` | `ustadz.*` | `ustadz.dashboard` |
| kepala_tahfidz | `/kepala` | `kepala.*` | `kepala.dashboard` |
| wali | `/wali` | `wali.*` | `wali.mutabaah` |
| shared (ustadz/kepala/wali) | — | `peta-juz.index`, `murajaah.index` | — |
| PDF | `/santri/{santri}` | `santris.rapor.pdf`, `santris.sertifikat.pdf` | — |

### Models & Relationships
| Model | Notes |
|-------|-------|
| User | `username` unique, `role`, `foto`; hasOne Wali/Ustadz/KepalaTahfidz |
| Santri | belongsTo kelompok/angkatan/wali; hasMany setorans/absensis; accessors `progress_juz`, `progress_percent`, `mutu_terakhir` |
| Setoran | belongsTo santri/surah/ustadz; status: `pending`, `approved`; nilai: `maqbul`, `jayyid`, `jayyid_jiddan`, `mumtaz` |
| Surah | 114 seeded rows: `nama_arab`, `jumlah_ayat`, `juz_mulai`, `juz_selesai` |
| Kelompok | belongsTo angkatan/ustadz; hasMany santris |
| JuzCoverage | `app/Support/JuzCoverage.php`: strict per-juz ayat coverage via `juz_starts`; statuses `mutqin`, `parsial`, `belum` |

**Mutqin (locked)**: a juz is Mutqin when every ayat in the juz range (from `juz_starts`) is covered by at least one approved ziyadah setoran.

---

## 6. Sprint Roadmap (source: `Roadmap.md`)

| Sprint | Scope | Status |
|--------|-------|--------|
| 0 | Foundation: Laravel, Tailwind, auth, design system, seed, login, placeholder dashboards | Done |
| 1 | Kelola Data Master (Admin): CRUD santri/wali/ustadz/kelompok/angkatan | Done |
| 2 | Input Setoran (Ustadz): form, cover penuh otomatis, auto-approve | Done |
| 3 | Absensi Kelompok (Ustadz): presensi, rekap | Done |
| 4 | Dasbor Kepala: stats + Chart.js, attention list | Done |
| 5 | Dasbor Wali: mutaba'ah multi-anak, riwayat, peta 30 juz, pesan doa | Done |
| 6 | Peta Hafalan 30 Juz (strict Mutqin via juz_starts, embed admin/ustadz, page kepala) | Done |
| 7 | Murajaah Bulanan | Next |
| 8 | PDF export + accessibility audit | Pending |

Work only inside the current sprint. Do not jump ahead or add navigation for unbuilt pages (R-24).

---

## 7. Development Workflow

```bash
# Dev server (user runs manually; agent shells time out on serve)
php artisan serve --host=0.0.0.0 --port=8000

# Assets (required after adding new Tailwind classes)
npm run build

# Database
php artisan migrate:fresh --seed

# Caches
php artisan view:clear && php artisan cache:clear

# Tests (Pest)
./vendor/bin/pest
```

### Commit Convention
- `feat(scope): description` new feature
- `bug: description` fix
- `chore: description` maintenance
- `refactor(scope): description` internal change

### Git
- Remote: `https://github.com/gazza-bot/SIMBATAHFIDZ` (branch `main`)
- Commit and push after each completed sprint item or fix
- `Roadmap.md` is tracked; `.env` and `public/build` are not
- Never commit `.env` or secrets

---

## 8. antislop (MANDATORY)

**Read `antislop.md` fully before any UI work.** It is a binding filter, not a suggestion.

### Mode Resolution (strict order)
1. Explicit session instruction (`during` / `after`)
2. Global preference (`~/.config/antislop/settings.json`, read `mode` only)
3. Ask the user, then wait

Project default: `during`.

### Hard Gates (absolute)
| Rule | Requirement |
|------|-------------|
| R-02 | No em dash (`—`) in agent-written text |
| R-03 | Mobile flawless: no overflow, 44px tap targets |
| R-17 | No invented numbers/stats |
| R-18 | No fake testimonials |
| R-23 | Ask before creating assets; otherwise explicit placeholder |
| R-24 | No links to pages that do not exist |
| R-25 | WCAG AA contrast (4.5:1 / 3:1 large) |
| R-26 | Every interactive element works or is removed |
| R-27 | Empty + loading + error states on every data UI |
| R-28 | No generic FAQ |
| R-32 | Keyboard reachable, visible focus |
| R-33 | No CSS/source patching via scripts |
| R-34 | Every shipped theme fully works |
| R-35 | Verify before deliver: run, click through, report element by element |
| R-36 | No fabricated claims |
| R-37 | Design direction required (this project has one) |
| R-38 | Real content or labeled placeholder |
| R-39 | Accessible semantics: roles, labels, alt, cursor, aria |
| R-40 | Media width/height, no blocking scripts, no fake metrics |

### Purpose-Gate (allowed with written reason)
R-01 color/gradients, R-04 icons, R-06 typography, R-07 background pattern, R-08 button arrows, R-09 badges, R-10 glassmorphism (max 1-2 elements), R-12 shadow, R-13 glow, R-14 feature cards, R-19 animation, R-22 illustrations.

### Quality Locks
R-05 layout, R-11 radius, R-15 CTA copy, R-16 no buzzwords, R-20 identity, R-21 dark mode, R-29 palette limit (2-3 + 1 accent), R-30 no cloning, R-31 every decision needs a one-line reason.

---

## 9. Locked Design Direction

| Dial | Value | Reason |
|------|-------|--------|
| ENERGY | 1 (Calm) | Pesantren context: trust, serenity, focus |
| RHYTHM | 2 (Consistent with breaks) | Dashboards uniform; split-screen login is the break |
| MOTION | 1 (Hover only + 600ms celebration) | Minimal distraction |
| Dark mode | None | Confirmed light-only |
| RTL | Required | Arabic Quranic text |
| Illustrations | None; dashed placeholders | No assets yet, honest placeholder per R-23 |

**Design Read**: calm Islamic modern dashboard + split-screen login for pesantren staff and parents, dial ENERGY 1 / RHYTHM 2 / MOTION 1.

---

## 10. Project-Specific UI Rules

### Reuse Components
`resources/views/components/`: button, input, card, card-santri, avatar, badge, modal, table, dropdown, dropdown-item, tabs, alert, pagination, breadcrumb, amalan-yaumiyah, peta-juz. Extend these, do not fork new styles.

### Layout Patterns
- App shell: sidebar (admin/ustadz/kepala), bottom bar for mobile (ustadz/wali), header, main
- Guest: `lg:grid-cols-12`, left 5 cols branding (primary bg), right 7 cols form
- Print: minimal, no chrome

### Navigation
- Sidebar lists only built pages. Sprint 0: Dashboard only. Add entries as sprints ship.
- Wali: no sidebar, bottom bar only.

### Icons & Images
- Inline SVG only, `aria-hidden="true"` for decorative
- Placeholder style: dashed border box (`border-dashed border-white/35 bg-white/5`)
- Reused glyphs: book (logo), user, lock, arrow, chevrons
- Arabic: `font-arabic` + `dir="rtl"` + `lang="ar"`

### States
Every data view needs empty, loading, error states (R-27). Placeholder dashboards currently omit them; add from Sprint 1 onward.

### Output Escaping
- Icons passed as props/HTML strings must render with `{!! !!}` (not `{{ }}`), or they show as raw tags.
- User content always `{{ }}`.

---

## 11. Database Notes

- MySQL only; `.env` uses `DB_CONNECTION=mysql`, database `simba_tahfidz`
- FK creation order matters: `surahs` before `setorans` (setorans migration timestamp `...140041`)
- Migrations are ordered next to absensis; keep new migrations timestamped after existing ones
- Seed is real dev data, not fabricated stats
- `migrate:fresh --seed` is safe locally; never run it against production data

---

## 12. Known Gaps / Next Actions

| Area | Status | Needed for |
|------|--------|-----------|
| Admin resource controllers | Not created (routes reference them) | Sprint 1 |
| Ustadz Setoran/Absensi controllers | Not created | Sprint 2-3 |
| Kepala/Wali controllers | Not created | Sprint 4-5 |
| PDF controllers | Not created | Sprint 8 |
| Chart.js in views | Installed, not wired | Sprint 4 |
| Empty/loading/error states | Missing | Sprint 1+ (R-27) |
| Peta Juz Mutqin logic | Done (JuzCoverage service) | Sprint 6 |
| Figma assets | Placeholders in use | Optional (R-23) |

---

## 13. Agent Checklist

1. Read `antislop.md`, then this file, then `Roadmap.md`.
2. Confirm current sprint scope; do not build outside it.
3. Check existing components and tokens before adding new ones.
4. Write a one-line reason for each design decision (R-31).
5. Verify before delivering (R-35):
   - `npm run build`
   - `php artisan view:clear`
   - curl/login flow for all 4 roles, expect HTTP 200
   - no escaped SVG, no dead links, no console errors
6. Commit with the convention above and push to `main`.
7. Update `Roadmap.md` checkboxes and this file's Sprint 0 note when a sprint closes.

---

*Reflects Sprint 5 completion: Dasbor Wali Santri (multi-anak, peta hafalan 30 juz, riwayat setoran & absensi, pesan kasih orang tua). Update after each sprint.*

<!-- antislop:start -->
## antislop
For UI, copy, people, mobile layout, or code comments work, read `antislop.md` (core) and then the skill for the task:
- UI / visual: `skills/antislop-ui/SKILL.md`
- Copy & text: `skills/antislop-copywriting/SKILL.md`
- People: `skills/antislop-human/SKILL.md`
- Mobile / responsive: `skills/antislop-layoutmobile/SKILL.md`
- Code comments: `skills/antislop-code/SKILL.md`
Before starting, follow the core's "Two Usage Modes" section in strict order: explicit session instruction first, then global preference, then ask. A session instruction always wins. For a resolved mode, say `antislop active: <mode> (session override).` or `antislop active: <mode> (global preference).` once before presenting findings or making edits, using the actual mode and source. Acknowledging the user's request without naming the source does not replace this notice.
Only an explicit choice of antislop during or after selects a session mode. A request to review, audit, or avoid file edits does not select a mode; read the global preference in that case. Another skill's mode does not select antislop's mode.
If the mode is unresolved, ask during/after and end the response; wait for the answer before any UI review, planning, or concept. For read-only tasks, put the active-mode notice only at the start of the final answer, never in progress messages. For editing tasks, announce before the first edit and omit it from the final answer.
To update antislop later: download `antislop.md` again, or run `npx antislop-ai --update` if it was installed as skill folders.
<!-- antislop:end -->
