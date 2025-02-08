
# Meeting Room Booking API

This is the backend for the Meeting Room Booking application, built using **Laravel 11**, **PostgreSQL 16**, **Redis**, and **PHP 8.4**.

---

## Requirements

Before you begin, ensure you have the following installed:

- **PHP**: Version 8.4 or later
- **Composer**: For managing PHP dependencies
- **PostgreSQL**: Version 16 for database management
- **Redis**: For caching and queues
- **Node.js**: For frontend dependencies (if applicable) and task runners
- **Git**: For version control
- **Apache/Nginx**: Web server to host the application

---

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/mrbs-backend.git
cd mrbs-backend
```

---

### Step 2: Install PHP Dependencies

Install all PHP dependencies using Composer:

```bash
composer install
```


---

### Step 3: Configure the Environment

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Update the `.env` file with your configuration details:
   ```env
   APP_NAME=MeetingRoomBooking
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost

   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password

   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379

   QUEUE_CONNECTION=redis
   ```

3. Generate the application key:
   ```bash
   php artisan key:generate
   ```

---

### Step 4: Setup the Database

1. Ensure PostgreSQL is running and create a new database for the application.
2. Run the migrations to set up the database schema:
   ```bash
   php artisan migrate
   ```
3. (Optional) Seed the database with initial data:
   ```bash
   php artisan db:seed
   ```

---

### Step 6: Link Storage

Create a symbolic link for public storage:

```bash
php artisan storage:link
```

---

### Step 7: Start the Application

Run the application using Laravel's built-in server:

```bash
php artisan serve
```

Access the app at: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Platform-Specific Notes

### Windows

- Use tools like **XAMPP**, **WAMP**, or **Laragon** to manage PHP, PostgreSQL, and Redis.
- Ensure PostgreSQL and Redis services are running before launching the application.

### Linux

1. Install dependencies:
   ```bash
   sudo apt update
   sudo apt install php8.4 postgresql redis
   ```
2. Start services:
   ```bash
   sudo service postgresql start
   sudo service redis-server start
   ```

### macOS

1. Install dependencies using Homebrew:
   ```bash
   brew install php postgresql redis
   ```
2. Start services:
   ```bash
   brew services start postgresql
   brew services start redis
   ```

---

## Additional Commands

### Running Queues

To handle jobs in the queue:
```bash
php artisan queue:work
```

### Clearing Cache

Clear various application caches:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Contributing

Feel free to fork this repository and contribute. Pull requests are welcome!

---

## License

This project is open-source and available under the [MIT License](LICENSE).
