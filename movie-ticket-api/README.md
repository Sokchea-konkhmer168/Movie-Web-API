# Movie Ticket Booking API

A RESTful API for movie ticket booking system built with PHP.

## Requirements
- PHP 8.0+
- MySQL 8.0+
- Composer

## Installation Steps

1. Install PHP and Composer
2. Clone this project
3. Configure database connection in `config/database.php`
4. Run database migrations
5. Start the development server

## Database Configuration

Based on your MySQL Workbench setup:
- Host: 127.0.0.1
- Port: 3306
- Username: root
- Password: (as configured)
- Database: movie_ticket_booking

## API Endpoints

### Movies
- GET /api/movies - List all movies
- POST /api/movies - Create new movie (auth required)
- GET /api/movies/{id} - Get movie details
- PUT /api/movies/{id} - Update movie (auth required)
- DELETE /api/movies/{id} - Delete movie (auth required)

### Cinemas
- GET /api/cinemas - List all cinemas
- POST /api/cinemas - Create new cinema (auth required)

### Shows
- GET /api/shows - List all shows
- POST /api/shows - Create new show (auth required)

### Bookings
- GET /api/bookings - List user's bookings (auth required)
- POST /api/bookings - Create new booking (auth required)

### Authentication
- POST /api/register - User registration
- POST /api/login - User login
- POST /api/logout - User logout (auth required)
