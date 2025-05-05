ToDo App
---

##  Установка

###  С использованием Docker

```bash
git clone <url репозитория>
cp .env.example .env
```

Настройте переменные в `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=todo_db
DB_USERNAME=todo_user
DB_PASSWORD=todo_password
DB_ROOT_PASSWORD=root
```

Запуск:

```bash
docker compose up -d
docker compose exec php-fpm composer install
docker compose exec php-fpm php artisan key:generate
docker compose exec php-fpm php artisan migrate
npm install
npm run build
```

###  Без использования Docker

```bash
git clone <url репозитория>
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

---

##  API-клиент

Пример работы через `api-client.php`. Запуск:

```bash
php api-client.php
```
---

##  Документация

Опубликована в storage/api-docs

---

## Тесты
```bash
docker compose exec php-fpm php artisan test
```
