# St. Mary's Academic Website System

Modern PHP MVC + MySQL website and admin CMS optimized for conversion and cPanel hosting.

## Core Features

- Responsive academic website with sticky shrinking header, animated top bar, CTA focus, WhatsApp float
- Pages: Home, About, Principal, Registrar, Contact, FAQs, Programmes, Library, Media Desk, Gallery
- Programmes, News, Careers, Tenders, Library, Gallery loaded dynamically from MySQL
- Secure admin panel with login, dashboard stats, CRUD-style content management, message inbox, settings
- SEO-ready page titles/meta, clean URLs via `.htaccess`, searchable + paginated listing pages

## Tech

- PHP 8+ (clean MVC architecture)
- MySQL / MariaDB
- Bootstrap 5 + custom CSS
- AOS animations + vanilla JavaScript interactions
- Google Font: Roboto

## Local Setup

1. Create MySQL database and import `database/stm_website.sql`.
2. Update DB credentials in `config/config.php`.
3. Point web server document root to `public/`.
4. Ensure these directories are writable:
   - `public/uploads/gallery`
   - `public/uploads/library`
5. Open the site root in browser.

## Default Admin Login

- URL: `/admin/login`
- Email: `admin@stm.ac.ke`
- Password: `password123`

## cPanel Deployment Guide

1. Zip project contents and upload to your cPanel `public_html/stm` (or preferred folder).
2. Set `public/` as web root if using addon domain/subdomain.
3. If you cannot point to `public/`, keep root `index.php` and ensure `.htaccess` works in `/public`.
4. In cPanel MySQL, create DB/user and import `database/stm_website.sql`.
5. Update `config/config.php` DB credentials.
6. In File Manager, set permissions:
   - folders: `755`
   - files: `644`
   - upload folders writable by PHP (`755` or `775` depending host setup)
7. Test URLs:
   - `/`
   - `/programmes`
   - `/media`
   - `/admin/login`

## Notes

- Replace sample image/PDF paths with real uploads from admin panel.
- For production security, change default admin password immediately.
