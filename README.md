# Amp Up Backend
This a backend application designed to support the functionality of the Amp Up mobile application.

### Description
The Amp Up Backend is an integral part of the Amp Up mobile application, providing essential services and functionalities required for its operation. This backend application is responsible for handling user authentication, managing data storage, and exposing API endpoints to support communication with the mobile app.

---

### Setup Instructions
To set up the Amp Up Backend locally, follow these steps:

Prerequisites
PHP (>= 7.4)
Composer
MySQL 

### Installation
1. Clone the repository:
```bash
git clone <repository-url>
```

2. Navigate to the project directory:
```bash
cd Amp-Up-App-Backend
```

3. Install PHP dependencies using Composer:
```bash
composer install
```

4. Create a copy of the .env.example file and rename it to .env:
```bash
cp .env.example .env
```

5. Generate a new application key:
```bash
php artisan key:generate
```

6. Configure the database connection in the .env file:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

```

7. Run the database migrations to create tables:
```bash
php artisan migrate
```

8. (Optional) Seed the database with dummy data:

```bash
php artisan db:seed
```

### Running the Application
9. To run the Amp Up Backend locally, use the following command:
```bash
php artisan serve
```
You can then access the application at http://localhost:8000.
