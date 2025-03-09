-- Xóa database cũ nếu tồn tại và tạo mới
DROP DATABASE IF EXISTS ecommerce;
CREATE DATABASE ecommerce;
USE ecommerce;

-- Bảng Roles (Vai trò người dùng)
CREATE TABLE roles (
    id TINYINT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Bảng Users (Người dùng)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(255),
    city VARCHAR(100),
    role_id TINYINT DEFAULT 1, -- 1: customer, 0: admin
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- Bảng Categories (Danh mục sản phẩm)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Bảng Brands (Thương hiệu)
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Bảng Sizes (Kích thước)
CREATE TABLE sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Bảng Colors (Màu sắc)
CREATE TABLE colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Bảng Products (Sản phẩm)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT NOT NULL,
    brand_id INT NOT NULL,
    gender ENUM('male', 'female', 'kids') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE CASCADE
);

-- Bảng Product Images (Hình ảnh sản phẩm)
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Bảng Product Variants (Sản phẩm có nhiều biến thể)
CREATE TABLE product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size_id INT NOT NULL,
    color_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (size_id) REFERENCES sizes(id) ON DELETE CASCADE,
    FOREIGN KEY (color_id) REFERENCES colors(id) ON DELETE CASCADE
);

-- Bảng Orders (Đơn hàng)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Bảng Order Items (Chi tiết đơn hàng)
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_variant_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
);

-- Bảng Cart (Giỏ hàng)
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_variant_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
);

-- Bảng Payment (Thanh toán)
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method ENUM('cod', 'bank_transfer', 'credit_card') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Bảng Feedback (Phản hồi sản phẩm)
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    message TEXT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert Categories
INSERT INTO categories (name) VALUES
('Backpacks'),
('Suitcases'),
('Duffel Bags'),
('Tote Bags'),
('Messenger Bags'),
('Briefcases');

-- Insert Brands
INSERT INTO brands (name) VALUES
('Samsonite'),
('American Tourister'),
('Tumi'),
('Delsey'),
('Travelpro'),
('Briggs & Riley');

-- Insert Sizes
INSERT INTO sizes (name) VALUES
('XS'), ('S'), ('M'), ('L'), ('XL');

-- Insert Colors
INSERT INTO colors (name) VALUES
('Black'), ('Blue'), ('Red'), ('Green'), ('Grey'), ('White'), ('Yellow');

-- Insert Products
INSERT INTO products (name, description, category_id, brand_id, gender) VALUES
-- Backpacks
('Samsonite Pro Backpack', 'Durable and spacious backpack for professionals.', 1, 1, 'male'),
('American Tourister Backpack', 'Stylish and lightweight backpack.', 1, 2, 'female'),
('Tumi Travel Backpack', 'High-end travel backpack.', 1, 3, 'male'),
('Delsey Compact Backpack', 'Compact and lightweight for daily use.', 1, 4, 'female'),
('Travelpro Adventure Backpack', 'Durable and water-resistant backpack.', 1, 5, 'male'),
('Briggs & Riley Business Backpack', 'Professional and spacious backpack.', 1, 6, 'male'),
-- Suitcases
('Samsonite Expandable Suitcase', 'Expandable and durable suitcase.', 2, 1, 'female'),
('American Tourister Hardcase', 'Hard shell suitcase with TSA lock.', 2, 2, 'female'),
('Tumi Luxury Suitcase', 'High-end suitcase for luxury travel.', 2, 3, 'female'),
('Delsey Ultra-Light Suitcase', 'Lightweight and sturdy suitcase.', 2, 4, 'female'),
-- Duffel Bags
('Tumi Alpha Duffel', 'Premium quality duffel bag for travel.', 3, 3, 'male'),
('Samsonite Travel Duffel', 'Durable duffel bag for long trips.', 3, 1, 'male'),
('Delsey Compact Duffel', 'Compact duffel bag with multiple compartments.', 3, 4, 'female'),
-- Tote Bags
('Travelpro Crew Tote', 'Stylish and practical tote bag.', 4, 5, 'female'),
('Briggs & Riley Large Tote', 'Spacious and durable tote bag.', 4, 6, 'female'),
-- Messenger Bags
('Samsonite Business Messenger', 'Professional messenger bag.', 5, 1, 'male'),
('Tumi Urban Messenger', 'Sleek and stylish messenger bag.', 5, 3, 'male'),
-- Briefcases
('Briggs & Riley Expandable Briefcase', 'Professional expandable briefcase.', 6, 6, 'male'),
('Tumi Executive Briefcase', 'Premium executive briefcase.', 6, 3, 'male');

-- Insert Product Variants
INSERT INTO product_variants (product_id, size_id, color_id, quantity, price)
SELECT p.name, s.name as size, c.name as color, FLOOR(RAND() * 50 + 10), ROUND(RAND() * 100 + 50, 2)
FROM products p
JOIN sizes s ON (p.gender = 'kids' AND s.name IN ('XS', 'S', 'M') OR 
                 p.gender = 'male' AND s.name IN ('M', 'L', 'XL') OR 
                 p.gender = 'female' AND s.name IN ('M', 'L', 'XL'))
JOIN colors c ON 1=1
ORDER BY RAND()
LIMIT 200;

-- insert feedback data
INSERT INTO feedback (user_id, product_id, rating, comment) VALUES
(1, 2, 4.5, 'Great product, very satisfied!'),
(2, 3, 3.8, 'Good quality, but shipping was slow.'),
(3, 1, 5.0, 'Absolutely amazing! Will buy again.'),
(4, 4, 4.2, 'Matches the description, reasonable price.'),
(5, 2, 2.5, 'Quality is below expectations, needs improvement.'),
(6, 5, 4.0, 'Decent product, worth the price.'),
(7, 3, 4.8, 'Fast delivery, well-packaged.'),
(8, 1, 3.5, 'Color is slightly different from the image, but quality is fine.'),
(9, 4, 5.0, 'Authentic product, highly recommended!'),
(10, 5, 4.3, 'Works well, I will recommend it to friends.');

-- product cout by categories
SELECT c.id, c.name, COUNT(p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id, c.name
    order by c.id;


-- Prudct by gender and price
SELECT p.id, p.name, 
                   COUNT(p.id) AS product_count, 
                   pv.price, 
                   pi.image_url AS image
            FROM products p
            LEFT JOIN product_variants pv ON p.id = pv.product_id
            LEFT JOIN product_images pi ON p.id = pi.product_id
            WHERE p.gender = 'male'
            GROUP BY p.id, p.name, pv.price, pi.image_url
            ORDER BY p.id
            
-- Priduct by brands
 SELECT DISTINCT b.id, b.name 
        FROM brands b
        JOIN products p ON b.id = p.brand_id
        ORDER BY b.id ASC
 
        