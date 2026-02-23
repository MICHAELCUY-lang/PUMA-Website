# PUMA Backend API

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

A Laravel-based REST API backend for the PUMA (Pengurus Materi Ajar) Website. This API provides endpoints for managing events, news articles, divisions, cabinets, aspirations, users, and members.

---

## 📋 Table of Contents

- [Requirements](#-requirements)
- [Installation](#-installation)
- [Environment Configuration](#-environment-configuration)
- [Database Setup](#-database-setup)
- [Running the Application](#-running-the-application)
- [API Endpoints](#-api-endpoints)
- [Authentication](#-authentication)
- [Project Structure](#-project-structure)
- [Testing](#-testing)
- [Troubleshooting](#-troubleshooting)
- [Additional Resources](#-additional-resources)

---

## 📦 Requirements

Before you begin, ensure you have the following installed:

| Requirement | Version |
|-------------|---------|
| PHP | ^8.2 |
| Composer | Latest |
| MySQL | 5.7+ / MariaDB 10.3+ |
| Node.js | 18+ (for frontend assets) |
| npm | Latest |

### Required PHP Extensions

- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML

---

## 🚀 Installation

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd PUMA-Backend
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
npm install
```

### Step 4: Quick Setup (Recommended)

The project includes a convenient setup script. Run:

```bash
composer setup
```

This command will automatically:
- Install Composer dependencies
- Copy `.env.example` to `.env` (if not exists)
- Generate application key
- Run database migrations
- Install npm dependencies
- Build frontend assets

### Step 5: Alternative Manual Setup

If you prefer manual setup:

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run build
```

---

## ⚙️ Environment Configuration

### 1. Copy the Example Environment File

```bash
cp .env.example .env
```

### 2. Configure Database Connection

Edit the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=puma_backend
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Configure Application URL

```env
APP_NAME="PUMA API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### 4. Session & Cache Configuration

The application uses database-driven sessions and cache by default:

```env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

---

## 🗄️ Database Setup

### Step 1: Create the Database

Create a new MySQL database named `puma_backend`:

```sql
CREATE DATABASE puma_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or using MySQL CLI:

```bash
mysql -u root -p -e "CREATE DATABASE puma_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Step 2: Run Migrations

```bash
php artisan migrate
```

### Step 3: (Optional) Seed the Database

If seeders are available:

```bash
php artisan db:seed
```

### Database Tables

The application creates the following tables:

| Table | Description |
|-------|-------------|
| `users` | User accounts for authentication |
| `personal_access_tokens` | API tokens (Laravel Sanctum) |
| `divisions` | Organization divisions (BOD, HRD, RNT, etc.) |
| `cabinets` | Cabinet periods |
| `cabinet_divisions` | Cabinet-division relationships |
| `members` | Organization members |
| `events` | Events and activities |
| `event_images` | Event image gallery |
| `news_articles` | News and articles |
| `aspirations` | User aspirations/suggestions |
| `comments` | User comments |
| `comment_likes` | Comment like relationships |
| `merchandises` | Merchandise items |
| `contact_submissions` | Contact form submissions |
| `profiles` | User profiles |
| `activity_logs` | Activity logging |
| `videos` | Video content |

---

## ▶️ Running the Application

### Development Server

Start all development services with a single command:

```bash
composer dev
```

This starts:
- 🌐 Laravel server at `http://localhost:8000`
- 📮 Queue worker for background jobs
- ⚡ Vite development server for hot reloading

### Individual Services

Start only the Laravel server:

```bash
php artisan serve
```

Start the queue worker:

```bash
php artisan queue:listen
```

### Production Build

```bash
npm run build
```

---

## 🔌 API Endpoints

All API endpoints are prefixed with `/api`.

### Authentication Routes

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/login` | User login | ❌ |
| POST | `/api/register` | User registration | ❌ |
| POST | `/api/logout` | User logout | ✅ |
| GET | `/api/me` | Get current user | ✅ |

### Events

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/events` | Get all events |
| GET | `/api/events/completed` | Get completed events |
| GET | `/api/events/upcoming` | Get upcoming events |
| GET | `/api/events/{id}` | Get single event |
| POST | `/api/events` | Create event |
| PUT | `/api/events/{id}` | Update event |
| DELETE | `/api/events/{id}` | Delete event |

### News Articles

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/news` | Get all news articles |
| GET | `/api/news/featured` | Get featured articles |
| GET | `/api/news/{id}` | Get single article |
| POST | `/api/news` | Create article |
| PUT | `/api/news/{id}` | Update article |
| DELETE | `/api/news/{id}` | Delete article |

### Divisions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/divisions` | Get all divisions |
| GET | `/api/divisions/code/{code}` | Get division by code |
| GET | `/api/divisions/{id}` | Get single division |
| POST | `/api/divisions` | Create division |
| PUT | `/api/divisions/{id}` | Update division |
| DELETE | `/api/divisions/{id}` | Delete division |

### Cabinets

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/cabinets` | Get all cabinets |
| GET | `/api/cabinets/{id}` | Get single cabinet |
| POST | `/api/cabinets` | Create cabinet |
| PUT | `/api/cabinets/{id}` | Update cabinet |
| DELETE | `/api/cabinets/{id}` | Delete cabinet |

### Aspirations

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/aspirations` | Get all aspirations |
| GET | `/api/aspirations/{id}` | Get single aspiration |
| POST | `/api/aspirations` | Submit aspiration |
| PUT | `/api/aspirations/{id}` | Update aspiration |
| DELETE | `/api/aspirations/{id}` | Delete aspiration |

### Users (Admin Only - Auth Required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users` | Get all users |
| GET | `/api/users/{id}` | Get single user |
| PUT | `/api/users/{id}` | Update user |
| DELETE | `/api/users/{id}` | Delete user |
| PUT | `/api/users/{id}/password` | Update password |

### Members

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/members` | Get all members |
| GET | `/api/members/{id}` | Get single member |
| POST | `/api/members` | Create member |
| PUT | `/api/members/{id}` | Update member |
| DELETE | `/api/members/{id}` | Delete member |

---

## 🔐 Authentication

This API uses **Laravel Sanctum** for token-based authentication.

### Login Example

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'
```

### Using the Token

Include the token in the Authorization header:

```bash
curl http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### API Response Format

All API responses follow this structure:

```json
{
  "success": true,
  "data": { ... },
  "message": "Optional message"
}
```

---

## 📁 Project Structure

```
PUMA-Backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # API Controllers
│   │   ├── Requests/          # Form Request Validation
│   │   └── Middleware/        # Custom Middleware
│   └── Models/                # Eloquent Models
├── bootstrap/                 # Application bootstrap
├── config/                    # Configuration files
├── database/
│   ├── migrations/            # Database migrations
│   ├── factories/             # Model factories
│   └── seeders/               # Database seeders
├── public/                    # Public assets
├── resources/                 # Views and raw assets
├── routes/
│   └── api.php                # API route definitions
├── storage/                   # Logs, cache, uploads
├── tests/                     # Test files
├── .env.example               # Example environment file
├── composer.json              # PHP dependencies
└── package.json               # Node dependencies
```

---

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Or using Pest directly:

```bash
php artisan test
```

For test coverage:

```bash
php artisan test --coverage
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. Database Connection Error

```
SQLSTATE[HY000] [2002] Connection refused
```

**Solution:** Ensure MySQL is running and check your `.env` database credentials.

#### 2. Permission Denied

```
The stream or file ... could not be opened: Permission denied
```

**Solution:** Fix storage permissions:

```bash
chmod -R 775 storage bootstrap/cache
```

On Windows (PowerShell as Admin):

```powershell
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

#### 3. Class Not Found Error

**Solution:** Regenerate autoload files:

```bash
composer dump-autoload
```

#### 4. API Returns 419 (CSRF Token Mismatch)

**Solution:** For API requests, ensure you're using `api` routes (prefixed with `/api`) which are exempt from CSRF verification.

#### 5. CORS Issues

**Solution:** Configure CORS in `config/cors.php`:

```php
'allowed_origins' => ['http://localhost:5173'], // Your frontend URL
```

Or publish and modify the CORS config:

```bash
php artisan config:publish cors
```

### Clear All Caches

```bash
php artisan optimize:clear
```

---

## 📚 Additional Resources

### API Documentation

- [Events API Setup](./EVENTS_API_SETUP.md) - Detailed events API guide
- [Events API Documentation](./API_DOCUMENTATION_EVENTS.md) - Complete events API reference

### Laravel Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Eloquent ORM](https://laravel.com/docs/eloquent)

### Frontend Integration

This backend powers the **PUMA-Website** frontend. Ensure the API URL is correctly configured in the frontend application.

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit changes: `git commit -m 'Add your feature'`
4. Push to the branch: `git push origin feature/your-feature`
5. Submit a pull request

---

## 📄 License

This project is proprietary software developed for PUMA SMK Immanuel.

---

<p align="center">
  <strong>Happy Coding! 🚀</strong>
</p>
