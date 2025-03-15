-- Start Giả định giỏ hàng
INSERT INTO cart (user_id, product_id, quantity, created_at)  
VALUES 
(1, 2, 1, NOW()),
(1, 2, 1, NOW()),
(1, 4, 7, NOW()),
(1, 2, 3, NOW()),
(1, 7, 8, NOW());
select * from cart;

-- Check giả định order_item
select * from order_items where order_id = 2;


-- Check giả định đơn hàng đã được đặt thành công
SELECT o.id AS order_id, o.user_id, o.status AS order_status, 
       p.amount, p.status AS payment_status, p.payment_method
FROM orders o
JOIN payments p ON o.id = p.order_id;