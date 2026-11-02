<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

AlMadinah Hotel - Simple PHP + MySQL Booking App (Bootstrap)
Run on localhost (XAMPP/LAMP). Place folder in htdocs (XAMPP) or web root.

Files:
- config.php         : DB connection
- init_db.sql        : SQL to create database & dummy data (from previous conversation)
- index.php          : Home page (list room types) + availability check + booking form
- booking.php        : Processes booking submissions
- check_booking.php  : Lookup and print booking by number & email
- login.php          : Login for admin & resepsionis
- logout.php         : Logout
- admin/dashboard.php: Admin area (manage tipe_kamar, kamar, users) - basic CRUD
- resepsionis/dashboard.php: Resepsionis area - view & filter pemesanan, update status
- assets/css/style.css : Custom CSS
- assets/js/script.js  : Minimal JS

Setup:
1. Import init_db.sql into phpMyAdmin OR run it to create 'ukk_hotel' and sample data.
2. Put this folder in your web server root (e.g., C:/xampp/htdocs/almadinah_hotel).
3. Edit config.php DB credentials if necessary.
4. Access http://127.0.0.1:8000/

Note: This is a simple, educational app. For production, add stronger validation, prepared statements, CSRF protections, and password hashing.

Feat yang perlu di benerin


A. Design Web 
1. Blade user/dashboard ✅
2. Blade reservasi/dashboard
3. Form Booking
4. Thankyou Page