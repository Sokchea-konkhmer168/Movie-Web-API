-- Movie Ticket Booking Database Schema
-- Based on the ER diagram requirements

CREATE DATABASE IF NOT EXISTS movie_ticket_booking;
USE movie_ticket_booking;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Movies table
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    rating VARCHAR(10),
    duration INT NOT NULL, -- in minutes
    release_date DATE,
    description TEXT,
    poster_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Cinemas table
CREATE TABLE cinemas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    number_of_halls INT NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Halls table
CREATE TABLE halls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hall_number VARCHAR(10) NOT NULL,
    capacity INT NOT NULL,
    cinema_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cinema_id) REFERENCES cinemas(id) ON DELETE CASCADE
);

-- Shows table
CREATE TABLE shows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    hall_id INT NOT NULL,
    show_time DATETIME NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    available_seats INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    show_id INT NOT NULL,
    seat_number VARCHAR(10) NOT NULL,
    booking_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    total_price DECIMAL(8,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (show_id) REFERENCES shows(id) ON DELETE CASCADE,
    UNIQUE KEY unique_seat_show (show_id, seat_number)
);

-- Insert sample data
INSERT INTO movies (title, genre, rating, duration, release_date, description) VALUES
('The Dark Knight', 'Action', 'PG-13', 152, '2008-07-18', 'Batman faces the Joker in Gotham City'),
('Inception', 'Sci-Fi', 'PG-13', 148, '2010-07-16', 'A thief enters people\'s dreams'),
('Avengers: Endgame', 'Action', 'PG-13', 181, '2019-04-26', 'The Avengers face Thanos'),
('Spider-Man: No Way Home', 'Action', 'PG-13', 148, '2021-12-17', 'Spider-Man faces villains from across the multiverse'),
('Black Panther', 'Action', 'PG-13', 134, '2018-02-16', 'T\'Challa becomes the new Black Panther and king of Wakanda'),
('Thor: Ragnarok', 'Action', 'PG-13', 130, '2017-11-03', 'Thor must escape Sakaar to save Asgard from Hela'),
('Iron Man', 'Action', 'PG-13', 126, '2008-05-02', 'Tony Stark becomes the armored superhero Iron Man'),
('Captain America: The Winter Soldier', 'Action', 'PG-13', 136, '2014-04-04', 'Steve Rogers uncovers a conspiracy within S.H.I.E.L.D.'),
('Guardians of the Galaxy', 'Action', 'PG-13', 121, '2014-08-01', 'A group of intergalactic misfits become unlikely heroes'),
('Doctor Strange', 'Action', 'PG-13', 115, '2016-11-04', 'A neurosurgeon discovers the world of magic and alternate dimensions'),
('Captain Marvel', 'Action', 'PG-13', 123, '2019-03-08', 'Carol Danvers becomes one of the universe\'s most powerful heroes'),
('Ant-Man', 'Action', 'PG-13', 117, '2015-07-17', 'Scott Lang becomes the size-changing superhero Ant-Man'),
('The Avengers', 'Action', 'PG-13', 143, '2012-05-04', 'Earth\'s mightiest heroes unite to stop Loki\'s invasion'),
('Avengers: Infinity War', 'Action', 'PG-13', 149, '2018-04-27', 'The Avengers fight to stop Thanos from collecting the Infinity Stones'),
('Black Widow', 'Action', 'PG-13', 134, '2021-07-09', 'Natasha Romanoff confronts her past as a spy'),
('Spider-Man: Homecoming', 'Action', 'PG-13', 133, '2017-07-07', 'Peter Parker balances being Spider-Man with high school life'),
('Thor: Love and Thunder', 'Action', 'PG-13', 119, '2022-07-08', 'Thor teams up with Jane Foster to stop Gorr the God Butcher'),
('Eternals', 'Action', 'PG-13', 156, '2021-11-05', 'Ancient beings emerge to protect Earth from the Deviants'),
('Shang-Chi and the Legend of the Ten Rings', 'Action', 'PG-13', 132, '2021-09-03', 'Shang-Chi must face his past and the Ten Rings organization');

INSERT INTO cinemas (name, location, number_of_halls, address) VALUES
('Legend ToulKok', 'ToulKok', 8, 'ToulKok Market Area, Phnom Penh'),
('Legend Eden Garden', 'Eden Garden', 10, 'Eden Garden Mall, Phnom Penh'),
('Legend Funmall', 'Funmall', 6, 'Funmall Shopping Center, Phnom Penh'),
('Legend Olympia', 'Olympia', 12, 'Olympia Mall, Phnom Penh'),
('Legend Sihanuk Ville', 'Sihanuk Ville', 8, 'Sihanuk Ville City Center'),
('Legend K Mall', 'K Mall', 10, 'K Mall Shopping Complex, Phnom Penh');

INSERT INTO halls (hall_number, capacity, cinema_id) VALUES
-- Legend ToulKok (cinema_id: 1)
('Hall-1', 120, 1),
('Hall-2', 150, 1),
('Hall-3', 100, 1),
('Hall-4', 180, 1),
-- Legend Eden Garden (cinema_id: 2)
('Hall-1', 200, 2),
('Hall-2', 150, 2),
('Hall-3', 120, 2),
('Hall-4', 100, 2),
('Hall-5', 180, 2),
-- Legend Funmall (cinema_id: 3)
('Hall-1', 130, 3),
('Hall-2', 160, 3),
('Hall-3', 110, 3),
-- Legend Olympia (cinema_id: 4)
('Hall-1', 250, 4),
('Hall-2', 200, 4),
('Hall-3', 180, 4),
('Hall-4', 150, 4),
('Hall-5', 120, 4),
('Hall-6', 100, 4),
-- Legend Sihanuk Ville (cinema_id: 5)
('Hall-1', 140, 5),
('Hall-2', 160, 5),
('Hall-3', 120, 5),
('Hall-4', 100, 5),
-- Legend K Mall (cinema_id: 6)
('Hall-1', 190, 6),
('Hall-2', 170, 6),
('Hall-3', 150, 6),
('Hall-4', 130, 6),
('Hall-5', 110, 6);

-- Insert sample users
INSERT INTO users (name, email, password, phone, role) VALUES
('Kun Bro Heng', 'RkunBroHeng@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '012345678', 'user'),
('Kai Sloy', 'kaiSloy@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '012345679', 'user'),
('Vin Cute Boy', 'Vincuteboy@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '012345680', 'user'),
('Yaya Peng Pos', 'YayaPengPos@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '012345681', 'user'),
('Dom Aut', 'DomAut@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '012345682', 'admin'),
('Ramey', 'Ramey@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '012345683', 'user');

-- Note: All sample users have the default password: "password123"

INSERT INTO shows (movie_id, hall_id, show_time, price, available_seats) VALUES
-- Shows at Legend ToulKok (halls 1-4)
(1, 1, '2024-01-15 14:00:00', 12.50, 120), -- The Dark Knight
(2, 2, '2024-01-15 15:30:00', 13.00, 150), -- Inception
(3, 3, '2024-01-15 19:00:00', 16.00, 100), -- Avengers: Endgame
(13, 4, '2024-01-15 20:30:00', 15.50, 180), -- Spider-Man: No Way Home

-- Shows at Legend Eden Garden (halls 5-9)
(12, 5, '2024-01-16 14:00:00', 15.00, 200), -- Black Panther
(11, 6, '2024-01-16 16:30:00', 14.50, 150), -- Thor: Ragnarok
(10, 7, '2024-01-16 19:00:00', 16.00, 120), -- Iron Man
(9, 8, '2024-01-16 21:30:00', 15.00, 100), -- Guardians of the Galaxy
(8, 9, '2024-01-17 14:00:00', 14.00, 180), -- Doctor Strange

-- Shows at Legend Funmall (halls 10-12)
(7, 10, '2024-01-17 15:30:00', 13.50, 130), -- Captain Marvel
(6, 11, '2024-01-17 18:00:00', 14.50, 160), -- Ant-Man
(5, 12, '2024-01-17 20:30:00', 16.00, 110), -- The Avengers

-- Shows at Legend Olympia (halls 13-18)
(4, 13, '2024-01-18 14:00:00', 17.00, 250), -- Avengers: Infinity War - Premium Hall
(3, 14, '2024-01-18 16:30:00', 16.50, 200), -- Avengers: Endgame
(13, 15, '2024-01-18 19:00:00', 15.50, 180), -- Spider-Man: No Way Home
(12, 16, '2024-01-18 21:30:00', 15.00, 150), -- Black Panther
(11, 17, '2024-01-19 14:30:00', 14.50, 120), -- Thor: Ragnarok
(10, 18, '2024-01-19 17:00:00', 14.00, 100), -- Iron Man

-- Shows at Legend Sihanuk Ville (halls 19-22)
(9, 19, '2024-01-19 15:00:00', 13.50, 140), -- Guardians of the Galaxy
(8, 20, '2024-01-19 18:00:00', 14.00, 160), -- Doctor Strange
(7, 21, '2024-01-19 20:30:00', 13.50, 120), -- Captain Marvel
(6, 22, '2024-01-20 14:00:00', 13.00, 100), -- Ant-Man

-- Shows at Legend K Mall (halls 23-27)
(5, 23, '2024-01-20 16:30:00', 15.50, 190), -- The Avengers
(4, 24, '2024-01-20 19:00:00', 16.50, 170), -- Avengers: Infinity War
(3, 25, '2024-01-20 21:30:00', 16.00, 150), -- Avengers: Endgame
(2, 26, '2024-01-21 14:00:00', 13.00, 130), -- Inception
(1, 27, '2024-01-21 17:00:00', 12.50, 110); -- The Dark Knight
