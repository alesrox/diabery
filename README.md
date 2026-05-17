# Diabery

Diabery is a specialized web application built on the Laravel framework, designed to function as an historical logbook for meal-specific insulin dosing. The platform focuses strictly on correlation analysis between dietary intake, administered insulin dosages, and subsequent blood glucose outcomes. By maintaining an accurate record of these variables, users can review historical trends to make informed decisions regarding optimal insulin-to-carbohydrate ratios for recurring meals.

---

## Key Features

* **Meal and Dosage Correlation:** Simultaneous logging of specific food intake, fast-acting insulin dosages, and baseline glucose levels.
* **Postprandial Tracking:** Recording of post-meal blood glucose outcomes to measure the precision and efficacy of the administered insulin dose.
* **Historical Reference Log:** A searchable directory of past meals and their glycemic impacts, serving as an empirical guide for future dosing calculations.

---

## Local Development Setup

Follow these instructions to clone the repository and establish a local development environment.

```bash
git clone https://github.com/YOUR_USERNAME/diabery.git
cd diabery
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate
php artisan serve
```