<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'message' => 'Movie Ticket Booking API',
    'version' => '1.0.0',
    'endpoints' => [
        'GET /api/movies' => 'Get all movies',
        'POST /api/movies' => 'Create movie (auth required)',
        'GET /api/movies/{id}' => 'Get movie by ID',
        'PUT /api/movies/{id}' => 'Update movie (auth required)',
        'DELETE /api/movies/{id}' => 'Delete movie (auth required)',
        
        'GET /api/shows' => 'Get all upcoming shows',
        'GET /api/shows?movie_id={id}' => 'Get shows by movie',
        'POST /api/shows' => 'Create show (auth required)',
        'GET /api/shows/{id}' => 'Get show by ID',
        'PUT /api/shows/{id}' => 'Update show (auth required)',
        'DELETE /api/shows/{id}' => 'Delete show (auth required)',
        
        'POST /api/auth/register' => 'User registration',
        'POST /api/auth/login' => 'User login',
        'GET /api/auth/profile' => 'Get user profile (auth required)',
        'PUT /api/auth/profile' => 'Update user profile (auth required)',
        'GET /api/auth/bookings' => 'Get user bookings (auth required)',
        
        'GET /api/bookings' => 'Get user bookings (auth required)',
        'POST /api/bookings' => 'Create booking (auth required)',
        'GET /api/bookings/{id}' => 'Get booking by ID (auth required)',
        'DELETE /api/bookings/{id}' => 'Cancel booking (auth required)'
    ],
    'authentication' => [
        'type' => 'Bearer Token',
        'header' => 'Authorization: Bearer {token}',
        'note' => 'Get token from login or register endpoint'
    ]
]);
?>
