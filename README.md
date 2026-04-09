# Task Management

A Laravel application for managing tasks inside projects. Tasks have a **priority** order per project (1 = top). Reordering in the browser updates priorities automatically. Data is stored in **MySQL** (or any database Laravel supports).

## Requirements

- **PHP 8.3+**
- **Composer**
- **MySQL 8** (or compatible)
- **Node.js 18+** and **npm** (for Vite / Tailwind front-end assets)

## Local setup

1. **Clone or copy the project** and install PHP dependencies:

   ```bash
   composer install
   ```

2. **Environment file**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure MySQL** in `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_manage
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Create the empty database in MySQL before migrating.

4. **Run migrations** (and optional demo data):

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Front-end assets**

   ```bash
   npm install
   npm run build
   ```

   For development with hot reload, use `npm run dev` in a separate terminal.

6. **Start the application**

   ```bash
   php artisan serve
   ```

   Open [http://127.0.0.1:8000](http://127.0.0.1:8000). You will be redirected to the task list.

## Using the app

- **Project dropdown** — Choose a project to see only that project’s tasks.
- **New project** — Use the small form on the task list page.
- **New task** — Assign a name and project; priority is set to the end of the list until you reorder.
- **Drag and drop** — Use the grip handle (⠿) on each row. The server saves order as priority **1, 2, 3, …** from top to bottom.
- **Edit / Delete** — Standard actions per task.

## Tests

Run unit testing for code quality:

```bash
php artisan test
```

## Static analysis & code style

Run **Laravel Pint** and **Larastan** together:

```bash
composer lint
```

Run them separately:

```bash
composer pint      # code style (Pint)
composer analyse   # static analysis (Larastan / PHPStan)
```

Code style is defined in **`pint.json`** (Symfony preset plus project rules and excludes). Run `./vendor/bin/pint` from the project root.

## Local development tooling

- **[Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)** — With `APP_DEBUG=true`, the debug bar appears on web responses in local development.
- **[Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper)** — Regenerate facade / macro stubs for your editor (files are gitignored):

  ```bash
  composer ide-helper
  ```

  Run this after major Composer or framework upgrades.

## Stack

- Laravel 11
- PHP 8.3+
- Blade + Tailwind (Vite)
- SortableJS (CDN) for drag-and-drop
- Laravel Pint + Larastan
- Debugbar + IDE Helper (dev)
