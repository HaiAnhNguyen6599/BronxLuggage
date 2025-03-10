-- Insert Brands
INSERT INTO brands (name) VALUES
('Samsonite'),
('American Tourister'),
('Tumi'),
('Delsey'),
('Travelpro'),
('Briggs & Riley');

-- Insert Categories
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
INSERT INTO colors (name) VALUES ('Black'), ('Blue'), ('Red'), ('Gray');


-- Insert Product
INSERT INTO products (name, description, category_id, brand_id, size_id, color_id, price, gender, inventory) VALUES
	-- Backpacks
('Samsonite Business Backpack', 'A sleek and professional backpack for business travelers.', 1, 1, 2, 1, 129.99, 'male', 50),
('American Tourister Casual Backpack', 'Lightweight and stylish backpack for daily use.', 1, 2, 2, 2, 79.99, 'female', 40),
('Tumi Travel Backpack', 'Premium backpack with multiple compartments.', 1, 3, 3, 3, 199.99, 'male', 30),
('Delsey Laptop Backpack', 'Durable backpack with laptop protection.', 1, 4, 2, 4, 89.99, 'female', 25),
	-- Suitcases
('Samsonite Hardshell Suitcase', 'High-quality suitcase for long trips.', 2, 1, 3, 1, 299.99, 'male', 20),
('American Tourister Spinner Suitcase', 'Easy to maneuver suitcase for frequent travelers.', 2, 2, 3, 2, 199.99, 'female', 30),
('Tumi Carry-On Suitcase', 'Luxury carry-on for business professionals.', 2, 3, 2, 3, 399.99, 'male', 15),
('Delsey Expandable Suitcase', 'Spacious and expandable suitcase.', 2, 4, 3, 4, 249.99, 'female', 18),
('Travelpro Lightweight Suitcase', 'Light and durable suitcase.', 2, 5, 3, 1, 179.99, 'male', 25),
	-- Duffel Bags
('Briggs & Riley Duffel Bag', 'Stylish and functional duffel bag.', 3, 6, 2, 2, 149.99, 'male', 40),
('Samsonite Travel Duffel', 'Perfect for weekend getaways.', 3, 1, 3, 3, 119.99, 'female', 35),
('American Tourister Gym Duffel', 'Ideal for gym and travel.', 3, 2, 2, 4, 89.99, 'male', 50),
('Tumi Large Duffel', 'Premium large duffel bag.', 3, 3, 3, 1, 299.99, 'female', 10),
	-- Tote Bags
('Delsey Casual Tote', 'Lightweight tote bag for everyday use.', 4, 4, 2, 2, 79.99, 'female', 45),
('Travelpro Business Tote', 'Professional tote bag for office.', 4, 5, 2, 3, 99.99, 'female', 30),
('Briggs & Riley Leather Tote', 'Luxury tote with premium leather.', 4, 6, 3, 4, 199.99, 'female', 20),
	-- Messenger Bags
('Samsonite Messenger Bag', 'Classic messenger bag for professionals.', 5, 1, 2, 1, 129.99, 'male', 35),
('American Tourister Sling Messenger', 'Compact and stylish sling messenger.', 5, 2, 1, 2, 79.99, 'male', 40),
('Tumi Executive Messenger', 'Premium messenger for business executives.', 5, 3, 3, 3, 249.99, 'male', 15),
	-- Briefcases
('Delsey Business Briefcase', 'Elegant and durable briefcase.', 6, 4, 2, 4, 179.99, 'male', 25),
('Briggs & Riley Expandable Briefcase', 'Spacious briefcase with expandable storage.', 6, 6, 3, 1, 299.99, 'male', 20),
('Briggs & Riley Briefcase', 'Spacious briefcase with expandable storage.', 6, 6, 3, 1, 299.99, 'male', 20);

-- Insert users
INSERT INTO users (name, email, password, role) VALUES
('Alice Johnson', 'alice@example.com', 'hashed_password_1', 'customer'),
('Bob Smith', 'bob@example.com', 'hashed_password_2', 'customer'),
('Charlie Brown', 'charlie@example.com', 'hashed_password_3', 'customer');

-- Insert feedback
INSERT INTO feedback (user_id, product_id, message, rating) VALUES
(1, 3, 'Great quality backpack, very comfortable!', 5),
(2, 5, 'Decent suitcase but a bit heavy.', 4),
(3, 7, 'Love the design and durability.', 5),
(1, 2, 'Straps could be more comfortable.', 3),
(2, 10, 'Spacious and stylish, worth the price.', 5),
(3, 12, 'Zipper broke after a few weeks.', 2),
(1, 8, 'Perfect for travel, highly recommend.', 5),
(2, 15, 'Material feels a bit cheap.', 3),
(3, 18, 'Exceeded my expectations!', 5),
(1, 20, 'Good but not waterproof.', 4);

-- Inser image
-- Thêm hình ảnh cho products (mỗi sản phẩm có 1 ảnh chính)
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(1, 'img/products/product1.jpg', 1),
(2, 'img/products/product2.jpg', 1),
(3, 'img/products/product3.jpg', 1),
(4, 'img/products/product4.jpg', 1),
(5, 'img/products/product5.jpg', 1),
(6, 'img/products/product6.jpg', 1),
(7, 'img/products/product7.jpg', 1),
(8, 'img/products/product8.jpg', 1),
(9, 'img/products/product9.jpg', 1),
(10, 'img/products/product10.jpg', 1),
(11, 'img/products/product11.jpg', 1),
(12, 'img/products/product12.jpg', 1),
(13, 'img/products/product13.jpg', 1),
(14, 'img/products/product14.jpg', 1),
(15, 'img/products/product15.jpg', 1),
(16, 'img/products/product16.jpg', 1),
(17, 'img/products/product17.jpg', 1),
(18, 'img/products/product18.jpg', 1),
(19, 'img/products/product19.jpg', 1),
(20, 'img/products/product20.jpg', 1);

