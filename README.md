# Laravel Scaffolding (based on Laravel 10.x)
**Laravel Scaffolding** is a Laravel 10.x based starter project. 
Most of the commonly needed features of an application like 
`Authentication`, `Authorisation`,`Module`, `User` & `Role management`,
`Application Admin`, `Backup`, `Sitemap` and `Log viewer` are available here.

## Core Features

* User Authentication
* User Profile with Avatar
* Separate Module e.g (Admin and Manager Module)
* Permission Group
* Role-Permissions for Users
* Admin Theme
    * Bootstrap 5
    * CoreUI, CoreUI Icons
* Frontend Theme
  * Bootstrap 5
  * CoreUI, CoreUI Icons
* External Libraries
    * Bootstrap 5
    * CoreUI
      * Chart
      * Simplebar
      * Icons
    * Datatables
    * Jquery
    * Select2
    * Toastr
    * Image Cropper
* Backup (Source, Files, Database as Zip)
* Log Viewer
* Sitemap
* SEO

# User Guide

## Installation

Follow the steps mentioned below to install and run the project.

1. Clone or download the repository
2. Go to the project directory and run `composer install`
3. Create `.env` file by copying the `.env.example`. You may use the command to do that `cp .env.example .env`
4. Update the database name and credentials in `.env` file
5. Clear `cache` and `config`
6. Run the command `php artisan migrate --seed`
7. You may create a virtualhost entry to access the application or run `php artisan serve` from the project root and visit `http://127.0.0.1:8000`

You may use the following account credentials to access the application backend.

```
User: admin@admin.com
Pass: admin
```

## Usage

1. Create new module (if necessary).
2. Assign module slug name to newly created web route file e.g `user.module:admin` in `routes\admin\main.php`
3. Make new directory in views (if necessary)
4. Create permission dependent on module and group
5. Assign default module to user at time of user creation
6. Set mysql path and timeout in `.env` file. e.g `DUMP_BINARY_PATH = 'D:/xampp/mysql/bin/'` and `DUMP_TIME_OUT = 600`
7. Download backup from `storage/app/app_name`. App name is mentioned in `.env` file.
8. Set `APP_URL` to domain name for sitemap auto crawling
## New Module

1. Make module directory in `app/Http/Controller` and create controllers in this directory
2. Make module directory in `resources/views` and create views in this directory
3. Make module directory in `routes` and create route in this directory
4. Make module directory in `public` and create assets in this directory

## Guidelines

1. Never ever create any query directly into blade file
2. Use naming convention in camel case
3. Assign `page_title`,`p_title`,`p_summary`,`p_description`,`method`,`action`,`url`,`url_text` and `enctype` in controller
4. Use `with`,`whereHas` instead of `join`
5. Use `create` or `update` instead of `save`
6. Return success and failure response in toaster
7. Create `$fillable` in newly created models and add `columns` in it
8. Use `LogsActivity` trait in model. Add `getActivitylogOptions` function in model. Example is added in `PermissionGroup` model.
9. Assign middlewares to authorized users e.g `auth`,`verified`,`xss`,`user.status` and `user.module`
10. Make newly created `middlewareName` as `middleware.name` in kernel `middlewareAliases`
11. Use image cropper if necessary to upload image.
12. Delete previous image/file in update or delete case
13. Non public files and images will be created in `Storage::disk('private')`. e.g `profile image` in `UserController`
14. Display image/file via route
15. SEO
    - Use `SeoTrait` in controller (if required)
    - .copy `seo` method from `Module` model and paste in relational model
    - use `setSeo` method, pass dynamic array of data. Example is added in `store` method in `ModuleController`
    - use `getSeo` method, pass model. Example is added in `show` method in `ModuleController`
      - Note: `setSeo` is creating dummy data. You have to pass dynamic data
        - Use following in `app.blade.php`
        - {!! SEOMeta::generate() !!} 
        - {!! OpenGraph::generate() !!} 
        - {!! Twitter::generate() !!} 
        - {!! JsonLd::generate() !!}
