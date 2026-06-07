# МТМУ — Системаи автоматии қабули хонандагон

Системаи рақамии қабули хонандагон ба синфҳои **0 ва 1** барои **Вазорати маориф ва илми Ҷумҳурии Тоҷикистон**.

Платформа имкон медиҳад, ки волидайн аризаҳоро онлайн ирсол кунанд, ҳуҷҷатҳоро боргузорӣ намоянд ва натиҷаро тавассути коди ягона пайгирӣ кунанд. Мақомоти маориф дар сатҳҳои гуногун (вазорат → вилоят → ноҳия → мактаб) аризаҳоро баррасӣ мекунанд.

---

## Технологияҳо (Stack)

| Қабат | Технология |
|-------|-----------|
| Backend | **Laravel 12** (PHP 8.2+), Sanctum |
| Frontend | **Vue 3** + **TypeScript** + **Vite** |
| Хранилище | **Pinia** |
| UI Kit | **Ant Design Vue 4** |
| Анимация | **GSAP** |
| Роутинг | **Vue Router 4** |
| База | **MySQL 8** |
| Деплой | **Docker + Nginx** |

---

## Иерархияи нақшҳо

```
Суперадмин (Вазорат) → Вилоят → Ноҳия → Мактаб → Ариза
```

| Нақш | Дастрасӣ |
|------|----------|
| `superadmin` | Ҳамаи аризаҳо + идоракунии вилоят/ноҳия/мактаб/корбарон |
| `admin_region` | Танҳо аризаҳои вилояти худ |
| `admin_district` | Танҳо аризаҳои ноҳияи худ |
| `admin_school` | Танҳо аризаҳои мактаби худ |
| `parent` | Ирсол ва пайгирии аризаҳои худ |

---

## Сохтори лоиҳа

```
school-admission/
├── backend/            # Laravel 12 API
│   ├── app/
│   │   ├── Models/             # Region, District, School, User, Application, Document
│   │   ├── Http/Controllers/Api/
│   │   ├── Http/Middleware/    # EnsureUserHasRole
│   │   └── Providers/
│   ├── config/
│   ├── database/migrations/
│   ├── database/seeders/
│   ├── routes/api.php
│   ├── Dockerfile
│   └── docker/                 # nginx.conf, entrypoint.sh, opcache.ini
│
├── frontend/           # Vue 3 SPA
│   ├── src/
│   │   ├── api/                # Сервисҳои axios
│   │   ├── components/
│   │   │   ├── common/         # AppLogo, AuthShell, StatCard
│   │   │   └── layout/         # AdminLayout, ParentLayout
│   │   ├── pages/              # public / parent / admin
│   │   ├── stores/             # auth, app (Pinia)
│   │   ├── router/
│   │   ├── theme/              # Design system (ранг + темаи Ant Design)
│   │   └── types/
│   ├── Dockerfile
│   └── nginx.conf
│
├── docker-compose.yml  # Деплойи "под ключ"
├── Makefile
└── scripts/dev-setup.sh
```

---

## Оғози зуд — Development (локалӣ)

### Талабот
- PHP 8.2+, Composer
- Node.js 20+, npm
- MySQL 8

### Як фармон
```bash
make setup        # composer install + npm install + migrate + seed
```

### Ё ба таври дастӣ

**Backend:**
```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve            # http://localhost:8000
```

**Frontend:**
```bash
cd frontend
npm install
npm run dev                  # http://localhost:5173
```

---

## Деплой — Production (Docker)

```bash
cp .env.example .env         # рамзи базаро тағйир диҳед!
docker compose up -d --build
```

Пас аз оғоз:
- **Frontend:** http://localhost:8080
- **API:** аз дохили шабака тавассути проксии `/api`

Entrypoint худкор иҷро мекунад: `key:generate`, `migrate`, `db:seed`, `storage:link`, `config:cache`, `route:cache`.

---

## Маълумоти вуруд (тестӣ — пас аз seed)

| Нақш | Логин | Рамз |
|------|-------|------|
| Суперадмин | `admin@mtmu.tj` | `admin123` |
| Маъмури вилоят | `sughd@mtmu.tj` (ва ғ.) | `admin123` |
| Волидайн | `+992901234567` | `parent123` |

> Маъмурон тавассути **email**, волидайн тавассути **рақами телефон** ворид мешаванд.

---

## Раванди ариза (Status flow)

```
pending  →  review  →  approved
                    ↘  rejected
```

### Формати коди ариза
```
{сол}-{моҳ}-{рӯз}-{соат}-MTMU__-{XXXX}
Мисол: 2025-08-15-10-MTMU__-3847
```

---

## Ҳуҷҷатҳои ҳатмӣ

1. Шаҳодатномаи таваллуди кӯдак
2. Шиноснома/ШҲ-и волидайн
3. Маълумотномаи тиббӣ (форма 026)
4. Корти эмгузаронӣ
5. Маълумотнома аз ҷойи зист

Форматҳои дастгиришаванда: **JPG, PNG, PDF** (то 10 МБ).

---

## API — нуқтаҳои асосӣ

| Метод | Масир | Тавсиф |
|-------|-------|--------|
| POST | `/api/auth/login-admin` | Вуруди маъмур |
| POST | `/api/auth/login-parent` | Вуруди волидайн |
| POST | `/api/auth/register-parent` | Бақайдгирии волидайн |
| POST | `/api/applications/check-status` | Тафтиши статус аз рӯи код |
| GET | `/api/public/regions` | Рӯйхати вилоятҳо |
| GET | `/api/applications` | Аризаҳо (вобаста ба нақш) |
| POST | `/api/applications` | Сохтани ариза |
| POST | `/api/applications/{id}/documents` | Боргузории ҳуҷҷат |
| PATCH | `/api/applications/{id}/status` | Тағйири статус (маъмур) |
| — | `/api/regions`, `/api/districts`, `/api/schools`, `/api/users` | CRUD (танҳо суперадмин) |

---

## Санаи қабул

**1 август – 1 сентябр** (танзимшаванда дар `ApplicationController`).

---

© Вазорати маориф ва илми Ҷумҳурии Тоҷикистон
