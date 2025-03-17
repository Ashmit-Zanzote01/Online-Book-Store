-- Create Database
--CREATE DATABASE shop_db;
--
--\c shop_db;

-- --------------------------------------------------------

-- Table structure for table `cart`
CREATE TABLE IF NOT EXISTS cart (
  id SERIAL PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  price INT NOT NULL,
  quantity INT NOT NULL,
  image VARCHAR(100) NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table `message`
CREATE TABLE IF NOT EXISTS message (
  id SERIAL PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  number VARCHAR(12) NOT NULL,
  message VARCHAR(500) NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table `orders`
CREATE TABLE IF NOT EXISTS orders (
  id SERIAL PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  number VARCHAR(12) NOT NULL,
  email VARCHAR(100) NOT NULL,
  method VARCHAR(50) NOT NULL,
  address VARCHAR(500) NOT NULL,
  total_products VARCHAR(1000) NOT NULL,
  total_price INT NOT NULL,
  placed_on VARCHAR(50) NOT NULL,
  payment_status VARCHAR(20) NOT NULL DEFAULT 'pending'
);

-- --------------------------------------------------------

-- Table structure for table `products`
CREATE TABLE IF NOT EXISTS products (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price INT NOT NULL,
  image VARCHAR(100) NOT NULL
);

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE IF NOT EXISTS users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(100) NOT NULL,
  user_type VARCHAR(20) NOT NULL DEFAULT 'user'
);

-- --------------------------------------------------------

-- Insert sample data into `products` table
INSERT INTO products (name, price, image) VALUES
('Product 1', 20, 'product1.jpg'),
('Product 2', 30, 'product2.jpg'),
('Product 3', 40, 'product3.jpg');

-- Insert sample data into `users` table
INSERT INTO users (name, email, password, user_type) VALUES
('User 1', 'user1@example.com', 'password1', 'user'),
('Admin', 'admin@example.com', 'password2', 'admin');
