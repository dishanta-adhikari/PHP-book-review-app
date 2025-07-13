# Book Review System

A robust book review platform developed using PHP, MySQL, and Bootstrap. Tailored for book authors and readers, it streamlines book publishing, browsing, and reviewing. The system includes secure authentication, role-based dashboards, protected book submission, and a responsive user interface.

---

## Features

### Core Functionality

- User Authentication – Secure login system with session management
- Role-Based Access – Separate dashboards for Author and User roles
- Book Management – Authors can add, edit, and delete books with cover image and PDF
- Review System – Users can review books with star ratings and comments
- Duplicate Review Prevention – Prevents multiple reviews by the same user per book

### Technical Features

- MVC Architecture – Clean separation of Controllers, Views, and Models
- Database Security – Uses prepared statements to guard against SQL injection
- Responsive UI – Mobile-friendly design powered by Bootstrap
- File Uploads – Supports secure cover image and PDF uploads
- Session Management – Ensures proper login sessions and access control

---

## Prerequisites

- XAMPP (Apache, MySQL, PHP)
- PHP 7.4 or newer
- MySQL 5.7 or newer
- Composer (for managing dependencies)

---

## Installation Guide

### Clone the Repository

```bash
git clone <repository-url>
cd PHP-book-review-app
```

### Move to XAMPP Directory

Place the project folder inside the `htdocs` directory:

```bash
C:/xampp/htdocs/
```

### Database Setup

- Open phpMyAdmin
- Create a database (e.g., book_review_db)
- Import the provided SQL file

### Environment Configuration

Copy and edit the environment file:

```bash
cp .env.example .env
```

Update the `.env` file with:

```
DB_HOST=localhost
DB_NAME=brs_db
DB_USER=root
DB_PASS=
APP_URL=http://localhost/PHP-book-review-app
```

### Install Dependencies

```bash
composer install
```

---

## Run the App

Open in your browser:

```bash
http://localhost/PHP-book-review-app
```

---

## User Roles

### Author

- Manage books
- Upload PDF and cover image
- View user reviews

### User

- Browse and review books
- Submit one review per book
- Read online or download PDFs

---

## Security Highlights

- Prepared Statements to prevent SQL Injection
- File Validation for secure uploads
- Session-Based Authentication
- Input Validation (Client & Server-side)
- XSS Protection through output escaping

---

## License

This project is open-source and available under the MIT License.

---

## Credits

Made with ❤️ for authors and readers.
