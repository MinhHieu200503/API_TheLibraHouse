<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Technical using this project 
<ul>
    <li>Programming language: PHP 8.2 </li>
    <li>Framework: Laravel 11.4 </li>
    <li>Database: Mysql 5.7 </li>
    <li>Build: Docker</li>
    <li>Deploy project to server VPS</li>

</ul>
# Feature
<ul>
    <li>Booking product,combo product cosmetic - spa </li>
    <li>Voucher,category</li>
    <li>Divide roll admin, staff, customer</li>
    <li>Authorization/ Authentication </li>
    <li>CRUD</li>
    <li>Save image file in server</li>
</ul>
# How to run project
<i>you must install <a href='https://www.docker.com/'>Docker</a> before </i>
<b>Step by Step to build project with Docker</b>
<ul>
    <li>docker-compose build</li>
    <li>docker-compose up -d</li>
    <li>docker ps (find ID 'X' image run laravel-app)</li>
    <li>docker exec -it X /bin/sh (login linux)</li>
    <li>php artisan key:generate</li>
    <li>php artisan migrate</li>
    <!-- run seeder to create sample data -->
    <li>php artisan db:seed --class=AdminSeeder  
    <!-- create link: to link storage in public folder -->
    <li>php artisan storage:link</li>
    <li>php artisan serve</li>
</ul>


#Error




<i style='text-align:center'>
    <b>----By HieuLaiDev -----</b>
</i>
