```markdown
# Karsa Digital - Backend

Backend API untuk website Karsa Digital (Laravel + PostgreSQL).

## 🛠 Tech Stack
- **Laravel 11**
- **PostgreSQL**
- **PHP 8.3**

## ✨ Fitur API
- CRUD Project / Portofolio
- Filter kategori
- Storage foto proyek
- Authentication (siap dikembangkan)

## 🖥️ Cara Menjalankan Lokal

```bash
# Clone repo
git clone https://github.com/Ariefhuda434/karsa-digital-be.git
cd karsa-digital-be

# Install dependencies
composer install

# Copy environment
cp .env.example .env

# Generate key
php artisan key:generate

# Jalankan migrasi
php artisan migrate

# Storage link (untuk foto)
php artisan storage:link

# Jalankan server
php artisan serve

jadi gini
