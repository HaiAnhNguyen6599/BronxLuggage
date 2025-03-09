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
        