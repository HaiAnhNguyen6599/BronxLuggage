-- Insert data giả định giỏ hàng 
INSERT INTO cart (user_id, product_id, quantity, created_at)  
VALUES 
(1, 19, 1, NOW()),
(1, 5, 5, NOW()),
(1, 1, 6, NOW()),
(1, 3, 1, NOW()),
(1, 2, 9, NOW());

-- Check giỏ hàng
select * from cart;

-- Check giả định order_item
-- select * from order_items where order_id = 3;


-- Check giả định đơn hàng đã được đặt thành công
SELECT o.id AS order_id, o.user_id, o.status AS order_status, 
       p.amount, p.status AS payment_status, p.payment_method
FROM orders o
JOIN payments p ON o.id = p.order_id;

-- Check feedback by product-- 
select * from feedback where product_id='20';