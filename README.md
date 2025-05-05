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

Then:
```bash
docker compose logs -f laravel
```
and wait for:

```bash
2025-05-05 08:49:16    INFO  Server running on [http://0.0.0.0:8000].  
2025-05-05 08:49:16 
2025-05-05 08:49:16   Press Ctrl+C to stop the server
```
