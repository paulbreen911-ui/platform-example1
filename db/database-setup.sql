-- Create database (run this first in PostgreSQL)
CREATE DATABASE my_website_db;

-- Connect to the database, then run the following:

-- Create users table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create an index for faster lookups
CREATE INDEX idx_username ON users(username);

-- Insert a demo user (username: testuser, password: password123)
INSERT INTO users (username, email, password) VALUES (
    'testuser',
    'testuser@example.com',
    '$2y$10$sampleHashedPassword' -- This hash represents 'password123'
);

-- To generate the proper hash, use this PHP code:
-- echo password_hash('password123', PASSWORD_BCRYPT);
-- Replace the hash above with the generated one
