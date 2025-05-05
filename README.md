<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions">
    <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

---

## About This Project

This repository contains a Laravel-based application, containerized using Docker, and ready to run out of the box.  
It includes automatic Composer setup, Laravel key generation, database migration, and optional SQLite support.

---

## 🐳 Quickstart (Docker)

To start the application locally:

```bash
git clone https://github.com/Seelf/KIS.MasJD
cd KIS.MasJD
docker compose up -d
```

---

## 🕒 Wait for server to start

After running `docker compose up -d`, open Docker Desktop (Logs tab for container `laravel`) or run:

```bash
docker compose logs -f laravel
```

**Now wait until the following log message appears:**

```
2025-05-05 08:49:16    INFO  Server running on [http://0.0.0.0:8000].  
2025-05-05 08:49:16 
2025-05-05 08:49:16   Press Ctrl+C to stop the server
```

⚠️ Do **not open the app** in your browser before this message appears. It will 500/blank out until Laravel is fully initialized.

---

Once that message is visible, open your browser at:

```
http://localhost:8000
```

---

## 🧰 What's included

- Laravel 10+ with Vite & SQLite
- Bitnami Laravel Docker image
- Automatic `.env` setup and `APP_KEY` generation
- Auto migrations on container start
- Vite-ready frontend (JS/CSS with `npm run build`)

---

## 📚 Laravel Overview

Laravel is a web application framework with expressive, elegant syntax. It streamlines many web dev tasks such as:

- [Fast routing](https://laravel.com/docs/routing)
- [Powerful IoC container](https://laravel.com/docs/container)
- Flexible [session](https://laravel.com/docs/session) & [cache](https://laravel.com/docs/cache)
- Elegant [Eloquent ORM](https://laravel.com/docs/eloquent)
- DB-agnostic [migrations](https://laravel.com/docs/migrations)
- Robust [queues](https://laravel.com/docs/queues)
- Real-time [broadcasting](https://laravel.com/docs/broadcasting)

Laravel is accessible and powerful, ideal for robust full-stack apps.

---

## 📖 Learning Laravel

- Official docs: https://laravel.com/docs  
- Hands-on: [Laravel Bootcamp](https://bootcamp.laravel.com)  
- Video tutorials: [Laracasts](https://laracasts.com)

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
