<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel

Run this command:

- ```
composer install
php artisan key:generate
```
- Set your database in .env file
- ```
php artisan migrate
php artisan db:seed --class=KomponenTabelSeeder
```
* Klik menu upload & pilih file excel pdrb pada root

## Panduan Konfigurasi Login Otomatis Non SSO
* Run "php artisan migrate" last version
* Run "php artisan db:seed --class=UsersInsertAdminSeeder"

## Panduan Konfigurasi Role Superadmin
* composer update, pastikan dependecies spatie terinstall
* Run "php artisan migrate" last version
* Run "php artisan db:seed --class=PermissionTableSeeder"
* Jadikan current user punya role "superadmin" dengan akses {url path}/authorization/set_my_role
* Baik left menu aplikasi maupun routes otomatis di filter berdasarkan ini.

## Seed DB Setting Konfigurasi
* php artisan db:seed --class=SettingSeeder

## Task
Sabit:
* Authentikasi SSO
* Upload with auth
* Level Access (page, approval, access upload just for satker)
* Fenomena all features
* Beranda

Bombom:
* Tabel PDRB Ringkasan (Tabel 1.1 - 1.16)
* Tabel PDRB Per kab/Kota (Tabel 3.1 - 3.10)
* Tabel History Putaran (Tabel 3.1 - 3.2)
* Tabel Arah Revisi Kab/Kota (Tabel 301 - 310)

Kharis:
* Tabel PDRB Resume (Tabel 2.1 - 2.20)
* Tabel Arah Revisi Total (Tabel 201 - 220)


## License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
