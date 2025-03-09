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

-- Cho phép size_id và color_id có thể NULL trong product_variants
ALTER TABLE product_variants 
MODIFY COLUMN size_id INT NULL,
MODIFY COLUMN color_id INT NULL;

-- Đảm bảo total_price trong orders tự động cập nhật từ order_items
ALTER TABLE orders 
DROP COLUMN total_price;


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

-- Insert Role
INSERT INTO roles (id, name) VALUES
(1, 'Customer');

-- Insert User
INSERT INTO users (name, email, password, phone, address, city, role_id) VALUES
('John Doe', 'john.doe@example.com', 'hashed_password_1', '123-456-7890', '123 Main St', 'New York', 1),
('Jane Smith', 'jane.smith@example.com', 'hashed_password_2', '234-567-8901', '456 Elm St', 'Los Angeles', 1),
('Michael Brown', 'michael.brown@example.com', 'hashed_password_3', '345-678-9012', '789 Oak St', 'Chicago', 1),
('Emily Davis', 'emily.davis@example.com', 'hashed_password_4', '456-789-0123', '321 Pine St', 'Houston', 1),
('David Wilson', 'david.wilson@example.com', 'hashed_password_5', '567-890-1234', '654 Maple St', 'Miami', 1),
('Sophia Johnson', 'sophia.johnson@example.com', 'hashed_password_6', '678-901-2345', '987 Cedar St', 'Seattle', 1),
('Daniel Martinez', 'daniel.martinez@example.com', 'hashed_password_7', '789-012-3456', '159 Birch St', 'San Francisco', 1),
('Olivia Taylor', 'olivia.taylor@example.com', 'hashed_password_8', '890-123-4567', '753 Willow St', 'Denver', 1),
('Admin One', 'admin1@example.com', 'hashed_password_9', '901-234-5678', 'Admin HQ', 'Washington', 1),
('Admin Two', 'admin2@example.com', 'hashed_password_10', '012-345-6789', 'Admin HQ', 'Boston', 1);



-- insert feedback data
INSERT INTO feedback (user_id, product_id, message, rating) VALUES
(1, 2, 'Amazing product! Very durable and stylish.', 5),
(2, 3, 'Good quality but the delivery took too long.', 3),
(3, 1, 'Exceeded my expectations. Highly recommended!', 5),
(4, 4, 'The product is okay, but could be improved.', 4),
(5, 2, 'Not worth the price. Disappointed.', 2),
(6, 5, 'Solid build and comfortable to use.', 4),
(7, 3, 'Fast shipping and excellent customer service.', 5),
(8, 1, 'Color slightly different than shown in pictures.', 3),
(9, 4, 'Great quality, will buy again!', 5),
(10, 5, 'Affordable and works as expected.', 4);


-- product cout by categories
SELECT c.id, c.name, COUNT(p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id, c.name
    order by c.id;


-- Product by gender and price
SELECT 
	p.id, p.name, 
	COUNT(p.id) AS product_count, 
	pv.price, 
	pi.image_url AS image,
    p.gender as gender
FROM products p
LEFT JOIN product_variants pv ON p.id = pv.product_id
LEFT JOIN product_images pi ON p.id = pi.product_id
WHERE p.gender = 'kids'
GROUP BY p.id, p.name, pv.price, pi.image_url
ORDER BY p.id;
            
-- Product by brands
SELECT DISTINCT b.id, b.name 
FROM brands b
JOIN products p ON b.id = p.brand_id
ORDER BY b.id ASC
        
-- Product with highest rating
SELECT 
    p.id, 
    p.name, 
    p.description, 
    p.brand_id,
    p.category_id,
    COALESCE(AVG(f.rating), 0) AS average_rating,
    COUNT(f.id) AS total_reviews,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image_url
FROM products p
LEFT JOIN feedback f ON p.id = f.product_id
GROUP BY p.id, p.name, p.description, p.brand_id, p.category_id
ORDER BY average_rating DESC, total_reviews DESC
LIMIT 8

-- Tính tổng giá trị đơn hàng
SELECT o.id, SUM(oi.quantity * oi.price) AS total_price
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;
 
-- Inser product image
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(1, 'img/products/product1_main.jpg', 1),  -- Ảnh chính
(1, 'img/products/product1_1.jpg', 0),
(1, 'img/products/product1_2.jpg', 0);

-- Chèn hình ảnh cho sản phẩm 2
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(2, 'img/products/product2_main.jpg', 1),  -- Ảnh chính
(2, 'img/products/product2_1.jpg', 0),
(2, 'img/products/product2_2.jpg', 0);

-- Chèn hình ảnh cho sản phẩm 3
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(3, 'img/products/product3_main.jpg', 1),  -- Ảnh chính
(3, 'img/products/product3_1.jpg', 0),
(3, 'img/products/product3_2.jpg', 0);

-- Chèn hình ảnh cho sản phẩm 4
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(4, 'img/products/product4_main.jpg', 1);  -- Ảnh chính

-- Chèn hình ảnh cho sản phẩm 5
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(5, 'img/products/product5_main.jpg', 1),  -- Ảnh chính
(5, 'img/products/product5_1.jpg', 0),
(5, 'img/products/product5_2.jpg', 0);

-- Chèn hình ảnh cho sản phẩm 6
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(6, 'img/products/product6_main.jpg', 1),  -- Ảnh chính
(6, 'img/products/product6_1.jpg', 0),
(6, 'img/products/product6_2.jpg', 0);

-- Chèn hình ảnh cho sản phẩm 7
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(7, 'img/products/product7_main.jpg', 1),  -- Ảnh chính
(7, 'img/products/product7_1.jpg', 0),
(7, 'img/products/product7_2.jpg', 0);

-- Chèn hình ảnh cho sản phẩm 8
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(8, 'img/products/product8_main.jpg', 1),  -- Ảnh chính
(8, 'img/products/product8_1.jpg', 0),
(8, 'img/products/product8_2.jpg', 0);
        