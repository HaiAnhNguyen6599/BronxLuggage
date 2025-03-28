-- Chèn một bản ghi tài khoản admin vào bảng users
INSERT INTO users (name, email, password, phone, address, city, role, created_at)
VALUES (
    'Admin User',                      -- Tên admin
    'admin@gmail.com',              -- Email admin
    '$2y$10$EdZCa15KVWnza0s1glX83.ndJ6UaWKyty1xWToLVKe4gzNgW0YGi2', -- Mật khẩu băm cho 'admin123'
    '0123456789',                     -- Số điện thoại (có thể thay đổi)
    '123 Admin Street',               -- Địa chỉ (có thể thay đổi)
    'Admin City',                     -- Thành phố (có thể thay đổi)
    'admin',                          -- Vai trò là admin
    NOW()                             -- Thời gian tạo là thời điểm hiện tại
),
('John Doe', 'johndoe@example.com', '$2y$10$b/Py0HLFd1/d.HHBo6r3Z.oucppMJ3MQ1qf/5h68TWQ6yUz7.4ZUC', '0987654321', '123 Main St', 'New York', 'customer', NOW()),
('Jane Smith', 'janesmith@example.com', '$2y$10$b/Py0HLFd1/d.HHBo6r3Z.oucppMJ3MQ1qf/5h68TWQ6yUz7.4ZUC', '0971234567', '456 Elm St', 'Los Angeles', 'customer', NOW()),
('Alice Johnson', 'alicejohnson@example.com', '$2y$10$b/Py0HLFd1/d.HHBo6r3Z.oucppMJ3MQ1qf/5h68TWQ6yUz7.4ZUC', '0962345678', '789 Oak St', 'Chicago', 'customer', NOW()),
('Bob Brown', 'bobbrown@example.com', '$2y$10$b/Py0HLFd1/d.HHBo6r3Z.oucppMJ3MQ1qf/5h68TWQ6yUz7.4ZUC', '0953456789', '321 Pine St', 'Houston', 'customer', NOW()),
('Charlie White', 'charliewhite@example.com', '$2y$10$b/Py0HLFd1/d.HHBo6r3Z.oucppMJ3MQ1qf/5h68TWQ6yUz7.4ZUC', '0944567890', '654 Cedar St', 'Phoenix', 'customer', NOW()),
('David Black', 'davidblack@example.com', '$2y$10$b/Py0HLFd1/d.HHBo6r3Z.oucppMJ3MQ1qf/5h68TWQ6yUz7.4ZUC', '0935678901', '987 Birch St', 'Philadelphia', 'customer', NOW()),
('Emma Green', 'emmagreen@example.com', '$2y$10$b/Py0HLFd1/d.HHBo6r3Z.oucppMJ3MQ1qf/5h68TWQ6yUz7.4ZUC', '0926789012', '741 Spruce St', 'San Antonio', 'customer', NOW()),
('Frank Harris', 'frankharris@example.com', '$2y$10$b/Py0HLFd1/d.HHBo6r3Z.oucppMJ3MQ1qf/5h68TWQ6yUz7.4ZUC', '0917890123', '852 Maple St', 'San Diego', 'customer', NOW()),
('Grace Lee', 'gracelee@example.com', '$2y$10$b/Py0HLFd1/d.HHBo6r3Z.oucppMJ3MQ1qf/5h68TWQ6yUz7.4ZUC', '0908901234', '963 Walnut St', 'Dallas', 'customer', NOW()),
('Henry Adams', 'henryadams@example.com', '$2y$10$b/Py0HLFd1/d.HHBo6r3Z.oucppMJ3MQ1qf/5h68TWQ6yUz7.4ZUC', '0899012345', '159 Redwood St', 'San Jose', 'customer', NOW());

-- Insert Brands- giữ nguyên
INSERT INTO brands (name) VALUES
('Samsonite'),
('American Tourister'),
('Tumi'),
('Delsey'),
('Travelpro'),
('Briggs & Riley');


-- Insert Categories- giữ nguyên
INSERT INTO categories (name) VALUES
('Backpacks'),
('Suitcases'),
('Duffel Bags'),
('Tote Bags'),
('Messenger Bags'),
('Briefcases');

-- Insert sizes
INSERT INTO sizes (name) VALUES ('Small'), ('Medium'), ('Large');

-- Insert colors
INSERT INTO colors (name) VALUES
('Black'),
('Blue'),
('Red'),
('Gray'),
('White'),
('Green'),
('Yellow'),
('Orange'),
('Purple'),
('Pink'),
('Brown'),
('Beige'),
('Navy'),
('Gold'),
('Silver');


INSERT INTO products (name, description, category_id, brand_id, size_id, color_id, price, gender) VALUES
('Tumi Business Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 3, 2, 3, 459.84, 'male'),
('Travelpro Business Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 5, 1, 4, 181.28, 'male'),
('Briggs & Riley Premium Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 6, 1, 9, 236.38, 'female'),
('Tumi Elite Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 3, 1, 15, 424.62, 'female'),
('American Tourister Pro Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 2, 2, 8, 131.4, 'male'),
('Delsey Compact Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 4, 3, 11, 466.14, 'male'),
('Briggs & Riley Traveler Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 6, 1, 6, 426.37, 'female'),
('Samsonite Traveler Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 1, 1, 6, 273.61, 'female'),
('Briggs & Riley Ultimate Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 6, 3, 9, 228.64, 'female'),
('Tumi Luxury Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 3, 2, 10, 132.31, 'male'),
('American Tourister Compact Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 2, 3, 12, 460.66, 'male'),
('Tumi Pro Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 3, 2, 9, 379.12, 'male'),
('Briggs & Riley Smart Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 6, 2, 12, 111.83, 'male'),
('Briggs & Riley Smart Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 6, 2, 9, 123.18, 'male'),
('Briggs & Riley Traveler Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 6, 3, 5, 403.7, 'female'),
('Samsonite Classic Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 1, 1, 11, 183.69, 'female'),
('Briggs & Riley Smart Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 6, 3, 3, 465.28, 'male'),
('Tumi Business Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 3, 3, 9, 367.38, 'female'),
('Delsey Ultimate Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 4, 3, 7, 258.03, 'male'),
('Delsey Classic Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 4, 3, 1, 313.83, 'female'),
('Samsonite Luxury Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 1, 3, 8, 321.78, 'male'),
('Tumi Elite Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 3, 2, 14, 238.6, 'male'),
('American Tourister Smart Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 2, 2, 4, 163.45, 'male'),
('Samsonite Business Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 1, 3, 4, 499.78, 'female'),
('American Tourister Pro Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 2, 3, 5, 380.56, 'male'),
('Delsey Pro Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 4, 2, 2, 222.81, 'male'),
('Samsonite Business Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 1, 3, 2, 352.23, 'female'),
('Briggs & Riley Smart Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 6, 3, 8, 237.59, 'male'),
('Samsonite Elite Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 1, 1, 1, 329.59, 'male'),
('Delsey Compact Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 4, 1, 8, 462.12, 'male'),
('Tumi Luxury Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 3, 1, 4, 308.49, 'female'),
('American Tourister Classic Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 2, 1, 4, 428.32, 'female'),
('Tumi Business Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 3, 2, 2, 448.57, 'male'),
('Travelpro Luxury Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 5, 1, 8, 351.98, 'female'),
('Briggs & Riley Smart Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 6, 2, 1, 542.69, 'female'),
('Delsey Elite Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 4, 1, 2, 99.99, 'male'),
('Tumi Elite Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 3, 2, 12, 48.99, 'female');



-- Insert images
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(1, 'img/products/product1.jpg', true),
(1, 'img/products/product1.1.jpg', false),
(1, 'img/products/product1.2.jpg', false),
--
(2, 'img/products/product2.jpg', true),
(2, 'img/products/product2.1.jpg', false),
(2, 'img/products/product2.2.jpg', false),
--
(3, 'img/products/product3.jpg', true),
(3, 'img/products/product3.1.jpg', false),
(3, 'img/products/product3.2.jpg', false),

(4, 'img/products/product4.jpg', true),
(4, 'img/products/product4.1.jpg', false),
(4, 'img/products/product4.2.jpg', false),
--
(5, 'img/products/product5.jpg', true),
(5, 'img/products/product5.1.jpg', false),
(5, 'img/products/product5.2.jpg', false),
--
(6, 'img/products/product6.jpg', true),
(6, 'img/products/product6.1.jpg', false),
(6, 'img/products/product6.2.jpg', false),
--
(7, 'img/products/product7.jpg', true),
(7, 'img/products/product7.1.jpg', false),
(7, 'img/products/product7.2.jpg', false),

(8, 'img/products/product8.jpg', true),
(8, 'img/products/product8.1.jpg', false),
(8, 'img/products/product8.2.jpg', false),
--
(9, 'img/products/product9.jpg', true),
(10, 'img/products/product10.jpg', true),
(11, 'img/products/product11.jpg', true),
(12, 'img/products/product12.jpg', true),
(13, 'img/products/product13.jpg', true),
(14, 'img/products/product14.jpg', true),
(15, 'img/products/product15.jpg', true),
(16, 'img/products/product16.jpg', true),
(17, 'img/products/product17.jpg', true),
(18, 'img/products/product18.jpg', true),
(19, 'img/products/product19.jpg', true),
(20, 'img/products/product20.jpg', true),
(21, 'img/products/product21.jpg', true),
(22, 'img/products/product22.jpg', true),
(23, 'img/products/product23.jpg', true),
(24, 'img/products/product24.jpg', true),
(25, 'img/products/product25.jpg', true),
(26, 'img/products/product26.jpg', true),
(27, 'img/products/product27.jpg', true),
(28, 'img/products/product28.jpg', true),
(29, 'img/products/product29.jpg', true),
(30, 'img/products/product30.jpg', true),
(31, 'img/products/product31.jpg', true),
(32, 'img/products/product32.jpg', true),
(33, 'img/products/product33.jpg', true),
(34, 'img/products/product34.jpg', true),
(35, 'img/products/product35.jpg', true),
(36, 'img/products/product36.jpg', true),
(37, 'img/products/product37.jpg', true);

-- Insert feedback


INSERT INTO feedback (user_id, product_id, message, rating) VALUES
(1, 1, 'Really sturdy suitcase, perfect for long trips!', 5),
(1, 1, 'This backpack looks sleek, but the padding could be better.', 3),
(1, 1, 'Fantastic briefcase, super professional vibe.', 5),
(1, 1, 'Tote bag is stylish but a little small for my needs.', 4),
(1, 1, 'Briefcase holds up well, loving the compartments.', 5),
(1, 1, 'Messenger bag is great, but the strap wears out fast.', 3),
-- Product 1
(2, 1, 'Great quality, highly recommend!', 4),
(3, 1, 'Spacious and durable.', 5),
(4, 1, 'Not as expected, but still okay.', 3),
(5, 1, 'A must-have for frequent travelers!', 5),
(6, 1, 'Zippers could be better.', 4),
(7, 1, 'Very lightweight and easy to carry.', 5),
(8, 1, 'Color fades a bit after a while.', 3),
(9, 1, 'Worth every penny.', 5),
(10, 1, 'Handles feel a bit flimsy.', 3),
(1, 1, 'Perfect size for weekend trips.', 4),
(2, 1, 'Highly functional and stylish.', 5),
(3, 1, 'Had an issue with the wheels.', 3),
(4, 1, 'Super spacious inside.', 4),
(5, 1, 'Locks work great.', 5),
(6, 1, 'Material feels premium.', 5),
(7, 1, 'Could use more compartments.', 4),
(8, 1, 'Very satisfied with the purchase.', 5),
(9, 1, 'The design is really modern.', 4),
(10, 1, 'Would buy again.', 5),

-- Product 2
(1, 2, 'Affordable and reliable!', 5),
(2, 2, 'Not as strong as I thought.', 3),
(3, 2, 'Good value for money.', 4),
(4, 2, 'Perfect for short trips.', 5),
(5, 2, 'Material is okay but scratches easily.', 3),
(6, 2, 'Lightweight and stylish.', 4),
(7, 2, 'Zippers broke after a few uses.', 2),
(8, 2, 'Amazing suitcase for the price.', 5),
(9, 2, 'Could use a better lock.', 3),
(10, 2, 'Very spacious and strong.', 5),
(1, 2, 'Wheels roll very smoothly.', 5),
(2, 2, 'I love the color!', 4),
(3, 2, 'Handles are not sturdy.', 3),
(4, 2, 'Highly functional and lightweight.', 5),
(5, 2, 'Great for frequent travelers.', 5),
(6, 2, 'Zipper got stuck after some time.', 3),
(7, 2, 'Decent quality but overpriced.', 3),
(8, 2, 'Perfect for my needs.', 5),
(9, 2, 'Would definitely buy again.', 5),
(10, 2, 'The compartments are very useful.', 4),

-- Product 3
(1, 3, 'Well-built and durable.', 5),
(2, 3, 'Scratches easily, but still good.', 3),
(3, 3, 'Smooth rolling wheels.', 5),
(4, 3, 'Great suitcase at this price.', 4),
(5, 3, 'Material feels solid.', 5),
(6, 3, 'Not too heavy, which I like.', 4),
(7, 3, 'Spacious and practical.', 5),
(8, 3, 'Had an issue with the handle.', 3),
(9, 3, 'Good for domestic travel.', 4),
(10, 3, 'Very easy to maneuver.', 5),
(1, 3, 'Affordable and durable.', 4),
(2, 3, 'Handles feel a bit weak.', 3),
(3, 3, 'Perfect for my needs.', 5),
(4, 3, 'Nice design, very modern.', 4),
(5, 3, 'Locks work well.', 5),
(6, 3, 'Good suitcase overall.', 4),
(7, 3, 'Decent but expected more.', 3),
(8, 3, 'Great deal for the price.', 5),
(9, 3, 'Would purchase again.', 5),
(10, 3, 'Very spacious inside.', 5);


-- Inser Orders
INSERT INTO orders (user_id, status, created_at)
SELECT 
    user_id,
    CASE FLOOR(RAND() * 5)  -- Chọn status ngẫu nhiên từ ENUM
        WHEN 0 THEN 'pending'
        WHEN 1 THEN 'processing'
        WHEN 2 THEN 'shipped'
        WHEN 3 THEN 'delivered'
        ELSE 'cancelled'
    END AS status,
    NOW() AS created_at
FROM (
    SELECT 1 AS user_id UNION ALL SELECT 1 UNION ALL SELECT 1 UNION ALL 
    SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1 UNION ALL 
    SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1 UNION ALL SELECT 1 UNION ALL 
    SELECT 2 UNION ALL SELECT 2 UNION ALL SELECT 2 UNION ALL 
    SELECT 2 UNION ALL SELECT 2 UNION ALL SELECT 2 UNION ALL 
    SELECT 2 UNION ALL SELECT 2 UNION ALL SELECT 2 UNION ALL SELECT 2 UNION ALL 
    SELECT 3 UNION ALL SELECT 3 UNION ALL SELECT 3 UNION ALL 
    SELECT 3 UNION ALL SELECT 3 UNION ALL SELECT 3 UNION ALL 
    SELECT 3 UNION ALL SELECT 3 UNION ALL SELECT 3 UNION ALL SELECT 3
) AS users;

-- Inser Order items
INSERT INTO order_items (order_id, product_id, quantity, price)
SELECT 
    o.id AS order_id,
    p.id AS product_id,
    GREATEST(1, FLOOR(1 + (RAND() * 2))) AS quantity, -- Đảm bảo số lượng ít nhất là 1
    LEAST(ROUND(p.price * GREATEST(1, FLOOR(1 + (RAND() * 2))), 2), 99999999.99) AS price -- Đảm bảo price hợp lệ
FROM orders o
JOIN products p ON p.id BETWEEN 1 AND 8 -- Chỉ lấy product_id từ 1-8
WHERE o.user_id IN (1, 2, 3)
ORDER BY RAND();







