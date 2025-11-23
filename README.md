# 🎓 OtakAtik Academy - Laravel Login/Register System

Laravel-based authentication system with beautiful UI design featuring a cute koala mascot! 🐨

## ✨ Features

- 🔐 **User Authentication** (Register, Login, Logout)
- 🗄️ **Oracle Database Integration**
- 🎨 **Beautiful UI** with Tailwind CSS
- 🐨 **Cute Koala Mascot**
- 🔔 **Success/Error Notifications**
- 📱 **Responsive Design**
- 🚀 **Modern Laravel 11**

## 🛠️ Tech Stack

- **Framework:** Laravel 11
- **Database:** Oracle 21c XE
- **Frontend:** Tailwind CSS
- **PHP Version:** 8.2.12
- **Oracle Driver:** OCI8

## 📦 Installation

### Prerequisites

- PHP 8.2+
- Composer
- Oracle Database 21c XE
- Oracle Instant Client
- OCI8 PHP Extension

### Setup

1. **Clone the repository:**
```bash
   git clone https://github.com/USERNAME/otakatik-laravel.git
   cd otakatik-laravel
```

2. **Install dependencies:**
```bash
   composer install
```

3. **Copy `.env` file:**
```bash
   cp .env.example .env
```

4. **Configure database in `.env`:**
```env
   DB_CONNECTION=oracle
   DB_HOST=127.0.0.1
   DB_PORT=1521
   DB_DATABASE=otakatik
   DB_USERNAME=user
   DB_PASSWORD=12345678
   DB_SERVICE_NAME=XE
```

5. **Generate application key:**
```bash
   php artisan key:generate
```

6. **Run migrations:**
```bash
   php artisan migrate
```

7. **Start development server:**
```bash
   php artisan serve
```

8. **Open browser:**
```
   http://127.0.0.1:8000
```

## 📸 Screenshots

### Login Page
Beautiful login interface with koala mascot and math-themed chalkboard background.

### Register Page
User-friendly registration form with real-time validation.

### Dashboard
Clean and modern dashboard with welcome message.

## 🎨 UI Features

- ✅ Animated koala mascot
- ✅ Chalkboard-themed background
- ✅ Smooth animations
- ✅ Success/error notifications
- ✅ Responsive design

## 📝 License

This project is open-source and available under the MIT License.

## 👨‍💻 Developer

Developed with ❤️ by Daniel

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

---

**⭐ Star this repo if you like it!**