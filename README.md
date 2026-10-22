# MyBlog

A full-featured blog platform built with **Laravel**, with role-based access, a post approval workflow, social features and an admin dashboard.

**Live demo:** https://nimesha.slhosted.lk/

## Features

- **Authentication:** register, login, password reset, email verification and Google sign-in
- **Roles and permissions:** admin / reader / visitor, with a permission manager in the admin panel
- **Posts:** create, edit, drafts, trash, categories, cover images and a post approval workflow (pending posts)
- **Discovery:** category filtering, search and trending posts
- **Engagement:** comments, likes and saved (bookmarked) posts
- **Social:** follow authors and a "Following" feed
- **Notifications:** in-app and email notifications for new posts and comments
- **Admin dashboard:** post stats, analytics, activity logs and weekly summary emails
- **Admin settings:** configurable SMTP email settings from the admin panel
- **UI:** responsive glass-style design with dark mode

## Tech Stack

- **Backend:** Laravel, PHP, Laravel Breeze
- **Frontend:** Blade, Tailwind CSS, Alpine.js, Vite
- **Database:** MySQL

## Getting Started

### Requirements

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL (for example via XAMPP)

### Installation

1. Clone the repository

```bash
   git clone https://github.com/nimesha1234455/myblog-laravel.git
   cd myblog-laravel
```

2. Install dependencies

```bash
   composer install
   npm install
```

3. Create the environment file and generate the app key

```bash
   cp .env.example .env
   php artisan key:generate
```

   On Windows Command Prompt, use `copy .env.example .env` instead of `cp`.

4. Create a MySQL database, then set your database details in `.env`

```env
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
```

5. Run the migrations and seeders

```bash
   php artisan migrate --seed
```

6. Link the storage folder (for uploaded images)

```bash
   php artisan storage:link
```

7. Build the frontend assets and start the server

```bash
   npm run build
   php artisan serve
```

   Open http://localhost:8000 in your browser.

### Optional configuration

- **Google sign-in:** add your Google OAuth credentials to `.env` (see `config/services.php` for the variable names).
- **Emails:** configure SMTP either in `.env` or from the SMTP Settings page in the admin panel.

## Author

**Nimesha Madushani**
HNDIT student at SLIATE ATI Badulla | Web Developer Intern at ServerClub.LK

- LinkedIn: https://www.linkedin.com/in/nimesha-madushani-bb5263371
- GitHub: https://github.com/nimesha1234455
