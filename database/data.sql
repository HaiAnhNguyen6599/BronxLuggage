


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
('Travelpro Luxury Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 5, 1, 8, 351.98, 'female');
-- ('Briggs & Riley Luxury Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 6, 3, 4, 264.09, 'male', 40),
-- ('Samsonite Luxury Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 1, 1, 10, 131.13, 'male', 75),
-- ('Delsey Elite Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 4, 1, 2, 269.14, 'male', 56),
-- ('Travelpro Ultimate Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 5, 2, 11, 177.85, 'female', 80),
-- ('American Tourister Compact Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 2, 3, 3, 475.48, 'male', 42),
-- ('Tumi Compact Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 3, 2, 7, 138.63, 'male', 95),
-- ('Samsonite Luxury Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 1, 2, 4, 422.59, 'female', 18),
-- ('American Tourister Classic Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 2, 2, 13, 422.62, 'female', 20),
-- ('Travelpro Elite Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 5, 3, 15, 337.84, 'female', 86),
-- ('Travelpro Luxury Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 5, 3, 7, 302.08, 'female', 51),
-- ('Delsey Elite Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 4, 1, 8, 405.98, 'male', 64),
-- ('American Tourister Ultimate Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 2, 2, 13, 111.45, 'male', 39),
-- ('American Tourister Premium Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 2, 3, 3, 498.78, 'female', 54),
-- ('Delsey Traveler Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 4, 2, 1, 456.88, 'female', 89),
-- ('Delsey Traveler Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 4, 1, 3, 499.8, 'female', 69),
-- ('Tumi Ultimate Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 3, 2, 8, 465.2, 'male', 29),
-- ('Briggs & Riley Ultimate Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 6, 2, 10, 434.89, 'male', 71),
-- ('American Tourister Classic Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 2, 1, 3, 499.29, 'male', 63),
-- ('Tumi Classic Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 3, 1, 3, 227.87, 'female', 61),
-- ('Briggs & Riley Traveler Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 6, 1, 4, 310.72, 'female', 13),
-- ('Samsonite Smart Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 1, 1, 11, 392.28, 'male', 33),
-- ('Briggs & Riley Classic Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 6, 1, 1, 385.35, 'male', 16),
-- ('Travelpro Smart Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 5, 3, 8, 368.08, 'male', 67),
-- ('Samsonite Business Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 1, 2, 13, 221.68, 'male', 81),
-- ('American Tourister Classic Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 2, 1, 7, 341.15, 'female', 81),
-- ('Tumi Traveler Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 3, 1, 1, 136.18, 'male', 38),
-- ('Travelpro Premium Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 5, 3, 12, 132.86, 'male', 32),
-- ('Travelpro Elite Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 5, 3, 12, 492.16, 'female', 87),
-- ('Delsey Compact Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 4, 2, 2, 350.26, 'male', 72),
-- ('Tumi Luxury Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 3, 1, 7, 438.33, 'female', 28),
-- ('Briggs & Riley Smart Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 6, 2, 6, 280.91, 'female', 25),
-- ('American Tourister Pro Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 2, 2, 5, 149.88, 'male', 66),
-- ('Delsey Luxury Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 4, 3, 1, 485.19, 'female', 20),
-- ('Travelpro Premium Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 5, 1, 3, 404.36, 'female', 83),
-- ('Tumi Elite Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 3, 2, 12, 67.74, 'female', 25),
-- ('Briggs & Riley Compact Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 6, 1, 7, 148.03, 'female', 91),
-- ('American Tourister Smart Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 2, 2, 10, 185.39, 'male', 31),
-- ('Samsonite Smart Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 1, 2, 14, 165.3, 'male', 15),
-- ('American Tourister Elite Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 2, 3, 12, 223.95, 'male', 45),
-- ('Travelpro Compact Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 5, 2, 4, 175.47, 'male', 44),
-- ('Tumi Premium Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 3, 1, 6, 159.79, 'female', 42),
-- ('Samsonite Elite Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 1, 3, 2, 84.59, 'female', 86),
-- ('Briggs & Riley Elite Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 6, 2, 13, 132.66, 'male', 16),
-- ('American Tourister Elite Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 2, 3, 11, 297.44, 'female', 76),
-- ('Delsey Ultimate Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 4, 2, 10, 74.26, 'male', 75),
-- ('American Tourister Smart Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 2, 2, 12, 216.26, 'male', 86),
-- ('Briggs & Riley Classic Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 6, 3, 4, 239.56, 'female', 79),
-- ('Delsey Luxury Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 4, 1, 2, 82.04, 'male', 44),
-- ('Samsonite Premium Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 1, 2, 6, 65.94, 'female', 36),
-- ('Delsey Traveler Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 4, 3, 8, 257.44, 'female', 60),
-- ('Samsonite Traveler Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 1, 1, 11, 398.28, 'male', 61),
-- ('Samsonite Traveler Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 1, 3, 11, 463.39, 'male', 47),
-- ('American Tourister Luxury Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 2, 1, 2, 62.12, 'male', 26),
-- ('American Tourister Luxury Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 2, 3, 11, 463.14, 'female', 41),
-- ('Tumi Business Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 3, 2, 11, 98.88, 'female', 63),
-- ('Briggs & Riley Business Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 6, 2, 7, 316.7, 'female', 74),
-- ('Travelpro Classic Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 5, 3, 9, 337.76, 'male', 47),
-- ('Briggs & Riley Pro Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 6, 2, 1, 68.22, 'female', 74),
-- ('Delsey Ultimate Suitcases', 'A high-quality suitcases designed for durability and style.', 2, 4, 1, 14, 488.83, 'male', 67),
-- ('American Tourister Smart Tote Bags', 'A high-quality tote bags designed for durability and style.', 4, 2, 3, 12, 57.02, 'male', 85),
-- ('Briggs & Riley Traveler Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 6, 2, 13, 180.92, 'male', 36),
-- ('Tumi Compact Duffel Bags', 'A high-quality duffel bags designed for durability and style.', 3, 3, 3, 10, 306.88, 'female', 71),
-- ('Briggs & Riley Luxury Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 6, 3, 10, 302.6, 'male', 90),
-- ('Tumi Business Briefcases', 'A high-quality briefcases designed for durability and style.', 6, 3, 1, 4, 164.51, 'female', 97),
-- ('Delsey Smart Backpacks', 'A high-quality backpacks designed for durability and style.', 1, 4, 3, 13, 139.5, 'female', 22),
-- ('Tumi Ultimate Messenger Bags', 'A high-quality messenger bags designed for durability and style.', 5, 3, 1, 4, 190.33, 'male', 77);


-- Insert images
INSERT INTO product_images (product_id, image_url, is_primary) VALUES
(1, 'img/products/product1.jpg', true),
(1, 'img/products/product1.1.jpg', false),
(1, 'img/products/product1.2.jpg', false);

-- Insert feedback
-- INSERT INTO feedback (user_id, product_id, message, rating) VALUES
-- (1, 1, 'Really sturdy suitcase, perfect for long trips!', 5),
-- (1, 1, 'This backpack looks sleek, but the padding could be better.', 3),
-- (1, 1, 'Fantastic briefcase, super professional vibe.', 5),
-- (1, 1, 'Tote bag is stylish but a little small for my needs.', 4),
-- (1, 1, 'Briefcase holds up well, loving the compartments.', 5),
-- (1, 1, 'Messenger bag is great, but the strap wears out fast.', 3);

