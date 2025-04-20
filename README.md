# MB Inventory

Laravel-based inventory and billing application for managing customers, products, stock, and invoices.

## Features

- Authentication and admin dashboard
- Customer, product, and inventory CRUD with CSV/Excel import & export
- Inventory history tracking
- Invoice generation with PDF (DomPDF) and bulk zip download
- Business settings

## Requirements

- PHP 8.2+
- Composer
- MySQL/MariaDB (or compatible database)
- Node.js (for Vite assets, optional in development)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure DB_* in .env
php artisan migrate
php artisan serve
```

## Stack

- Laravel 12
- AdminLTE UI
- DomPDF & PhpSpreadsheet
