# Movie Ticket Booking API - Setup Guide

## 🛠️ **Technology Stack**

### **Backend Technologies:**
- **PHP 8.0+** - Server-side scripting language
- **MySQL 8.0+** - Relational database management
- **Apache 2.4+** - Web server (via XAMPP)
- **XAMPP** - Local development environment

### **Frontend Technologies:**
- **HTML5** - Web interface structure
- **CSS3** - Styling and responsive design  
- **JavaScript (ES6+)** - Client-side interactivity
- **JSON** - Data exchange format

### **Security & Authentication:**
- **Bearer Token Authentication** - API security
- **PHP password_hash()** - Password encryption
- **PDO Prepared Statements** - SQL injection prevention
- **CORS Headers** - Cross-origin request handling

### **Database Features:**
- **InnoDB Storage Engine** - ACID compliance
- **Foreign Key Constraints** - Referential integrity
- **Auto-increment IDs** - Primary key management
- **UTF8MB4 Character Set** - Full Unicode support

### **API Architecture:**
- **RESTful API Design** - Standard HTTP methods
- **JSON Response Format** - Lightweight data exchange
- **MVC Pattern** - Organized code structure
- **Stateless Design** - Scalable architecture

## Prerequisites
1. Install XAMPP (includes PHP and MySQL)
2. Download from: https://www.apachefriends.org/

## Database Setup

1. **Start MySQL in XAMPP Control Panel**

2. **Open MySQL Workbench** with your configuration:
   - Host: 127.0.0.1
   - Port: 3306
   - Username: root
   - Password: (leave empty or set your password)

3. **Create Database and Tables**:
   ```sql
   -- Open the database/schema.sql file and run all queries
   -- This will create the database and sample data
   ```

4. **Update Database Password**:
   - Edit `src/Database.php`
   - Update the password field with your MySQL password

## Running the API

1. **Copy project to htdocs**:
   ```bash
   # Copy the movie-ticket-api folder to your XAMPP htdocs directory
   # Usually: C:\xampp\htdocs\movie-ticket-api
   ```

2. **Start Apache in XAMPP Control Panel**

3. **Test the API**:
   - Open browser: http://localhost/movie-ticket-api/api/
   - You should see the API documentation

## Testing with Postman

### 1. Register a User
```
POST http://localhost/movie-ticket-api/api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "phone": "1234567890"
}
```

### 2. Login
```
POST http://localhost/movie-ticket-api/api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```
**Save the token from the response for authenticated requests**

### 3. Get Movies
```
GET http://localhost/movie-ticket-api/api/movies
```

### 4. Create a Booking
```
POST http://localhost/movie-ticket-api/api/bookings
Content-Type: application/json
Authorization: Bearer YOUR_TOKEN_HERE

{
    "show_id": 1,
    "seat_number": "A1"
}
```

### 5. Get Your Bookings
```
GET http://localhost/movie-ticket-api/api/bookings
Authorization: Bearer YOUR_TOKEN_HERE
```

## API Endpoints Summary

- **Movies**: `/api/movies` (GET, POST, PUT, DELETE)
- **Shows**: `/api/shows` (GET, POST, PUT, DELETE)
- **Authentication**: `/api/auth/register`, `/api/auth/login`
- **User Profile**: `/api/auth/profile` (GET, PUT)
- **Bookings**: `/api/bookings` (GET, POST, DELETE)

## Notes
- All POST, PUT, DELETE operations (except register/login) require authentication
- Use Bearer token in Authorization header
- Database includes sample movies, cinemas, halls, and shows for testing
