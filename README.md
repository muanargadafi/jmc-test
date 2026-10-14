# Aplikasi Pengelolaan Barang (Inventaris & Pengadaan)

**Tes Teknis — Fullstack Programmer PT JMC Indonesia**

Aplikasi web modern untuk pengelolaan data inventaris dan transaksi barang masuk dengan sistem keamanan berbasis peran (_Role-Based Access Control_), autentikasi berbasis sesi (Laravel Sanctum SPA), serta antarmuka pengguna yang responsif.

---

## 1. Link Deployment (Live App)

- **URL Aplikasi (Production)** : [https://jmc-test-vert.vercel.app](https://jmc-test-vert.vercel.app)
- **URL Backend API** : [https://jmc-test-15t3f.sevalla.app/](https://jmc-test-15t3f.sevalla.app/)

---

## 2. Link Video Demo

- **Tautan Video Demo** : [https://drive.google.com/file/d/11MI4GFTdnoAygNBqOdnJvQCBrUeLzJzw/view?usp=sharing](https://drive.google.com/file/d/11MI4GFTdnoAygNBqOdnJvQCBrUeLzJzw/view?usp=sharing)

---

## 3. Tech Stack yang Digunakan

| Komponen                     | Teknologi                                             | Deskripsi                                                                       |
| :--------------------------- | :---------------------------------------------------- | :------------------------------------------------------------------------------ |
| **Frontend Framework**       | **Vue.js 3 / Nuxt 4** (`^4.5.2`)                      | Framework SSR/SPA modern berbasis Vue 3 dengan Composition API                  |
| **UI & Styling**             | **Nuxt UI** (`^4.11.2`) & **Tailwind CSS** (`^4.3.3`) | Komponen UI terintegrasi dengan utilitas Tailwind CSS 4                         |
| **State & Table Management** | **@tanstack/vue-table** & **VueUse**                  | Data table dinamis (pagination, filter, sorting) & reactive utilities           |
| **Form Validation**          | **Zod** (`^4.6.5`)                                    | Validasi skema data pada form sisi klien                                        |
| **Backend Framework**        | **Laravel 12** (PHP `^8.3`)                           | RESTful API dengan struktur bersih, Form Request, Resources, dan Service layer  |
| **Authentication**           | **Laravel Sanctum** (Stateful SPA Session)            | Autentikasi aman berbasis cookie & proteksi CSRF (_Cross-Site Request Forgery_) |
| **Database Engine**          | **PostgreSQL 18** (Alpine)                            | RDBMS utama untuk penyimpanan data relasional                                   |
| **Cache & In-Memory Store**  | **Redis** (Alpine)                                    | Session store, cache, dan rate limiting API via Redis                           |
| **Import / Export**          | **Maatwebsite Excel** (`^4.0`)                        | Seeder data otomatis berbasis CSV/Excel                                         |
| **Containerization**         | **Docker & Laravel Sail**                             | Lingkungan pengembangan terisolasi (App, PostgreSQL, Redis, pgAdmin)            |

---

## 4. Akun Pengujian (Kredensial)

Sistem telah dilengkapi dengan _database seeder_ otomatis untuk akun Administrator.

### Akun Administrator (Super Admin)

- **Username** : `admin`
- **Email** : `admin@gmail.com`
- **Password** : `Admin@12345`
- **Role** : `Admin`
- **Hak Akses** :
  - Akses Dashboard & Statistik
  - Manajemen Pengguna / Users (CRUD, Lock/Unlock akun, Tetapkan Role)
  - Master Data Kategori (CRUD dengan proteksi relasi data)
  - Master Data Sub Kategori (CRUD dengan proteksi relasi data)
  - Transaksi Barang Masuk (Lihat data, Tambah, Edit, Hapus, Verifikasi)

### Akun Operator

Akun Operator dapat dibuat langsung oleh Administrator melalui menu **Manajemen Pengguna (`/users`)** dengan memilih role **Operator**.

- **Hak Akses Operator** :
  - Akses Transaksi Barang Masuk (`/incoming-goods`)
  - Input transaksi barang masuk beserta item barang
  - Verifikasi penerimaan barang masuk
  - _Catatan: Operator dibatasi dari menu Manajemen Pengguna dan Master Data (dilindungi oleh Middleware & Gate Abilities)._

### Kredensial Database Management (pgAdmin 4 via Docker Sail)

- **URL pgAdmin** : [http://localhost:8081](http://localhost:8081)
- **Email** : `admin@example.com`
- **Password** : `secret`

---

## 5. Petunjuk Instalasi Lokal

Anda dapat menjalankan proyek ini di lingkungan lokal dengan memilih salah satu metode di bawah ini:

- **Metode A (Rekomendasi)**: Menggunakan **Docker & Laravel Sail** (Cepat dan tidak memerlukan instalasi PostgreSQL/Redis lokal).
- **Metode B**: Menggunakan lingkungan PHP & Node.js Native.

---

### Prasyarat Sistem

- **Git**
- **Node.js** (v18.x / v20.x atau lebih baru)
- **pnpm** (disarankan) atau `npm`
- **PHP** (>= 8.3) & **Composer**
- **Docker & Docker Compose** (jika menggunakan Metode A)

---

### Metode A: Instalasi Menggunakan Docker & Laravel Sail (Direkomendasikan)

#### 1. Clone Repository

```bash
git clone https://github.com/username/repository-name.git
cd repository-name
```

#### 2. Setup Backend (Laravel)

```bash
# Pindah ke direktori backend
cd backend

# Salin file konfigurasi environment
cp .env.example .env

# Install dependency PHP via Composer
composer install

# Jalankan container Docker (Laravel, PostgreSQL, Redis, pgAdmin)
./vendor/bin/sail up -d

# Generate Application Key
./vendor/bin/sail artisan key:generate

# Jalankan migrasi database beserta data awal (Seeder: Roles & Admin)
./vendor/bin/sail artisan migrate --seed
```

> **Catatan:** Backend sekarang aktif dan berjalan di `http://localhost` (Port 80).

#### 3. Setup Frontend (Nuxt.js)

Buka terminal baru di root repository:

```bash
# Pindah ke direktori frontend
cd frontend

# Salin file konfigurasi environment
cp .env.example .env
```

Pastikan isi `.env` di folder `frontend` mengarah ke backend yang sedang aktif:

```env
NUXT_PUBLIC_APP_NAME="Aplikasi Pengelolaan Barang"
PORT=3000

NUXT_PUBLIC_API_BASE_URL=http://localhost:80
NUXT_PUBLIC_API_PREFIX=/api/v1
```

Lanjutkan dengan instalasi dependency dan jalankan aplikasi:

```bash
# Install dependency frontend
pnpm install
# (atau: npm install)

# Jalankan development server
pnpm dev
# (atau: npm run dev)
```

> **Akses Aplikasi:**
>
> - **Frontend Web**: Buka browser di [http://localhost:3000](http://localhost:3000)
> - **Backend API**: [http://localhost/api/v1](http://localhost/api/v1)
> - **pgAdmin 4**: [http://localhost:8081](http://localhost:8081)

---

### Metode B: Instalasi Manual / Native (Tanpa Docker)

Jika Anda ingin menjalankan tanpa Docker, pastikan layanan **PostgreSQL** dan **Redis** telah aktif di komputer lokal Anda.

#### 1. Setup Backend

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
```

Sesuaikan konfigurasi koneksi database pada `backend/.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nama_database_anda
DB_USERNAME=username_postgres_anda
DB_PASSWORD=password_postgres_anda

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

FRONTEND_URL=http://localhost:3000
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
SESSION_DOMAIN=localhost
```

Jalankan migrasi database dan seeder:

```bash
php artisan migrate --seed
```

Jalankan server backend:

```bash
php artisan serve --port=8000
```

#### 2. Setup Frontend

Buka terminal baru:

```bash
cd frontend
cp .env.example .env
```

Sesuaikan nilai `NUXT_PUBLIC_API_BASE_URL` pada `frontend/.env` jika backend menggunakan port 8000:

```env
NUXT_PUBLIC_API_BASE_URL=http://localhost:8000
NUXT_PUBLIC_API_PREFIX=/api/v1
```

Jalankan frontend:

```bash
pnpm install
pnpm dev
```

Buka aplikasi di [http://localhost:3000](http://localhost:3000).

---

## 6. Fitur Unggulan Sistem

1. **Sistem Autentikasi Modern (Sanctum SPA)**
   - Menggunakan autentikasi berbasis sesi stateful dengan proteksi token CSRF otomatis dari Nuxt.
   - Rate limiting pada endpoint login untuk mencegah _brute-force attack_.
   - Penguncian akun (_account lock_) secara instan.

2. **Hak Akses & Otorisasi Ketat (RBAC & Gates)**
   - Pemisahan peran Administrator dan Operator secara granular di backend dan frontend middleware.
   - Halaman dan aksi dibatasi sesuai dengan _abilities_ token/sesi pengguna (`barang-masuk.manage`, `master-data.manage`, `users.manage`).

3. **Master Data Management**
   - CRUD Kategori dan Sub Kategori dengan validasi integritas relasi (kategori/sub kategori tidak dapat dihapus jika masih digunakan pada transaksi barang masuk).

4. **Transaksi Barang Masuk (Incoming Goods)**
   - Pembuatan transaksi barang masuk dengan penomoran otomatis/kustom, tanggal terima, dan supplier.
   - Multi-item transaksi dengan rincian kategori, sub kategori, nama item, jumlah kuantitas, satuan, dan kalkulasi otomatis.
   - Fitur verifikasi status barang masuk (Pending $\rightarrow$ Verified).

5. **Antarmuka Responsif & Intuitif**
   - Desain clean menggunakan Nuxt UI dan Tailwind CSS.
   - Filter, pencarian kata kunci, dan pagination data tabel tanpa reload halaman.

---

## 7. Struktur Direktori Repository

```text
├── backend/                  # RESTful API Backend (Laravel 12)
│   ├── app/
│   │   ├── Enums/            # Enum Definisi Role & Ability
│   │   ├── Http/Controllers/ # Controller REST API V1
│   │   ├── Models/           # Eloquent Models & Relasi
│   │   └── Services/         # Business Logic Layer
│   ├── config/               # Konfigurasi CORS, Sanctum, Session, dll.
│   ├── database/
│   │   ├── migrations/       # Migrasi Skema Tabel Database
│   │   └── seeders/          # Seeder Roles, Akun Admin, dsb.
│   ├── routes/
│   │   └── api.php           # Routing API V1 & Health Check
│   └── compose.yaml          # Konfigurasi Docker Sail (App, PG, Redis, pgAdmin)
│
├── frontend/                 # Frontend Web App (Nuxt 4 / Vue 3)
│   ├── app/
│   │   ├── components/       # Komponen UI Reusable
│   │   ├── composables/      # State & API Client Logic (useApi, useAuth, dll.)
│   │   ├── layouts/          # Layout Default & Auth
│   │   ├── middleware/       # Route Guard & Ability Middleware
│   │   ├── pages/            # Halaman Dashboard, Master Data, Transaksi, dll.
│   │   └── utils/schemas/    # Skema Validasi Form Zod
│   ├── nuxt.config.js        # Konfigurasi Nuxt & Environment
│   └── package.json          # Dependency Frontend
│
└── README.md                 # Dokumentasi Proyek & Panduan Instalasi
```

---

## 8. Ringkasan Endpoint API Utama

| Method     | Endpoint                             | Hak Akses       | Keterangan                                    |
| :--------- | :----------------------------------- | :-------------- | :-------------------------------------------- |
| `POST`     | `/api/v1/auth/login`                 | Publik          | Otentikasi & pembuatan session                |
| `POST`     | `/api/v1/auth/logout`                | Auth            | Menghapus sesi aktif                          |
| `GET`      | `/api/v1/auth/me`                    | Auth            | Mengambil profil & _abilities_ pengguna aktif |
| `GET`      | `/api/v1/categories/options`         | Admin, Operator | Mendapatkan opsi daftar kategori              |
| `GET`      | `/api/v1/sub-categories/options`     | Admin, Operator | Mendapatkan opsi daftar sub kategori          |
| `GET/POST` | `/api/v1/users`                      | Admin           | Daftar dan tambah pengguna baru               |
| `PATCH`    | `/api/v1/users/{id}/toggle-lock`     | Admin           | Mengunci / membuka akses akun pengguna        |
| `GET/POST` | `/api/v1/categories`                 | Admin           | Manajemen master data kategori                |
| `GET/POST` | `/api/v1/sub-categories`             | Admin           | Manajemen master data sub kategori            |
| `GET/POST` | `/api/v1/incoming-goods`             | Admin, Operator | Daftar & pembuatan transaksi barang masuk     |
| `PATCH`    | `/api/v1/incoming-goods/{id}/verify` | Admin, Operator | Verifikasi penerimaan transaksi barang masuk  |
