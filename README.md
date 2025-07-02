# 📚 Book Review System

A simple PHP-based Book Review System where users can register, log in, and post reviews for books, while authors can manage their uploaded books and see feedback. Built using PHP, MySQL, and Bootstrap.

---

## 🚀 Features

### 👤 User Features:

- User registration and login
- Browse and review uploaded books
- Submit ratings and feedback
- View reviews from others

### 🧑‍💻 Author Features:

- Author registration and login
- Upload books (PDF or image)
- View reviews for their books

---

## 🛠️ Tech Stack

- **Backend**: PHP (Procedural)
- **Database**: MySQL
- **Frontend**: HTML, CSS, Bootstrap
- **Authentication**: Session-based login

---

## 🗃️ Database Setup

The SQL file is located at:

```
/database/brs_db.sql
```

Import it into your MySQL server before launching the app.

---

## 🔧 Installation

1. **Clone the repository**

```bash
git clone https://github.com/dishanta-adhikari/book-review-app.git
```

2. **Move to server directory**  
   Place the folder in your local server directory (`htdocs/` if using XAMPP).

3. **Configure database**  
   Edit `conn.php` and set your DB credentials:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db = "book_review_system";
```

4. **Start Apache and MySQL**

5. **Visit in browser**

```
http://localhost/book-review-app/
```

---

## 📸 Screenshots _(optional)_

---

## 🙋 Author

**Dishanta Adhikari**
🔗 [LinkedIn](https://www.linkedin.com/in/dishanta-adhikari)
💻 [GitHub](https://github.com/dishanta-adhikari)

---

## 📌 License

This project is for educational and demo purposes. Feel free to use and modify it.

---

## 💡 Contributing

If you'd like to contribute, feel free to fork the repo and submit a pull request or open an issue with suggestions.
